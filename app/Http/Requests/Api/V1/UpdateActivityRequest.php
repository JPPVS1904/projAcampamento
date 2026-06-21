<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Camping;
use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateActivityRequest extends FormRequest
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
            // Campos gerais da Activity
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'image' => ['sometimes', 'nullable', 'string', 'max:255'],
            'place' => ['sometimes', 'required', 'string', 'max:255'],
            'start_date' => ['sometimes', 'required', 'date'],
            'duration_days' => ['sometimes', 'required', 'integer', 'min:1'],
            'total_vacancies' => ['sometimes', 'required', 'integer', 'min:0'],
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
            'activitable_type' => ['sometimes', 'required', 'string', Rule::in([Camping::class, Event::class])],

            // Campos específicos do activitable (aninhados)
            'activitable_data' => ['sometimes', 'required', 'array'],

            // ── Camping fields ──
            'activitable_data.notice' => ['sometimes', 'nullable', 'string', 'max:255'],
            'activitable_data.term' => ['sometimes', 'nullable', 'string', 'max:255'],
            'activitable_data.camper_fee' => ['sometimes', 'numeric', 'min:0'],
            'activitable_data.servant_fee' => ['sometimes', 'numeric', 'min:0'],
            'activitable_data.planned_man_vacancies' => ['sometimes', 'integer', 'min:0'],
            'activitable_data.planned_woman_vacancies' => ['sometimes', 'integer', 'min:0'],
            'activitable_data.planned_couple_vacancies' => ['sometimes', 'integer', 'min:0'],
            'activitable_data.raffle_camper_subscription_start_date' => ['sometimes', 'date'],
            'activitable_data.raffle_camper_subscription_end_date' => ['sometimes', 'date'],
            'activitable_data.raffle_camper_date' => ['sometimes', 'date'],
            'activitable_data.raffle_servant_subscription_start_date' => ['sometimes', 'date'],
            'activitable_data.raffle_servant_subscription_end_date' => ['sometimes', 'date'],
            'activitable_data.raffle_servant_date' => ['sometimes', 'date'],
            'activitable_data.camper_registration_start_date' => ['sometimes', 'date'],
            'activitable_data.camper_registration_end_date' => ['sometimes', 'date'],
            'activitable_data.camper_payment_link' => ['sometimes', 'nullable', 'string', 'max:255'],
            'activitable_data.camper_payment_date' => ['sometimes', 'nullable', 'date'],
            'activitable_data.servant_registration_start_date' => ['sometimes', 'date'],
            'activitable_data.servant_registration_end_date' => ['sometimes', 'date'],
            'activitable_data.servant_payment_link' => ['sometimes', 'nullable', 'string', 'max:255'],
            'activitable_data.servant_payment_date' => ['sometimes', 'nullable', 'date'],

            // ── Event fields ──
            'activitable_data.minimal_age' => ['sometimes', 'integer', 'min:0'],
            'activitable_data.ticket_price' => ['sometimes', 'integer', 'min:0'],
            'activitable_data.sale_start_date' => ['sometimes', 'date'],
            'activitable_data.payment_link' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
