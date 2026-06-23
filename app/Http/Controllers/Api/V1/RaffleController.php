<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Camping;
use App\Models\PreRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RaffleController extends Controller
{
    /**
     * List active campings with their raffle status.
     */
    public function index(): JsonResponse
    {
        $activities = Activity::with(['activitable', 'category'])
            ->where('activitable_type', Camping::class)
            ->whereRaw('DATE_ADD(start_date, INTERVAL duration_days DAY) >= ?', [now()])
            ->get();

        $result = $activities->map(function ($activity) {
            $camping = $activity->activitable;

            // Count how many campers have already been selected (selection_method_id is not null)
            $selectedCampers = PreRegistration::where('activity_id', $activity->id)
                ->where('subscription_type', 'Campista')
                ->whereHas('campingPreRegistration', function ($q) {
                    $q->whereNotNull('selection_method_id');
                })
                ->count();

            // Count total camper subscribers
            $totalCamperSubscribers = PreRegistration::where('activity_id', $activity->id)
                ->where('subscription_type', 'Campista')
                ->count();

            // Count total servant subscribers
            $totalServantSubscribers = PreRegistration::where('activity_id', $activity->id)
                ->where('subscription_type', 'Servo')
                ->count();

            // Count selected servants
            $selectedServants = PreRegistration::where('activity_id', $activity->id)
                ->where('subscription_type', 'Servo')
                ->whereHas('campingPreRegistration', function ($q) {
                    $q->whereNotNull('selection_method_id');
                })
                ->count();

            return [
                'activity_id' => $activity->id,
                'name' => $activity->name,
                'image' => $activity->image,
                'category' => $activity->category?->name,
                'start_date' => $activity->start_date,
                'duration_days' => $activity->duration_days,
                'raffle_camper_date' => $camping->raffle_camper_date,
                'raffle_servant_date' => $camping->raffle_servant_date,
                'planned_man_vacancies' => $camping->planned_man_vacancies,
                'planned_woman_vacancies' => $camping->planned_woman_vacancies,
                'planned_couple_vacancies' => $camping->planned_couple_vacancies,
                'total_camper_subscribers' => $totalCamperSubscribers,
                'total_servant_subscribers' => $totalServantSubscribers,
                'selected_campers' => $selectedCampers,
                'selected_servants' => $selectedServants,
                'camper_raffle_done' => $selectedCampers > 0,
                'servant_raffle_done' => $selectedServants > 0,
            ];
        });

        return response()->json(['data' => $result]);
    }

    /**
     * Perform the camper raffle for a given activity.
     *
     * Randomly selects subscribers per vacancy type:
     * - Male campers (sex = 'M', marital_status != casado) => planned_man_vacancies
     * - Female campers (sex = 'F', marital_status != casado) => planned_woman_vacancies
     * - Couple campers (marital_status = casado) => planned_couple_vacancies
     */
    public function raffleCampers(Request $request, int $activityId): JsonResponse
    {
        $activity = Activity::with('activitable')->findOrFail($activityId);

        if ($activity->activitable_type !== Camping::class) {
            return response()->json(['message' => 'Esta atividade não é um acampamento.'], 422);
        }

        $camping = $activity->activitable;

        // Check if raffle date has arrived
        if (now()->lt($camping->raffle_camper_date)) {
            return response()->json(['message' => 'A data do sorteio de campistas ainda não chegou.'], 422);
        }

        // Check if raffle was already done
        $alreadySelected = PreRegistration::where('activity_id', $activityId)
            ->where('subscription_type', 'Campista')
            ->whereHas('campingPreRegistration', function ($q) {
                $q->whereNotNull('selection_method_id');
            })
            ->count();

        if ($alreadySelected > 0) {
            return response()->json(['message' => 'O sorteio de campistas já foi realizado para este acampamento.'], 422);
        }

        return DB::transaction(function () use ($activityId, $camping) {
            // Get all camper pre-registrations with user data
            $allCamperSubs = PreRegistration::with(['user', 'campingPreRegistration'])
                ->where('activity_id', $activityId)
                ->where('subscription_type', 'Campista')
                ->get();

            // Married status IDs — married users are treated as "couple"
            // marital_status_id = 2 is typically "Casado(a)" based on CakePHP schema
            $marriedStatusId = 2;

            // Separate subscribers by type
            $maleSingles = $allCamperSubs->filter(function ($sub) use ($marriedStatusId) {
                return $sub->user &&
                    $sub->user->sex === 'M' &&
                    $sub->user->marital_status_id != $marriedStatusId;
            });

            $femaleSingles = $allCamperSubs->filter(function ($sub) use ($marriedStatusId) {
                return $sub->user &&
                    $sub->user->sex === 'F' &&
                    $sub->user->marital_status_id != $marriedStatusId;
            });

            $couples = $allCamperSubs->filter(function ($sub) use ($marriedStatusId) {
                return $sub->user &&
                    $sub->user->marital_status_id == $marriedStatusId;
            });

            $results = [
                'male' => $this->selectByRaffle($maleSingles, $camping->planned_man_vacancies),
                'female' => $this->selectByRaffle($femaleSingles, $camping->planned_woman_vacancies),
                'couple' => $this->selectByRaffle($couples, $camping->planned_couple_vacancies),
            ];

            $totalSelected = $results['male']['selected'] + $results['female']['selected'] + $results['couple']['selected'];

            return response()->json([
                'message' => "Sorteio de campistas realizado com sucesso! {$totalSelected} campistas selecionados.",
                'data' => $results,
            ]);
        });
    }

    /**
     * Select random subscribers and mark them as selected.
     */
    private function selectByRaffle($subscribers, int $vacancies): array
    {
        $shuffled = $subscribers->shuffle();
        $selected = $shuffled->take($vacancies);
        $substitutes = $shuffled->slice($vacancies)->values();

        // Mark selected
        foreach ($selected as $sub) {
            if ($sub->campingPreRegistration) {
                $sub->campingPreRegistration->update([
                    'selection_method_id' => 1, // 1 = Sorteio
                    'substitute_position' => null,
                ]);
            }
        }

        // Mark substitutes with position
        foreach ($substitutes as $index => $sub) {
            if ($sub->campingPreRegistration) {
                $sub->campingPreRegistration->update([
                    'substitute_position' => $index + 1,
                ]);
            }
        }

        return [
            'vacancies' => $vacancies,
            'subscribers' => $subscribers->count(),
            'selected' => $selected->count(),
            'substitutes' => $substitutes->count(),
        ];
    }
}
