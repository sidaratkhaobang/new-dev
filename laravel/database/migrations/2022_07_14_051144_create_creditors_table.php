<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creditors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code', 20)->nullable();

            $table->unsignedBigInteger('province_id')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('tel')->nullable();
            $table->string('mobile')->nullable();
            $table->string('fax')->nullable();

            $table->string('contact_name')->nullable();
            $table->string('contact_position')->nullable();
            $table->text('contact_address')->nullable();

            $table->string('tax_no')->nullable();
            $table->integer('credit_terms')->nullable();
            $table->text('payment_condition')->nullable();
            $table->string('authorized_sign')->nullable();
            $table->text('remark')->nullable();

            $table->status();
            $table->userFields();

            $table->refId();

            $table->foreign('province_id')->references('id')->on('provinces')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('creditors');
    }
}
