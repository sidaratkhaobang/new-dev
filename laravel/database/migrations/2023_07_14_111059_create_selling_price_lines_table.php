<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellingPriceLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selling_price_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('selling_price_id')->nullable();
            $table->uuid('car_id')->nullable();
            $table->decimal('price', 10, 2)->nullable()->default(0);
            $table->decimal('vat', 10, 2)->nullable()->default(0);
            $table->decimal('total', 10, 2)->nullable()->default(0);
            $table->string('status', 100)->nullable();
            $table->userFields();

            $table->foreign('selling_price_id')->references('id')->on('selling_prices')->nullOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('selling_price_lines');
    }
}
