<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFestivalRequest extends FormRequest
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
            'minimal_age' => ['sometimes', 'required', 'integer', 'min:0'],
            'is_paid_festival' => ['sometimes', 'required', 'boolean'],
            'ticket_price' => ['sometimes', 'required', 'integer', 'min:0'],
            'sale_start_date' => ['sometimes', 'required', 'date'],
            'payment_link' => ['sometimes', 'required', 'integer'],
        ];
    }
}
