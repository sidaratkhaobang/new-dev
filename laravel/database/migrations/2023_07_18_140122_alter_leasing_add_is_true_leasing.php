<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLeasingAddIsTrueLeasing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leasings', function (Blueprint $table) {
            $table->boolean('is_true_leasing')->default(false)->after('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leasings', function (Blueprint $table) {
            $table->dropColumn(['is_true_leasing']);
        });
    }
}
