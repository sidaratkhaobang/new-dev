<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractFormCheckListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_form_check_lists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contract_form_id');
            $table->foreign('contract_form_id')->references('id')->on('contract_forms')->cascadeOnDelete();
            $table->text('name')->nullable();
            $table->integer('seq')->nullable();
            $table->status();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_form_check_lists');
    }
}
