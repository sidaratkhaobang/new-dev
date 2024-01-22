<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRentalBillAddCheckCustomerAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rental_bills', function (Blueprint $table) {
            $table->boolean('check_customer_address')->default(true)->after('check_withholding_tax');
            $table->uuid('customer_billing_address_id')->after('check_customer_address')->nullable();

            $table->foreign('customer_billing_address_id')->references('id')->on('customer_billing_addresses')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rental_bills', function (Blueprint $table) {
            $table->dropColumn('check_customer_address');
            $table->dropForeign(['customer_billing_address_id']);
            $table->dropColumn('customer_billing_address_id');
        });
    }
}
