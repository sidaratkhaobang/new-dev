<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rental_bill_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rental_bill_id');
            $table->uuid('rental_line_id')->nullable(); // for reference
            $table->uuidMorphs('item'); // product , product_additional , promotion
            $table->boolean('is_free')->default(false);
            $table->boolean('is_from_product')->default(false);
            $table->boolean('is_from_promotion')->default(false);
            $table->boolean('is_from_coupon')->default(false);
            $table->uuid('car_id')->nullable();
            $table->string('name', 500)->nullable();
            $table->string('description', 2000)->nullable();
            $table->integer('amount')->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('vat', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->foreign('rental_bill_id')->references('id')->on('rental_bills')->cascadeOnDelete();
            $table->foreign('rental_line_id')->references('id')->on('rental_lines')->nullOnDelete();
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
        Schema::dropIfExists('rental_bill_lines');
    }
};
