<?php

use App\Enums\OfficeTypeEnum;
use App\Enums\CustomerTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerBillingAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_billing_addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->string('billing_customer_type', 20)->default(CustomerTypeEnum::GOVERNMENT);
            $table->tinyInteger('office_type')->default(OfficeTypeEnum::HEAD_OFFICE);
            $table->string('name');
            $table->string('tax_no', 20)->nullable();
            $table->unsignedBigInteger('province_id')->nullable();
            $table->text('address')->nullable();
            $table->string('tel', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('zipcode', 10)->nullable();
            $table->userFields();

            $table->foreign('province_id')->references('id')->on('provinces')->nullOnDelete();
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
        Schema::dropIfExists('customer_billing_addresses');
    }
}
