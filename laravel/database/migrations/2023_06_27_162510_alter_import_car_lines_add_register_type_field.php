<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterImportCarLinesAddRegisterTypeField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('import_car_lines', function (Blueprint $table) {
            $table->string('registration_type')->after('install_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('import_car_lines', function (Blueprint $table) {
            $table->dropColumn('registration_type');
        });
    }
}
