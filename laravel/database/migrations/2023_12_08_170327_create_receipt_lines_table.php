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
        Schema::create('receipt_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('receipt_id');
            $table->uuidMorphs('reference');
            $table->string('name', 100)->nullable();
            $table->decimal('subtotal', 12, 2)->nullable();
            $table->decimal('vat', 12, 2)->nullable();
            $table->decimal('total', 12, 2)->nullable();

            $table->foreign('receipt_id')->references('id')->on('receipts')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receipt_lines');
    }
};
