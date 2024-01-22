<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstallEquipmentPoLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('install_equipment_po_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('install_equipment_po_id');
            $table->uuid('install_equipment_line_id');
            $table->uuid('accessory_id');
            $table->integer('amount')->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('vat', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->foreign('install_equipment_po_id')->references('id')->on('install_equipment_purchase_orders')->cascadeOnDelete();
            $table->foreign('install_equipment_line_id')->references('id')->on('install_equipment_lines')->cascadeOnDelete();
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
        Schema::dropIfExists('install_equipment_po_lines');
    }
}
