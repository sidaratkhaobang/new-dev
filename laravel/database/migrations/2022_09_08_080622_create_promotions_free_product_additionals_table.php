<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionsFreeProductAdditionalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions_free_product_additionals', function (Blueprint $table) {
            $table->uuid('promotion_id');
            $table->uuid('product_additional_id');

            $table->primary(['promotion_id', 'product_additional_id'], 'promotions_free_product_additionals_pk');

            $table->foreign('promotion_id')->references('id')->on('promotions')->cascadeOnDelete();
            $table->foreign('product_additional_id', 'product_additionals_fk')->references('id')->on('product_additionals')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotions_free_product_additionals');
    }
}
