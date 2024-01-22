<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('compensations', function (Blueprint $table) {
            $table->date('verdict_date')->nullable()->after('type');
            $table->string('vmi_no_parties', 50)->nullable()->after('id_card_parties');
            $table->string('worksheet_no', 20)->nullable()->after('id');;
            $table->uuid('creator_id')->nullable()->after('province_parties_id');

            $table->string('sending_channel', 100)->nullable()->after('recipient_document');
            $table->string('sending_channel_detail', 100)->nullable()->after('sending_channel');
            $table->foreign('creator_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('compensations', function (Blueprint $table) {
            $table->dropForeign(['creator_id']);
            $table->dropColumn(['creator_id', 'verdict_date', 'vmi_no_parties', 'worksheet_no', 'sending_channel', 'sending_channel_detail']);
        });
    }
};