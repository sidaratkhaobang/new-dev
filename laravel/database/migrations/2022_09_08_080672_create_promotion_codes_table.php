<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_codes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('promotion_id');
            $table->string('code', 50)->unique();

            $table->dateTime('start_sale_date')->nullable();
            $table->dateTime('end_sale_date')->nullable();

            $table->boolean('is_offline')->default(false);
            $table->boolean('is_free')->default(false);
            $table->decimal('selling_price')->default(0);
            $table->boolean('is_sold')->default(false);
            $table->dateTime('sold_date')->nullable();
            $table->uuid('owner_id')->nullable();
            $table->text('payment_description')->nullable();

            $table->boolean('can_reuse')->default(false);
            $table->integer('quota')->default(1);

            $table->uuid('customer_id')->nullable();
            $table->boolean('is_booking')->default(false);
            $table->boolean('is_used')->default(false);
            $table->dateTime('use_date')->nullable();

            $table->foreign('promotion_id')->references('id')->on('promotions')->cascadeOnDelete();
            $table->foreign('owner_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotion_codes');
    }
}
