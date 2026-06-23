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
        $activities = Activity::with(['activitable', 'category.sectors'])
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

            // Build sector vacancies from category
            $sectorVacancies = [];
            if ($activity->category) {
                foreach ($activity->category->sectors as $sector) {
                    $sectorVacancies[] = [
                        'sector_id' => $sector->id,
                        'sector_name' => $sector->name,
                        'place' => $sector->place,
                        'raffle_vacancies' => $sector->pivot->raffle_vacancies,
                        'base_vacancies' => $sector->pivot->base_vacancies,
                    ];
                }
            }

            return [
                'activity_id' => $activity->id,
                'name' => $activity->name,
                'image' => $activity->image,
                'category' => $activity->category?->name,
                'category_id' => $activity->category_id,
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
                'sector_vacancies' => $sectorVacancies,
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
                'male' => $this->selectByRaffle($maleSingles, $camping->planned_man_vacancies, $activity),
                'female' => $this->selectByRaffle($femaleSingles, $camping->planned_woman_vacancies, $activity),
                'couple' => $this->selectByRaffle($couples, $camping->planned_couple_vacancies, $activity),
            ];

            $totalSelected = $results['male']['selected'] + $results['female']['selected'] + $results['couple']['selected'];

            return response()->json([
                'message' => "Sorteio de campistas realizado com sucesso! {$totalSelected} campistas selecionados.",
                'data' => $results,
            ]);
        });
    }

    /**
     * Perform the servant raffle for a given activity.
     *
     * 3-phase raffle:
     * 1. Assign servants to their 1st sector preference
     * 2. Assign remaining servants to their 2nd sector preference
     * 3. Fill remaining sector vacancies with unassigned servants
     */
    public function raffleServants(Request $request, int $activityId): JsonResponse
    {
        $activity = Activity::with(['activitable', 'category.sectors'])->findOrFail($activityId);

        if ($activity->activitable_type !== Camping::class) {
            return response()->json(['message' => 'Esta atividade não é um acampamento.'], 422);
        }

        $camping = $activity->activitable;

        // Check if raffle date has arrived
        if (now()->lt($camping->raffle_servant_date)) {
            return response()->json(['message' => 'A data do sorteio de servos ainda não chegou.'], 422);
        }

        // Check if raffle was already done
        $alreadySelected = PreRegistration::where('activity_id', $activityId)
            ->where('subscription_type', 'Servo')
            ->whereHas('campingPreRegistration', function ($q) {
                $q->whereNotNull('selection_method_id');
            })
            ->count();

        if ($alreadySelected > 0) {
            return response()->json(['message' => 'O sorteio de servos já foi realizado para este acampamento.'], 422);
        }

        if (!$activity->category) {
            return response()->json(['message' => 'A atividade não possui categoria definida.'], 422);
        }

        return DB::transaction(function () use ($activityId, $activity) {
            // Get all servant pre-registrations
            $allServantSubs = PreRegistration::with(['user', 'campingPreRegistration'])
                ->where('activity_id', $activityId)
                ->where('subscription_type', 'Servo')
                ->get();

            if ($allServantSubs->isEmpty()) {
                return response()->json(['message' => 'Nenhum servo inscrito para este acampamento.'], 422);
            }

            // Build sector vacancy map from category
            $sectorVacancies = [];
            foreach ($activity->category->sectors as $sector) {
                $sectorVacancies[$sector->id] = [
                    'sector_id' => $sector->id,
                    'sector_name' => $sector->name,
                    'raffle_vacancies' => $sector->pivot->raffle_vacancies,
                    'remaining' => $sector->pivot->raffle_vacancies,
                    'categories_sectors_id' => $sector->pivot->id,
                    'selected' => [],
                    'subscribers_1st' => 0,
                    'subscribers_2nd' => 0,
                ];
            }

            $selectedIds = collect(); // Track already-selected pre_registration IDs

            // === PHASE 1: 1st preference ===
            foreach ($sectorVacancies as $sectorId => &$sectorData) {
                $preferring = $allServantSubs->filter(function ($sub) use ($sectorId, $selectedIds) {
                    return !$selectedIds->contains($sub->id) &&
                        $sub->campingPreRegistration &&
                        $sub->campingPreRegistration->sector_id == $sectorId;
                });

                $sectorData['subscribers_1st'] = $preferring->count();
                $shuffled = $preferring->shuffle();
                $toSelect = $shuffled->take($sectorData['remaining']);

                foreach ($toSelect as $sub) {
                    $selectedIds->push($sub->id);
                    $sectorData['selected'][] = $sub->id;
                    $sectorData['remaining']--;
                }
            }
            unset($sectorData);

            // === PHASE 2: 2nd preference ===
            foreach ($sectorVacancies as $sectorId => &$sectorData) {
                if ($sectorData['remaining'] <= 0) continue;

                $preferring2nd = $allServantSubs->filter(function ($sub) use ($sectorId, $selectedIds) {
                    return !$selectedIds->contains($sub->id) &&
                        $sub->campingPreRegistration &&
                        $sub->campingPreRegistration->sector2_id == $sectorId;
                });

                $sectorData['subscribers_2nd'] = $preferring2nd->count();
                $shuffled = $preferring2nd->shuffle();
                $toSelect = $shuffled->take($sectorData['remaining']);

                foreach ($toSelect as $sub) {
                    $selectedIds->push($sub->id);
                    $sectorData['selected'][] = $sub->id;
                    $sectorData['remaining']--;
                }
            }
            unset($sectorData);

            // === PHASE 3: Fill remaining vacancies with unassigned servants ===
            $unassigned = $allServantSubs->filter(function ($sub) use ($selectedIds) {
                return !$selectedIds->contains($sub->id);
            })->shuffle();

            foreach ($sectorVacancies as $sectorId => &$sectorData) {
                if ($sectorData['remaining'] <= 0 || $unassigned->isEmpty()) continue;

                $toSelect = $unassigned->splice(0, $sectorData['remaining']);
                foreach ($toSelect as $sub) {
                    $selectedIds->push($sub->id);
                    $sectorData['selected'][] = $sub->id;
                    $sectorData['remaining']--;
                }
            }
            unset($sectorData);

            // === Persist results ===
            $totalSelected = 0;
            foreach ($sectorVacancies as $sectorId => $sectorData) {
                foreach ($sectorData['selected'] as $preRegId) {
                    $sub = $allServantSubs->firstWhere('id', $preRegId);
                    if ($sub && $sub->campingPreRegistration) {
                        $sub->campingPreRegistration->update([
                            'selection_method_id' => 1, // 1 = Sorteio
                            'substitute_position' => null,
                        ]);
                    }

                    // Record in categories_sectors_users
                    DB::table('categories_sectors_users')->insert([
                        'user_id' => $sub->user_id,
                        'categories_sectors_id' => $sectorData['categories_sectors_id'],
                        'activity_id' => $activityId,
                        'is_coordinator' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    \App\Models\InboxMessage::create([
                        'user_id' => $sub->user_id,
                        'title' => 'Você foi convocado!',
                        'content' => "Parabéns! Você foi convocado(a) como Servo(a) no setor {$sectorData['sector_name']} para a atividade {$activity->name}. Acesse a aba Minhas Inscrições para mais detalhes."
                    ]);

                    $totalSelected++;
                }
            }

            // Mark remaining servants as substitutes
            $substitutePosition = 1;
            $remaining = $allServantSubs->filter(function ($sub) use ($selectedIds) {
                return !$selectedIds->contains($sub->id);
            });

            foreach ($remaining as $sub) {
                if ($sub->campingPreRegistration) {
                    $sub->campingPreRegistration->update([
                        'substitute_position' => $substitutePosition++,
                    ]);
                }
            }

            // Build response
            $resultData = [];
            foreach ($sectorVacancies as $sectorId => $sectorData) {
                $resultData[] = [
                    'sector_id' => $sectorData['sector_id'],
                    'sector_name' => $sectorData['sector_name'],
                    'vacancies' => $sectorData['raffle_vacancies'],
                    'selected' => count($sectorData['selected']),
                    'subscribers_1st_pref' => $sectorData['subscribers_1st'],
                    'subscribers_2nd_pref' => $sectorData['subscribers_2nd'],
                ];
            }

            return response()->json([
                'message' => "Sorteio de servos realizado com sucesso! {$totalSelected} servos selecionados.",
                'data' => [
                    'total_selected' => $totalSelected,
                    'total_substitutes' => $remaining->count(),
                    'sectors' => $resultData,
                ],
            ]);
        });
    }

    /**
     * Select random subscribers and mark them as selected.
     */
    private function selectByRaffle($subscribers, int $vacancies, Activity $activity): array
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
                
                if ($sub->user_id) {
                    \App\Models\InboxMessage::create([
                        'user_id' => $sub->user_id,
                        'title' => 'Você foi sorteado!',
                        'content' => "Parabéns! Você foi sorteado(a) como Campista para a atividade {$activity->name}. Acesse a aba Minhas Inscrições para realizar o pagamento e confirmar sua vaga."
                    ]);
                }
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
