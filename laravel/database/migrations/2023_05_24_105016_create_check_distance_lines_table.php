<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckDistanceLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_distance_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('check_distance_id');
            $table->string('code')->nullable();
            $table->text('name')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('check')->nullable();
            $table->text('remark')->nullable();
            $table->userFields();

            $table->foreign('check_distance_id')->references('id')->on('check_distances')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('check_distance_lines');
    }
}
