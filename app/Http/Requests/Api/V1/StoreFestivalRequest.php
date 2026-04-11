<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreFestivalRequest extends FormRequest
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
            'minimal_age' => ['required', 'integer', 'min:0'],
            'is_paid_festival' => ['required', 'boolean'],
            'ticket_price' => ['required', 'integer', 'min:0'],
            'sale_start_date' => ['required', 'date'],
            'payment_link' => ['required', 'integer'],
        ];
    }
}
