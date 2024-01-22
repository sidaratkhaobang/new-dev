<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterScbPaymentLogsAddTypeRequestField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scb_payment_logs', function (Blueprint $table) {
            $table->string('type_request')->after('id');
            $table->renameColumn('tranID_respones', 'tranID_response');
            $table->renameColumn('reference_2_respones', 'reference_2_response');
            $table->renameColumn('amount_respones', 'amount_response');
            // $table->string('tranID_response')->after('resMesg');
            // $table->string('reference_2_response')->after('tranID_response');
            // $table->string('amount_response')->after('paymentID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scb_payment_logs', function (Blueprint $table) {
            $table->dropColumn('type_request');
            $table->dropColumn('tranID_respones');
            $table->dropColumn('reference_2_respones');
            $table->dropColumn('amount_respones');
        });
    }
}
