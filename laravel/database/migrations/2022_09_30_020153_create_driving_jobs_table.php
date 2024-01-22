<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\DrivingJobStatusEnum;

class CreateDrivingJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driving_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no', 20)->nullable();
            $table->nullableUuidMorphs('job');

            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->dateTime('actual_start_date')->nullable();
            $table->dateTime('actual_end_date')->nullable();
            $table->boolean('arrived_office')->default(false);

            $table->decimal('est_distance')->nullable()->default(0);
            $table->decimal('income')->nullable()->default(0);

            $table->uuid('driver_id')->nullable();
            $table->boolean('is_confirm_wage')->default(false);

            $table->string('status', 20)->default(DrivingJobStatusEnum::PENDING);

            $table->userFields();

            $table->foreign('driver_id')->references('id')->on('drivers')->nullOnDelete();
        });

        Schema::table('driving_jobs', function (Blueprint $table) {
            $table->uuid('parent_id')->nullable()->after('status');

            $table->foreign('parent_id')->references('id')->on('driving_jobs')->nullOnDelete();
        });

        Schema::table('car_park_transfers', function (Blueprint $table) {
            $table->uuid('driving_job_id')->nullable()->after('car_park_id');

            $table->foreign('driving_job_id')->references('id')->on('driving_jobs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driving_jobs');
    }
}
