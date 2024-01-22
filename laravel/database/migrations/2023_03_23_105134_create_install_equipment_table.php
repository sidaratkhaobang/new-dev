<?php

use App\Enums\InstallEquipmentStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstallEquipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('install_equipments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no', 20)->nullable();
            $table->uuid('car_id');
            $table->uuid('po_id')->nullable();
            $table->integer('install_day_amount')->default(0);
            $table->string('status', 20)->default(InstallEquipmentStatusEnum::WAITING);
            $table->text('remark')->nullable();  
            $table->uuid('supplier_id');
            $table->boolean('is_in_house_install')->default(false);
            $table->boolean('cancel_reason')->default(false);
            $table->refId();
            $table->userFields();

            $table->foreign('car_id')->references('id')->on('cars')->cascadeOnDelete();
            $table->foreign('po_id')->references('id')->on('purchase_orders')->nullOnDelete();
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
        Schema::dropIfExists('install_equipments');
    }
}
