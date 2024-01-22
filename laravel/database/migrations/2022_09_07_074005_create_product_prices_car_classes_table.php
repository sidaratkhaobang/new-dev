<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPricesCarClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_prices_car_classes', function (Blueprint $table) {
            $table->uuid('product_price_id');
            $table->uuid('car_class_id');

            $table->primary(['product_price_id', 'car_class_id']);

            $table->foreign('product_price_id')->references('id')->on('product_prices')->cascadeOnDelete();
            $table->foreign('car_class_id')->references('id')->on('car_classes')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_prices_car_classes');
    }
}
