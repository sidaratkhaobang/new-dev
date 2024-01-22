<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLtRentalLinesMonthAddSubTotalPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lt_rental_lines_months', function (Blueprint $table) {
            $table->renameColumn('price', 'subtotal_price');
            $table->renameColumn('purchase_options', 'subtotal_purchase_options');
            $table->decimal('vat_price', 10, 2)->default(0)->after('purchase_options');
            $table->decimal('vat_purchase_options', 10, 2)->default(0)->after('vat_price');
            $table->decimal('total_price', 10, 2)->default(0)->after('vat_purchase_options');
            $table->decimal('total_purchase_options', 10, 2)->default(0)->after('total_price');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lt_rental_lines_months', function (Blueprint $table) {
            $table->renameColumn('subtotal_price', 'price');
            $table->renameColumn('tranID_respones', 'tranID_response');
            $table->dropColumn('subtotal_purchase_options');
            $table->dropColumn('vat_price');
            $table->dropColumn('vat_purchase_options');
            $table->dropColumn('total_price');

        });
    }
}
