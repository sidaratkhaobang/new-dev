<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepairOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repair_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no')->nullable();
            $table->uuid('repair_id')->nullable();
            $table->decimal('check_distance')->nullable();
            $table->date('expected_repair_date')->nullable();
            $table->date('repair_date')->nullable();
            $table->uuid('center_id')->nullable();
            $table->text('remark')->nullable();
            $table->date('receive_repair_order_date')->nullable();
            $table->date('receive_quotation')->nullable();
            $table->tinyInteger('is_expenses')->nullable();
            $table->string('status')->nullable();
            $table->text('reason')->nullable();
            $table->userFields();

            $table->foreign('repair_id')->references('id')->on('repairs')->nullOnDelete();
            $table->foreign('center_id')->references('id')->on('creditors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('repair_orders');
    }
}
