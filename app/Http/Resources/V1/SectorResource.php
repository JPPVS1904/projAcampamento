<?php

namespace App\Http\Resources\V1;

use App\Models\Sector;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Sector */
class SectorResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'place' => $this->place,
            'base_vacancies' => $this->base_vacancies,
            'raffle_vacancies' => $this->raffle_vacancies,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
