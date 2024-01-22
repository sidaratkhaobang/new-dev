<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInstallEquipmentChangeRefId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('install_equipments', function (Blueprint $table) {
            $table->renameColumn('ref_id', 'group_id');
            $table->date('start_date')->nullable()->after('install_day_amount');
            $table->date('end_date')->nullable()->after('install_day_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('install_equipments', function (Blueprint $table) {
            $table->renameColumn('group_id', 'ref_id');
        });
    }
}
