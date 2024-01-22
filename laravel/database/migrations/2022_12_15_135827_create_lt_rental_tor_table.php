<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLtRentalTorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lt_rental_tors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('remark_tor')->nullable();
            $table->uuid('lt_rental_id');

            $table->foreign('lt_rental_id')->references('id')->on('lt_rentals')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lt_rental_tors');
    }
}
