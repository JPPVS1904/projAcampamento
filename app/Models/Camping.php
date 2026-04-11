<?php

namespace App\Models;

use Database\Factories\CampingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'notice',
    'term',
    'image',
    'minimal_age',
    'maximal_age',
    'camper_fee',
    'servant_fee',
    'planned_man_vacancies',
    'planned_woman_vacancies',
    'planned_couple_vacancies',
    'raffle_man_vacancies',
    'raffle_woman_vacancies',
    'raffle_couple_vacancies',
    'raffle_total_vacancies',
    'raffle_camper_subscription_start_date',
    'raffle_camper_subscription_end_date',
    'raffle_camper_date',
    'raffle_servant_subscription_start_date',
    'raffle_servant_subscription_end_date',
    'raffle_servant_date',
    'camper_registration_start_date',
    'camper_registration_end_date',
    'camper_payment_link',
    'camper_payment_date',
    'servant_registration_start_date',
    'servant_registration_end_date',
    'servant_payment_link',
    'servant_payment_date',
])]
class Camping extends Model
{
    /** @use HasFactory<CampingFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'minimal_age' => 'integer',
            'maximal_age' => 'integer',
            'camper_fee' => 'decimal:2',
            'servant_fee' => 'decimal:2',
            'planned_man_vacancies' => 'integer',
            'planned_woman_vacancies' => 'integer',
            'planned_couple_vacancies' => 'integer',
            'raffle_man_vacancies' => 'integer',
            'raffle_woman_vacancies' => 'integer',
            'raffle_couple_vacancies' => 'integer',
            'raffle_total_vacancies' => 'integer',
            'raffle_camper_subscription_start_date' => 'datetime',
            'raffle_camper_subscription_end_date' => 'datetime',
            'raffle_camper_date' => 'datetime',
            'raffle_servant_subscription_start_date' => 'datetime',
            'raffle_servant_subscription_end_date' => 'datetime',
            'raffle_servant_date' => 'datetime',
            'camper_registration_start_date' => 'datetime',
            'camper_registration_end_date' => 'datetime',
            'camper_payment_link' => 'datetime',
            'camper_payment_date' => 'datetime',
            'servant_registration_start_date' => 'datetime',
            'servant_registration_end_date' => 'datetime',
            'servant_payment_link' => 'datetime',
            'servant_payment_date' => 'datetime',
        ];
    }

    public function event(): MorphOne
    {
        return $this->morphOne(Event::class, 'eventable');
    }
}
