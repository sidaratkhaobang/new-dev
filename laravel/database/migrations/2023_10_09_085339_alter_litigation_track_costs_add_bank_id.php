<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLitigationTrackCostsAddBankId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('litigation_track_costs', function (Blueprint $table) {
            $table->uuid('bank_id')->nullable()->after('date');
            $table->renameColumn('payment_channels', 'payment_channel');
            $table->foreign('bank_id')->references('id')->on('banks')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('litigation_track_costs', function (Blueprint $table) {
            $table->dropForeign(['bank_id']);
            $table->dropColumn(['bank_id']);
            $table->renameColumn('payment_channel', 'payment_channels');
        });
    }
}
