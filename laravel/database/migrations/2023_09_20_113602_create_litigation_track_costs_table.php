<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLitigationTrackCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('litigation_track_costs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('litigation_id');
            $table->string('list')->nullable();
            $table->string('number')->nullable();
            $table->date('date')->nullable();
            $table->string('payment_channels', 50)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
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
        Schema::dropIfExists('litigation_track_costs');
    }
}