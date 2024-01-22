<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class CreateProvincesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geographies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();

            $table->refId();
        });

        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('code', 2);
            $table->string('name_th');
            $table->string('name_en');
            $table->unsignedBigInteger('geography_id')->nullable();
            $table->timestamps();

            $table->refId();

            $table->foreign('geography_id')->references('id')->on('geographies')->nullOnDelete();
        });

        Schema::create('amphures', function (Blueprint $table) {
            $table->id();
            $table->string('code', 4);
            $table->string('name_th');
            $table->string('name_en');
            $table->unsignedBigInteger('province_id')->nullable();
            $table->timestamps();

            $table->foreign('province_id')->references('id')->on('provinces')->nullOnDelete();
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('zip_code', 8);
            $table->string('name_th');
            $table->string('name_en');
            $table->unsignedBigInteger('amphure_id')->nullable();
            $table->timestamps();

            $table->foreign('amphure_id')->references('id')->on('amphures')->nullOnDelete();
        });

        Artisan::call("ud:import_provinces");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('districts');
        Schema::dropIfExists('amphures');
        Schema::dropIfExists('provinces');
        Schema::dropIfExists('geographies');
    }
}
