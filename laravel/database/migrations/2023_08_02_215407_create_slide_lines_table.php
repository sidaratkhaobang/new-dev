<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlideLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slide_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('slide_id')->nullable();
            $table->string('type', 50)->nullable();
            $table->uuid('car_id')->nullable();
            $table->userFields();

            $table->foreign('slide_id')->references('id')->on('slides')->nullOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('slide_lines');
    }
}
