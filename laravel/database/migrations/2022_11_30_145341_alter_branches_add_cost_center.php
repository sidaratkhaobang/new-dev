<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBranchesAddCostCenter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->string('cost_center', 30)->nullable()->after('code');
        });

        Schema::table('sap_interface_lines', function (Blueprint $table) {
            $table->string('line_type', 30)->nullable()->after('sap_interface_id');
            $table->string('remark', 200)->nullable()->after('text');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sap_interface_lines', function (Blueprint $table) {
            $table->dropColumn('remark');
            $table->dropColumn('line_type');
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('cost_center');
        });
    }
}
