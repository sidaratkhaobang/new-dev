<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLitigationTrackStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('litigation_track_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('litigation_id');
            $table->date('date')->nullable();
            $table->text('description')->nullable();
            $table->string('status', 50)->nullable();
            $table->userFields();

            $table->foreign('litigation_id')->references('id')->on('litigations')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('litigation_track_statuses');
    }
}
