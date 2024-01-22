<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOrderPromotionCodeAddReceiptId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_promotion_codes', function (Blueprint $table) {
            $table->uuid('receipt_id')->after('customer_billing_address_id')->nullable();

            $table->foreign('receipt_id')->references('id')->on('receipts')->nullOnDelete();
        });

        Schema::table('rental_bills', function (Blueprint $table) {
            $table->uuid('receipt_id')->after('customer_billing_address_id')->nullable();

            $table->foreign('receipt_id')->references('id')->on('receipts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_promotion_codes', function (Blueprint $table) {
            //
        });

        Schema::table('rental_bills', function (Blueprint $table) {
            //
        });
    }
}
