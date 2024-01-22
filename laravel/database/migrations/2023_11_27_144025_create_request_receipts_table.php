<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_receipts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no', 50)->nullable();
            $table->uuid('branch_id')->nullable();
            $table->string('type', 20)->nullable();
            $table->string('title', 100)->nullable();
            $table->text('detail')->nullable();
            $table->string('status', 50)->nullable();
            $table->boolean('is_select_db_customer')->default(true);
            $table->uuid('customer_id')->nullable();
            $table->string('customer_name', 100)->nullable();
            $table->string('customer_tax_no', 20)->nullable();
            $table->text('customer_address')->nullable();
            $table->unsignedBigInteger('customer_province_id')->nullable();
            $table->unsignedBigInteger('customer_district_id')->nullable();
            $table->unsignedBigInteger('customer_subdistrict_id')->nullable();
            $table->userFields();

            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('customer_province_id')->references('id')->on('provinces')->nullOnDelete();
            $table->foreign('customer_district_id')->references('id')->on('amphures')->nullOnDelete();
            $table->foreign('customer_subdistrict_id')->references('id')->on('districts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_receipts');
    }
};
