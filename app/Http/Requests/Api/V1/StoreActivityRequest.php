<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Camping;
use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActivityRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'place' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1900', 'max:2155'],
            'start_date' => ['required', 'date'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'total_vacancies' => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'activitable_type' => ['required', 'string', Rule::in([Camping::class, Event::class])],

            // Campos específicos do activitable (aninhados)
            'activitable_data' => ['required', 'array'],

            // Camping fields
            'activitable_data.notice' => ['nullable', 'required_if:activitable_type,' . Camping::class, 'string', 'max:255'],
            'activitable_data.term' => ['nullable', 'required_if:activitable_type,' . Camping::class, 'string', 'max:255'],
            'activitable_data.minimal_age' => ['required_if:activitable_type,' . Camping::class, 'integer', 'min:0'],
            'activitable_data.maximal_age' => ['required_if:activitable_type,' . Camping::class, 'integer', 'min:0'],
            'activitable_data.camper_fee' => ['required_if:activitable_type,' . Camping::class, 'numeric', 'min:0'],
            'activitable_data.servant_fee' => ['required_if:activitable_type,' . Camping::class, 'numeric', 'min:0'],
            'activitable_data.planned_man_vacancies' => ['nullable', 'integer', 'min:0'],
            'activitable_data.planned_woman_vacancies' => ['nullable', 'integer', 'min:0'],
            'activitable_data.planned_couple_vacancies' => ['nullable', 'integer', 'min:0'],
            'activitable_data.raffle_man_vacancies' => ['required_if:activitable_type,' . Camping::class, 'integer', 'min:0'],
            'activitable_data.raffle_woman_vacancies' => ['required_if:activitable_type,' . Camping::class, 'integer', 'min:0'],
            'activitable_data.raffle_couple_vacancies' => ['required_if:activitable_type,' . Camping::class, 'integer', 'min:0'],
            'activitable_data.raffle_total_vacancies' => ['required_if:activitable_type,' . Camping::class, 'integer', 'min:0'],
            'activitable_data.raffle_camper_subscription_start_date' => ['required_if:activitable_type,' . Camping::class, 'date'],
            'activitable_data.raffle_camper_subscription_end_date' => ['required_if:activitable_type,' . Camping::class, 'date'],
            'activitable_data.raffle_camper_date' => ['required_if:activitable_type,' . Camping::class, 'date'],
            'activitable_data.raffle_servant_subscription_start_date' => ['required_if:activitable_type,' . Camping::class, 'date'],
            'activitable_data.raffle_servant_subscription_end_date' => ['required_if:activitable_type,' . Camping::class, 'date'],
            'activitable_data.raffle_servant_date' => ['required_if:activitable_type,' . Camping::class, 'date'],
            'activitable_data.camper_registration_start_date' => ['required_if:activitable_type,' . Camping::class, 'date'],
            'activitable_data.camper_registration_end_date' => ['required_if:activitable_type,' . Camping::class, 'date'],
            'activitable_data.camper_payment_link' => ['nullable', 'string', 'max:255'],
            'activitable_data.camper_payment_date' => ['nullable', 'date'],
            'activitable_data.servant_registration_start_date' => ['required_if:activitable_type,' . Camping::class, 'date'],
            'activitable_data.servant_registration_end_date' => ['required_if:activitable_type,' . Camping::class, 'date'],
            'activitable_data.servant_payment_link' => ['nullable', 'string', 'max:255'],
            'activitable_data.servant_payment_date' => ['nullable', 'date'],

            // Event fields
            'activitable_data.is_paid_festival' => ['required_if:activitable_type,' . Event::class, 'boolean'],
            'activitable_data.ticket_price' => ['required_if:activitable_type,' . Event::class, 'integer', 'min:0'],
            'activitable_data.sale_start_date' => ['required_if:activitable_type,' . Event::class, 'date'],
            'activitable_data.payment_link' => ['nullable', 'string', 'max:255'],
        ];
    }
}
