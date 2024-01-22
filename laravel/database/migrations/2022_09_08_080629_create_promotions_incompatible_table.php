<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionsIncompatibleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions_incompatible', function (Blueprint $table) {
            $table->uuid('promotion_id');
            $table->uuid('promotion_incompatible_id');

            $table->primary(['promotion_id', 'promotion_incompatible_id'], 'promotions_incompatible_pk');

            $table->foreign('promotion_id')->references('id')->on('promotions')->cascadeOnDelete();
            $table->foreign('promotion_incompatible_id', 'promotion_incompatible_id_fk')->references('id')->on('promotions')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotions_incompatible');
    }
}
