<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarAuctionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_auctions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('car_id')->nullable();
            $table->datetime('request_finance_date')->nullable();
            $table->datetime('expected_finance_date')->nullable();
            $table->datetime('expected_transfer_ownership_date')->nullable();
            $table->datetime('transfer_ownership_date')->nullable();
            $table->datetime('close_cmi_vmi_date')->nullable();
            $table->datetime('pick_up_date')->nullable();
            $table->datetime('book_date')->nullable();
            $table->datetime('send_auction_date')->nullable();
            $table->datetime('auction_date')->nullable();
            $table->string('depreciation_age', 100)->nullable();
            $table->decimal('depreciation_month', 10, 2)->nullable();
            $table->string('depreciation_age_remain', 100)->nullable();
            $table->decimal('depreciation_current', 10, 2)->nullable();
            $table->decimal('target', 10, 2)->nullable();
            $table->decimal('median_price', 10, 2)->nullable();
            $table->string('auto_grate')->nullable();
            $table->string('nature')->nullable();
            $table->text('remark')->nullable();
            $table->decimal('redbook', 10, 2)->nullable();
            $table->decimal('auction_price', 10, 2)->nullable();
            $table->decimal('tls_price', 10, 2)->nullable();
            $table->text('reason')->nullable();
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->decimal('vat_selling price', 10, 2)->nullable();
            $table->decimal('total_selling price', 10, 2)->nullable();
            $table->decimal('profit_loss', 10, 2)->nullable();
            $table->decimal('tax_refund', 10, 2)->nullable();
            $table->decimal('other_psice', 10, 2)->nullable();
            $table->string('customer')->nullable();
            $table->text('address')->nullable();
            $table->userFields();

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
        Schema::dropIfExists('car_auctions');
    }
}
