<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepairOrderDateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repair_order_date', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('repair_order_id')->nullable();
            $table->date('center_date')->nullable();
            $table->string('status')->default(STATUS_ACTIVE);
            $table->userFields();

            $table->foreign('repair_order_id')->references('id')->on('repair_orders')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('repair_order_date');
    }
}
