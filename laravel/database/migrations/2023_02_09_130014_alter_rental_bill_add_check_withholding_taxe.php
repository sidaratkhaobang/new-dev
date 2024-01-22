<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRentalBillAddCheckWithholdingTaxe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rental_bills', function (Blueprint $table) {
            $table->boolean('check_withholding_tax')->nullable()->after('payment_response_desc');
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
            $table->dropColumn('check_withholding_tax');
        });
    }
}
