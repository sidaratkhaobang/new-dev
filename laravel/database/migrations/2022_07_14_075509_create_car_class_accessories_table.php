<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarClassAccessoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_class_accessories', function (Blueprint $table) {
            $table->uuid('car_class_id');
            $table->uuid('accessory_id');

            $table->string('remark')->nullable();

            $table->primary(['car_class_id', 'accessory_id']);

            $table->foreign('car_class_id')->references('id')->on('car_classes')->cascadeOnDelete();
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
        Schema::dropIfExists('car_class_accessories');
    }
}
