<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marital_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sectors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->enum('place', ['Interno', 'Externo']);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('selection_methods', function (Blueprint $table) {
            $table->id();
            $table->string('method', 255);
            $table->string('description', 255);
            $table->enum('type', ['Acampamento', 'Evento']);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->integer('order');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->enum('type', ['Acampamento', 'Evento']);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('campings', function (Blueprint $table) {
            $table->id();
            $table->string('notice', 255);
            $table->string('term', 255);
            $table->text('image');
            $table->integer('minimal_age');
            $table->integer('maximal_age');
            $table->decimal('camper_fee', 8, 2);
            $table->decimal('servant_fee', 8, 2);
            $table->integer('planned_man_vacancies');
            $table->integer('planned_woman_vacancies');
            $table->integer('planned_couple_vacancies');
            $table->integer('raffle_man_vacancies');
            $table->integer('raffle_woman_vacancies');
            $table->integer('raffle_couple_vacancies');
            $table->integer('raffle_total_vacancies');
            $table->dateTime('raffle_camper_subscription_start_date');
            $table->dateTime('raffle_camper_subscription_end_date');
            $table->dateTime('raffle_camper_date');
            $table->dateTime('raffle_servant_subscription_start_date');
            $table->dateTime('raffle_servant_subscription_end_date');
            $table->dateTime('raffle_servant_date');
            $table->dateTime('camper_registration_start_date');
            $table->dateTime('camper_registration_end_date');
            $table->string('camper_payment_link', 255);
            $table->dateTime('camper_payment_date');
            $table->dateTime('servant_registration_start_date');
            $table->dateTime('servant_registration_end_date');
            $table->string('servant_payment_link', 255);
            $table->dateTime('servant_payment_date');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->integer('minimal_age');
            $table->boolean('is_paid_festival');
            $table->integer('ticket_price');
            $table->dateTime('sale_start_date');
            $table->string('payment_link', 255); // Changed to string, assuming CakePHP's 'integer' might be a typo for a link, or we'll stick to what we need
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('cpf', 255)->unique();
            $table->string('name', 255);
            $table->date('birthday');
            $table->enum('sex', ['M', 'F']);
            $table->string('phone', 255);
            $table->string('email', 255)->unique();
            $table->string('photo', 255)->nullable();
            $table->boolean('is_counselor')->default(false);
            $table->boolean('is_admin')->default(false);
            $table->string('password', 255);
            $table->string('access_token', 255)->nullable();
            $table->string('refresh_token', 255)->nullable();
            $table->foreignId('marital_status_id')->constrained('marital_statuses');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('text', 255);
            $table->integer('order');
            $table->enum('type', ['Aberta', 'Fechada']);
            $table->boolean('accept_generic_answer')->default(false);
            $table->foreignId('section_id')->constrained('sections');
            $table->unsignedBigInteger('depends_on_option_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->string('text', 255);
            $table->foreignId('question_id')->constrained('questions');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->foreign('depends_on_option_id')->references('id')->on('options');
        });

        Schema::create('categories_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('question_id')->constrained('questions');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('categories_sectors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('sector_id')->constrained('sectors');
            $table->integer('base_vacancies');
            $table->integer('raffle_vacancies');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('street', 255);
            $table->string('number', 50);
            $table->string('neighborhood', 255);
            $table->string('city', 255);
            $table->string('cep', 8);
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('image');
            $table->string('place', 255);
            $table->year('year');
            $table->dateTime('start_date');
            $table->integer('duration_days');
            $table->integer('total_vacancies');
            $table->foreignId('category_id')->constrained('categories');
            $table->string('activitable_type', 255);
            $table->unsignedBigInteger('activitable_id');
            $table->index(['activitable_type', 'activitable_id']);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('categories_sectors_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('categories_sectors_id')->constrained('categories_sectors');
            $table->foreignId('activity_id')->constrained('activities');
            $table->boolean('is_coordinator')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('camping_pre_registration', function (Blueprint $table) {
            $table->id();
            $table->integer('substitute_position')->nullable();
            $table->boolean('is_quitter')->default(false);
            $table->foreignId('selection_method_id')->nullable()->constrained('selection_methods');
            $table->foreignId('spouse_id')->nullable()->constrained('users');
            $table->foreignId('sector_id')->nullable()->constrained('sectors');
            $table->foreignId('sector2_id')->nullable()->constrained('sectors');
        });

        Schema::create('pre_registrations', function (Blueprint $table) {
            $table->id();
            $table->enum('subscription_type', ['Servo', 'Campista', 'Participante']);
            $table->boolean('is_fee_paid')->default(false);
            $table->string('payment_code', 255)->nullable();
            $table->string('qrcode_data', 255)->nullable();
            $table->boolean('is_qrcode_used')->nullable()->default(false);
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('activity_id')->constrained('activities');
            $table->foreignId('camping_pre_registration_id')->constrained('camping_pre_registration');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pre_registration_id')->constrained('pre_registrations');
            $table->foreignId('question_id')->constrained('questions');
            $table->text('answer')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answers');
        Schema::dropIfExists('pre_registrations');
        Schema::dropIfExists('camping_pre_registration');
        Schema::dropIfExists('categories_sectors_users');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('categories_sectors');
        Schema::dropIfExists('categories_questions');
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['depends_on_option_id']);
        });
        Schema::dropIfExists('options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('users');
        Schema::dropIfExists('events');
        Schema::dropIfExists('campings');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('selection_methods');
        Schema::dropIfExists('sectors');
        Schema::dropIfExists('marital_statuses');
    }
};
