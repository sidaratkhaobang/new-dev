<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLtRentalTorLineAccessoriesAddTypeAccessory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lt_rental_tor_line_accessories', function (Blueprint $table) {
            $table->string('type_accessories')->nullable()->after('tor_section');
        });

        Schema::table('lt_rental_line_accessories', function (Blueprint $table) {
            $table->string('type_accessories')->nullable()->after('tor_section');
        });

        Schema::table('purchase_requisition_line_accessories', function (Blueprint $table) {
            $table->string('type_accessories')->nullable()->after('remark');
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
            $table->dropColumn('type_accessories');
        });

        Schema::table('lt_rental_line_accessories', function (Blueprint $table) {
            $table->dropColumn('type_accessories');
        });

        Schema::table('purchase_requisition_line_accessories', function (Blueprint $table) {
            $table->dropColumn('type_accessories');
        });
    }
}
