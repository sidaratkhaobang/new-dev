<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCancelVmiCmiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cancel_vmi_cmis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('ref');
            $table->string('worksheet_no')->nullable();
            $table->uuid('lot_id')->nullable();
            $table->string('reason')->nullable();
            $table->text('remark')->nullable();
            $table->datetime('request_cancel_date')->nullable();
            $table->datetime('actual_cancel_date')->nullable();
            $table->decimal('refund', 10, 2)->nullable()->default(0);
            $table->decimal('refund_stamp', 10, 2)->nullable()->default(0);
            $table->decimal('refund_vat', 10, 2)->nullable()->default(0);
            $table->string('credit_note')->nullable();
            $table->datetime('credit_note_date')->nullable();
            $table->datetime('check_date')->nullable();
            $table->datetime('send_account_date')->nullable();
            $table->string('status', 100)->nullable();
            $table->userFields();

            $table->foreign('lot_id')->references('id')->on('insurance_lots')->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cancel_vmi_cmis');
    }
}
