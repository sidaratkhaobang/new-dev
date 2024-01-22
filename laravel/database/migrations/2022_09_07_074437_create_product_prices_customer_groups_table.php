<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPricesCustomerGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_prices_customer_groups', function (Blueprint $table) {
            $table->uuid('product_price_id');
            $table->uuid('customer_group_id');

            $table->primary(['product_price_id', 'customer_group_id'], 'product_prices_customer_groups_pk');

            $table->foreign('product_price_id')->references('id')->on('product_prices')->cascadeOnDelete();
            $table->foreign('customer_group_id')->references('id')->on('customer_groups')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_prices_customer_groups');
    }
}
