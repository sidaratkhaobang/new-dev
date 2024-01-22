<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRentalBillsAddIsTax35Fields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rental_bills', function (Blueprint $table) {
            $table->integer('withholding_tax_value')->default(0)->after('check_withholding_tax');
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
            $table->dropColumn('withholding_tax_value');
        });
    }
}
