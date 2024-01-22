<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expressways', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type', 100)->nullable();
            $table->string('name', 100)->nullable();
            $table->boolean('is_expressway')->nullable();
            $table->string('status', 50)->nullable();
            $table->userFields();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expressways');
    }
};
