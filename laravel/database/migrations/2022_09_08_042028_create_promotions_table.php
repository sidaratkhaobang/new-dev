<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\DiscountTypeEnum;
use App\Enums\DiscountModeEnum;
use App\Enums\PromotionTypeEnum;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code');
            $table->uuid('branch_id')->nullable();
            $table->string('promotion_type', 30)->default(PromotionTypeEnum::PROMOTION);
            $table->string('discount_type', 30)->default(DiscountTypeEnum::PERCENT);
            $table->tinyInteger('discount_mode')->default(DiscountModeEnum::ALL);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->tinyInteger('priority')->default(0);
            $table->tinyInteger('package_amount')->default(1);

            $table->dateTime('start_sale_date')->nullable();
            $table->dateTime('end_sale_date')->nullable();

            $table->boolean('is_check_min_total')->default(false);
            $table->decimal('min_total', 10, 2)->default(0);

            $table->boolean('is_check_min_hours')->default(false);
            $table->decimal('min_hours', 8, 2)->default(0);

            $table->boolean('is_check_min_days')->default(false);
            $table->decimal('min_days', 8, 2)->default(0);

            $table->boolean('is_check_min_distance')->default(false);
            $table->decimal('min_distance', 10, 2)->default(0);

            $table->date('start_travel_date')->nullable();
            $table->date('end_travel_date')->nullable();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->status();
            $table->userFields();

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
        Schema::dropIfExists('promotions');
    }
}
