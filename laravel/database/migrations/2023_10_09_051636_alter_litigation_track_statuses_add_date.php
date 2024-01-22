<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLitigationTrackStatusesAddDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('litigation_track_statuses', function (Blueprint $table) {
            $table->date('appointment_date')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('litigation_track_statuses', function (Blueprint $table) {
            $table->dropColumn(['appointment_date']);
        });
    }
}
