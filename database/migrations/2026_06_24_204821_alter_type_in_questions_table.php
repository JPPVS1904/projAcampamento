<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Change enum to string. Since altering ENUM can be tricky in some MySQL versions with Doctrine,
        // we use a raw statement.
        DB::statement("ALTER TABLE questions MODIFY COLUMN type VARCHAR(50)");
    }

    public function down()
    {
        DB::statement("ALTER TABLE questions MODIFY COLUMN type ENUM('Aberta', 'Fechada')");
    }
};
