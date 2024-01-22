<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contract_id');
            $table->foreign('contract_id')->references('id')->on('contracts')->cascadeOnDelete();
            $table->uuid('car_id');
            $table->foreign('car_id')->references('id')->on('cars')->cascadeOnDelete();
            $table->dateTime('pick_up_date')->nullable();
            $table->dateTime('expected_return_date')->nullable();
            $table->dateTime('return_date')->nullable();
            $table->string('car_user')->nullable();
            $table->string('tell',20)->nullable();
            $table->boolean('is_fine')->nullable();
            $table->decimal('percent_fine', 10, 2)->nullable();
            $table->decimal('fine', 10, 2)->nullable();
            $table->string('status')->nullable();
            $table->userFields();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_lines');
    }
}
