<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIeInspectionsInstallEquipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ie_inspections_install_equipments', function (Blueprint $table) {
            $table->uuid('ie_inspection_id');
            $table->uuid('install_equipment_id');

            $table->primary(['ie_inspection_id', 'install_equipment_id'], 'ie_inspection_pk');

            $table->foreign('ie_inspection_id')->references('id')->on('install_equipment_inspections')->cascadeOnDelete();
            $table->foreign('install_equipment_id')->references('id')->on('install_equipments')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ie_inspections_install_equipments');
    }
}
