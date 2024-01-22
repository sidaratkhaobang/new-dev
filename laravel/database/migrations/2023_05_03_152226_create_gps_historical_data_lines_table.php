<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGpsHistoricalDataLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gps_historical_data_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('gps_historical_data_id');
            $table->uuid('car_id')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->userFields();

            $table->foreign('gps_historical_data_id')->references('id')->on('gps_historical_datas')->cascadeOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gps_historical_data_lines');
    }
}
