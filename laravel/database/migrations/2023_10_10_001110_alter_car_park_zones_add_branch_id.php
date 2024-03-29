<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCarParkZonesAddBranchId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_park_zones', function (Blueprint $table) {
            $table->uuid('branch_id')->nullable()->after('name');

            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_park_zones', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);

            $table->dropColumn('branch_id');
        });
    }
}
