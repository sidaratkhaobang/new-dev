<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBomLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bom_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('bom_id');
            $table->uuid('car_class_id')->nullable();
            $table->uuid('car_color_id')->nullable();
            $table->integer('amount')->default(0);
            $table->text('remark')->nullable();

            $table->foreign('bom_id')->references('id')->on('boms')->cascadeOnDelete();
            $table->foreign('car_class_id')->references('id')->on('car_classes')->nullOnDelete();
            $table->foreign('car_color_id')->references('id')->on('car_colors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bom_lines');
    }
}
