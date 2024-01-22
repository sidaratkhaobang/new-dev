<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDrivingJobsAddOriginDestionation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driving_jobs', function (Blueprint $table) {
            $table->string('origin')->nullable()->after('est_distance');
            $table->string('destination')->nullable()->after('origin');
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
            $table->dropColumn(['origin', 'destination']);
        });
    }
}
