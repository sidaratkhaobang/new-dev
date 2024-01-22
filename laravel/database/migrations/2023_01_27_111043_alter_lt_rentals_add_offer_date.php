<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLtRentalsAddOfferDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lt_rentals', function (Blueprint $table) {
            $table->date('offer_date')->nullable()->after('require_date');
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
            $table->dropColumn('offer_date');
        });
    }
}
