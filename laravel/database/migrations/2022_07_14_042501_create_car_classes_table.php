<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_classes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('full_name')->nullable();
            $table->text('description')->nullable();

            $table->uuid('car_type_id')->nullable();

            $table->integer('engine_size')->nullable();
            $table->integer('manufacturing_year')->nullable();

            // car part
            $table->uuid('gear_id')->nullable();
            $table->uuid('drive_system_id')->nullable();
            $table->uuid('car_seat_id')->nullable();
            $table->uuid('side_mirror_id')->nullable();
            $table->uuid('air_bag_id')->nullable();
            $table->uuid('central_lock_id')->nullable();
            $table->uuid('front_brake_id')->nullable();
            $table->uuid('rear_brake_id')->nullable();
            $table->uuid('abs_id')->nullable();
            $table->uuid('anti_thift_system_id')->nullable();

            $table->integer('oil_tank_capacity')->nullable();
            $table->string('oil_type')->nullable();

            // car part 2
            $table->uuid('car_battery_id')->nullable();
            $table->uuid('car_tire_id')->nullable();
            $table->uuid('car_wiper_id')->nullable();

            $table->text('remark')->nullable();
            $table->string('website')->nullable();

            $table->status();
            $table->userFields();

            $table->refId();

            $table->foreign('gear_id')->references('id')->on('car_parts')->nullOnDelete();
            $table->foreign('drive_system_id')->references('id')->on('car_parts')->nullOnDelete();
            $table->foreign('car_seat_id')->references('id')->on('car_parts')->nullOnDelete();
            $table->foreign('side_mirror_id')->references('id')->on('car_parts')->nullOnDelete();
            $table->foreign('air_bag_id')->references('id')->on('car_parts')->nullOnDelete();
            $table->foreign('central_lock_id')->references('id')->on('car_parts')->nullOnDelete();
            $table->foreign('front_brake_id')->references('id')->on('car_parts')->nullOnDelete();
            $table->foreign('rear_brake_id')->references('id')->on('car_parts')->nullOnDelete();
            $table->foreign('abs_id')->references('id')->on('car_parts')->nullOnDelete();
            $table->foreign('anti_thift_system_id')->references('id')->on('car_parts')->nullOnDelete();

            $table->foreign('car_battery_id')->references('id')->on('car_batteries')->nullOnDelete();
            $table->foreign('car_tire_id')->references('id')->on('car_tires')->nullOnDelete();
            $table->foreign('car_wiper_id')->references('id')->on('car_wipers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_classes');
    }
}
