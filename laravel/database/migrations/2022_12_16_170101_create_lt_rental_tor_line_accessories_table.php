<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLtRentalTorLineAccessoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lt_rental_tor_line_accessories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lt_rental_tor_line_id');
            $table->uuid('accessory_id')->nullable();
            $table->integer('amount')->default(0);

            $table->foreign('accessory_id')->references('id')->on('accessories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lt_rental_tor_line_accessories');
    }
}
