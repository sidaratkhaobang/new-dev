<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLtRentalTorLineAddCheckDelivery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lt_rental_tor_lines', function (Blueprint $table) {
            $table->tinyInteger('check_delivery')->nullable()->default(0)->after('purchase_options');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lt_rental_tor_lines', function (Blueprint $table) {
            $table->dropColumn('check_delivery');
        });
    }
}
