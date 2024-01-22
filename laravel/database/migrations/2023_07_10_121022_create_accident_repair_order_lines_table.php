<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccidentRepairOrderLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accident_repair_order_lines', function (Blueprint $table) {
            $table->uuid('accident_repair_order_id')->nullable();
            $table->uuid('accident_claim_line_id')->nullable();

            $table->foreign('accident_repair_order_id')->references('id')->on('accident_repair_orders')->cascadeOnDelete();
            $table->foreign('accident_claim_line_id')->references('id')->on('accident_claim_lines')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accident_repair_order_lines');
    }
}
