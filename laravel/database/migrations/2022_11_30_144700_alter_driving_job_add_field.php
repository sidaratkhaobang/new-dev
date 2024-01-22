<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDrivingJobAddField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driving_jobs', function (Blueprint $table) {
            $table->string('driving_job_type', 50)->nullable()->after('remark');
            $table->string('self_drive_type', 50)->nullable()->after('driving_job_type');
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
            $table->dropColumn('driving_job_type');
            $table->dropColumn('self_drive_type');
        });
    }
}
