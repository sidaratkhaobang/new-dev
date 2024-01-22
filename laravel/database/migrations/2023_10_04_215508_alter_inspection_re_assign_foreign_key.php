<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AlterInspectionReAssignForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('config_approve_lines')->update(['department_id' => null]);
        DB::table('approve_lines')->update(['department_id' => null]);
        DB::table('inspection_steps')->update(['inspection_department_id' => null]);
        DB::table('inspection_jobs')->update(['inspection_department_id' => null]);
        DB::table('inspection_job_steps')->update(['inspection_department_id' => null]);

        Schema::table('config_approve_lines', function (Blueprint $table) {
            $table->dropForeign(['department_id']);

            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
        });

        Schema::table('approve_lines', function (Blueprint $table) {
            $table->dropForeign(['department_id']);

            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
        });

        Schema::table('inspection_steps', function (Blueprint $table) {
            $table->dropForeign(['inspection_department_id']);

            $table->foreign('inspection_department_id')->references('id')->on('departments')->nullOnDelete();
        });

        Schema::table('inspection_jobs', function (Blueprint $table) {
            $table->dropForeign(['inspection_department_id']);

            $table->foreign('inspection_department_id')->references('id')->on('departments')->nullOnDelete();
        });

        Schema::table('inspection_job_steps', function (Blueprint $table) {
            $table->dropForeign(['inspection_department_id']);

            $table->foreign('inspection_department_id')->references('id')->on('departments')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
