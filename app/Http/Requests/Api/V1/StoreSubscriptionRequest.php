<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\SubscriptionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubscriptionRequest extends FormRequest
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
            'subscription_date' => ['required', 'date'],
            'subscription_type' => ['required', Rule::enum(SubscriptionType::class)],
            'was_selected' => ['required', 'boolean'],
            'substitute_position' => ['required', 'integer', 'min:0'],
            'paid_the_fee' => ['required', 'boolean'],
            'is_quitter' => ['required', 'boolean'],
            'payment_code' => ['required', 'string', 'max:255'],
            'qrcode_data' => ['required', 'string', 'max:255'],
            'used_qrcode' => ['required', 'boolean'],
            'selection_method_id' => ['required', 'integer', 'exists:selection_methods,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'spouse_id' => ['nullable', 'integer', 'exists:users,id'],
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'sector_id' => ['nullable', 'integer', 'exists:sectors,id'],
        ];
    }
}
