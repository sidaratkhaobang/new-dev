<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code', 10)->nullable();
            $table->uuid('car_brand_id')->nullable();
            $table->uuid('car_category_id')->nullable();
            $table->uuid('car_group_id')->nullable();

            $table->status();
            $table->userFields();

            $table->refId();

            $table->foreign('car_brand_id')->references('id')->on('car_brands')->nullOnDelete();
            $table->foreign('car_category_id')->references('id')->on('car_categories')->nullOnDelete();
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
        Schema::dropIfExists('car_types');
    }
}
