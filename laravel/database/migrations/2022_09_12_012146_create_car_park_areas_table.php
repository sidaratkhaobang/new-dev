<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\CarParkSlotSizeEnum;

class CreateCarParkAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_park_areas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('car_park_zone_id');
            $table->tinyInteger('area_size')->default(CarParkSlotSizeEnum::SMALL);
            $table->integer('start_number')->default(0);
            $table->integer('end_number')->default(0);
            $table->string('zone_type', 50)->nullable();

            $table->status();
            $table->userFields();

            $table->foreign('car_park_zone_id')->references('id')->on('car_park_zones')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_park_areas');
    }
}
