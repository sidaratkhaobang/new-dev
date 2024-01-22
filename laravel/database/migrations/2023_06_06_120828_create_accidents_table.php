<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accidents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no')->nullable();
            $table->string('accident_type')->nullable();
            $table->string('job_type')->nullable();
            $table->uuid('job_id')->nullable();
            $table->string('claim_type')->nullable();
            $table->string('claim_by')->nullable();
            $table->datetime('report_date')->nullable();
            $table->string('reporter')->nullable();
            $table->string('report_tel')->nullable();
            $table->string('report_no')->nullable();
            $table->uuid('car_id');
            $table->datetime('accident_date')->nullable();
            $table->string('driver')->nullable();
            $table->string('main_area')->nullable();
            $table->string('case')->nullable();
            $table->text('accident_description')->nullable();
            $table->text('accident_place')->nullable();
            $table->text('current_place')->nullable();
            $table->string('region')->nullable();
            $table->unsignedBigInteger('province')->nullable();
            $table->unsignedBigInteger('district')->nullable();
            $table->unsignedBigInteger('subdistrict')->nullable();
            $table->boolean('is_parties')->nullable();
            $table->text('wrong_type')->nullable();
            $table->boolean('is_wounded')->nullable();
            $table->integer('amount_wounded_driver')->nullable();
            $table->boolean('is_deceased')->nullable();
            $table->integer('amount_wounded_parties')->nullable();
            $table->integer('amount_deceased_driver')->nullable();
            $table->integer('amount_deceased_parties')->nullable();
            $table->boolean('is_repair')->nullable();
            $table->uuid('cradle')->nullable();
            $table->text('remark')->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_replacement')->nullable();
            $table->string('replacement_type')->nullable();
            $table->datetime('replacement_expect_date')->nullable();
            $table->text('replacement_expect_place')->nullable(); 
            $table->boolean('is_driver_replacement')->nullable();
            $table->userFields();
            

            $table->foreign('car_id')->references('id')->on('cars')->cascadeOnDelete();
            $table->foreign('province')->references('id')->on('provinces')->cascadeOnDelete();
            $table->foreign('district')->references('id')->on('amphures')->cascadeOnDelete();
            $table->foreign('subdistrict')->references('id')->on('districts')->cascadeOnDelete();
            $table->foreign('cradle')->references('id')->on('cradles')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accidents');
    }
}
