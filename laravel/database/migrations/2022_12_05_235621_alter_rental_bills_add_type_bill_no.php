<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRentalBillsAddTypeBillNo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rental_bills', function (Blueprint $table) {
            $table->string('worksheet_no', 20)->nullable()->after('rental_id');
            $table->string('bill_type', 20)->nullable()->after('worksheet_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rental_bills', function (Blueprint $table) {
            $table->dropColumn(['worksheet_no', 'bill_type']);
        });
    }
}
