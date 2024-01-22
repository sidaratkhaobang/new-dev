<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TransferTypeEnum;

class CreateCarParkTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_park_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no', 20)->nullable();
            $table->tinyInteger('transfer_type')->default(TransferTypeEnum::IN);
            $table->string('reason')->nullable();
            $table->date('est_transfer_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('cancel_reason')->nullable();

            $table->uuid('car_status_id')->nullable();
            $table->uuid('car_id')->nullable();
            $table->uuid('car_park_id')->nullable();
            //$table->uuid('driving_job_id')->nullable();

            $table->status();
            $table->userFields();

            $table->foreign('car_status_id')->references('id')->on('car_statuses')->nullOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->nullOnDelete();
            $table->foreign('car_park_id')->references('id')->on('car_parks')->nullOnDelete();
            //$table->foreign('driving_job_id')->references('id')->on('driving_jobs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_park_transfers');
    }
}
