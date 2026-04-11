<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Camping;
use App\Models\Festival;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventRequest extends FormRequest
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
            'image' => ['sometimes', 'required', 'string', 'max:255'],
            'place' => ['sometimes', 'required', 'string', 'max:255'],
            'year' => ['sometimes', 'required', 'integer', 'min:1900', 'max:2155'],
            'start_date' => ['sometimes', 'required', 'date'],
            'duration_days' => ['sometimes', 'required', 'integer', 'min:1'],
            'total_vacancies' => ['sometimes', 'required', 'integer', 'min:0'],
            'eventable_id' => ['sometimes', 'required', 'integer'],
            'eventable_type' => ['sometimes', 'required', 'string', Rule::in([Camping::class, Festival::class])],
        ];
    }
}
