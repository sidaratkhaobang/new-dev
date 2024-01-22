<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLtRentalTorLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lt_rental_tor_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lt_rental_tor_id');
            $table->uuid('car_class_id')->nullable();
            $table->uuid('car_color_id')->nullable();
            $table->integer('amount')->default(0);
            $table->boolean('is_rental_line')->default(false);

            $table->decimal('rental_price', 10, 2)->nullable();

            $table->string('remark')->nullable();
            $table->boolean('have_accessories')->default(false);
            $table->decimal('purchase_options', 10, 2)->default(0);

            $table->foreign('lt_rental_tor_id')->references('id')->on('lt_rental_tors')->cascadeOnDelete();
            $table->foreign('car_class_id')->references('id')->on('car_classes')->nullOnDelete();
            $table->foreign('car_color_id')->references('id')->on('car_colors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lt_rental_tor_lines');
    }
}
