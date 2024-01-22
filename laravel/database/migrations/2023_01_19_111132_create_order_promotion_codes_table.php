<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPromotionCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_promotion_codes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('amount')->default(0)->nullable();
            $table->decimal('vat', 10, 2)->default(0);
            $table->uuid('customer_id')->nullable();
            $table->text('payment_description')->nullable();

            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_promotion_codes');
    }
}
