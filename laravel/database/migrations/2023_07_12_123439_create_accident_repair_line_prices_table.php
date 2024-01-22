<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccidentRepairLinePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accident_repair_line_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_repair_order_id')->nullable();
            $table->string('supplier',100)->nullable();
            $table->decimal('spare_parts', 10, 2)->nullable();
            $table->decimal('discount_spare_parts', 10, 2)->nullable();
            $table->userFields();

            $table->foreign('accident_repair_order_id')->references('id')->on('accident_repair_orders')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accident_repair_line_prices');
    }
}
