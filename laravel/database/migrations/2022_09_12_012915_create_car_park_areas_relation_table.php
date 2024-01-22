<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarParkAreasRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_park_areas_relation', function (Blueprint $table) {
            $table->uuid('car_park_area_id');
            $table->uuid('car_group_id');

            $table->primary(['car_park_area_id', 'car_group_id'], 'car_park_areas_relation_pk');

            $table->foreign('car_park_area_id')->references('id')->on('car_park_areas')->cascadeOnDelete();
            $table->foreign('car_group_id')->references('id')->on('car_groups')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_park_zones_relation');
    }
}
