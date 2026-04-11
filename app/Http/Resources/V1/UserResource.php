<?php

namespace App\Http\Resources\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cpf' => $this->cpf,
            'name' => $this->name,
            'birthday' => $this->birthday,
            'sex' => $this->sex,
            'phone' => $this->phone,
            'email' => $this->email,
            'is_counselor' => $this->is_counselor,
            'marital_status_id' => $this->marital_status_id,
            'marital_status' => MaritalStatusResource::make($this->whenLoaded('maritalStatus')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
