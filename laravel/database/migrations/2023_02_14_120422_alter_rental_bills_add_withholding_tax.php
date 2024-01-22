<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRentalBillsAddWithholdingTax extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rental_bills', function (Blueprint $table) {
            $table->decimal('withholding_tax', 10, 2)->default(0)->after('vat');
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
            $table->dropColumn('withholding_tax');
        });
    }
}
