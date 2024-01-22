<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCheckDistancesAddRefId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('check_distances', function (Blueprint $table) {
            $table->refId();
        });

        Schema::table('check_distance_lines', function (Blueprint $table) {
            $table->refId();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('check_distances', function (Blueprint $table) {
            $table->dropColumn(['ref_id']);
        });

        Schema::table('check_distance_lines', function (Blueprint $table) {
            $table->dropColumn(['ref_id']);
        });
    }
}
