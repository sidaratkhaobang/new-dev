<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrivingJobCheckinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driving_job_checkins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('driving_job_id');
            $table->uuid('location_id')->nullable();
            $table->string('location_name', 200)->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->dateTime('arrived_at')->nullable();
            $table->dateTime('departured_at')->nullable();

            $table->foreign('driving_job_id')->references('id')->on('driving_jobs')->cascadeOnDelete();
            $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driving_job_checkins');
    }
}
