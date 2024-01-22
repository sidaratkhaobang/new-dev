<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsurancePackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insurance_package', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255)->nullable();
            $table->string('tpbi_person')->nullable();
            $table->string('tpbi_aggregate')->nullable();
            $table->string('tppd_aggregate')->nullable();
            $table->string('pa_driver')->nullable();
            $table->string('pa_passenger')->nullable();
            $table->string('medical_exp')->nullable();
            $table->string('baibond')->nullable();
            $table->string('deductible')->nullable();

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
        Schema::table('insurance_package', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['deleted_by']);
        });
        Schema::dropIfExists('insurance_package');
    }
}
