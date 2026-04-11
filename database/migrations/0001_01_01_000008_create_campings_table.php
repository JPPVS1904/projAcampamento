<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('campings', function (Blueprint $table) {
            $table->id();

            $table->string('notice');
            $table->string('term');
            $table->string('image');

            $table->integer('minimal_age');
            $table->integer('maximal_age');

            $table->decimal('camper_fee');
            $table->decimal('servant_fee');

            $table->integer('planned_man_vacancies');
            $table->integer('planned_woman_vacancies');
            $table->integer('planned_couple_vacancies');

            $table->integer('raffle_man_vacancies');
            $table->integer('raffle_woman_vacancies');
            $table->integer('raffle_couple_vacancies');
            $table->integer('raffle_total_vacancies');

            $table->datetime('raffle_camper_subscription_start_date');
            $table->datetime('raffle_camper_subscription_end_date');
            $table->datetime('raffle_camper_date');
            $table->datetime('raffle_servant_subscription_start_date');
            $table->datetime('raffle_servant_subscription_end_date');
            $table->datetime('raffle_servant_date');

            $table->datetime('camper_registration_start_date');
            $table->datetime('camper_registration_end_date');
            $table->datetime('camper_payment_link');
            $table->datetime('camper_payment_date');
            $table->datetime('servant_registration_start_date');
            $table->datetime('servant_registration_end_date');
            $table->datetime('servant_payment_link');
            $table->datetime('servant_payment_date');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campings');
    }
};
