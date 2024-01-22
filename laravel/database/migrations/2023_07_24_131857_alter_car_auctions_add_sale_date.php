<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCarAuctionsAddSaleDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_auctions', function (Blueprint $table) {
            $table->datetime('sale_date')->nullable()->after('send_auction_date');
            $table->uuid('auction_id')->nullable()->after('sale_date');

            $table->foreign('auction_id')->references('id')->on('auction_places')->nullOnDelete();
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
            $table->dropColumn(['sale_date']);
            $table->dropColumn(['auction_id']);
        });
    }
}
