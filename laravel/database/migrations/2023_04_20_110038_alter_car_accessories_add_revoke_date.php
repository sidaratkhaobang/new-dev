<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCarAccessoriesAddRevokeDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_accessories', function (Blueprint $table) {
            $table->date('install_date')->nullable()->after('type_accessories');
            $table->date('revoke_date')->nullable()->after('install_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_accessories', function (Blueprint $table) {
            $table->dropColumn(['install_date', 'revoke_date']);
        });
    }
}
