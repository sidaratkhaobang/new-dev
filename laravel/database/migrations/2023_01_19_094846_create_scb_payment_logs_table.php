<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScbPaymentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scb_payment_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('request')->nullable();
            $table->string('user')->nullable();
            $table->string('password')->nullable();
            $table->string('tranID_request')->nullable();
            $table->dateTime('tranDate')->nullable();
            $table->string('channel')->nullable();
            $table->string('account')->nullable();
            $table->decimal('amount_request', 10, 2)->nullable();
            $table->string('reference_1')->nullable();
            $table->string('reference_2_request')->nullable();
            $table->string('reference_3')->nullable();
            $table->string('branchCode')->nullable();
            $table->string('terminalID')->nullable();
            $table->string('response')->nullable();
            $table->string('resCode')->nullable();
            $table->string('resMesg')->nullable();
            $table->string('tranID_respones')->nullable();
            $table->string('reference_2_respones')->nullable();
            $table->string('paymentID',36)->nullable();
            $table->decimal('amount_respones', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scb_payment_logs');
    }
}
