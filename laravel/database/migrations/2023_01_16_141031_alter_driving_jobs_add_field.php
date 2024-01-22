<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDrivingJobsAddField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driving_jobs', function (Blueprint $table) {
            $table->dateTime('estimate_rented_date')->nullable()->after('estimate_end_date');
            $table->dateTime('actual_rented_date')->nullable()->after('actual_arrive_date');
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
            $table->dropColumn(['estimate_rented_date']);
            $table->dropColumn(['actual_rented_date']);
        });
    }
}
