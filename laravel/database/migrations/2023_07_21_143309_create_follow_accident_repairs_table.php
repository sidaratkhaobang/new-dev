<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowAccidentRepairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follow_accident_repairs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_repair_order_id')->nullable();
            $table->string('follow_up_status', 100)->nullable();
            $table->datetime('received_data_date')->nullable();
            $table->text('problem')->nullable();
            $table->text('solution')->nullable();
            $table->userFields();

            $table->foreign('accident_repair_order_id')->references('id')->on('accident_repair_orders')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('follow_accident_repairs');
    }
}
