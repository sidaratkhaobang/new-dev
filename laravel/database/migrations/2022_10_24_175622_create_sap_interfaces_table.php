<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSapInterfacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sap_interfaces', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('account_type', 2);
            $table->string('transfer_type', 30);
            $table->string('transfer_sub_type', 30);
            $table->nullableMorphs('reference');

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
        Schema::dropIfExists('sap_interfaces');
    }
}
