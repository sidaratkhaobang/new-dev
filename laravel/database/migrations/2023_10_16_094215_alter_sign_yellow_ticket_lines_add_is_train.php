<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSignYellowTicketLinesAddIsTrain extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sign_yellow_ticket_lines', function (Blueprint $table) {
            $table->boolean('is_train')->nullable()->comment('การเข้าอบรม')->after('is_mistake');
            $table->string('tel', 100)->change(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sign_yellow_ticket_lines', function (Blueprint $table) {
            $table->dropColumn('is_train');
            $table->string('tel', 20)->change(); 
        });
    }
}
