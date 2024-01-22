<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckDistancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_distances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('car_class_id');
            $table->decimal('distance', 10, 2)->default(0);
            $table->integer('month')->nullable();
            $table->integer('amount')->nullable();
            $table->userFields();

            $table->foreign('car_class_id')->references('id')->on('car_classes')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('check_distances');
    }
}
