<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverWagesJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_wages_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('driving_job_id');
            $table->uuid('driver_wage_id')->nullable();
            $table->string('driver_wage_name')->nullable();
            $table->decimal('amount')->default(0);
            $table->string('remark')->nullable();

            $table->foreign('driving_job_id')->references('id')->on('driving_jobs')->cascadeOnDelete();
            $table->foreign('driver_wage_id')->references('id')->on('driver_wages')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_wages_jobs');
    }
}
