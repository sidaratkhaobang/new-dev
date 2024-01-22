<?php

use App\Enums\InspectionStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectionJobStepLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspection_job_step_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('inspection_job_step_id')->nullable();
            $table->string('inspection_status', 10)->default(InspectionStatusEnum::DRAFT);
            $table->string('remark', 100)->nullable();
            $table->string('remark_reason', 100)->nullable();

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
        Schema::dropIfExists('inspection_job_step_logs');
    }
}
