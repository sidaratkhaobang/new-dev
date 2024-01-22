<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->nullableUuidMorphs('job');
            $table->uuid('branch_id')->nullable();
            $table->string('invoice_type', 20)->nullable();
            $table->string('invoice_no', 30)->nullable();
            $table->string('customer_code', 100)->nullable();
            $table->string('customer_name', 100)->nullable();
            $table->text('customer_address')->nullable();
            $table->string('customer_tax_no', 20)->nullable();
            $table->string('payment_terms', 20)->nullable();
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('our_reference', 50)->nullable();
            $table->text('remark')->nullable();
            $table->decimal('sub_total', 12, 2)->nullable();
            $table->decimal('vat', 12, 2)->nullable();
            $table->decimal('total', 12, 2)->nullable();
            $table->userFields();

            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}