<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealerCheckCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dealer_check_cars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lt_rental_id')->nullable();
            $table->uuid('dealer_id')->nullable();
            // $table->uuid('car_class_id')->nullable();
            // $table->uuid('car_color_id')->nullable();
            $table->uuid('tor_line_id')->nullable();
            $table->integer('amount')->nullable();
            $table->date('delivery_month_year')->nullable();
            $table->date('response_date')->nullable();
            $table->userFields();

            $table->foreign('lt_rental_id')->references('id')->on('lt_rentals')->cascadeOnDelete();
            $table->foreign('dealer_id')->references('id')->on('creditors')->cascadeOnDelete();
            $table->foreign('tor_line_id')->references('id')->on('lt_rental_tor_lines')->cascadeOnDelete();
            // $table->foreign('car_class_id')->references('id')->on('car_classes')->cascadeOnDelete();
            // $table->foreign('car_color_id')->references('id')->on('car_colors')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dealer_check_cars');
    }
}
