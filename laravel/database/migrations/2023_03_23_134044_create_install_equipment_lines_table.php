<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstallEquipmentLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('install_equipment_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('install_equipment_id');
            $table->uuid('accessory_id');
            $table->integer('amount')->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->text('remark')->nullable();
            $table->date('install_date')->nullable();

            $table->foreign('install_equipment_id')->references('id')->on('install_equipments')->cascadeOnDelete();
            $table->foreign('accessory_id')->references('id')->on('accessories')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('install_equipment_lines');
    }
}
