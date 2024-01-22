<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentalBillsPromotionCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rental_bills_promotion_codes', function (Blueprint $table) {
            $table->uuid('rental_bill_id');
            $table->uuid('promotion_code_id');

            $table->primary(['rental_bill_id', 'promotion_code_id'], 'bills_promotion_codes_pk');

            $table->foreign('rental_bill_id')->references('id')->on('rental_bills')->cascadeOnDelete();
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
        Schema::dropIfExists('rental_bills_promotion_codes');
    }
}
