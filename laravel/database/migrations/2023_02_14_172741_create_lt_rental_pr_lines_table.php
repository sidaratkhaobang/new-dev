<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLtRentalPrLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lt_rental_pr_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lt_rental_id');
            $table->uuid('lt_rental_line_id');
            $table->uuid('lt_rental_month_id');
            $table->integer('amount')->default(0);
            $table->text('remark')->nullable();

            $table->foreign('lt_rental_id')->references('id')->on('lt_rentals')->cascadeOnDelete();
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
        Schema::dropIfExists('lt_rental_pr_lines');
    }
}
