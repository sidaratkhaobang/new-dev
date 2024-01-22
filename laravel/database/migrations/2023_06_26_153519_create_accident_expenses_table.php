<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccidentExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accident_expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id')->nullable();
            $table->text('list')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->text('remark')->nullable();
            $table->userFields();


            $table->foreign('accident_id')->references('id')->on('accidents')->cascadeOnDelete();   
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accident_expenses');
    }
}
