<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCarParkTransferAddIsDifferenceBranch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_park_transfers', function (Blueprint $table) {
            $table->boolean('is_difference_branch')->default(false)->after('car_id');
            $table->boolean('is_singular')->default(true)->after('is_difference_branch');
            $table->uuid('branch_id')->nullable()->after('is_singular');

            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
        });

        Schema::table('driving_jobs', function (Blueprint $table) {
            $table->dropForeign(['car_park_transfer_id']);

            $table->dropColumn('car_park_transfer_id');
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
            $table->dropColumn('is_difference_branch');
            $table->dropColumn('is_singular');

            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
}
