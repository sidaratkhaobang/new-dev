<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLtRentalPrLinesCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lt_rental_pr_lines_cars', function (Blueprint $table) {
            $table->uuid('lt_rental_pr_line_id');
            $table->uuid('car_id');
            $table->primary(['lt_rental_pr_line_id', 'car_id'], 'lt_rental_pr_line_car_pk');
            $table->foreign('lt_rental_pr_line_id')->references('id')->on('lt_rental_pr_lines')->cascadeOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lt_rental_pr_lines_cars');
    }
}
