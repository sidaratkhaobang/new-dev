<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAccidentsRenameProvinceToProvinceId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accidents', function (Blueprint $table) {
            $table->renameColumn('province', 'province_id');
            $table->renameColumn('district', 'district_id');
            $table->renameColumn('subdistrict', 'subdistrict_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accidents', function (Blueprint $table) {
            $table->renameColumn('province_id', 'province');
            $table->renameColumn('district_id', 'district');
            $table->renameColumn('subdistrict_id', 'subdistrict');
        });
    }
}
