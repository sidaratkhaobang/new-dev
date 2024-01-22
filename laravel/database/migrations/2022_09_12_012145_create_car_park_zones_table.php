<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\CarParkSlotSizeEnum;

class CreateCarParkZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_park_zones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 10  )->nullable();
            $table->string('name')->nullable();
            $table->tinyInteger('zone_size')->default(CarParkSlotSizeEnum::SMALL);

            $table->status();
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
        Schema::dropIfExists('car_park_zones');
    }
}
