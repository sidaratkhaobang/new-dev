<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGpsServiceChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gps_service_charges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('year', 100);
            $table->decimal('total_budget', 10, 2)->default(0)->nullable();
            $table->decimal('total_air_time_gps', 10, 2)->default(0)->nullable();
            $table->decimal('total_air_time_dvr', 10, 2)->default(0)->nullable();
            $table->decimal('total', 10, 2)->default(0)->nullable();
            $table->decimal('total_actual', 10, 2)->default(0)->nullable();
            $table->userFields();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gps_service_charges');
    }
}
