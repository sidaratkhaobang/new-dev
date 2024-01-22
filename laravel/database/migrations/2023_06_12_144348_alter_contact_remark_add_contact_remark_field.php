<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterContactRemarkAddContactRemarkField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lt_rentals', function (Blueprint $table) {
            $table->text('contact_remark')->nullable()->after('contact_tel');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lt_rentals', function (Blueprint $table) {
            $table->dropColumn('contact_remark');
        });
    }
}
