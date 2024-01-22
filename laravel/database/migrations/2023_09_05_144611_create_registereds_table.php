<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegisteredsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registereds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->nullableUuidMorphs('job');
            $table->uuid('lot_id')->nullable();
            $table->uuid('car_id')->nullable();
            $table->uuid('po_id')->nullable();
            $table->string('worksheet_no', 30)->nullable();
            $table->date('document_date')->nullable();
            $table->date('send_registered_date')->nullable();
            $table->date('registered_date')->nullable();
            $table->date('receive_information_date')->nullable();
            $table->date('car_tax_exp_date')->nullable();
            $table->date('receive_registered_dress_date')->nullable();
            $table->date('receive_document_sale_date')->nullable();
            $table->date('receive_cmi')->nullable();
            $table->boolean('is_roof_receipt')->nullable();
            $table->date('receive_roof_receipt_date')->nullable();
            $table->boolean('is_lock_license_plate')->nullable();
            $table->string('type_lock_license_plate', 50)->nullable();
            $table->string('detail_lock_license_plate', 100)->nullable();
            $table->text('description')->nullable();
            $table->uuid('car_characteristic_transport_id')->nullable();
            $table->string('color_registered', 50)->nullable();
            $table->string('registered_sign', 50)->nullable();
            $table->text('remark')->nullable();
            $table->string('memo_no', 20)->nullable();
            $table->decimal('receipt_avance', 10, 2)->nullable();
            $table->decimal('operation_fee_avance', 10, 2)->nullable();
            $table->string('link', 200)->nullable();
            $table->boolean('is_license_plate')->nullable();
            $table->boolean('is_registration_book')->nullable();
            $table->boolean('is_tax_sign')->nullable();
            $table->date('receipt_date')->nullable();
            $table->string('receipt_no', 50)->nullable();
            $table->decimal('tax', 10, 2)->nullable();
            $table->decimal('service_fee', 10, 2)->nullable();
            $table->string('status')->nullable();
            $table->userFields();

            $table->foreign('lot_id')->references('id')->on('insurance_lots')->nullOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->nullOnDelete();
            $table->foreign('po_id')->references('id')->on('purchase_orders')->nullOnDelete();
            $table->foreign('car_characteristic_transport_id')->references('id')->on('car_characteristic_transports')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registereds');
    }
}
