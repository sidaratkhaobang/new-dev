<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompensationDemandLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compensation_demand_letters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('compensation_id');
            $table->date('delivery_date')->nullable();
            $table->string('rp_no', 50)->nullable();
            $table->date('receive_date')->nullable();
            $table->string('recipient_name', 100)->nullable();
            $table->text('remark')->nullable();
            $table->userFields();

            $table->foreign('compensation_id')->references('id')->on('compensations')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compensation_demand_letters');
    }
}
