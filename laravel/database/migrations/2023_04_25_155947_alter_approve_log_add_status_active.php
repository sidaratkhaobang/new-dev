<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterApproveLogAddStatusActive extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approve_logs', function (Blueprint $table) {
            $table->datetime('approved_date')->nullable()->change();
            $table->boolean('status_active')->default(1)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approve_logs', function (Blueprint $table) {
            $table->date('approved_date')->nullable()->change();
            $table->dropColumn('status_active');
        });
    }
}
