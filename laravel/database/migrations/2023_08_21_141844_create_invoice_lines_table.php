<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('invoice_id');
            $table->string('title', 200)->nullable();
            $table->text('description')->nullable();
            $table->decimal('sub_total', 12, 2)->nullable();
            $table->decimal('vat', 12, 2)->nullable();
            $table->decimal('total', 12, 2)->nullable();

            $table->foreign('invoice_id')->references('id')->on('invoices')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_lines');
    }
}