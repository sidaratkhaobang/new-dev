<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComparisonPriceLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comparison_price_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('comparison_price_id');
            $table->uuidMorphs('item');
            $table->string('name', 500)->nullable();
            $table->integer('amount')->default(0);

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('vat', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->foreign('comparison_price_id')->references('id')->on('comparison_prices')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comparison_price_lines');
    }
}
