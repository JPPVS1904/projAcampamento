<?php

namespace App\Http\Resources\V1;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Subscription */
class SubscriptionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subscription_date' => $this->subscription_date,
            'subscription_type' => $this->subscription_type,
            'was_selected' => $this->was_selected,
            'substitute_position' => $this->substitute_position,
            'paid_the_fee' => $this->paid_the_fee,
            'is_quitter' => $this->is_quitter,
            'payment_code' => $this->payment_code,
            'qrcode_data' => $this->qrcode_data,
            'used_qrcode' => $this->used_qrcode,
            'selection_method_id' => $this->selection_method_id,
            'user_id' => $this->user_id,
            'spouse_id' => $this->spouse_id,
            'event_id' => $this->event_id,
            'sector_id' => $this->sector_id,
            'user' => UserResource::make($this->whenLoaded('user')),
            'spouse' => UserResource::make($this->whenLoaded('spouse')),
            'event' => EventResource::make($this->whenLoaded('event')),
            'sector' => SectorResource::make($this->whenLoaded('sector')),
            'selection_method' => SelectionMethodResource::make($this->whenLoaded('selectionMethod')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
