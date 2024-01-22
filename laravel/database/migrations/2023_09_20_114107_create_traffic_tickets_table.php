<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrafficTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traffic_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->nullableUuidMorphs('job');
            $table->text('metadata')->nullable();
            $table->uuid('car_id')->nullable();
            $table->datetime('offense_date')->nullable();
            $table->date('document_date')->nullable();
            $table->string('traffic_ticket_no', 50)->nullable();
            $table->decimal('fine', 10, 2)->nullable();
            $table->string('document_type', 50)->nullable();
            $table->string('charge', 200)->nullable();
            $table->integer('speed')->nullable();
            $table->string('location', 200)->nullable();
            $table->unsignedBigInteger('region_id')->nullable();
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('subdistrict_id')->nullable();
            $table->uuid('police_station_id')->nullable();
            $table->date('notification_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('deadline_date')->nullable();
            $table->string('status_send_po', 50)->nullable();
            $table->date('send_po_date')->nullable();
            $table->boolean('is_respond')->nullable();
            $table->date('respond_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('status', 50)->nullable();
            $table->text('remark')->nullable();
            $table->userFields();

            $table->foreign('car_id')->references('id')->on('cars')->nullOnDelete();
            $table->foreign('region_id')->references('id')->on('geographies')->nullOnDelete();
            $table->foreign('province_id')->references('id')->on('provinces')->nullOnDelete();
            $table->foreign('district_id')->references('id')->on('amphures')->nullOnDelete();
            $table->foreign('subdistrict_id')->references('id')->on('districts')->nullOnDelete();
            $table->foreign('police_station_id')->references('id')->on('police_stations')->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('traffic_tickets');
    }
}
