<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLtRentalLinesMonthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lt_rental_lines_months', function (Blueprint $table) {
            $table->uuid('lt_rental_line_id');
            $table->uuid('lt_rental_month_id');
            $table->primary(['lt_rental_line_id', 'lt_rental_month_id'], 'lt_rental_lines_months_pk');

            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('purchase_options', 10, 2)->default(0);

            $table->foreign('lt_rental_line_id')->references('id')->on('lt_rental_lines')->cascadeOnDelete();
            $table->foreign('lt_rental_month_id')->references('id')->on('lt_rental_month')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lt_rental_lines_months');
    }
}
