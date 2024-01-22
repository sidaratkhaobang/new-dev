<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInstallEquipmentsAddLotNoField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('install_equipments', function (Blueprint $table) {
            $table->uuid('lot_id')->nullable()->after('group_id');
            $table->string('lot_no', 20)->nullable()->after('lot_id');
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
        Schema::table('install_equipments', function (Blueprint $table) {
            $table->dropForeign(['lot_id']);
            $table->dropColumn(['lot_id']);
            $table->dropColumn(['lot_no']);
        });
    }
}
