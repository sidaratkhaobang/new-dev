<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsRentalCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars_rental_categories', function (Blueprint $table) {
            $table->uuid('car_id');
            $table->uuid('rental_category_id');

            $table->primary(['car_id', 'rental_category_id'], 'cars_rental_categories_pk');

            $table->foreign('car_id')->references('id')->on('cars')->cascadeOnDelete();
            $table->foreign('rental_category_id')->references('id')->on('rental_categories')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cars_rental_categories');
    }
}
