<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDealerCheckCarsTableAddIsReadyToDeliver extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dealer_check_cars', function (Blueprint $table) {
            $table->boolean('is_ready_to_deliver')->default(true)->after('delivery_month_year');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dealer_check_cars', function (Blueprint $table) {
            $table->dropColumn(['is_ready_to_deliver']);
        });
    }
}
