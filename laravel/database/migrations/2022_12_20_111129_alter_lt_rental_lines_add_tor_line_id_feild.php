<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLtRentalLinesAddTorLineIdFeild extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lt_rental_lines', function (Blueprint $table) {
            $table->uuid('lt_rental_tor_line_id')->nullable();
            $table->foreign('lt_rental_tor_line_id')->references('id')->on('lt_rental_tor_lines')->nullOnDelete();
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
            $table->dropForeign(['lt_rental_tor_line_id']);
            $table->dropColumn('lt_rental_tor_line_id');
        });
    }
}
