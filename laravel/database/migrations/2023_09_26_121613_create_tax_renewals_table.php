<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxRenewalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_renewals', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('รหัสอ้างอิงงานต่ออายุภาษีรถยนต์');
            $table->uuid('car_id')->nullable()->comment('รถ');
            $table->string('worksheet_no', 50)->nullable()->comment('เลขที่ใบงาน');
            $table->date('request_cmi_date')->nullable()->comment('วันที่ส่งขอพรบ.');
            $table->date('receive_cmi_date')->nullable()->comment('วันที่ได้รับ พรบ.');
            $table->boolean('is_check_inspection')->nullable()->comment('ต้องตรวจ ตรอ. หรือไม่');
            $table->date('receive_inspection_date')->nullable()->comment('วันที่ได้รับเอกสารตรวจ ตรอ.');
            $table->boolean('is_check_lpg_ngv')->nullable()->comment('ต้องตรวจสภาพ (LPG/NGV) หรือไม่');
            $table->boolean('is_check_blue_sign')->nullable()->comment('ต้องตรวจสภาพ (ป้ายฟ้า) หรือไม่');
            $table->boolean('is_check_yellow_sign')->nullable()->comment('ต้องตรวจสภาพ (ป้ายเหลือง) หรือไม่');
            $table->boolean('is_check_green_sign')->nullable()->comment('ต้องตรวจสภาพ (ป้ายเขียวบริการ) หรือไม่');
            $table->boolean('is_receive_documents')->nullable()->comment('การได้รับเอกสารการตรวจครบถ้วน');
            $table->date('request_registration_book_date')->nullable()->comment('วันที่ขอเล่มทะเบียน');
            $table->date('receive_registration_book_date')->nullable()->comment('วันที่ได้รับเล่มทะเบียน');
            $table->date('tax_forwarding_date')->nullable()->comment('วันที่ส่งต่อภาษี');
            $table->string('provider', 50)->nullable()->comment('ผู้รับต่อภาษี');
            $table->date('receive_tax_label_date')->nullable()->comment('วันที่ได้รับป้ายภาษี');
            $table->date('car_tax_exp_date')->nullable()->comment('วันหมดอายุภาษีรถยนต์');
            $table->date('send_tax_date')->nullable()->comment('วันที่ส่งให้ลูกค้า');
            $table->string('link', 200)->nullable()->comment('ลิงก์ไฟล์แนบสำเนาทะเบียนรถ');
            $table->string('ems', 50)->nullable()->comment('เลข EMS');
            $table->string('recipient_name', 100)->nullable()->comment('ชื่อผู้รับ');
            $table->string('tel', 20)->nullable()->comment('เบอร์โทร');
            $table->text('contact')->nullable()->comment('สถานที่ติดต่อ/จัดส่งเอกสาร');
            $table->date('return_registration_book_date')->nullable()->comment('วันที่ส่งคืนเล่มทะเบียน');
            $table->uuid('returner')->nullable()->comment('ผู้คืน');
            $table->text('remark')->nullable();
            $table->string('memo_no', 20)->nullable()->comment('เลขที่ Memo เบิกเงิน');
            $table->decimal('receipt_avance', 10, 2)->nullable()->comment('ค่าใบเสร็จ Avance');
            $table->decimal('operation_fee_avance', 10, 2)->nullable()->comment('ค่าดำเนินการ Avance');
            $table->date('receipt_date')->nullable()->comment('วันที่ออกใบเสร็จ');
            $table->string('receipt_no', 50)->nullable()->comment('เลขที่ใบเสร็จ');
            $table->decimal('tax', 10, 2)->nullable()->comment('ค่าภาษี');
            $table->decimal('service_fee', 10, 2)->nullable()->comment('ค่าบริการ');
            $table->string('status')->nullable()->comment('สถานะ');
            $table->userFields();

            $table->foreign('car_id')->references('id')->on('cars')->cascadeOnDelete();
            $table->foreign('returner')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tax_renewals');
    }
}
