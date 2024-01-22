<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGpsServiceChargeLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gps_service_charge_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('gps_service_charge_id');
            $table->string('month', 100);
            $table->decimal('budget', 10, 2)->default(0)->nullable();
            $table->decimal('air_time_gps', 10, 2)->default(0)->nullable();
            $table->decimal('air_time_dvr', 10, 2)->default(0)->nullable();
            $table->decimal('total', 10, 2)->default(0)->nullable();
            $table->decimal('actual', 10, 2)->default(0)->nullable();
            $table->userFields();

            $table->foreign('gps_service_charge_id')->references('id')->on('gps_service_charges')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gps_service_charge_lines');
    }
}
