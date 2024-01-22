<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\WageCalType;
use App\Enums\WageCalDay;
use App\Enums\WageCalTime;

class CreateDriverWagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_wages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->uuid('driver_wage_category_id')->nullable();
            $table->boolean('is_standard')->default(false);
            $table->string('wage_cal_type', 20)->default(WageCalType::PER_MONTH);
            $table->string('wage_cal_day', 20)->default(WageCalDay::ALL);
            $table->string('wage_cal_time', 20)->default(WageCalTime::ALL);
            $table->boolean('is_special_wage')->default(false);
            $table->integer('seq')->default(0);

            $table->status();
            $table->userFields();

            $table->foreign('driver_wage_category_id')->references('id')->on('driver_wage_categories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_wages');
    }
}
