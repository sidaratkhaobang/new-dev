<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckBillingStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_billing_status', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('check_billing_date_id');
            $table->date('sending_billing_date')->nullable();
            $table->date('check_billing_date')->nullable();
            $table->string('detail', 200)->nullable();
            $table->string('status', 50)->nullable();
            $table->userFields();

            $table->foreign('check_billing_date_id')->references('id')->on('check_billing_dates')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('check_billing_status');
    }
}
