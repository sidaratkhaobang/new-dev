<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCarsAddCarBrandIdField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->uuid('car_brand_id')->nullable()->after('keys_address');
            $table->uuid('car_group_id')->nullable()->after('car_brand_id');
            $table->uuid('car_categorie_id')->nullable()->after('car_group_id');
            $table->date('registered_date')->nullable()->after('car_brand_id');
            $table->string('car_age')->nullable()->after('car_categorie_id');
            $table->date('start_date')->nullable()->after('car_age');
            $table->string('car_age_start')->nullable()->after('start_date');
            $table->string('car_storage')->nullable()->after('car_age_start');
            $table->string('car_park')->nullable()->after('car_storage');

            $table->foreign('car_brand_id')->references('id')->on('car_brands')->cascadeOnDelete();
            $table->foreign('car_group_id')->references('id')->on('car_groups')->cascadeOnDelete();
            $table->foreign('car_categorie_id')->references('id')->on('car_categories')->cascadeOnDelete();
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
            $table->dropColumn('car_brand_id');
            $table->dropColumn('car_group_id');
            $table->dropColumn('car_categorie_id');
            $table->dropColumn('registered_date');
            $table->dropColumn('car_age');
            $table->dropColumn('start_date');
            $table->dropColumn('car_age_start');
            $table->dropColumn('car_storage');
            $table->dropColumn('car_park');
        });
    }
}
