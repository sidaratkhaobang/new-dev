<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\InspectionStatusEnum;
use App\Enums\TransferTypeEnum;
use App\Enums\TransferReasonEnum;

class CreateInspectionJobStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspection_job_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('inspection_job_id');
            $table->uuid('inspection_step_id')->nullable();
            $table->string('inspection_status', 10)->default(InspectionStatusEnum::DRAFT);
            $table->tinyInteger('transfer_type')->default(TransferTypeEnum::IN);
            $table->string('transfer_reason', 30)->default(TransferReasonEnum::DELIVER_CUSTOMER);
            $table->uuid('inspection_form_id')->nullable();

            $table->uuid('inspector')->nullable();
            $table->dateTime('inspection_date')->nullable();
            $table->tinyInteger('oil_quantity')->nullable();
            $table->tinyInteger('mileage')->nullable();
            $table->string('delivery_staff_name', 255)->nullable();
            $table->uuid('inspection_department_id')->nullable();

            $table->foreign('inspection_department_id')->references('id')->on('user_departments')->cascadeOnDelete();
            $table->foreign('inspection_form_id')->references('id')->on('inspection_forms')->nullOnDelete();
            $table->foreign('inspection_job_id')->references('id')->on('inspection_jobs')->cascadeOnDelete();
            $table->foreign('inspector')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inspection_job_steps');
    }
}
