<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestPremiumCarclassLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_premium_carclass_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('request_premium_id', 36);
            $table->char('lt_rental_line_id', 36);
            $table->string('registration_type')->nullable();
            $table->decimal('sum_insured_car', 10, 2)->nullable();
            $table->decimal('sum_insured_accessories', 10, 2)->nullable();
            $table->decimal('sum_insured', 10, 2)->nullable();
            $table->char('insurer_id', 36)->nullable();
            $table->char('insurance_package_id', 36)->nullable();
            $table->decimal('car_purchase_price', 10, 2)->nullable();
            $table->decimal('tpbi_person', 10, 2)->nullable();
            $table->decimal('tpbi_aggregate', 10, 2)->nullable();
            $table->decimal('tppd_aggregate', 10, 2)->nullable();
            $table->decimal('deductible', 10, 2)->nullable();
            $table->string('own_damage')->nullable();
            $table->string('fire_and_theft')->nullable();
            $table->decimal('deductible_car', 10, 2)->nullable();
            $table->decimal('pa_driver', 10, 2)->nullable();
            $table->decimal('pa_passenger', 10, 2)->nullable();
            $table->decimal('medical_exp', 10, 2)->nullable();
            $table->decimal('bailbond', 10, 2)->nullable();
            $table->userFields();
//    FK
            $table->foreign('request_premium_id')->references('id')->on('request_premiums')->cascadeOnDelete();
            $table->foreign('lt_rental_line_id')->references('id')->on('lt_rental_lines')->cascadeOnDelete();
            $table->foreign('insurer_id')->references('id')->on('insurers')->cascadeOnDelete();
            $table->foreign('insurance_package_id')->references('id')->on('insurance_package')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_premium_carclass_lines', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['deleted_by']);
            $table->dropForeign(['request_premium_id']);
            $table->dropForeign(['lt_rental_line_id']);
            $table->dropForeign(['insurer_id']);
            $table->dropForeign(['insurance_package_id']);
        });
        Schema::dropIfExists('request_premium_carclass_lines');
    }
}
