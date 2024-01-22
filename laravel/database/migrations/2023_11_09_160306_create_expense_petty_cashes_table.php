<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_petty_cashes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('petty_cash_type', 50)->nullable();
            $table->uuidMorphs('reference');
            $table->uuid('expense_type_id')->nullable();
            $table->string('name', 100)->nullable();
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('vat', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->text('remark')->nullable();
            $table->userFields();

            $table->foreign('expense_type_id')->references('id')->on('expense_types')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expense_petty_cashes');
    }
};