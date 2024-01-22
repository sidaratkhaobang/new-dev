<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetAccessoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_accessories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('asset_car_id')->nullable();
            $table->uuid('poa_id')->nullable();
            $table->uuid('lot_id')->nullable();
            $table->uuid('car_id')->nullable();
            $table->uuid('hire_purchase_id')->nullable();
            $table->userFields();

            $table->foreign('asset_car_id')->references('id')->on('asset_cars')->nullOnDelete();
            $table->foreign('poa_id')->references('id')->on('install_equipment_purchase_orders')->nullOnDelete();
            $table->foreign('lot_id')->references('id')->on('insurance_lots')->nullOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->nullOnDelete();
            $table->foreign('hire_purchase_id')->references('id')->on('hire_purchases')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asset_accessories');
    }
}
