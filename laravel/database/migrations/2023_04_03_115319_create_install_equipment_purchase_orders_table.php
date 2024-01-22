<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstallEquipmentPurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('install_equipment_purchase_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no', 20)->nullable();
            $table->uuid('install_equipment_id');
            $table->uuid('supplier_id');
            $table->uuid('car_id');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('vat', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->integer('amount')->default(0);
            $table->string('time_of_delivery')->nullable();
            $table->string('payment_term')->nullable();
            $table->string('contact')->nullable();
            $table->string('car_user')->nullable();
            $table->uuid('quotation_id')->nullable();
            $table->text('remark')->nullable();
            $table->string('status', 20)->nullable();
            $table->text('reject_reason')->nullable();
            $table->userFields();

            $table->foreign('install_equipment_id')->references('id')->on('install_equipments')->cascadeOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->cascadeOnDelete();
            $table->foreign('supplier_id')->references('id')->on('creditors')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('install_equipment_purchase_orders');
    }
}
