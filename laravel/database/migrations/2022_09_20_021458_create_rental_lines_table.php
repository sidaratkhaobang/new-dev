<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\OrderLineTypeEnum;

class CreateRentalLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rental_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rental_id');
            $table->uuidMorphs('item'); // product , product_additional , promotion
            //$table->tinyInteger('order_line_type')->default(OrderLineTypeEnum::OTHER);
            $table->boolean('is_free')->default(false);
            $table->boolean('is_from_promotion')->default(false);
            $table->boolean('is_from_coupon')->default(false);
            $table->uuid('car_id')->nullable();
            $table->string('name', 500)->nullable();
            $table->string('description')->nullable();
            $table->integer('amount')->default(0);

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('vat', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->foreign('rental_id')->references('id')->on('rentals')->cascadeOnDelete();
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
        Schema::dropIfExists('rental_lines');
    }
}
