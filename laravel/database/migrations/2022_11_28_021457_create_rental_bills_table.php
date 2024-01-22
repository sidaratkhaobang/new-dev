<?php

use App\Enums\RentalStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentalBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rental_bills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rental_id');

            // promotion
            $table->uuid('promotion_id')->nullable();
            $table->uuid('promotion_code_id')->nullable();

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('coupon_discount', 10, 2)->default(0);
            $table->decimal('vat', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->string('payment_method', 20)->nullable(); // Payment Method Enum
            $table->text('payment_remark')->nullable();

            $table->string('status', 20)->default(RentalStatusEnum::DRAFT);
            $table->userFields();

            $table->foreign('rental_id')->references('id')->on('rentals')->cascadeOnDelete();
            $table->foreign('promotion_id')->references('id')->on('promotions')->nullOnDelete();
            $table->foreign('promotion_code_id')->references('id')->on('promotion_codes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rental_bills');
    }
}
