<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGpsCheckSignalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gps_check_signals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('job'); // short term or others
            $table->uuid('branch_id')->nullable();
            $table->boolean('check_main_branch')->default(false)->nullable();
            $table->uuid('car_id')->nullable();
            $table->date('must_check_date')->nullable();
            $table->date('check_date')->nullable();
            $table->string('status')->nullable();
            $table->date('repair_date')->nullable();
            $table->boolean('repair_immediately')->nullable();
            $table->text('remark')->nullable();
            $table->text('remark_repair')->nullable();
            $table->date('main_branch_date')->nullable();
            $table->text('remark_main_branch')->nullable();
            $table->string('status_main_branch')->nullable();
            $table->userFields();

            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
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
        Schema::dropIfExists('gps_check_signals');
    }
}
