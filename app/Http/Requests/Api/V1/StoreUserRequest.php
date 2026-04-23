<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\Sex;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'cpf' => ['required', 'string', 'max:14'],
            'name' => ['required', 'string', 'max:255'],
            'birthday' => ['required', 'date'],
            'sex' => ['required', Rule::enum(Sex::class)],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'is_counselor' => ['required', 'boolean'],
            'picture' => ['string'],
            'document' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8'],
            'marital_status_id' => ['required', 'integer', 'exists:marital_statuses,id'],
        ];
    }
}
