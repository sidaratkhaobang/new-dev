<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSapInterfacesAddStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sap_interfaces', function (Blueprint $table) {
            $table->string('document_type', 30)->nullable()->after('transfer_sub_type');
            $table->string('status', 30)->nullable()->after('reference_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sap_interfaces', function (Blueprint $table) {
            $table->dropColumn('document_type');
            $table->dropColumn('status');
        });
    }
}
