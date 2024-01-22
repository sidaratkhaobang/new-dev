<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingSlipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_slips', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('รหัสอ้างอิงงาน');
            $table->uuid('billing_slip_old_id')->comment('รหัสอ้างอิงงานใบรับวางบิลเก่า');
            $table->string('worksheet_no',50)->nullable()->comment('เลขที่ใบรับวางบิล');
            $table->string('center_id',36)->nullable()->comment('ศูนย์ให้บริการ');
            $table->date('billing_date')->nullable()->comment('วันที่รับวางบิล');
            $table->date('receive_money_date')->nullable()->comment('วันที่รับเงินจากบริษัท');
            $table->string('bill_recipient',100)->nullable()->comment('ผู้รับวางบิล');
            $table->text('remark')->nullable()->comment('หมายเหตุ');
            $table->status();
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
        Schema::dropIfExists('billing_slips');
    }
}
