<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsGlAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_gl_accounts', function (Blueprint $table) {
            $table->uuid('product_id');
            $table->uuid('gl_account_id');

            $table->primary(['product_id', 'gl_account_id'], 'products_gl_accounts_pk');

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('gl_account_id')->references('id')->on('gl_accounts')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products_gl_accounts');
    }
}
