<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLtRentalLineAccessoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lt_rental_line_accessories', function (Blueprint $table) {
            $table->uuid('id')->primary('lt_rental_line_accessories_pk');
            $table->uuid('lt_rental_line_id');
            $table->uuid('accessory_id')->nullable();
            $table->integer('amount')->default(0);

            $table->foreign('lt_rental_line_id', 'lt_rental_line_id_fk')->references('id')->on('lt_rental_lines')->cascadeOnDelete();
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
        Schema::dropIfExists('lt_rental_line_accessories');
    }
}
