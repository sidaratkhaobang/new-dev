<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterImportCarLinesAddTypeCarFinance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('import_car_lines', function (Blueprint $table) {
            $table->string('type_car_financing', 20)->nullable();
            $table->dropColumn('is_car_financing_only');
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
            $table->dropColumn('type_car_financing');
            $table->string('is_car_financing_only')->nullable();
        });
    }
}
