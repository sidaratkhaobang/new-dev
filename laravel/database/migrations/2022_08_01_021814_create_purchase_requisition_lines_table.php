<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequisitionLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_requisition_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('purchase_requisition_id');
            $table->uuid('car_class_id')->nullable();
            $table->uuid('car_color_id')->nullable();
            $table->integer('amount')->default(0);

            $table->foreign('purchase_requisition_id')->references('id')->on('purchase_requisitions')->cascadeOnDelete();
            $table->foreign('car_class_id')->references('id')->on('car_classes')->nullOnDelete();
            $table->foreign('car_color_id')->references('id')->on('car_colors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_requisition_car_classes');
    }
}
