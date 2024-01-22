<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepairListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repair_lists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code');
            $table->text('name');
            $table->decimal('price', 10, 2)->default(0)->nullable();
            $table->smallInteger('status')->default(STATUS_ACTIVE);
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
        Schema::dropIfExists('repair_lists');
    }
}
