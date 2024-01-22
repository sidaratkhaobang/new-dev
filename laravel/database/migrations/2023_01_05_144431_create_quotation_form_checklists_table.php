<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationFormChecklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_form_checklists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quotation_form_id');
            $table->string('name');
            $table->integer('seq');
            $table->status();

            $table->foreign('quotation_form_id')->references('id')->on('quotation_forms')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotation_form_checklists');
    }
}
