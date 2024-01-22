<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->uuid('location_group_id')->nullable();

            $table->unsignedBigInteger('province_id')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();

            $table->boolean('can_transportation_car')->default(false);
            $table->boolean('can_transportation_boat')->default(false);

            $table->status();
            $table->userFields();

            $table->foreign('location_group_id')->references('id')->on('location_groups')->nullOnDelete();
            $table->foreign('province_id')->references('id')->on('provinces')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
}
