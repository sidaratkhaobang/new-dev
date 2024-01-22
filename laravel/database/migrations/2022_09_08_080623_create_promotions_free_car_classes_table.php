<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionsFreeCarClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions_free_car_classes', function (Blueprint $table) {
            $table->uuid('promotion_id');
            $table->uuid('car_class_id');

            $table->primary(['promotion_id', 'car_class_id'], 'promotions_free_car_classes_pk');

            $table->foreign('promotion_id')->references('id')->on('promotions')->cascadeOnDelete();
            $table->foreign('car_class_id', 'car_class_id_fk')->references('id')->on('car_classes')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotions_free_car_classes');
    }
}
