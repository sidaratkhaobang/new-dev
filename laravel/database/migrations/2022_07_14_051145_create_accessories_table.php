<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accessories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code', 10)->nullable();
            $table->string('version')->nullable();
            $table->decimal('price', 10, 2)->nullable()->default(0);

            $table->uuid('creditor_id')->nullable();

            $table->status();
            $table->userFields();

            $table->refId();

            $table->foreign('creditor_id')->references('id')->on('creditors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accessories');
    }
}
