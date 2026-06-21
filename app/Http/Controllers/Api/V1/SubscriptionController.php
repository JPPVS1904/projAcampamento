<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SubscriptionResource;
use App\Models\CampingPreRegistration;
use App\Models\PreRegistration;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * List subscriptions (pre_registrations), optionally filtered by user_id.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = PreRegistration::with([
            'activity.activitable',
            'activity.category',
            'campingPreRegistration',
        ]);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        return SubscriptionResource::collection(
            $query->paginate($request->input('per_page', 100))
        );
    }

    /**
     * Store a new subscription (creates pre_registration + camping_pre_registration).
     */
    public function store(Request $request): SubscriptionResource
    {
        $validated = $request->validate([
            'subscription_type' => ['required', 'string', 'in:Servo,Campista,Participante'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'activity_id' => ['nullable', 'integer', 'exists:activities,id'],
            'event_id' => ['nullable', 'integer'],
        ]);

        // Support both activity_id and legacy event_id
        $activityId = $validated['activity_id'] ?? $validated['event_id'] ?? null;

        return DB::transaction(function () use ($validated, $activityId) {
            // Create camping_pre_registration first (required FK)
            $campingPreReg = CampingPreRegistration::create([
                'substitute_position' => null,
                'is_quitter' => false,
                'selection_method_id' => null,
                'spouse_id' => null,
                'sector_id' => null,
                'sector2_id' => null,
            ]);

            $preRegistration = PreRegistration::create([
                'subscription_type' => $validated['subscription_type'],
                'is_fee_paid' => false,
                'payment_code' => null,
                'qrcode_data' => null,
                'is_qrcode_used' => false,
                'user_id' => $validated['user_id'],
                'activity_id' => $activityId,
                'camping_pre_registration_id' => $campingPreReg->id,
            ]);

            return SubscriptionResource::make(
                $preRegistration->load(['activity.activitable', 'activity.category', 'campingPreRegistration'])
            );
        });
    }

    /**
     * Show a single subscription.
     */
    public function show(PreRegistration $subscription): SubscriptionResource
    {
        return SubscriptionResource::make(
            $subscription->load(['activity.activitable', 'activity.category', 'campingPreRegistration'])
        );
    }

    /**
     * Update a subscription.
     */
    public function update(Request $request, PreRegistration $subscription): SubscriptionResource
    {
        $validated = $request->validate([
            'subscription_type' => ['sometimes', 'string', 'in:Servo,Campista,Participante'],
            'paid_the_fee' => ['sometimes', 'boolean'],
            'was_selected' => ['sometimes', 'boolean'],
            'is_quitter' => ['sometimes', 'boolean'],
        ]);

        DB::transaction(function () use ($validated, $subscription) {
            // Update pre_registration fields
            $preRegData = [];
            if (isset($validated['subscription_type'])) {
                $preRegData['subscription_type'] = $validated['subscription_type'];
            }
            if (isset($validated['paid_the_fee'])) {
                $preRegData['is_fee_paid'] = $validated['paid_the_fee'];
            }
            if (!empty($preRegData)) {
                $subscription->update($preRegData);
            }

            // Update camping_pre_registration fields
            if ($subscription->campingPreRegistration) {
                $campingData = [];
                if (isset($validated['was_selected'])) {
                    // If was_selected is true, assign selection_method_id = 1 (Sorteio)
                    $campingData['selection_method_id'] = $validated['was_selected'] ? 1 : null;
                }
                if (isset($validated['is_quitter'])) {
                    $campingData['is_quitter'] = $validated['is_quitter'];
                }
                if (!empty($campingData)) {
                    $subscription->campingPreRegistration->update($campingData);
                }
            }
        });

        return SubscriptionResource::make(
            $subscription->load(['activity.activitable', 'activity.category', 'campingPreRegistration'])
        );
    }

    /**
     * Delete a subscription.
     */
    public function destroy(PreRegistration $subscription): Response
    {
        DB::transaction(function () use ($subscription) {
            if ($subscription->campingPreRegistration) {
                $subscription->campingPreRegistration->delete();
            }
            $subscription->delete();
        });

        return response()->noContent();
    }
}
