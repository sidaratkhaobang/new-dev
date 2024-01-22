<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\QuotationTypeEnum;
use App\Enums\QuotationStatusEnum;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('qt_no', 20)->nullable();
            $table->string('qt_type', 30)->default(QuotationTypeEnum::SHORT_TERM_RENTAL);
            $table->nullableUuidMorphs('reference');

            // customer
            $table->uuid('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->text('customer_address')->nullable();
            $table->string('customer_tel', 20)->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_zipcode', 10)->nullable();
            $table->unsignedBigInteger('customer_province_id')->nullable();

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('vat', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->text('remark')->nullable();

            $table->string('status', 20)->default(QuotationStatusEnum::DRAFT);
            $table->userFields();

            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('customer_province_id')->references('id')->on('provinces')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotations');
    }
}
