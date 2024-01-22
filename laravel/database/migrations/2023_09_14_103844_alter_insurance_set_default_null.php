<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInsuranceSetDefaultNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('compulsory_motor_insurances', function (Blueprint $table) {
            $table->decimal('sum_insured_car', 10, 2)->nullable()->default(null)->change();
            $table->decimal('sum_insured_accessory', 10, 2)->nullable()->default(null)->change();
           
            $table->decimal('premium', 10, 2)->nullable()->default(null)->change();
            $table->decimal('discount', 10, 2)->nullable()->default(null)->change();
            $table->decimal('stamp_duty', 10, 2)->nullable()->default(null)->change();
            $table->decimal('tax', 10, 2)->nullable()->default(null)->change();
            $table->decimal('premium_total', 10, 2)->nullable()->default(null)->change();
            $table->decimal('withholding_tax', 10, 2)->nullable()->default(null)->change();
        });

        Schema::table('voluntary_motor_insurances', function (Blueprint $table) {
            $table->decimal('sum_insured_car', 10, 2)->nullable()->default(null)->change();
            $table->decimal('sum_insured_accessory', 10, 2)->nullable()->default(null)->change();
           
            $table->decimal('pa', 10, 2)->nullable()->default(null)->change();
            $table->decimal('pa_and_bb', 10, 2)->nullable()->default(null)->change();
            $table->decimal('pa_per_endorsement', 10, 2)->nullable()->default(null)->change();
            $table->decimal('pa_total_premium', 10, 2)->nullable()->default(null)->change();
            $table->decimal('id_deductible', 10, 2)->nullable()->default(null)->change();
            $table->decimal('discount_deductible', 10, 2)->nullable()->default(null)->change();
            $table->decimal('fit_discount', 10, 2)->nullable()->default(null)->change();
            $table->decimal('fleet_discount', 10, 2)->nullable()->default(null)->change();
            $table->decimal('ncb', 10, 2)->nullable()->default(null)->change();
            $table->decimal('good_vmi', 10, 2)->nullable()->default(null)->change();
            $table->decimal('bad_vmi', 10, 2)->nullable()->default(null)->change();
            $table->decimal('other_discount_percent', 10, 2)->nullable()->default(null)->change();
            $table->decimal('other_discount', 10, 2)->nullable()->default(null)->change();
            $table->decimal('gps_discount', 10, 2)->nullable()->default(null)->change();
            $table->decimal('total_discount', 10, 2)->nullable()->default(null)->change();
            $table->decimal('net_discount', 10, 2)->nullable()->default(null)->change();
            $table->decimal('cct', 10, 2)->nullable()->default(null)->change();
            $table->decimal('gross', 10, 2)->nullable()->default(null)->change();

            $table->decimal('premium', 10, 2)->nullable()->default(null)->change();
            $table->decimal('discount', 10, 2)->nullable()->default(null)->change();
            $table->decimal('stamp_duty', 10, 2)->nullable()->default(null)->change();
            $table->decimal('tax', 10, 2)->nullable()->default(null)->change();
            $table->decimal('premium_total', 10, 2)->nullable()->default(null)->change();
            $table->decimal('withholding_tax', 10, 2)->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
