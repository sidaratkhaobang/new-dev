<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCarsAddGpsEventName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->string('gps_event_th', 100)->nullable()->after('current_location');
            $table->string('gps_event_en', 100)->nullable()->after('gps_event_th');
            $table->dateTime('gps_event_timestamp')->nullable()->after('gps_event_en');
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->string('current_location', 500)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['gps_event_th', 'gps_event_en', 'gps_event_timestamp']);
        });
    }
}
