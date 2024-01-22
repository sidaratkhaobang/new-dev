<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\ReceiptStatusEnum;

class CreateReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no', 20)->nullable();
            $table->string('receipt_type', 30)->nullable();
            $table->uuid('rental_bill_id')->nullable();
            $table->nullableUuidMorphs('reference');

            $table->uuid('customer_id')->nullable();
            $table->string('customer_name', 255)->nullable();
            $table->text('customer_address')->nullable();
            $table->string('customer_tel', 20)->nullable();
            $table->string('customer_email', 255)->nullable();
            $table->string('customer_code', 20)->nullable();
            $table->string('customer_tax_no', 255)->nullable();

            $table->decimal('subtotal', 12, 2)->default(0)->nullable();
            $table->decimal('withholding_tax', 12, 2)->default(0)->nullable();
            $table->decimal('vat', 12, 2)->default(0)->nullable();
            $table->decimal('total', 12, 2)->default(0)->nullable();

            $table->text('remark')->nullable();
            $table->string('status', 255)->default(ReceiptStatusEnum::ACTIVE);
            $table->userFields();

            $table->foreign('rental_bill_id')->references('id')->on('rental_bills')->nullOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        });
        Schema::table('receipts', function (Blueprint $table) {
            $table->uuid('parent_id')->nullable()->after('status');

            $table->foreign('parent_id')->references('id')->on('receipts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receipts');
    }
}
