<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectionFormChecklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspection_form_checklists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('car_part', 20)->nullable();
            $table->integer('seq');
            $table->status();

            $table->uuid('inspection_form_section_id');

            $table->foreign('inspection_form_section_id', 'inspection_form_section_id_fk')->references('id')->on('inspection_form_sections')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inspection_form_checklists');
    }
}
