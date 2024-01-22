<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConditionQuotationChecklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('condition_quotation_checklists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('condition_quotations_id');
            $table->string('name');
            $table->integer('seq');
            $table->status();

            $table->foreign('condition_quotations_id')->references('id')->on('condition_quotations')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('condition_quotation_checklists');
    }
}
