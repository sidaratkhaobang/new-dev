<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPromotionCodeLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_promotion_code_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_promotion_code_id')->nullable();
            $table->uuid('promotion_code_id')->nullable();
            $table->decimal('vat', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->foreign('order_promotion_code_id')->references('id')->on('order_promotion_codes')->cascadeOnDelete();
            $table->foreign('promotion_code_id')->references('id')->on('promotion_codes')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_promotion_code_lines');
    }
}
