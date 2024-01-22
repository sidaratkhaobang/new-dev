<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\POStatusEnum;
use App\Enums\POTypeEnum;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('po_no', 20)->nullable();
            $table->string('po_type', 20)->index()->default(POTypeEnum::CAR);
            $table->uuid('creditor_id')->nullable();
            $table->date('request_date')->nullable();
            $table->date('require_date')->nullable();
            $table->text('remark')->nullable();

            $table->uuid('pr_id')->nullable();

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('vat', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->text('reason')->nullable();
            $table->string('time_of_delivery')->nullable();
            $table->string('payment_condition', 20)->nullable();

            $table->string('status', 20)->default(POStatusEnum::DRAFT);
            $table->userFields();

            $table->timestamp('reviewed_at')->nullable();
            $table->uuid('reviewed_by')->nullable();

            $table->refId();

            $table->foreign('creditor_id')->references('id')->on('creditors')->nullOnDelete();
            $table->foreign('pr_id')->references('id')->on('purchase_requisitions')->nullOnDelete();
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
        Schema::dropIfExists('purchase_orders');
    }
}
