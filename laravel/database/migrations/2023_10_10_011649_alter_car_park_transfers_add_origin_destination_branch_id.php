<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCarParkTransfersAddOriginDestinationBranchId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_park_transfers', function (Blueprint $table) {
            $table->uuid('origin_branch_id')->nullable()->after('car_id');
            $table->uuid('destination_branch_id')->nullable()->after('origin_branch_id');

            $table->foreign('origin_branch_id')->references('id')->on('branches')->nullOnDelete();
            $table->foreign('destination_branch_id')->references('id')->on('branches')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_park_transfers', function (Blueprint $table) {
            $table->dropForeign(['origin_branch_id', 'destination_branch_id']);

            $table->dropColumn('origin_branch_id');
            $table->dropColumn('destination_branch_id');
        });
    }
}
