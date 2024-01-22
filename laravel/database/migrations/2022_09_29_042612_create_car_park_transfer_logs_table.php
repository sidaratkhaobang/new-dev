<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TransferTypeEnum;

class CreateCarParkTransferLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_park_transfer_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('car_park_transfer_id');
            $table->tinyInteger('transfer_type')->default(TransferTypeEnum::IN);
            $table->dateTime('transfer_date')->nullable();
            $table->string('license_plate')->nullable();
            $table->string('engine_no')->nullable();
            $table->string('chassis_no')->nullable();
            $table->string('parking_slot',20)->nullable();
            $table->uuid('driver_id')->nullable();
            $table->uuid('car_park_id')->nullable();

            $table->userFields();

            $table->foreign('car_park_transfer_id')->references('id')->on('car_park_transfers')->cascadeOnDelete();
            $table->foreign('driver_id')->references('id')->on('drivers')->nullOnDelete();
            $table->foreign('car_park_id')->references('id')->on('car_parks')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_park_transfer_logs');
    }
}
