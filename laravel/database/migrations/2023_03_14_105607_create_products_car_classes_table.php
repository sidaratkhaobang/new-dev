<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsCarClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_car_classes', function (Blueprint $table) {
            $table->uuid('product_id');
            $table->uuid('car_class_id');
            $table->primary(['product_id', 'car_class_id'], 'products_car_classes_pk');
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
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
        Schema::dropIfExists('products_car_classes');
    }
}
