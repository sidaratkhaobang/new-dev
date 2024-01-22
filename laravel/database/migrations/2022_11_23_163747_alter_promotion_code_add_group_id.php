<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPromotionCodeAddGroupId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promotion_codes', function (Blueprint $table) {
            $table->uuid('group_id')->nullable()->after('use_date');

            $table->foreign('group_id')->references('id')->on('promotion_codes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promotion_codes', function (Blueprint $table) {
            $table->dropColumn('group_id');
        });
    }
}
