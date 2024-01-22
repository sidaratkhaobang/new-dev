<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\RentalTypeEnum;
use App\Enums\PRStatusEnum;

class CreatePurchaseRequisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_requisitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('pr_no', 20)->nullable();
            $table->uuid('creditor_id')->nullable();
            $table->date('request_date')->nullable();
            $table->date('require_date')->nullable();
            $table->string('rental_type', 20)->default(RentalTypeEnum::SHORT);
            $table->text('remark')->nullable();
            $table->text('reject_reason')->nullable();
            $table->text('cancel_reason')->nullable();

            $table->string('rental_refer', 255)->nullable();
            $table->string('contract_refer', 255)->nullable();
            $table->nullableUuidMorphs('reference');

            $table->string('status', 20)->default(PRStatusEnum::DRAFT);
            $table->userFields();

            $table->timestamp('reviewed_at')->nullable();
            $table->uuid('reviewed_by')->nullable();

            $table->refId();

            $table->foreign('creditor_id')->references('id')->on('creditors')->nullOnDelete();
            $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_requisitions');
    }
}
