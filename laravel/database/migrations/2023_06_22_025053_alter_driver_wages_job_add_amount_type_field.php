<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDriverWagesJobAddAmountTypeField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_wages_jobs', function (Blueprint $table) {
            $table->string('amount_type', 20)->nullable()->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_wages_jobs', function (Blueprint $table) {
            $table->dropColumn('amount_type');
        });
    }
}
