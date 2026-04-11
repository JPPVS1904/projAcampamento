<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\Place;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSectorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'place' => ['sometimes', 'required', Rule::enum(Place::class)],
            'base_vacancies' => ['sometimes', 'required', 'integer', 'min:0'],
            'raffle_vacancies' => ['sometimes', 'required', 'integer', 'min:0'],
        ];
    }
}
