<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCradleTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cradle_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cradle_id');
            $table->string('type')->nullable();
            $table->userFields();

            $table->foreign('cradle_id')->references('id')->on('cradles')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cradle_types');
    }
}
