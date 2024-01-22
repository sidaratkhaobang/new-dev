<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBranchesAddDocumentPrefix extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->string('document_prefix', 4)->nullable()->after('cost_center');
            $table->string('registered_code', 4)->nullable()->after('document_prefix');
            $table->boolean('is_head_office')->default(false)->after('is_main');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('is_head_office');
            $table->dropColumn('registered_code');
            $table->dropColumn('document_prefix');
        });
    }
}
