<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangeRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('change_registrations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no', 50)->nullable()->comment('เลขที่ใบงาน');
            $table->uuid('hire_purchase_id')->nullable()->comment('รหัสอ้างอิงสัญญาเช่าซื้อ');
            $table->string('type', 50)->nullable()->comment('ประเภท');
            $table->uuid('car_id')->nullable()->comment('รถ');
            $table->date('receive_case_date')->nullable()->comment('วันที่รับเรื่อง');
            $table->text('remark')->nullable();
            $table->string('requester_type_contact', 50)->nullable()->comment('ประเภทผู้ขอ ผู้ติดต่อ');
            $table->string('name_contact', 50)->nullable()->comment('ลูกค้า/ผู้ติดต่อ');
            $table->string('tel_contact', 50)->nullable()->comment('เบอร์โทรศัพท์มือถือ ผู้ติดต่อ');
            $table->string('email_contact', 50)->nullable()->comment('อีเมลผู้ติดต่อ');
            $table->text('address_contact')->nullable()->comment('ที่อยู่ผู้ติดต่อ');
            $table->boolean('is_tax_sign')->nullable()->comment('ต้องการป้ายภาษี');
            $table->tinyInteger('amount_tax_sign')->nullable()->comment('จำนวนป้ายภาษีที่ต้องการ');
            $table->boolean('is_license_plate')->nullable()->comment('ต้องการป้ายเหล็ก');
            $table->boolean('is_registration_book')->nullable()->comment('การได้รับเล่มทะเบียน');
            $table->string('link', 200)->nullable()->comment('ลิงก์ไฟล์แนบสำเนาทะเบียนรถ');
            $table->date('request_registration_book_date')->nullable()->comment('วันที่ขอเล่มทะเบียน');
            $table->date('receive_registration_book_date')->nullable()->comment('วันที่ได้รับเล่มทะเบียน');
            $table->tinyInteger('amount_license_plate')->nullable()->comment('จำนวนป้ายที่เหล็กที่ต้องการ');
            $table->string('requester_type_recipient', 50)->nullable()->comment('ประเภทผู้ขอ ผู้รับป้าย');
            $table->string('name_recipient', 100)->nullable()->comment('ลูกค้า/ผู้ติดต่อ ผู้รับป้าย');
            $table->string('tel_recipient', 20)->nullable()->comment('เบอร์โทรมือถือ ผู้รับป้าย');
            $table->string('email_recipient', 20)->nullable()->comment('อีเมล ผู้รับป้าย');
            $table->text('address_recipient')->nullable()->comment('ที่อยู่ผู้รับป้าย');
            $table->string('name_receipt', 100)->nullable()->comment('ชื่อ-นามสกุล');
            $table->string('tax_no_receipt', 20)->nullable()->comment('หมายเลขประจำผู้เสียภาษี');
            $table->string('tel_receipt', 20)->nullable()->comment('เบอร์โทรมือถือ');
            $table->string('email_receipt', 50)->nullable()->comment('อีเมล');
            $table->text('address_receipt')->nullable()->comment('ที่อยู่');
            $table->string('detail_change', 100)->nullable()->comment('สิ่งที่ต้องการเปลี่ยน');
            $table->boolean('is_car_alternate_tls')->nullable()->comment('รถที่นำมาสลับเป็นรถในระบบของ TLS ใช่หรือไม่');
            $table->string('car_owner_type', 50)->nullable()->comment('รถในนาม');
            $table->string('car_swap', 20)->nullable()->comment('ทะเบียนที่ต้องการสลับ');
            $table->uuid('car_class')->nullable()->comment('รุ่นรถ');
            $table->string('engine_no', 50)->nullable()->comment('หมายเลขเครื่องยนต์');
            $table->string('chassis_no', 50)->nullable()->comment('หมายเลขตัวถัง');
            $table->date('payment_date')->nullable()->comment('วันที่ชำระเงิน');
            $table->string('delivery_channel', 20)->nullable()->comment('ช่องทางการจัดส่ง');
            $table->string('ems', 50)->nullable()->comment('เลข EMS');
            $table->date('delivery_date')->nullable()->comment('วันที่จัดส่ง');
            $table->string('license_plate_new_main', 20)->nullable()->comment('ทะเบียนใหม่รถหลัก');
            $table->string('license_plate_new_swap', 20)->nullable()->comment('ทะเบียนใหม่รถที่นำมาสลับ');
            $table->date('registered_date')->nullable()->comment('วันที่จดทะเบียนเสร็จ');
            $table->boolean('is_power_attorney_tls')->nullable()->comment('ต้องใช้หนังสือมอบอำนาจจาก TLS');
            $table->boolean('request_power_attorney_tls_date')->nullable()->comment('วันที่ขอหนังสือมอบอำนาจ  (TLS)');
            $table->date('receive_power_attorney_tls_date')->nullable()->comment('วันที่ได้รับหนังสือมอบอำนาจ (Leasing)');
            $table->boolean('is_power_attorney')->nullable()->comment('ต้องใช้หนังสือมอบอำนาจจาก Leasing');
            $table->date('request_power_attorney_date')->nullable()->comment('วันที่ขอหนังสือมอบอำนาจจาก (Leasing)');
            $table->date('receive_power_attorney_date')->nullable()->comment('วันที่ได้รับหนังสือมอบอำนาจจาก (Leasing)');
            $table->date('processing_date')->nullable()->comment('วันที่ส่งดำเนินการที่กรมขนส่ง');
            $table->date('completion_date')->nullable()->comment('วันดำเนินการเสร็จ');
            $table->date('return_registration_book_date')->nullable()->comment('วันที่ส่งเล่มทะเบียนคืน');
            $table->string('memo_no', 20)->nullable()->comment('เลขที่ MEMO เบิกเงิน advance');
            $table->decimal('receipt_avance', 10, 2)->nullable()->comment('ค่าใบเสร็จ Avance');
            $table->decimal('operation_fee_avance', 10, 2)->nullable()->comment('ค่าดำเนินการ Avance');
            $table->date('receipt_date')->nullable()->comment('วันที่ออกใบเสร็จ');
            $table->string('receipt_no', 50)->nullable()->comment('เลขที่ใบเสร็จ');
            $table->decimal('receipt_fee', 10, 2)->nullable()->comment('ค่าใบเสร็จ');
            $table->decimal('service_fee', 10, 2)->nullable()->comment('ค่าบริการ');
            $table->string('status')->nullable()->comment('สถานะ');
            $table->userFields();

            $table->foreign('hire_purchase_id')->references('id')->on('hire_purchases')->cascadeOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->cascadeOnDelete();
            $table->foreign('car_class')->references('id')->on('car_classes')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('change_registrations');
    }
}
