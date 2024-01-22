<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsuranceCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insurers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code',255)->nullable();
            $table->string('insurance_name_th',255)->nullable();
            $table->string('insurance_name_en',255)->nullable();
            $table->string('insurance_tel',255)->nullable();
            $table->string('insurance_web',255)->nullable();
            $table->string('insurance_fax',255)->nullable();
            $table->string('insurance_email',255)->nullable();
            $table->text('insurance_address')->nullable();
            $table->string('contact_name',255)->nullable();
            $table->string('contact_tel',255)->nullable();
            $table->string('contact_email',255)->nullable();
            $table->text('remark')->nullable();
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
        Schema::table('insurers', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['deleted_by']);
        });
        Schema::dropIfExists('insurers');
    }
}
