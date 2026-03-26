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
        Schema::create('camps', function (Blueprint $table) {
            $table->id();
            $table->integer('idade_min');
            $table->integer('idade_max');
            $table->date('data_inicio_camp_insc');
            $table->date('data_fim_camp_insc');
            $table->date('data_camp_sorteio');
            $table->date('data_inicio_servo_insc');
            $table->date('data_fim_servo_insc');
            $table->date('data_servo_sorteio');
            $table->date('data_inicio_registro_camp');
            $table->date('data_fim_registro_camp');
            $table->date('data_fim_pagamento_camp');
            $table->date('data_inicio_registro_servo');
            $table->date('data_fim_registro_servo');
            $table->date('data_fim_pagamento_servo');
            $table->integer('num_vagas_masc');
            $table->integer('num_vagas_fem');
            $table->integer('num_vagas_casal');
            $table->integer('num_vagas_total');
            $table->string('termo');
            $table->string('imagem');
            $table->decimal('valor_insc_camp');
            $table->decimal('valor_insc_servo');
            $table->timestamps();
            $table->softDeletes();

            // Chaves Estrangeiras
            $table->foreignId('event_id')->constrained('events');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camps');
    }
};
