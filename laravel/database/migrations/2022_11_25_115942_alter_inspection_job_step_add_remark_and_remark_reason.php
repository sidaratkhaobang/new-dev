<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInspectionJobStepAddRemarkAndRemarkReason extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inspection_job_steps', function (Blueprint $table) {
            $table->string('remark', 100)->nullable()->after('inspection_status');
            $table->string('remark_reason', 100)->nullable()->after('remark');
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
