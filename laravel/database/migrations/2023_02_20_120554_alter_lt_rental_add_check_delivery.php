<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLtRentalAddCheckDelivery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lt_rentals', function (Blueprint $table) {
            $table->tinyInteger('check_delivery')->nullable()->after('purchase_option_check');
            $table->text('reason_delivery')->nullable()->after('check_delivery');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lt_rentals', function (Blueprint $table) {
            $table->dropColumn('check_delivery');
            $table->dropColumn('reason_delivery');
        });
    }
}
