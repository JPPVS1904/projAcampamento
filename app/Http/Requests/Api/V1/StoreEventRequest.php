<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Camping;
use App\Models\Festival;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
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
            'image' => ['string', 'max:255'],
            'place' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1900', 'max:2155'],
            'start_date' => ['required', 'date'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'total_vacancies' => ['required', 'integer', 'min:0'],
            'eventable_id' => ['required', 'integer'],
            'eventable_type' => ['required', 'string', Rule::in([Camping::class, Festival::class])],
        ];
    }
}
