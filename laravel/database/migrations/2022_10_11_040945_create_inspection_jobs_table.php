<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\InspectionTypeEnum;
use App\Enums\InspectionStatusEnum;
use App\Enums\TransferReasonEnum;
use App\Enums\TransferTypeEnum;

class CreateInspectionJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspection_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('car_id')->nullable();
            $table->string('worksheet_no', 20)->nullable();
            $table->nullableUuidMorphs('item'); // short term, long term, other
            $table->tinyInteger('transfer_type')->default(TransferTypeEnum::IN);
            $table->dateTime('open_date')->nullable();
            $table->uuid('inspection_flow_id')->nullable();
            $table->string('inspection_type')->default(InspectionTypeEnum::SELF_DRIVE);
            $table->string('inspection_status', 10)->default(InspectionStatusEnum::DRAFT);
            $table->string('recipient_staff_name', 255)->nullable();
            $table->string('recipient_staff_tel', 100)->nullable();
            $table->string('transfer_reason', 30)->nullable();
            $table->uuid('inspection_department_id')->nullable();
            $table->string('remark', 50)->nullable();
            $table->dateTime('inspection_date')->nullable();
            
            $table->foreign('inspection_department_id')->references('id')->on('user_departments')->cascadeOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inspection_jobs');
    }
}
