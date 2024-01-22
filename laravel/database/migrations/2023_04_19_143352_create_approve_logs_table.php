<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApproveLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approve_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('approve_id');
            $table->uuid('approve_line_id')->nullable();
            $table->integer('seq')->nullable();
            $table->uuid('user_id')->nullable();
            $table->date('approved_date')->nullable();
            $table->string('status')->nullable();
            $table->string('reason')->nullable();
            $table->userFields();

            $table->foreign('approve_id')->references('id')->on('approves')->cascadeOnDelete();
            $table->foreign('approve_line_id')->references('id')->on('approve_lines')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approve_logs');
    }
}
