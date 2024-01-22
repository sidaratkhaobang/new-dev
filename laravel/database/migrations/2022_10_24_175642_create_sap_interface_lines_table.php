<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSapInterfaceLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sap_interface_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sap_interface_id')->nullable();
            $table->boolean('flag')->nullable();

            $table->date('entry_date')->nullable();
            $table->string('document_number', 20)->nullable();

            $table->date('posting_date')->nullable();
            $table->date('document_date')->nullable();
            $table->string('document_type', 6)->nullable();
            $table->string('company_code', 4)->nullable();
            $table->string('branch_number', 4)->nullable();
            $table->string('currency', 4)->nullable();
            $table->string('reference_document', 20)->nullable();
            $table->string('header_text', 50)->nullable();
            $table->string('posting_key', 2)->nullable();
            $table->string('account_no', 20)->nullable();
            $table->decimal('amount_in_document', 12, 2)->nullable();
            $table->decimal('amount_in_local_currency', 12, 2)->nullable();
            $table->string('cost_center', 20)->nullable();
            $table->decimal('base_amount', 12, 2)->nullable();
            $table->string('tax_code', 2)->nullable();
            $table->string('assignment', 50)->nullable(); // old code from customers table
            $table->string('text', 100)->nullable();

            $table->foreign('sap_interface_id')->references('id')->on('sap_interfaces')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sap_interface_lines');
    }
}
