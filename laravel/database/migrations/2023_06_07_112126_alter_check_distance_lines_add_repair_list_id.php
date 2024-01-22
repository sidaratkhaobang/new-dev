<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCheckDistanceLinesAddRepairListId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('check_distance_lines', function (Blueprint $table) {
            $table->uuid('repair_list_id')->nullable()->after('check_distance_id');
            $table->dropColumn(['code', 'name']);

            $table->foreign('repair_list_id')->references('id')->on('repair_lists')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('check_distance_lines', function (Blueprint $table) {
            $table->string('code')->nullable()->after('repair_list_id');
            $table->text('name')->nullable()->after('code');
            $table->dropForeign(['repair_list_id']);
            $table->dropColumn(['repair_list_id']);
        });
    }
}
