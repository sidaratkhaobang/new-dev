<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRepairOrdersAddInvoiceNo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('repair_orders', function (Blueprint $table) {
            $table->string('invoice_no', 20)->after('reason')->nullable();
            $table->string('actual_mileage', 20)->after('invoice_no')->nullable();
            $table->decimal('sub_total', 10, 2)->after('actual_mileage')->nullable();
            $table->integer('percent_vat')->after('sub_total')->nullable();
            $table->decimal('vat', 10, 2)->after('percent_vat')->nullable();
            $table->decimal('discount', 10, 2)->after('vat')->nullable();
            $table->string('rubber_week', 100)->after('discount')->nullable();
            $table->text('remark_expenses')->after('rubber_week')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('repair_orders', function (Blueprint $table) {
            $table->dropColumn('invoice_no');
            $table->dropColumn('actual_mileage');
            $table->dropColumn('sub_total');
            $table->dropColumn('percent_vat');
            $table->dropColumn('vat');
            $table->dropColumn('discount');
            $table->dropColumn('rubber_week');
            $table->dropColumn('remark_expenses');
        });
    }
}
