<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\CalculateTypeEnum;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('sku');
            $table->uuid('service_type_id')->nullable();
            $table->string('calculate_type', 10)->default(CalculateTypeEnum::HOURLY);
            $table->decimal('standard_price', 10, 2)->default(0);
            $table->uuid('branch_id')->nullable();

            $table->boolean('booking_day_mon')->default(false);
            $table->boolean('booking_day_tue')->default(false);
            $table->boolean('booking_day_wed')->default(false);
            $table->boolean('booking_day_thu')->default(false);
            $table->boolean('booking_day_fri')->default(false);
            $table->boolean('booking_day_sat')->default(false);
            $table->boolean('booking_day_sun')->default(false);

            $table->time('start_booking_time')->nullable();
            $table->time('end_booking_time')->nullable();
            $table->tinyInteger('reserve_booking_duration')->nullable()->default(0); // hour
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->boolean('is_used_application')->default(false);
            $table->tinyInteger('fix_days')->nullable();
            $table->time('fix_return_time')->nullable();

            $table->status();
            $table->userFields();

            $table->foreign('service_type_id')->references('id')->on('service_types')->nullOnDelete();
            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
