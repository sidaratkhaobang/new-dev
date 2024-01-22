<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepairOrderLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repair_order_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('repair_order_id');
            $table->string('repair_list_type')->nullable();
            $table->uuid('repair_list_id')->nullable();
            $table->string('check')->nullable();
            $table->decimal('price')->nullable();
            $table->integer('amount')->nullable();
            $table->decimal('discount')->nullable();
            $table->decimal('vat')->nullable();
            $table->decimal('total')->nullable();
            $table->text('remark')->nullable();
            $table->userFields();

            $table->foreign('repair_order_id')->references('id')->on('repair_orders')->cascadeOnDelete();
            $table->foreign('repair_list_id')->references('id')->on('repair_lists')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('repair_order_lines');
    }
}
