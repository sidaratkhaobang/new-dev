<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterConditionQuotationsAndConditionGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('condition_quotations', function (Blueprint $table) {
            $table->uuid('condition_group_id')->nullable()->after('condition_type');
            $table->foreign('condition_group_id')->references('id')->on('condition_groups')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('condition_quotations', function (Blueprint $table) {
            $table->dropForeign(['condition_group_id']);
            $table->dropColumn('condition_group_id');
        });
    }
}
