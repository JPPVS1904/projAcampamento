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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->dateTime('subscription_date');

            $table->enum('subscription_type', ['Servo', 'Campista', 'Participante']);
            $table->boolean('was_selected');
            $table->integer('substitute_position');
            $table->boolean('paid_the_fee');
            $table->boolean('is_quitter');

            $table->string('payment_code');

            $table->string('qrcode_data');
            $table->boolean('used_qrcode');

            $table->foreignId('selection_method_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('spouse_id')->references('id')->on('users')->constrained()->nullable();
            $table->foreignId('event_id')->constrained();
            $table->foreignId('sector_id')->constrained()->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
