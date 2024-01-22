<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditorsTypesRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creditors_types_relation', function (Blueprint $table) {
            $table->uuid('creditor_id');
            $table->uuid('creditor_type_id');

            $table->primary(['creditor_id', 'creditor_type_id']);

            $table->foreign('creditor_id')->references('id')->on('creditors')->cascadeOnDelete();
            $table->foreign('creditor_type_id')->references('id')->on('creditor_types')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('creditors_types_relation');
    }
}
