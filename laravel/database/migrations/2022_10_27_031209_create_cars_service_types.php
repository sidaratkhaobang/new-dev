<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsServiceTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars_service_types', function (Blueprint $table) {
            $table->uuid('car_id');
            $table->uuid('service_type_id');

            $table->primary(['car_id', 'service_type_id'], 'cars_service_types_pk');

            $table->foreign('car_id')->references('id')->on('cars')->cascadeOnDelete();
            $table->foreign('service_type_id')->references('id')->on('service_types')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cars_service_types');
    }
}
