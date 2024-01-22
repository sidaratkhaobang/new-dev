<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInsuranceLotsAddLeasingId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('insurance_lots', function (Blueprint $table) {
            $table->uuid('leasing_id')->nullable()->after('lot_no');
            $table->foreign('leasing_id')->references('id')->on('creditors')->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('insurance_lots', function (Blueprint $table) {
            $table->dropForeign(['leasing_id']);
            $table->dropColumn('leasing_id');
        });
    }
}
