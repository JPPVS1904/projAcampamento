<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('camping_pre_registration', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->after('is_quitter');
        });
    }

    public function down()
    {
        Schema::table('camping_pre_registration', function (Blueprint $table) {
            $table->dropColumn('is_approved');
        });
    }
};
