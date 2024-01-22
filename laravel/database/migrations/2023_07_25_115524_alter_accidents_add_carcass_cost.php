<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAccidentsAddCarcassCost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accidents', function (Blueprint $table) {
            $table->decimal('carcass_cost',10,2)->nullable()->after('claim_no');
            $table->decimal('repair_cost_parties',10,2)->nullable()->after('carcass_cost');
            $table->tinyInteger('is_stop_gps')->nullable()->after('repair_cost_parties');
            $table->tinyInteger('is_status_rental_car')->nullable()->after('is_stop_gps');
            $table->tinyInteger('is_pick_up_book')->nullable()->after('is_status_rental_car');
            $table->tinyInteger('is_off_installments')->nullable()->after('is_pick_up_book');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accidents', function (Blueprint $table) {
            $table->dropColumn(['carcass_cost']);
            $table->dropColumn(['repair_cost_parties']);
            $table->dropColumn(['is_stop_gps']);
            $table->dropColumn(['is_status_rental_car']);
            $table->dropColumn(['is_pick_up_book']);
            $table->dropColumn(['is_off_installments']);
        });
    }
}
