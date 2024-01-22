<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInstallEquipmentPoRenameQuotationId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('install_equipment_purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['quotation_id']);
            $table->string('quotation_remark')->nullable()->after('remark');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('install_equipment_purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['quotation_remark']);
            $table->uuid('quotation_id')->nullable();
        });
    }
}
