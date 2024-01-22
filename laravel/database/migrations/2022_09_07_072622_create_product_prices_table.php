<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0);
            $table->tinyInteger('priority')->default(0);
            $table->boolean('is_product_additional_free')->default(false);

            $table->boolean('booking_day_mon')->default(false);
            $table->boolean('booking_day_tue')->default(false);
            $table->boolean('booking_day_wed')->default(false);
            $table->boolean('booking_day_thu')->default(false);
            $table->boolean('booking_day_fri')->default(false);
            $table->boolean('booking_day_sat')->default(false);
            $table->boolean('booking_day_sun')->default(false);

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->status();
            $table->userFields();

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_prices');
    }
}
