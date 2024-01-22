<?php

use App\Enums\ReplacementCarStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReplacementCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replacement_cars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no', 20);
            $table->string('replacement_type', 40);
            $table->nullableUuidMorphs('job');
            $table->uuid('branch_id')->nullable();
            $table->uuid('main_car_id');
            $table->uuid('replacement_car_id')->nullable();
            $table->dateTime('replacement_expect_date')->nullable();
            $table->text('replacement_expect_place')->nullable();
            $table->boolean('is_need_driver')->default(false);
            $table->boolean('is_need_slide')->default(false);
            $table->string('customer_name')->nullable();
            $table->string('tel', 20)->nullable();
            $table->dateTime('replacement_date')->nullable();
            $table->text('replacement_place')->nullable();
            $table->text('remark')->nullable();
            $table->boolean('is_spec_low')->default(false);
            $table->text('spec_low_reason')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->string('status')->default(ReplacementCarStatusEnum::PENDING_INSPECT);
            $table->userFields();

            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            $table->foreign('main_car_id')->references('id')->on('cars')->cascadeOnDelete();
            $table->foreign('replacement_car_id')->references('id')->on('cars')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('replacement_cars');
    }
}