<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOwnershipTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ownership_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('รหัสอ้างอิงงานโอนกรรมสิทธิ์รถยนต์');
            $table->uuid('hire_purchase_id')->nullable()->comment('รหัสอ้างอิงสัญญาเช่าซื้อ');
            $table->uuid('car_id')->nullable()->comment('รถ');
            $table->string('worksheet_no', 50)->nullable()->comment('เลขที่ใบงานโอนกรรมสิทธิ์');
            $table->date('request_transfer_kit_date')->nullable()->comment('วันที่ขอชุดโอนและเล่มทะเบียน');
            $table->date('receive_transfer_kit_date')->nullable()->comment('วันที่ได้รับชุดโอนและเล่มทะเบียน');
            $table->date('request_power_attorney_tls_date')->nullable()->comment('วันที่ขอหนังสือมอบอำนาจ (TLS)');
            $table->date('receive_power_attorney_tls_date')->nullable()->comment('วันที่ได้รับหนังสือมอบอำนาจ (TLS)');
            $table->date('find_copy_chassis_date')->nullable()->comment('วันที่หาลอกลาย');
            $table->date('transfer_date')->nullable()->comment('วันที่ส่งโอนที่กรมขนส่ง');
            $table->date('receive_registration_book_date')->nullable()->comment('วันที่รับเล่มคืน');
            $table->date('car_ownership_date')->nullable()->comment('วันที่ครอบครองรถ');
            $table->date('return_registration_book_date')->nullable()->comment('วันที่ส่งเล่มทะเบียนคืนบัญชี');
            $table->string('link', 200)->nullable()->comment('ลิงก์ไฟล์แนบสำเนาทะเบียนรถ');
            $table->text('remark')->nullable()->comment('หมายเหตุ');
            $table->string('memo_no', 20)->nullable()->comment('เลขที่ Memo เบิกเงิน');
            $table->decimal('receipt_avance', 10, 2)->nullable()->comment('ค่าใบเสร็จ Avance');
            $table->decimal('operation_fee_avance', 10, 2)->nullable()->comment('ค่าดำเนินการ Avance');
            $table->date('receipt_date')->nullable()->comment('วันที่ออกใบเสร็จ');
            $table->string('receipt_no', 50)->nullable()->comment('เลขที่ใบเสร็จ');
            $table->decimal('receipt_fee', 10, 2)->nullable()->comment('ค่าใบเสร็จ');
            $table->decimal('tax', 10, 2)->nullable()->comment('ภาษี');
            $table->decimal('service_fee', 10, 2)->nullable()->comment('ค่าบริการ');
            $table->string('status')->nullable()->comment('สถานะ');
            $table->userFields();

            $table->foreign('hire_purchase_id')->references('id')->on('hire_purchases')->cascadeOnDelete();
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
        Schema::dropIfExists('ownership_transfers');
    }
}
