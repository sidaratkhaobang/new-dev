<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterImportCarLinesAddIsCarFinancingOnly extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('import_car_lines', function (Blueprint $table) {
            $table->string('is_car_financing_only',20)->after('remark');
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
            $table->dropColumn('is_car_financing_only');
        });
    }
}
