<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsGroupsRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations_groups_relation', function (Blueprint $table) {
            $table->uuid('location_id');
            $table->uuid('location_group_id');

            $table->primary(['location_id', 'location_group_id']);

            $table->foreign('location_id')->references('id')->on('locations')->cascadeOnDelete();
            $table->foreign('location_group_id')->references('id')->on('location_groups')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations_groups_relation');
    }
}
