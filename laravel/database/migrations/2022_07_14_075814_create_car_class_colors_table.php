<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarClassColorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_class_colors', function (Blueprint $table) {
            $table->uuid('car_class_id');
            $table->uuid('car_color_id');

            $table->decimal('standard_price')->nullable()->default(0);
            $table->decimal('color_price')->nullable()->default(0);
            $table->string('remark')->nullable();

            $table->primary(['car_class_id', 'car_color_id']);

            $table->foreign('car_class_id')->references('id')->on('car_classes')->cascadeOnDelete();
            $table->foreign('car_color_id')->references('id')->on('car_colors')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_class_colors');
    }
}
