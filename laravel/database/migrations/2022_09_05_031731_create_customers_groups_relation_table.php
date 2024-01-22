<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersGroupsRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers_groups_relation', function (Blueprint $table) {
            $table->uuid('customer_id');
            $table->uuid('customer_group_id');

            $table->primary(['customer_id', 'customer_group_id'], 'customers_groups_relation_pk');

            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->foreign('customer_group_id')->references('id')->on('customer_groups')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers_groups_relation');
    }
}
