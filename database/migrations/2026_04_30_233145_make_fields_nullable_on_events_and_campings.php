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
        Schema::table('events', function (Blueprint $table) {
            $table->string('image')->nullable()->change();
        });

        Schema::table('campings', function (Blueprint $table) {
            $table->string('notice')->nullable()->change();
            $table->string('term')->nullable()->change();
            $table->string('image')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('image')->nullable(false)->change();
        });

        Schema::table('campings', function (Blueprint $table) {
            $table->string('notice')->nullable(false)->change();
            $table->string('term')->nullable(false)->change();
            $table->string('image')->nullable(false)->change();
        });
    }
};
