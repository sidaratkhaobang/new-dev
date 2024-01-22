<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no');
            $table->string('worksheet_no_customer')->nullable();
            $table->string('contract_type')->nullable();
            $table->string('job_type')->nullable();
            $table->uuid('job_id')->nullable();
            $table->decimal('contract_value', 10, 2)->nullable();
            $table->text('remark')->nullable();
            $table->dateTime('date_document')->nullable();
            $table->dateTime('date_offer_sign')->nullable();
            $table->dateTime('date_send_contract')->nullable();
            $table->dateTime('date_return_contract')->nullable();
            $table->string('calculate_rent')->nullable();
            $table->string('start_rent')->nullable();
            $table->string('end_rent')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('contracts');
    }
}
