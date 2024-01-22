<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompensationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compensations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id');
            $table->string('type', 50)->nullable();
            $table->uuid('insurer_parties_id')->nullable();
            $table->string('insurer_parties_name', 100)->nullable();
            $table->text('insurer_parties_address')->nullable();
            $table->string('name_parties', 100)->nullable();
            $table->string('tel_parties', 20)->nullable();
            $table->text('address_parties')->nullable();
            $table->string('id_card_parties', 13)->nullable();
            $table->string('claim_no_parties', 50)->nullable();
            $table->string('car_type_parties', 50)->nullable();
            $table->uuid('car_brand_parties_id')->nullable();
            $table->string('license_plate_parties', 50)->nullable();
            $table->unsignedBigInteger('province_parties_id')->nullable();
            $table->decimal('claim_amount', 10, 2)->nullable();
            $table->integer('claim_days')->nullable();
            $table->decimal('termination_amount', 10, 2)->nullable();
            $table->integer('termination_days')->nullable();
            $table->decimal('oic_amount', 10, 2)->nullable();
            $table->decimal('actual_payment_amount', 10, 2)->nullable();
            $table->string('recipient_document', 100)->nullable();
            $table->date('receive_date')->nullable();
            $table->date('confirmation_date')->nullable();
            $table->string('payment_channels', 50)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->text('remark')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 50)->nullable();
            $table->userFields();

            $table->foreign('accident_id')->references('id')->on('accidents')->cascadeOnDelete();
            $table->foreign('insurer_parties_id')->references('id')->on('insurers')->nullOnDelete();
            $table->foreign('car_brand_parties_id')->references('id')->on('car_brands')->nullOnDelete();
            $table->foreign('province_parties_id')->references('id')->on('provinces')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compensations');
    }
}
