<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Camping extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
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
    'servant_payment_date'
    ];

    public function activity()
    {
        return $this->morphOne(Activity::class, 'activitable');
    }
}
