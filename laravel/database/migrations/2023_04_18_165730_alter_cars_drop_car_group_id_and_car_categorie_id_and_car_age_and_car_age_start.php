<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCarsDropCarGroupIdAndCarCategorieIdAndCarAgeAndCarAgeStart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropForeign(['car_group_id']);
            $table->dropForeign(['car_categorie_id']);
            $table->dropColumn(['car_group_id', 'car_categorie_id', 'car_age', 'car_age_start']);
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
            $table->uuid('car_group_id')->nullable()->after('car_brand_id');
            $table->uuid('car_categorie_id')->nullable()->after('car_group_id');
            $table->string('car_age')->nullable()->after('car_categorie_id');
            $table->string('car_age_start')->nullable()->after('start_date');
        });
    }
}
