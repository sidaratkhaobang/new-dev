<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentalProductAdditionalsTable extends Migration
{
    public function up()
    {
        Schema::create('rental_product_additionals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rental_id');
            $table->uuid('product_additional_id')->nullable();
            $table->uuid('car_id')->nullable();
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('amount')->default(0);
            $table->boolean('is_free')->default(false);
            $table->boolean('is_from_product')->default(false);
            $table->boolean('is_from_promotion')->default(false);
            $table->boolean('outbound_is_check')->nullable();
            $table->boolean('inbound_approve')->nullable();
            $table->text('inbound_remark')->nullable();

            $table->foreign('rental_id')->references('id')->on('rentals')->cascadeOnDelete();
            $table->foreign('product_additional_id')->references('id')->on('product_additionals')->nullOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rental_product_additionals');
    }
}
