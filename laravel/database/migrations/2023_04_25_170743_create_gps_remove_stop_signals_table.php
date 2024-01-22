<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGpsRemoveStopSignalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gps_remove_stop_signals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no', 100)->nullable();
            $table->string('job_type')->nullable();
            $table->uuid('car_id')->nullable();
            $table->boolean('is_check_gps')->nullable();
            $table->date('inform_date')->nullable();
            $table->date('remove_date')->nullable();
            $table->string('remove_status')->nullable();
            $table->date('stop_date')->nullable();
            $table->string('stop_status')->nullable();
            $table->text('remark')->nullable();
            $table->text('remove_remark')->nullable();
            $table->text('stop_remark')->nullable();
            $table->userFields();

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
        Schema::dropIfExists('gps_remove_stop_signals');
    }
}
