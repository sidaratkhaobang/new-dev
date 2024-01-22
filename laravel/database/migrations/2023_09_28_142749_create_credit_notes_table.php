<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->nullableUuidMorphs('job');
            $table->uuid('branch_id')->nullable();
            $table->string('credit_note_no', 30)->nullable();
            $table->uuid('customer_id')->nullable();
            $table->string('customer_code', 100)->nullable();
            $table->string('customer_name', 100)->nullable();
            $table->text('customer_address')->nullable();
            $table->string('customer_tax_no', 20)->nullable();
            $table->date('credit_note_date')->nullable();
            $table->string('reference_type', 100)->nullable();
            $table->string('reference_no', 50)->nullable();
            $table->date('reference_date')->nullable();
            $table->text('remark')->nullable();
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('vat', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->string('status', 50)->nullable();
            $table->userFields();

            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credit_notes');
    }
}
