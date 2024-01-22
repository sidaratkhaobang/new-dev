<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignYellowTicketLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sign_yellow_ticket_lines', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('รหัสอ้างอิงรายการคดี');
            $table->uuid('sign_yellow_ticket_id')->nullable()->comment('รหัสอ้างอิงใบสั่ง');
            $table->date('incident_date')->nullable()->comment('วันที่เกิดเหตุ');
            $table->string('case',200)->nullable()->comment('คดีที่เกิดเหตุ');
            $table->unsignedBigInteger('location_id')->nullable()->comment('สถานที่ที่เกิดเหตุ');
            $table->decimal('amount', 10, 2)->nullable()->comment('จำนวนเงิน');
            $table->string('driver_name',100)->nullable()->comment('ชื่อผู้ขับ');
            $table->string('tel',20)->nullable()->comment('เบอร์โทร');
            $table->boolean('is_mistake')->nullable()->comment('กระทำความผิดจริงหรือไม่');
            $table->string('institution',50)->nullable()->comment('หน่วยงานที่รับผิดชอบ');
            $table->date('notification_date')->nullable()->comment('วันที่แจ้งข้อมูลดำเนินการค่าปรับ');
            $table->string('receipt_no',50)->nullable()->comment('เลขที่ใบเสร็จ');
            $table->date('payment_date')->nullable()->comment('วันทีเงิน');
            $table->boolean('is_payment_fine')->nullable()->comment('การชำระค่าปรับ');
            $table->date('payment_fine_date')->nullable()->comment('วันที่ชำระ');

            $table->foreign('sign_yellow_ticket_id')->references('id')->on('sign_yellow_tickets')->cascadeOnDelete();
            $table->foreign('location_id')->references('id')->on('provinces')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sign_yellow_ticket_lines');
    }
}
