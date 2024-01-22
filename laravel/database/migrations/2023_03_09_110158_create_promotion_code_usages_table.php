<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionCodeUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_code_usages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('promotion_code_id');
            $table->uuid('customer_id');
            $table->uuidMorphs('item'); // short term or others
            $table->dateTime('use_date')->nullable();

            $table->foreign('promotion_code_id')->references('id')->on('promotion_codes')->cascadeOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotion_code_usages');
    }
}