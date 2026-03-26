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
        Schema::create('users', function (Blueprint $table) {

            $table->id();
            $table->string('cpf', 11)->unique();
            $table->date('dt_nasc');
            $table->string('nome');
            $table->char('sexo', 1);
            $table->string('email');
            $table->string('telefone')->nullable();
            $table->string('password');
            $table->string('token')->nullable();
            $table->string('refresh_token')->nullable();
            $table->string('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Chaves Estrangeiras
            $table->foreignId('role_id')->constrained('roles');
            $table->foreignId('marital_status_id')->constrained('marital_statuses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
