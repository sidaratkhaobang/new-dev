<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLtRentalTorLineAccessoriesAddRemark extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lt_rental_tor_line_accessories', function (Blueprint $table) {
            $table->text('remark')->nullable()->after('amount');
            $table->text('tor_section')->nullable()->after('remark');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lt_rental_tor_line_accessories', function (Blueprint $table) {
            $table->dropColumn('remark');
            $table->dropColumn('tor_section');
        });
    }
}
