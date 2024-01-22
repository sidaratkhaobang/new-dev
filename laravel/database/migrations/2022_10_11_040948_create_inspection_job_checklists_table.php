<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectionJobChecklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspection_job_checklists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('inspection_job_step_id');
            $table->uuid('inspection_form_section_id')->nullable();
            $table->string('inspection_form_section_name')->nullable();

            $table->uuid('inspection_form_checklist_id')->nullable();
            $table->string('inspection_form_checklist_name')->nullable();
            $table->boolean('is_pass')->nullable();
            $table->string('remark')->nullable();
            $table->foreign('inspection_job_step_id')->references('id')->on('inspection_job_steps')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inspection_job_checklists');
    }
}
