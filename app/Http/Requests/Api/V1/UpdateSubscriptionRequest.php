<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\SubscriptionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubscriptionRequest extends FormRequest
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
            'subscription_date' => ['sometimes', 'required', 'date'],
            'subscription_type' => ['sometimes', 'required', Rule::enum(SubscriptionType::class)],
            'was_selected' => ['sometimes', 'required', 'boolean'],
            'substitute_position' => ['sometimes', 'required', 'integer', 'min:0'],
            'paid_the_fee' => ['sometimes', 'required', 'boolean'],
            'is_quitter' => ['sometimes', 'required', 'boolean'],
            'payment_code' => ['sometimes', 'required', 'string', 'max:255'],
            'qrcode_data' => ['sometimes', 'required', 'string', 'max:255'],
            'used_qrcode' => ['sometimes', 'required', 'boolean'],
            'selection_method_id' => ['sometimes', 'required', 'integer', 'exists:selection_methods,id'],
            'user_id' => ['sometimes', 'required', 'integer', 'exists:users,id'],
            'spouse_id' => ['nullable', 'integer', 'exists:users,id'],
            'event_id' => ['sometimes', 'required', 'integer', 'exists:events,id'],
            'sector_id' => ['nullable', 'integer', 'exists:sectors,id'],
        ];
    }
}
