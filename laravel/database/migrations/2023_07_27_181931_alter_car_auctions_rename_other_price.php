<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCarAuctionsRenameOtherPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_auctions', function (Blueprint $table) {
            $table->renameColumn('other_psice', 'other_price');
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
            $table->renameColumn('other_price', 'other_psice');
        });
    }
}
