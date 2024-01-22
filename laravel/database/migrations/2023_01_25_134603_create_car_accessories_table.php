<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarAccessoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_accessories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('car_id')->nullable();
            $table->uuid('accessory_id')->nullable();
            $table->text('remark')->nullable();
            $table->integer('amount')->nullable();

            $table->foreign('car_id')->references('id')->on('cars')->cascadeOnDelete();
            $table->foreign('accessory_id')->references('id')->on('accessories')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_accessories');
    }
}
