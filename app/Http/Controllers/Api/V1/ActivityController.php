<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreActivityRequest;
use App\Http\Requests\Api\V1\UpdateActivityRequest;
use App\Http\Resources\V1\ActivityResource;
use App\Models\Activity;
use App\Models\Camping;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
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
            // Criar o modelo específico (Camping ou Event)
            /** @var Camping|Event $activitable */
            $activitable = $activitableType::create($activitableData);

            // Criar a Activity associada
            $activity = Activity::create([
                'name' => $validated['name'],
                'image' => $validated['image'] ?? '',
                'place' => $validated['place'],
                'year' => $validated['year'],
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
            // Atualizar dados específicos do activitable se fornecidos
            if (isset($validated['activitable_data'])) {
                $activity->activitable->update($validated['activitable_data']);
            }

            // Atualizar campos gerais da Activity
            $activityData = collect($validated)->except(['activitable_data'])->toArray();
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
            // Deletar o activitable associado
            if ($activity->activitable) {
                $activity->activitable->delete();
            }
            $activity->delete();
        });

        return response()->noContent();
    }
}
