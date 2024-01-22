<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInspectionJobStepsAddIsNeedDpfField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inspection_job_steps', function (Blueprint $table) {
            $table->boolean('is_need_dpf')->default(false)->after('is_need_send_mobile');

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
            $table->dropColumn('is_need_dpf');
        });
    }
}
