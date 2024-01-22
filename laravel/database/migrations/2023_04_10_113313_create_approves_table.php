<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApprovesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approves', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('config_approve_id');
            $table->nullableUuidMorphs('job');
            $table->string('status_state')->nullable();
            $table->string('status')->nullable();
            $table->userFields();

            $table->foreign('config_approve_id')->references('id')->on('config_approves')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approves');
    }
}

