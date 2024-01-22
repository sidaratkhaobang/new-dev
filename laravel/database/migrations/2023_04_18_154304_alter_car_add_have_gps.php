<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCarAddHaveGps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->boolean('have_gps')->nullable()->after('status');
            $table->string('vid')->nullable()->after('have_gps');
            $table->string('serial_number')->nullable()->after('vid');
            $table->boolean('sim')->nullable()->after('serial_number');
            $table->boolean('have_dvr')->nullable()->after('sim');
            $table->string('dvr')->nullable()->after('have_dvr');
            $table->boolean('have_censor_oil')->nullable()->after('dvr');
            $table->string('censor_oil')->nullable()->after('have_censor_oil');
            $table->boolean('have_censor_speed')->nullable()->after('censor_oil');
            $table->string('speed')->nullable()->after('have_censor_speed');
            $table->string('fleet')->nullable()->after('speed');
            $table->string('status_gps')->nullable()->after('fleet');
            $table->string('current_location')->nullable()->after('status_gps');
            $table->dateTime('illegality_date')->nullable()->after('current_location');
            $table->text('remark')->nullable()->after('illegality_date');
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
            $table->dropColumn(['have_gps', 'vid', 'serial_number', 'sim', 'have_dvr', 'dvr', 'have_censor_oil', 'censor_oil', 'have_censor_speed', 'speed', 'fleet', 'status_gps', 'current_location', 'illegality_date', 'remark']);
        });
    }
}
