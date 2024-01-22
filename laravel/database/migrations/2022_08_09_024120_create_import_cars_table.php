<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\ImportCarStatusEnum;

class CreateImportCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_cars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('po_id')->nullable();
            $table->text('remark')->nullable();

            $table->string('status', 20)->default(ImportCarStatusEnum::PENDING);
            $table->userFields();

            $table->foreign('po_id')->references('id')->on('purchase_orders')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('import_cars');
    }
}
