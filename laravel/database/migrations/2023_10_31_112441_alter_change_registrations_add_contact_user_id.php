<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('change_registrations', function (Blueprint $table) {
            $table->uuid('contact_user_id')->nullable()->comment('ชื่อ - นามสกุล ผู้ติดต่อ')->after('name_contact');
            $table->uuid('recipient_user_id')->nullable()->comment('ชื่อ - นามสกุล ผู้รับป้าย')->after('name_recipient');
            $table->date('request_power_attorney_tls_date')->nullable()->change();

            $table->foreign('contact_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('recipient_user_id')->references('id')->on('users')->cascadeOnDelete();    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('change_registrations', function (Blueprint $table) {
            $table->dropForeign(['recipient_user_id','contact_user_id']);
            $table->dropColumn(['recipient_user_id','contact_user_id']);
            $table->boolean('request_power_attorney_tls_date')->nullable()->change();
        });
    }
};
