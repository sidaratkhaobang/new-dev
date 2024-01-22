<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlAccountsCustomerGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gl_accounts_customer_groups', function (Blueprint $table) {
            $table->uuid('gl_account_id');
            $table->uuid('customer_group_id');

            $table->primary(['gl_account_id', 'customer_group_id'], 'gl_accounts_customer_groups_pk');

            $table->foreign('gl_account_id')->references('id')->on('gl_accounts')->cascadeOnDelete();
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
        Schema::dropIfExists('gl_accounts_customer_groups');
    }
}
