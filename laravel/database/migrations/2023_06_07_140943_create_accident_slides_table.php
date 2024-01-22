<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccidentSlidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accident_slides', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id')->nullable();
            $table->string('job_type')->nullable();
            $table->uuid('job_id')->nullable();
            $table->string('slide_driver')->nullable();
            $table->datetime('slide_date')->nullable();
            $table->decimal('slide_price',10,2)->nullable();
            $table->string('slide_from')->nullable();
            $table->string('slide_to')->nullable();
            $table->string('slide_tel')->nullable();
            $table->userFields();

            $table->foreign('accident_id')->references('id')->on('accidents')->cascadeOnDelete();    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accident_slides');
    }
}
