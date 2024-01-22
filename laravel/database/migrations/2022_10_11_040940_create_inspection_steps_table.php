<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TransferTypeEnum;
use App\Enums\TransferReasonEnum;

class CreateInspectionStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspection_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('inspection_flow_id');
            $table->tinyInteger('seq')->default(1);
            $table->tinyInteger('transfer_type')->default(TransferTypeEnum::IN);
            $table->string('transfer_reason', 30)->default(TransferReasonEnum::DELIVER_CUSTOMER);
            $table->uuid('inspection_form_id');
            $table->uuid('inspection_department_id')->nullable();
            $table->boolean('is_need_images')->default(false);
            $table->boolean('is_need_inspector_sign')->default(false);

            $table->foreign('inspection_department_id')->references('id')->on('user_departments')->cascadeOnDelete();
            $table->foreign('inspection_flow_id')->references('id')->on('inspection_flows')->cascadeOnDelete();
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
        Schema::dropIfExists('inspection_steps');
    }
}
