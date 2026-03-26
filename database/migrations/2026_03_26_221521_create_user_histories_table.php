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
        Schema::create('user_histories', function (Blueprint $table) {
            $table->id();
            $table->year('ano_conclusao');
            $table->char('funcao', 1)->nullable(); // S: servo, C: campista

            $table->timestamps();
            $table->softDeletes();

            // Chaves Estrangeiras
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('camp_id')->constrained('camps');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_histories');
    }
};
