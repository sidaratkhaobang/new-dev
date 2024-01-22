<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrepareFinancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prepare_finances', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('รหัสอ้างอิงจัดเตรียมไฟแนนส์');
            $table->uuid('lot_id')->nullable()->comment('เลข Lot');
            $table->date('creation_date')->comment('วันที่จัดทำ');
            $table->date('billing_date')->comment('วันที่วางบิล');
            $table->date('payment_date')->comment('วันที่จ่ายเงิน');
            $table->string('contact',100)->comment('ผู้ติดต่อ');
            $table->string('tel',20)->comment('เบอร์โทร');
            $table->string('status',50)->comment('สถานะ');
            $table->text('remark')->comment('หมายเหตุ');
            $table->foreign('lot_id')->references('id')->on('insurance_lots')->cascadeOnDelete();
            $table->userFields();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prepare_finances', function (Blueprint $table) {
            $table->dropForeign(['lot_id']);
        });
        Schema::dropIfExists('prepare_finances');
    }
}
