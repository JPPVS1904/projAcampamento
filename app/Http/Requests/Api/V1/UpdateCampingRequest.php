<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCampingRequest extends FormRequest
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
            'notice' => ['sometimes', 'required', 'string', 'max:255'],
            'term' => ['sometimes', 'required', 'string', 'max:255'],
            'image' => ['sometimes', 'required', 'string', 'max:255'],
            'minimal_age' => ['sometimes', 'required', 'integer', 'min:0'],
            'maximal_age' => ['sometimes', 'required', 'integer', 'min:0'],
            'camper_fee' => ['sometimes', 'required', 'numeric', 'min:0'],
            'servant_fee' => ['sometimes', 'required', 'numeric', 'min:0'],
            'planned_man_vacancies' => ['sometimes', 'required', 'integer', 'min:0'],
            'planned_woman_vacancies' => ['sometimes', 'required', 'integer', 'min:0'],
            'planned_couple_vacancies' => ['sometimes', 'required', 'integer', 'min:0'],
            'raffle_man_vacancies' => ['sometimes', 'required', 'integer', 'min:0'],
            'raffle_woman_vacancies' => ['sometimes', 'required', 'integer', 'min:0'],
            'raffle_couple_vacancies' => ['sometimes', 'required', 'integer', 'min:0'],
            'raffle_total_vacancies' => ['sometimes', 'required', 'integer', 'min:0'],
            'raffle_camper_subscription_start_date' => ['sometimes', 'required', 'date'],
            'raffle_camper_subscription_end_date' => ['sometimes', 'required', 'date'],
            'raffle_camper_date' => ['sometimes', 'required', 'date'],
            'raffle_servant_subscription_start_date' => ['sometimes', 'required', 'date'],
            'raffle_servant_subscription_end_date' => ['sometimes', 'required', 'date'],
            'raffle_servant_date' => ['sometimes', 'required', 'date'],
            'camper_registration_start_date' => ['sometimes', 'required', 'date'],
            'camper_registration_end_date' => ['sometimes', 'required', 'date'],
            'camper_payment_link' => ['sometimes', 'required', 'date'],
            'camper_payment_date' => ['sometimes', 'required', 'date'],
            'servant_registration_start_date' => ['sometimes', 'required', 'date'],
            'servant_registration_end_date' => ['sometimes', 'required', 'date'],
            'servant_payment_link' => ['sometimes', 'required', 'date'],
            'servant_payment_date' => ['sometimes', 'required', 'date'],
        ];
    }
}
