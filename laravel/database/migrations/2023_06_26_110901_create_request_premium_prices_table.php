<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestPremiumPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_premium_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('request_premium_car_line_id', 36);
            $table->char('lt_rental_month_id', 36);
            $table->decimal('premium_year_one', 10, 2)->nullable();
            $table->decimal('premium_all_year', 10, 2)->nullable();
            $table->decimal('premium_cmi', 10, 2)->nullable();
            $table->decimal('premium_year_one_plus_cmi', 10, 2)->nullable();
            $table->decimal('premium_cmi_plus_all_year', 10, 2)->nullable();
            $table->userFields();

//            FK
            $table->foreign('request_premium_car_line_id')->references('id')->on('request_premium_carclass_lines')->cascadeOnDelete();
            $table->foreign('lt_rental_month_id')->references('id')->on('lt_rental_month')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_premium_prices', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['deleted_by']);
            $table->dropForeign(['request_premium_car_line_id']);
            $table->dropForeign(['lt_rental_month_id']);
        });
        Schema::dropIfExists('request_premium_prices');
    }
}
