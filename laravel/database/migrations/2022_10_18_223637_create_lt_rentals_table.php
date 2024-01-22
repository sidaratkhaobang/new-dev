<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\LongTermRentalJobType;
use App\Enums\LongTermRentalStatusEnum;
use App\Enums\SpecStatusEnum;
use App\Enums\ComparisonPriceStatusEnum;
use App\Enums\AuctionResultEnum;

class CreateLtRentalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lt_rentals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no', 20)->nullable();
            $table->string('job_type', 30)->default(LongTermRentalJobType::AUCTION);
            $table->tinyInteger('rental_duration')->nullable(); // month
            $table->date('auction_submit_date')->nullable();
            $table->date('auction_winning_date')->nullable();

            $table->boolean('need_pay_auction')->default(false);
            $table->boolean('is_paid_auction')->default(false);
            $table->string('won_auction', 20)->default(AuctionResultEnum::WAITING);
            $table->decimal('bidder_price', 12, 2)->default(0);
            $table->string('bidder_name')->nullable();
            //$table->decimal('bidder_price_per_month', 12, 2)->default(0);

            $table->date('actual_delivery_date')->nullable();
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();

            $table->uuid('reject_reason_id')->nullable();
            $table->text('reject_reason_description')->nullable();
            $table->text('remark')->nullable();

            // customer
            $table->uuid('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->text('customer_address')->nullable();
            $table->string('customer_tel', 20)->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_zipcode', 10)->nullable();
            $table->unsignedBigInteger('customer_province_id')->nullable();
            // TODO

            $table->uuid('creditor_id')->nullable();

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('vat', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->uuid('quotation_id')->nullable();
            $table->text('quotation_remark')->nullable();

            $table->string('status', 20)->default(LongTermRentalStatusEnum::NEW);
            $table->string('spec_status', 20)->default(SpecStatusEnum::DRAFT);
            $table->string('comparison_price_status', 20)->default(ComparisonPriceStatusEnum::DRAFT);
            $table->userFields();

            $table->foreign('reject_reason_id')->references('id')->on('auction_reject_reasons')->nullOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('customer_province_id')->references('id')->on('provinces')->nullOnDelete();
            $table->foreign('creditor_id')->references('id')->on('creditors')->nullOnDelete();
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
        Schema::dropIfExists('lt_rentals');
    }
}
