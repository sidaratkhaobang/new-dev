<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRentalBillsAddPaymentFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rental_bills', function (Blueprint $table) {
            $table->string('payment_gateway', 20)->nullable()->after('payment_remark');
            $table->text('payment_url')->nullable()->after('payment_gateway');
            $table->boolean('is_paid')->default(false)->after('payment_url');
            $table->dateTime('payment_date')->nullable()->after('is_paid');
            $table->text('payment_response_desc')->nullable()->after('payment_date');
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
            $table->dropColumn('payment_gateway');
            $table->dropColumn('payment_url');
            $table->dropColumn('is_paid');
            $table->dropColumn('payment_date');
            $table->dropColumn('payment_response_desc');
        });
    }
}
