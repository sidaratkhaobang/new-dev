<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code', 10)->nullable();
            $table->uuid('car_category_type_id')->nullable();
            $table->uuid('car_group_id')->nullable();
            $table->tinyInteger('reserve_small_size')->default(1);
            $table->tinyInteger('reserve_big_size')->default(1);

            $table->status();
            $table->userFields();

            $table->refId();

            $table->foreign('car_category_type_id')->references('id')->on('car_category_types')->nullOnDelete();
            $table->foreign('car_group_id')->references('id')->on('car_groups')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_categories');
    }
}
