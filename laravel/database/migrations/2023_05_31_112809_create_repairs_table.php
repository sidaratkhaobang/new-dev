<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repairs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no')->nullable();
            $table->uuid('car_id');
            $table->nullableUuidMorphs('job');
            $table->string('repair_type')->nullable();
            $table->dateTime('repair_date')->nullable();
            $table->string('contact')->nullable();
            $table->string('tel', 20)->nullable();
            $table->decimal('mileage', 10, 2)->default(0)->nullable();
            $table->string('place')->nullable();
            $table->text('remark')->nullable();
            $table->boolean('in_center')->default(false);
            $table->date('in_center_date')->nullable();
            $table->boolean('is_driver_in_center')->nullable();
            $table->boolean('out_center')->default(false);
            $table->date('out_center_date')->nullable();
            $table->boolean('is_driver_out_center')->nullable();
            $table->boolean('is_replacement')->nullable();
            $table->string('replacement_type')->nullable();
            $table->date('replacement_date')->nullable();
            $table->string('replacement_place')->nullable();
            $table->string('informer_type')->nullable();
            $table->uuid('informer')->nullable();
            $table->string('open_by')->nullable();
            $table->string('status')->nullable();
            $table->userFields();

            $table->foreign('car_id')->references('id')->on('cars')->cascadeOnDelete();
            $table->foreign('informer')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('repairs');
    }
}
