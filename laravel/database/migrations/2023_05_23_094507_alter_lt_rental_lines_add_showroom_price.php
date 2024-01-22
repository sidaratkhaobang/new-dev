<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLtRentalLinesAddShowroomPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lt_rental_lines', function (Blueprint $table) {
            $table->decimal('showroom_price', 10, 2)->nullable()->after('rental_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lt_rental_lines', function (Blueprint $table) {
            $table->dropColumn(['showroom_price']);
        });
    }
}
