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
        Schema::table('campings', function (Blueprint $table) {
            $table->string('camper_payment_link')->nullable()->change();
            $table->string('servant_payment_link')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campings', function (Blueprint $table) {
            $table->datetime('camper_payment_link')->nullable()->change();
            $table->datetime('servant_payment_link')->nullable()->change();
        });
    }
};
