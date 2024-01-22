<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRepairOrderLinesAddAddDebt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('repair_order_lines', function (Blueprint $table) {
            $table->decimal('add_debt', 10, 2)->after('vat')->nullable();
            $table->decimal('reduce_debt', 10, 2)->after('add_debt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('repair_order_lines', function (Blueprint $table) {
            $table->dropColumn('add_debt');
            $table->dropColumn('reduce_debt');
        });
    }
}
