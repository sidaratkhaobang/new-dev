<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCarAddCarCharacteristicId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->uuid('car_characteristic_id')->nullable()->after('purchase_price');
            $table->uuid('car_category_id')->nullable()->after('car_characteristic_id');
            $table->date('car_tax_exp_date')->nullable()->after('car_category_id');;

            $table->foreign('car_characteristic_id')->references('id')->on('car_characteristics')->nullOnDelete();
            $table->foreign('car_category_id')->references('id')->on('car_categories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropForeign(['car_characteristic_id']);
            $table->dropForeign(['car_category_id']);
            $table->dropColumn(['car_characteristic_id','car_category_id']);
            $table->dropColumn(['car_tax_exp_date']);
        });
    }
}
