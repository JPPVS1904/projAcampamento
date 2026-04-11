<?php

namespace App\Http\Resources\V1;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Event */
class EventResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image,
            'place' => $this->place,
            'year' => $this->year,
            'start_date' => $this->start_date,
            'duration_days' => $this->duration_days,
            'total_vacancies' => $this->total_vacancies,
            'eventable_id' => $this->eventable_id,
            'eventable_type' => $this->eventable_type,
            'eventable' => $this->whenLoaded('eventable'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
