<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreActivityRequest;
use App\Http\Requests\Api\V1\UpdateActivityRequest;
use App\Http\Resources\V1\ActivityResource;
use App\Models\Activity;
use App\Models\Camping;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Activity::with(['activitable', 'category']);

        if ($request->boolean('available')) {
            $query->whereRaw('DATE_ADD(start_date, INTERVAL duration_days DAY) >= ?', [now()]);
        }

        return ActivityResource::collection($query->paginate($request->input('per_page', 100)));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityRequest $request): ActivityResource
    {
        $validated = $request->validated();
        $activitableData = $validated['activitable_data'];
        $activitableType = $validated['activitable_type'];

        return DB::transaction(function () use ($validated, $activitableData, $activitableType) {
            if ($activitableType === Camping::class) {
                $activitableData = $this->autofillCampingData($activitableData, $validated);
                // Auto-calculate total_vacancies from planned vacancies
                $validated['total_vacancies'] = $activitableData['raffle_total_vacancies'];
            }

            if ($activitableType === Event::class) {
                $activitableData = $this->autofillEventData($activitableData);
                // Se total_vacancies <= 0, marcar como ilimitado (0)
                if (($validated['total_vacancies'] ?? 0) <= 0) {
                    $validated['total_vacancies'] = 0;
                }
                // Auto-atribuir categoria "Evento" se não fornecida
                if (empty($validated['category_id'])) {
                    $validated['category_id'] = $this->getOrCreateEventCategory()->id;
                }
            }

            /** @var Camping|Event $activitable */
            $activitable = $activitableType::create($activitableData);

            // Auto-derive year from start_date
            $year = Carbon::parse($validated['start_date'])->year;

            $activity = Activity::create([
                'name' => $validated['name'],
                'image' => $validated['image'] ?? '',
                'place' => $validated['place'],
                'year' => $year,
                'start_date' => $validated['start_date'],
                'duration_days' => $validated['duration_days'],
                'total_vacancies' => $validated['total_vacancies'],
                'category_id' => $validated['category_id'],
                'activitable_type' => $activitableType,
                'activitable_id' => $activitable->id,
            ]);

            return ActivityResource::make($activity->load(['activitable', 'category']));
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity): ActivityResource
    {
        return ActivityResource::make($activity->load(['activitable', 'category']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateActivityRequest $request, Activity $activity): ActivityResource
    {
        $validated = $request->validated();

        return DB::transaction(function () use ($validated, $activity) {
            if (isset($validated['activitable_data'])) {
                $activitableData = $validated['activitable_data'];

                if ($activity->activitable_type === Camping::class) {
                    // Merge com dados existentes para recalcular corretamente
                    $existing = $activity->activitable->toArray();
                    $merged = array_merge($existing, $activitableData);

                    $categoryId = $validated['category_id'] ?? $activity->category_id;
                    $validatedForAutofill = array_merge($validated, ['category_id' => $categoryId, 'image' => $validated['image'] ?? $activity->image ?? '']);
                    $activitableData = $this->autofillCampingData($merged, $validatedForAutofill);

                    // Recalcular total_vacancies da activity
                    $validated['total_vacancies'] = $activitableData['raffle_total_vacancies'];
                }

                if ($activity->activitable_type === Event::class) {
                    $activitableData = $this->autofillEventData($activitableData);
                    if (isset($validated['total_vacancies']) && $validated['total_vacancies'] <= 0) {
                        $validated['total_vacancies'] = 0;
                    }
                    // Auto-atribuir categoria "Evento" se não fornecida
                    if (empty($validated['category_id']) && empty($activity->category_id)) {
                        $validated['category_id'] = $this->getOrCreateEventCategory()->id;
                    }
                }

                $activity->activitable->update($activitableData);
            }

            $activityData = collect($validated)->except(['activitable_data'])->toArray();

            // Auto-derive year from start_date if start_date is being updated
            if (isset($activityData['start_date'])) {
                $activityData['year'] = Carbon::parse($activityData['start_date'])->year;
            }

            if (!empty($activityData)) {
                $activity->update($activityData);
            }

            return ActivityResource::make($activity->load(['activitable', 'category']));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity): Response
    {
        DB::transaction(function () use ($activity) {
            if ($activity->activitable) {
                $activity->activitable->delete();
            }
            $activity->delete();
        });

        return response()->noContent();
    }

    /**
     * Auto-preenche dados do Camping baseado na categoria e vagas planejadas.
     *
     * - minimal_age e maximal_age derivados da categoria
     * - raffle_*_vacancies = planned_*_vacancies
     * - raffle_total_vacancies = soma dos raffle
     * - image default
     */
    private function autofillCampingData(array $data, array $validated): array
    {
        // Auto-preencher faixa etária pela categoria
        $category = Category::findOrFail($validated['category_id']);
        $ageRange = $this->getAgeRangeByCategory($category->name);
        $data['minimal_age'] = $ageRange['minimal_age'];
        $data['maximal_age'] = $ageRange['maximal_age'];

        // Vagas de sorteio = vagas planejadas
        $data['raffle_man_vacancies'] = $data['planned_man_vacancies'] ?? 0;
        $data['raffle_woman_vacancies'] = $data['planned_woman_vacancies'] ?? 0;
        $data['raffle_couple_vacancies'] = $data['planned_couple_vacancies'] ?? 0;
        $data['raffle_total_vacancies'] =
            $data['raffle_man_vacancies'] +
            $data['raffle_woman_vacancies'] +
            ($data['raffle_couple_vacancies'] * 2);

        // Image default (campo obrigatório na tabela campings)
        $data['image'] = $data['image'] ?? $validated['image'] ?? '';

        return $data;
    }

    /**
     * Auto-preenche dados do Event.
     *
     * - is_paid_festival = ticket_price > 0
     */
    private function autofillEventData(array $data): array
    {
        $data['is_paid_festival'] = ($data['ticket_price'] ?? 0) > 0;

        return $data;
    }

    /**
     * Busca ou cria a categoria padrão para Eventos.
     */
    private function getOrCreateEventCategory(): Category
    {
        return Category::firstOrCreate(
            ['name' => 'Evento', 'type' => 'Evento'],
        );
    }

    /**
     * Retorna a faixa etária baseada no nome da categoria.
     *
     * Mirim: 10-12 | FAC: 14-17 | Juvenil: 18-24 | Sênior: 25-60
     * Casais/Discipulados: sem restrição de idade
     */
    private function getAgeRangeByCategory(string $categoryName): array
    {
        $name = mb_strtolower($categoryName);

        return match (true) {
            str_contains($name, 'mirim') => ['minimal_age' => 10, 'maximal_age' => 12],
            str_contains($name, 'fac') => ['minimal_age' => 14, 'maximal_age' => 17],
            str_contains($name, 'juvenil') => ['minimal_age' => 18, 'maximal_age' => 24],
            str_contains($name, 'sênior'),
            str_contains($name, 'senior'),
            str_contains($name, 'sénior') => ['minimal_age' => 25, 'maximal_age' => 60],
            str_contains($name, 'casais'),
            str_contains($name, 'casal') => ['minimal_age' => 0, 'maximal_age' => 150],
            str_contains($name, 'discipulado') => ['minimal_age' => 0, 'maximal_age' => 150],
            default => ['minimal_age' => 0, 'maximal_age' => 150],
        };
    }
}
