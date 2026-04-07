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
            Schema::create('pre_regristrations', function (Blueprint $table) {
                $table->id();
                // parceiro_id (id do cônjuge - auto-relacionamento)
                $table->foreignId('parceiro_id')->nullable()->constrained('users');

                $table->date('data_insc');
                $table->char('tipo_insc_acamp', 1); // S ou C

                $table->tinyInteger('sorteado')->default(0);
                $table->integer('posicao')->nullable();

                $table->string('codigo_pag_acamp')->nullable();
                $table->tinyInteger('status_pagamento')->default(0);

                $table->string('aqcode_ingresso')->nullable();
                $table->tinyInteger('habilitado_qrcode')->default(0);

                // Timestamps e SoftDeletes
                $table->timestamps();
                $table->softDeletes();

                // Chaves Estrangeiras para outras tabelas
                $table->foreignId('camp_id')->constrained('camps');
                $table->foreignId('festival_id')->constrained('festivals');
                $table->foreignId('user_id')->constrained('users');
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_regristrations');
    }
};
