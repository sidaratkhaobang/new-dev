<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverWagesMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_wages_mapping', function (Blueprint $table) {
            $table->uuid('driver_wage_id');
            $table->uuid('driver_wage_map_id');

            $table->primary(['driver_wage_id', 'driver_wage_map_id'], 'driver_wages_mapping_pk');

            $table->foreign('driver_wage_id')->references('id')->on('driver_wages')->cascadeOnDelete();
            $table->foreign('driver_wage_map_id')->references('id')->on('driver_wages')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_wages_mapping');
    }
}
