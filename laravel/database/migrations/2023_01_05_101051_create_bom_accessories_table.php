<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBomAccessoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bom_accessories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('bom_id');
            $table->uuid('accessories_id')->nullable();
            $table->integer('amount')->default(0);

            $table->foreign('bom_id')->references('id')->on('boms')->cascadeOnDelete();
            $table->foreign('accessories_id')->references('id')->on('accessories')->nullOnDelete(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bom_accessories');
    }
}
