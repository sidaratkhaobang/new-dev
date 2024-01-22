<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\ImportCarLineStatusEnum;

class CreateImportCarLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_car_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('import_car_id');
            $table->uuid('po_line_id')->nullable();

            $table->string('engine_no')->nullable();
            $table->string('chassis_no')->nullable();
            $table->date('install_date')->nullable(); // installation_completed_date

            $table->string('status', 20)->default(ImportCarLineStatusEnum::PENDING);

            $table->date('delivery_date')->nullable();
            $table->string('delivery_location')->nullable();
            $table->text('reject_reason')->nullable();

            $table->string('status_delivery', 20)->default(ImportCarLineStatusEnum::PENDING);
            $table->dateTime('verification_date')->nullable();

            $table->foreign('import_car_id')->references('id')->on('import_cars')->cascadeOnDelete();
            $table->foreign('po_line_id')->references('id')->on('purchase_order_lines')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('import_car_lines');
    }
}
