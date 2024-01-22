<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignYellowTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sign_yellow_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('รหัสอ้างอิงใบสั่งป้ายเหลือง');
            $table->date('receive_document_date')->nullable()->comment('วันที่ได้รับข้อมูลใบสั่ง');
            $table->uuid('car_id')->nullable()->comment('รถ');
            $table->text('remark')->nullable();
            $table->string('status')->nullable()->comment('สถานะ');
            $table->userFields();

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
        Schema::dropIfExists('sign_yellow_tickets');
    }
}
