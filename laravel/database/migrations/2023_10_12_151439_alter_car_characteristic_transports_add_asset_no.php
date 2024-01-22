<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCarCharacteristicTransportsAddAssetNo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_characteristic_transports', function (Blueprint $table) {
            $table->string('asset_no', 100)->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_characteristic_transports', function (Blueprint $table) {
            $table->dropColumn('asset_no');
        });
    }
}
