<?php

namespace App\Http\Resources\V1;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Activity */
class ActivityResource extends JsonResource
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
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category'),
            'activitable_type' => $this->activitable_type,
            'activitable_id' => $this->activitable_id,
            'activitable' => $this->whenLoaded('activitable'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
