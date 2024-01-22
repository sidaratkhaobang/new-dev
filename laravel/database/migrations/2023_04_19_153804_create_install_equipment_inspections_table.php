<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstallEquipmentInspectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('install_equipment_inspections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('inspection_date')->nullable();
            $table->uuid('car_id');
            $table->string('status')->nullable();

            $table->foreign('car_id')->references('id')->on('cars')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('install_equipment_inspections');
    }
}
