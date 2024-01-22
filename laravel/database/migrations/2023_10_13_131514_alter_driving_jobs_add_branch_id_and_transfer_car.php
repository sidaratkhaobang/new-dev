<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDrivingJobsAddBranchIdAndTransferCar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driving_jobs', function (Blueprint $table) {
            $table->uuid('branch_id')->nullable()->after('worksheet_no');
            $table->uuid('car_park_transfer_id')->nullable()->after('driver_name');

            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            $table->foreign('car_park_transfer_id')->references('id')->on('car_park_transfers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driving_jobs', function (Blueprint $table) {
            $table->dropForeign('branch_id');
            $table->dropForeign('car_park_transfer_id');

            $table->dropColumn('branch_id');
            $table->dropColumn('car_park_transfer_id');
        });
    }
}
