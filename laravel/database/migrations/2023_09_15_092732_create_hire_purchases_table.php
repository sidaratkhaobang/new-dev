<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHirePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hire_purchases', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('รหัสอ้างอิงสัญญาเช่าซื้อรถ');
            $table->uuid('car_id')->comment('รถ');
            $table->uuid('lot_id')->nullable()->comment('เลข Lot');
            $table->uuid('po_id')->nullable()->comment('เลขที่ใบสั่งซื้อ');
            $table->string('contract_no', 50)->nullable()->comment('เลขที่สัญญา');
            $table->date('finance_date')->nullable()->comment('วันที่จัดไฟแนนซ์');
            $table->date('number_installments')->nullable()->comment('จำนวนงวดที่ผ่อนชำระ');
            $table->date('contract_start_date')->nullable()->comment('วันที่เริ่มต้นสัญญา');
            $table->date('contract_end_date')->nullable()->comment('วันที่ชำระงวดสุดท้าย / สิ้นสุดสัญญา');
            $table->date('first_payment_date')->nullable()->comment('วันที่ชำระงวดแรก');
            $table->decimal('amount_installments', 12, 2)->nullable()->comment('จำนวนผ่อนชำระต่องวด (บาท)');
            $table->string('pay_installments', 2)->nullable()->comment('ชำระค่างวดทุกวันที่');
            $table->boolean('payment')->nullable()->comment('การชำระเงิน');
            $table->string('interest_rate_percent', 20)->nullable()->comment('อัตราดอกเบี้ย (%)');
            $table->boolean('interest_rate')->nullable()->comment('อัตราดอกเบี้ย');
            $table->string('down_payment_percent', 20)->nullable()->comment('วงเงินดาวน์ (%)');
            $table->string('rv_percent', 20)->nullable()->comment('RV (%)');
            $table->text('remark')->nullable()->comment('หมายเหตุ');
            $table->string('account_closing_date', 20)->nullable()->comment('วันที่ปิดบัญชี');
            $table->date('actual_last_payment_date')->nullable()->comment('วันที่จ่ายเงินงวดสุดท้ายจริง');
            $table->date('finance_receipt_date')->nullable()->comment('วันที่ไฟแนนส์ออกใบเสร็จ');
            $table->string('status', 50)->nullable()->comment('สถานะ');
            $table->userFields();
            $table->foreign('car_id')->references('id')->on('cars')->cascadeOnDelete();
            $table->foreign('lot_id')->references('id')->on('insurance_lots')->nullOnDelete();
            $table->foreign('po_id')->references('id')->on('purchase_orders')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hire_purchases', function (Blueprint $table) {
            $table->dropForeign(['car_id']);
            $table->dropForeign(['lot_id']);
            $table->dropForeign(['po_id']);
        });
        Schema::dropIfExists('hire_purchases');
    }
}
