<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCarAuctionsRenameVatSellingPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_auctions', function (Blueprint $table) {
            $table->renameColumn('`vat_selling price`', '`vat_selling_price`');
            $table->renameColumn('`total_selling price`', '`total_selling_price`');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_auctions', function (Blueprint $table) {
            $table->renameColumn('`vat_selling_price`', '`vat_selling price`');
            $table->renameColumn('`total_selling_price`', '`total_selling price`');
        });
    }
}
