<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompulsoryMotorInsurancesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compulsory_motor_insurances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no', 20)->nullable();
            $table->string('type', 20)->nullable();
            $table->nullableUuidMorphs('job');
            $table->uuid('lot_id')->nullable();
            $table->string('lot_number', 20)->nullable();
            $table->string('registration_type')->nullable();
            $table->string('car_class_insurance_id')->nullable();
            $table->string('type_vmi')->nullable();
            $table->string('type_cmi')->nullable();
            $table->decimal('sum_insured_car', 10, 2)->nullable()->default(0);
            $table->decimal('sum_insured_accessory', 10, 2)->nullable()->default(0);
            $table->integer('year')->nullable();
            $table->uuid('insurer_id')->nullable();
            $table->uuid('beneficiary_id')->nullable();
            $table->text('remark')->nullable();
            $table->uuid('car_id');
            $table->datetime('send_date')->nullable();
            $table->datetime('receive_date')->nullable();
            $table->datetime('check_date')->nullable();
            $table->string('number_bar_cmi')->nullable();
            $table->string('policy_reference_cmi')->nullable();
            $table->string('endorse_cmi')->nullable();
            $table->datetime('term_start_date')->nullable();
            $table->datetime('term_end_date')->nullable();

            $table->decimal('premium', 10, 2)->nullable()->default(0);
            $table->decimal('discount', 10, 2)->nullable()->default(0);
            $table->decimal('stamp_duty', 10, 2)->nullable()->default(0);
            $table->decimal('tax', 10, 2)->nullable()->default(0);
            $table->decimal('premium_total', 10, 2)->nullable()->default(0);
            $table->decimal('withholding_tax', 10, 2)->nullable()->default(0);
            $table->string('statement_no')->nullable();
            $table->string('tax_invoice_no')->nullable();
            $table->datetime('statement_date')->nullable();
            $table->datetime('account_submission_date')->nullable();
            $table->datetime('operated_date')->nullable();
            $table->string('status_pay_premium', 20)->nullable();
            $table->string('status', 20)->nullable();
            $table->string('status_cmi', 20)->nullable();
            $table->userFields();

            $table->foreign('lot_id')->references('id')->on('insurance_lots')->nullOnDelete();
            $table->foreign('insurer_id')->references('id')->on('insurers')->nullOnDelete();
            $table->foreign('beneficiary_id')->references('id')->on('leasings')->nullOnDelete();
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
        Schema::dropIfExists('compulsory_motor_insurances');
    }
}