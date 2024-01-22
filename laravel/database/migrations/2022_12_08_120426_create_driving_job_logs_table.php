<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrivingJobLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driving_job_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('driving_job_id')->nullable();
            $table->uuid('rental_id')->nullable();
            $table->uuid('car_id')->nullable();

            $table->foreign('driving_job_id')->references('id')->on('driving_jobs')->cascadeOnDelete();
            $table->foreign('rental_id')->references('id')->on('rentals')->cascadeOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driving_job_logs');
    }
}
