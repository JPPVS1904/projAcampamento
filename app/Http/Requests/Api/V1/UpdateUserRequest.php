<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\Sex;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'cpf' => ['sometimes', 'required', 'string', 'max:14'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'birthday' => ['sometimes', 'required', 'date'],
            'sex' => ['sometimes', 'required', Rule::enum(Sex::class)],
            'phone' => ['sometimes', 'required', 'string', 'max:20'],
            'email' => ['sometimes', 'required', 'email', 'max:255'],
            'is_counselor' => ['sometimes', 'required', 'boolean'],
            'password' => ['sometimes', 'required', 'string', 'min:8'],
            'marital_status_id' => ['sometimes', 'required', 'integer', 'exists:marital_statuses,id'],
        ];
    }
}
