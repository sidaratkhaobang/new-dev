<?php

use App\Enums\TransferTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentalProductTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rental_product_transports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rental_id');
            $table->tinyInteger('transfer_type')->default(TransferTypeEnum::IN);
            $table->string('brand_name', 100)->nullable();
            $table->string('class_name', 100)->nullable();
            $table->string('color_name', 100)->nullable();
            $table->string('license_plate', 100)->nullable();
            $table->string('chassis_no', 100)->nullable();
            $table->string('engine_no', 100)->nullable();
            $table->text('remark')->nullable();

            $table->foreign('rental_id')->references('id')->on('rentals')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rental_product_transports');
    }
}
