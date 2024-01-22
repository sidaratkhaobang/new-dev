<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterImportCarLineAddLotId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('import_car_lines', function (Blueprint $table) {
            $table->uuid('lot_id')->nullable()->after('registration_type');

            $table->foreign('lot_id')->references('id')->on('insurance_lots')->nullOnDelete();
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
            $table->dropForeign(['lot_id']);
            $table->dropColumn(['lot_id']);
        });
    }
}
