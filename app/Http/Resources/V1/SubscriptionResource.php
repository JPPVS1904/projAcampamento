<?php

namespace App\Http\Resources\V1;

use App\Models\PreRegistration;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin PreRegistration */
class SubscriptionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $campingPreReg = $this->whenLoaded('campingPreRegistration');
        $activity = $this->whenLoaded('activity');

        return [
            'id' => $this->id,
            'subscription_type' => $this->subscription_type,
            'paid_the_fee' => (bool) $this->is_fee_paid,
            'was_selected' => $campingPreReg && isset($campingPreReg->selection_method_id)
                ? $campingPreReg->selection_method_id !== null
                : false,
            'substitute_position' => $campingPreReg->substitute_position ?? null,
            'is_quitter' => $campingPreReg ? (bool) ($campingPreReg->is_quitter ?? false) : false,
            'is_approved' => $campingPreReg ? (bool) ($campingPreReg->is_approved ?? false) : false,
            'has_answered_form' => $this->answers()->exists(),
            'payment_code' => $this->payment_code,
            'qrcode_data' => $this->qrcode_data,
            'used_qrcode' => (bool) $this->is_qrcode_used,
            'user_id' => $this->user_id,
            'activity_id' => $this->activity_id,
            'event' => $this->when($activity, function () use ($activity) {
                return [
                    'id' => $activity->id,
                    'name' => $activity->name,
                    'image' => $activity->image,
                    'place' => $activity->place,
                    'year' => $activity->year,
                    'start_date' => $activity->start_date,
                    'duration_days' => $activity->duration_days,
                    'total_vacancies' => $activity->total_vacancies,
                    'activitable_type' => $activity->activitable_type,
                    'activitable_id' => $activity->activitable_id,
                    'activitable' => $activity->activitable,
                    'category' => $activity->category,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
