<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\Place;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSectorRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'place' => ['required', Rule::enum(Place::class)],
            'base_vacancies' => ['required', 'integer', 'min:0'],
            'raffle_vacancies' => ['required', 'integer', 'min:0'],
        ];
    }
}
