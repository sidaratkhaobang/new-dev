<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slides', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no', 100)->nullable();
            $table->nullableUuidMorphs('job');
            $table->string('type', 50)->nullable();
            $table->uuid('car_id')->nullable();
            $table->datetime('origin_date')->nullable();
            $table->string('origin_place', 100)->nullable();
            $table->string('origin_contact', 100)->nullable();
            $table->string('origin_tel', 20)->nullable();
            $table->datetime('destination_date')->nullable();
            $table->string('destination_place', 100)->nullable();
            $table->string('destination_contact', 100)->nullable();
            $table->string('destination_tel', 20)->nullable();
            $table->text('remark')->nullable();
            $table->string('status', 50)->nullable();
            $table->userFields();

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
        Schema::dropIfExists('slides');
    }
}
