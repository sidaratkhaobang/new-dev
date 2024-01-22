<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRentalsAddPaymentGateway extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->string('payment_gateway', 20)->nullable()->after('payment_remark');
            $table->boolean('is_paid')->default(false)->after('payment_gateway');
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
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn('payment_response_desc');
            $table->dropColumn('payment_date');
            $table->dropColumn('is_paid');
            $table->dropColumn('payment_gateway');
        });
    }
}
