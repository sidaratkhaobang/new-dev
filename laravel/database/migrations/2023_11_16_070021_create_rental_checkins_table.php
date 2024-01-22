<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rental_checkins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rental_id');
            $table->uuid('car_id');
            $table->uuid('location_id')->nullable();
            $table->string('location_name', 200)->nullable();
            $table->string('lat', 200)->nullable();
            $table->string('lng', 200)->nullable();
            $table->dateTime('arrived_at')->nullable();
            $table->dateTime('departured_at')->nullable();
            $table->timestamps();

            $table->foreign('rental_id')->references('id')->on('rentals')->cascadeOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->cascadeOnDelete();
            $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rental_checkins');
    }
};
