<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentalCategoriesServiceTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rental_categories_service_types', function (Blueprint $table) {
            $table->uuid('rental_category_id');
            $table->uuid('service_type_id');

            $table->primary(['rental_category_id', 'service_type_id'], 'rental_categories_service_types_pk');

            $table->foreign('rental_category_id')->references('id')->on('rental_categories')->cascadeOnDelete();
            $table->foreign('service_type_id')->references('id')->on('service_types')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rental_categories_service_types');
    }
}
