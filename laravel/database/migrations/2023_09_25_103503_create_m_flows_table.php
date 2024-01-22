<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMFlowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_flows', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->nullableUuidMorphs('job');
            $table->text('metadata')->nullable();
            $table->uuid('car_id');
            $table->datetime('offense_date')->nullable();
            $table->date('document_date')->nullable();
            $table->uuid('m_flow_station_id')->nullable();
            $table->decimal('fee', 10, 2)->nullable();
            $table->decimal('fine', 10, 2)->nullable();
            $table->date('notification_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('status')->nullable();
            $table->userFields();

            $table->foreign('car_id')->references('id')->on('cars')->cascadeOnDelete();
            $table->foreign('m_flow_station_id')->references('id')->on('m_flow_stations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_flows');
    }
}