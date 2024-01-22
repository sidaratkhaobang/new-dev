<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRentalProductAdditionalsAddRentalBillId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rental_product_additionals', function (Blueprint $table) {
            $table->uuid('rental_bill_id')->nullable()->after('rental_id');
            $table->foreign('rental_bill_id')->references('id')->on('rental_bills')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rental_product_additionals', function (Blueprint $table) {
            $table->dropForeign(['rental_bill_id']);
            $table->dropColumn('rental_bill_id');
        });   
    }
}
