<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches_locations', function (Blueprint $table) {
            $table->uuid('branch_id');
            $table->uuid('location_id');
            $table->uuid('location_group_id')->nullable();

            $table->boolean('can_origin')->default(true);
            $table->boolean('can_stopover')->default(true);
            $table->boolean('can_destination')->default(true);

            $table->primary(['branch_id', 'location_id']);

            $table->foreign('branch_id')->references('id')->on('branches')->cascadeOnDelete();
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
        Schema::dropIfExists('branch_locations');
    }
}
