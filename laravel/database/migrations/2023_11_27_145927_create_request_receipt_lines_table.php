<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_receipt_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('request_receipt_id');
            $table->string('list_name', 100)->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->decimal('fee_deducted', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->string('status', 50)->nullable();
            $table->userFields();

            $table->foreign('request_receipt_id')->references('id')->on('request_receipts')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_request_receipt_lines');
    }
};
