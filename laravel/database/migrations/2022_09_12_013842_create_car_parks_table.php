<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\CarParkStatusEnum;

class CreateCarParksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_parks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('car_park_area_id');
            $table->integer('car_park_number');

            $table->boolean('is_permanent_disabled')->default(false);
            $table->date('start_disabled_date')->nullable();
            $table->date('end_disabled_date')->nullable();

            $table->uuid('car_id')->nullable();
            $table->string('status', 20)->default(CarParkStatusEnum::FREE);

            $table->userFields();

            $table->foreign('car_park_area_id')->references('id')->on('car_park_areas')->cascadeOnDelete();
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
        Schema::dropIfExists('car_parks');
    }
}
