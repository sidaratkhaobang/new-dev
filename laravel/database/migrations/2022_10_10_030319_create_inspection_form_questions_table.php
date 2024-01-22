<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectionFormQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspection_form_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->integer('seq');
            $table->status();

            $table->uuid('inspection_form_id');

            $table->foreign('inspection_form_id')->references('id')->on('inspection_forms')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inspection_form_questions');
    }
}
