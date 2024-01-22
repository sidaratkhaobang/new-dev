<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequisitionLineAccessoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_requisition_line_accessories', function (Blueprint $table) {
            $table->uuid('id')->primary('pr_line_accessories_pk');
            $table->uuid('purchase_requisition_line_id');
            $table->uuid('accessory_id')->nullable();
            $table->integer('amount')->default(0);
            $table->text('remark')->nullable();

            $table->foreign('purchase_requisition_line_id', 'pr_line_fk')->references('id')->on('purchase_requisition_lines')->cascadeOnDelete();
            $table->foreign('accessory_id')->references('id')->on('accessories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_requisition_line_parts');
    }
}
