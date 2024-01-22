<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRentalsAddCustomerAddressField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->uuid('customer_billing_address_id')->nullable()->after('customer_id');

            $table->foreign('customer_billing_address_id')->references('id')->on('customer_billing_addresses')->nullOnDelete();
        });

        Schema::table('rental_lines', function (Blueprint $table) {
            $table->boolean('is_from_product')->default(false)->after('is_free');
            $table->decimal('unit_price', 10, 2)->default(0)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropForeign(['customer_billing_address_id']);
            $table->dropColumn('customer_billing_address_id');
        });

        Schema::table('rental_lines', function (Blueprint $table) {
            $table->dropColumn('is_from_product');
            $table->dropColumn('unit_price');
        });
    }
}
