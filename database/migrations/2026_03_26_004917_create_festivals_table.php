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
        Schema::create('festivals', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('idade_min');
            $table->boolean('venda_ingresso');
            $table->date('inicio_venda');
            $table->date('limite_venda');
            $table->decimal('valor_ingresso', 8, 2);
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
        Schema::dropIfExists('festivals');
    }
};
