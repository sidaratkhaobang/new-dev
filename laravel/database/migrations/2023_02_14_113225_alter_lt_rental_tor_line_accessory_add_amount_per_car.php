<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLtRentalTorLineAccessoryAddAmountPerCar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lt_rental_tor_line_accessories', function (Blueprint $table) {
            $table->integer('amount_per_car')->nullable()->after('accessory_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lt_rental_tor_line_accessories', function (Blueprint $table) {
            $table->dropColumn('amount_per_car');
        });
    }
}
