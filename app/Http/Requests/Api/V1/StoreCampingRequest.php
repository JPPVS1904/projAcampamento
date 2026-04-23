<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampingRequest extends FormRequest
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
            'notice' => ['string', 'max:255'],
            'term' => ['string', 'max:255'],
            'image' => ['string', 'max:255'],
            'minimal_age' => ['required', 'integer', 'min:0'],
            'maximal_age' => ['required', 'integer', 'min:0'],
            'camper_fee' => ['required', 'numeric', 'min:0'],
            'servant_fee' => ['required', 'numeric', 'min:0'],
            'planned_man_vacancies' => ['integer', 'min:0'],
            'planned_woman_vacancies' => ['integer', 'min:0'],
            'planned_couple_vacancies' => ['integer', 'min:0'],
            'raffle_man_vacancies' => ['required', 'integer', 'min:0'],
            'raffle_woman_vacancies' => ['required', 'integer', 'min:0'],
            'raffle_couple_vacancies' => ['required', 'integer', 'min:0'],
            'raffle_total_vacancies' => ['required', 'integer', 'min:0'],
            'raffle_camper_subscription_start_date' => ['required', 'date'],
            'raffle_camper_subscription_end_date' => ['required', 'date'],
            'raffle_camper_date' => ['required', 'date'],
            'raffle_servant_subscription_start_date' => ['required', 'date'],
            'raffle_servant_subscription_end_date' => ['required', 'date'],
            'raffle_servant_date' => ['required', 'date'],
            'camper_registration_start_date' => ['required', 'date'],
            'camper_registration_end_date' => ['required', 'date'],
            'camper_payment_link' => ['date'],
            'camper_payment_date' => ['date'],
            'servant_registration_start_date' => ['required', 'date'],
            'servant_registration_end_date' => ['required', 'date'],
            'servant_payment_link' => ['required', 'date'],
            'servant_payment_date' => ['required', 'date'],
        ];
    }
}
