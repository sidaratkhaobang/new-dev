<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInspectionStepAddIsNeedSendMobileField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inspection_steps', function (Blueprint $table) {
            $table->boolean('is_need_send_mobile')->default(false)->after('is_need_inspector_sign');
            $table->uuid('inspection_role_id')->nullable()->after('is_need_send_mobile');

            $table->foreign('inspection_role_id')->references('id')->on('roles')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inspection_steps', function (Blueprint $table) {       
            $table->dropColumn('is_need_send_mobile');
            $table->dropForeign('inspection_role_id');
            $table->dropColumn('inspection_role_id');
        });
    }
}
