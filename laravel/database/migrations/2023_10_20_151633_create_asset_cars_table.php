<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssetCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_cars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('po_id')->nullable();
            $table->uuid('lot_id')->nullable();
            $table->uuid('car_id')->nullable();
            $table->uuid('hire_purchase_id')->nullable();
            $table->uuid('registered_id')->nullable();
            $table->string('status', 50)->nullable();
            $table->userFields();

            $table->foreign('po_id')->references('id')->on('purchase_orders')->nullOnDelete();
            $table->foreign('lot_id')->references('id')->on('insurance_lots')->nullOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->nullOnDelete();
            $table->foreign('hire_purchase_id')->references('id')->on('hire_purchases')->nullOnDelete();
            $table->foreign('registered_id')->references('id')->on('registereds')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asset_cars');
    }
}
