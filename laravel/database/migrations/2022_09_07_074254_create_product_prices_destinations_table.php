<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPricesDestinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_prices_destinations', function (Blueprint $table) {
            $table->uuid('product_price_id');
            $table->uuid('destination_id');

            $table->primary(['product_price_id', 'destination_id'], 'product_prices_destinations_pk');

            $table->foreign('product_price_id', 'product_price_fk')->references('id')->on('product_prices')->cascadeOnDelete();
            $table->foreign('destination_id', 'destination_id_fk')->references('id')->on('locations')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_prices_destinations');
    }
}
