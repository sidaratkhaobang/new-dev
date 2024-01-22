<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccidentRepairOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accident_repair_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id')->nullable();
            $table->string('worksheet_no',50)->nullable();
            $table->boolean('is_appointment')->default(1);
            $table->dateTime('appointment_date')->nullable();
            $table->text('appointment_place')->nullable();
            $table->string('contacts_tls',50)->nullable();
            $table->string('contacts_insurance',50)->nullable();
            $table->string('contacts_customer',50)->nullable();
            $table->dateTime('bidding_date')->nullable();
            $table->decimal('wage', 10, 2)->nullable();
            $table->decimal('spare_parts', 10, 2)->nullable();
            $table->decimal('discount_spare_parts', 10, 2)->nullable();
            $table->string('offer_gm',50)->nullable();
            $table->dateTime('repair_date')->nullable();
            $table->integer('amount_completed')->nullable();
            $table->dateTime('actual_repair_date')->nullable();
            $table->uuid('cradle_id')->nullable();
            $table->unsignedBigInteger('cradle_area_id')->nullable();
            $table->text('remark')->nullable();
            $table->text('reason')->nullable();
            $table->string('status', 50)->nullable();

            $table->foreign('accident_id')->references('id')->on('accidents')->cascadeOnDelete();
            $table->foreign('cradle_id')->references('id')->on('cradles')->cascadeOnDelete();
            $table->foreign('cradle_area_id')->references('id')->on('provinces')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accident_repair_orders');
    }
}
