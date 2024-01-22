<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPricesOriginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_prices_origins', function (Blueprint $table) {
            $table->uuid('product_price_id');
            $table->uuid('origin_id');

            $table->primary(['product_price_id', 'origin_id'], 'product_prices_origins_pk');

            $table->foreign('product_price_id')->references('id')->on('product_prices')->cascadeOnDelete();
            $table->foreign('origin_id')->references('id')->on('locations')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_prices_origins');
    }
}
