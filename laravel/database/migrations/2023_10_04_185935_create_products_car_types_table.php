<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsCarTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_car_types', function (Blueprint $table) {
            $table->uuid('product_id');
            $table->uuid('car_type_id');
            $table->primary(['product_id', 'car_type_id'], 'products_car_types_pk');
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('car_type_id')->references('id')->on('car_types')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products_car_types');
    }
}
