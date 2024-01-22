<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoluntaryMotorInsurancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('voluntary_motor_insurances');
        Schema::create('voluntary_motor_insurances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no', 20)->nullable();
            $table->string('type', 20)->nullable();
            $table->string('repair_type')->nullable();
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
            $table->string('insurance_type', 20)->nullable();
            $table->uuid('insurer_id')->nullable();
            $table->uuid('beneficiary_id')->nullable();
            $table->uuid('insurance_package_id')->nullable(); //เงื่อนไข
            $table->text('remark')->nullable();
            $table->uuid('car_id');
            $table->datetime('send_date')->nullable();
            $table->datetime('receive_date')->nullable();
            $table->datetime('check_date')->nullable();

            $table->string('policy_reference_child_vmi')->nullable();
            $table->string('policy_reference_vmi')->nullable();
            $table->string('endorse_vmi')->nullable();
            
            $table->datetime('term_start_date')->nullable();
            $table->datetime('term_end_date')->nullable();

            $table->decimal('pa', 10, 2)->nullable()->default(0);
            $table->decimal('pa_and_bb', 10, 2)->nullable()->default(0);
            $table->decimal('pa_per_endorsement', 10, 2)->nullable()->default(0);
            $table->decimal('pa_total_premium', 10, 2)->nullable()->default(0);
            $table->decimal('id_deductible', 10, 2)->nullable()->default(0);
            $table->decimal('discount_deductible', 10, 2)->nullable()->default(0);
            $table->decimal('fit_discount', 10, 2)->nullable()->default(0);
            $table->decimal('fleet_discount', 10, 2)->nullable()->default(0);
            $table->decimal('ncb', 10, 2)->nullable()->default(0);
            $table->decimal('good_vmi', 10, 2)->nullable()->default(0);
            $table->decimal('bad_vmi', 10, 2)->nullable()->default(0);
            $table->decimal('other_discount_percent', 10, 2)->nullable()->default(0);
            $table->decimal('other_discount', 10, 2)->nullable()->default(0);
            $table->decimal('gps_discount', 10, 2)->nullable()->default(0);
            $table->decimal('total_discount', 10, 2)->nullable()->default(0);
            $table->decimal('net_discount', 10, 2)->nullable()->default(0);
            $table->decimal('cct', 10, 2)->nullable()->default(0);
            $table->decimal('gross', 10, 2)->nullable()->default(0);

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
            $table->string('status_vmi', 20)->nullable();

            $table->decimal('tpbi_person', 10, 2)->nullable();
            $table->decimal('tpbi_aggregate', 10, 2)->nullable();
            $table->decimal('tppd_aggregate', 10, 2)->nullable();
            $table->decimal('deductible', 10, 2)->nullable();

            $table->decimal('own_damage', 10, 2)->nullable();
            $table->decimal('fire_and_theft', 10, 2)->nullable();
            $table->decimal('deductible_car', 10, 2)->nullable();
            $table->decimal('pa_driver', 10, 2)->nullable();
            $table->decimal('pa_passenger', 10, 2)->nullable();
            $table->decimal('medical_exp', 10, 2)->nullable();
            $table->decimal('bail_bond', 10, 2)->nullable();
            $table->userFields();

            $table->foreign('lot_id')->references('id')->on('insurance_lots')->nullOnDelete();
            $table->foreign('insurer_id')->references('id')->on('insurers')->nullOnDelete();
            $table->foreign('beneficiary_id')->references('id')->on('leasings')->nullOnDelete();
            $table->foreign('insurance_package_id')->references('id')->on('insurance_package')->cascadeOnDelete();
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
        Schema::dropIfExists('voluntary_motor_insurances');
    }
}
