<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepairLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repair_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('repair_id');
            $table->date('date')->nullable();
            $table->text('description')->nullable();
            $table->string('check')->nullable();
            $table->text('qc')->nullable();
            $table->userFields();

            $table->foreign('repair_id')->references('id')->on('repairs')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('repair_lines');
    }
}
