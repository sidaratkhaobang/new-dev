<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBorrowCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('borrow_cars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no', 20)->nullable();
            $table->uuid('branch_id')->nullable();
            $table->string('borrow_type')->nullable();
            $table->text('purpose')->nullable();
            $table->uuid('car_id')->nullable();
            $table->text('remark')->nullable();
            $table->boolean('is_driver')->nullable();
            $table->string('contact')->nullable();
            $table->string('tell',20)->nullable();
            $table->text('place')->nullable();
            $table->text('pickup_place')->nullable();
            $table->text('return_place')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->dateTime('delivery_date')->nullable();
            $table->text('reason')->nullable();
            $table->uuid('borrow_branch_id')->nullable();
            $table->string('status')->nullable();
            $table->userFields();
            


            $table->foreign('branch_id')->references('id')->on('branches')->cascadeOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->cascadeOnDelete();
            $table->foreign('borrow_branch_id')->references('id')->on('branches')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('borrow_cars');
    }
}
