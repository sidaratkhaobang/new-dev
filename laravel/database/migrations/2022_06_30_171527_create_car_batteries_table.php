<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarBatteriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_batteries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('version')->nullable();
            $table->string('detail')->nullable();
            $table->decimal('price')->nullable()->default(0);

            $table->status();
            $table->userFields();

            $table->refId();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_batteries');
    }
}
