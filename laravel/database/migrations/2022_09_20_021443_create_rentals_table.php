<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\RentalTypeEnum;
use App\Enums\OrderChannelEnum;
use App\Enums\RentalStatusEnum;
use App\Enums\RentalStateEnum;
use App\Enums\PaymentMethodEnum;

class CreateRentalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no', 20)->nullable();
            $table->string('rental_type', 20)->default(RentalTypeEnum::OTHER);
            $table->string('order_channel', 20)->default(OrderChannelEnum::OTHER);
            $table->string('rental_state', 20)->nullable(); // RentalStateEnum
            $table->uuid('service_type_id')->nullable();
            $table->dateTime('pickup_date')->nullable();
            $table->dateTime('return_date')->nullable();
            $table->uuid('branch_id')->nullable();
            $table->uuid('product_id')->nullable();
            $table->uuid('origin_id')->nullable();
            $table->string('origin_lat')->nullable();
            $table->string('origin_lng')->nullable();
            $table->string('origin_name')->nullable();
            $table->text('origin_address')->nullable();
            $table->uuid('destination_id')->nullable();
            $table->string('destination_lat')->nullable();
            $table->string('destination_lng')->nullable();
            $table->string('destination_name')->nullable();
            $table->text('destination_address')->nullable();
            $table->integer('avg_distance')->nullable();

            // customer
            $table->uuid('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->text('customer_address')->nullable();
            $table->string('customer_tel', 20)->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_zipcode', 10)->nullable();
            $table->unsignedBigInteger('customer_province_id')->nullable();
            $table->boolean('is_required_tax_invoice')->default(false);
            // TODO

            // promotion
            $table->uuid('promotion_id')->nullable();
            $table->uuid('promotion_code_id')->nullable();

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('coupon_discount', 10, 2)->default(0);
            $table->decimal('vat', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->string('payment_method', 20)->nullable(); // PaymentMethodEnum
            $table->text('payment_remark')->nullable();

            $table->text('remark')->nullable();

            $table->string('status', 20)->default(RentalStatusEnum::DRAFT);
            $table->uuid('quotation_id')->nullable();
            $table->userFields();

            $table->foreign('service_type_id')->references('id')->on('service_types')->nullOnDelete();
            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
            $table->foreign('origin_id')->references('id')->on('locations')->nullOnDelete();
            $table->foreign('destination_id')->references('id')->on('locations')->nullOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('customer_province_id')->references('id')->on('provinces')->nullOnDelete();
            $table->foreign('promotion_id')->references('id')->on('promotions')->nullOnDelete();
            $table->foreign('promotion_code_id')->references('id')->on('promotion_codes')->nullOnDelete();
            $table->foreign('quotation_id')->references('id')->on('quotations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rentals');
    }
}
