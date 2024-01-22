<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInspectionJobStepAddInspectionLocation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inspection_job_steps', function (Blueprint $table) {
            $table->string('inspection_location', 100)->nullable()->after('inspector');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inspection_job_steps', function (Blueprint $table) {
            //
        });
    }
}
