<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_cars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no', 20)->nullable();
            $table->uuid('branch_id')->nullable();
            $table->uuid('car_id')->nullable();
            $table->uuid('transfer_branch_id')->nullable();
            $table->text('remark')->nullable();
            $table->boolean('is_driver')->nullable();
            $table->string('contact')->nullable();
            $table->string('tell',20)->nullable();
            $table->text('place')->nullable();
            $table->dateTime('delivery_date')->nullable();
            $table->dateTime('confirmation_date')->nullable();
            $table->uuid('confirmation_user_id')->nullable();
            $table->dateTime('pick_up_date')->nullable();
            $table->uuid('pick_up_user_id')->nullable();
            $table->text('reason')->nullable();
            $table->string('status')->nullable();
            $table->userFields();


            $table->foreign('branch_id')->references('id')->on('branches')->cascadeOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->cascadeOnDelete();
            $table->foreign('transfer_branch_id')->references('id')->on('branches')->cascadeOnDelete();
            $table->foreign('confirmation_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('pick_up_user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfer_cars');
    }
}
