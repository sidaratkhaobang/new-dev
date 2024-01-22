<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingSlipLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_slip_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('billing_slip_id')->nullable();
            $table->string('billing_slip_no',36)->nullable();
            $table->string('amount_document',36)->nullable();
            $table->string('amount',36)->nullable();
            $table->text('remark')->nullable();
            $table->foreign('billing_slip_id')->references('id')->on('billing_slips')->cascadeOnDelete();
            $table->status();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billing_slip_lines', function (Blueprint $table) {
            $table->dropForeign(['billing_slip_id']);
        });
        Schema::dropIfExists('billing_slip_lines');
    }
}
