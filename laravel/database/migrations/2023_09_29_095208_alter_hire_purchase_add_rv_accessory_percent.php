<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterHirePurchaseAddRvAccessoryPercent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hire_purchases', function (Blueprint $table) {
            $table->string('rv_accessory_percent', 20)->nullable()->after('rv_percent');
            $table->renameColumn('rv_percent', 'rv_car_percent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hire_purchases', function (Blueprint $table) {
            $table->renameColumn('rv_car_percent', 'rv_percent');
            $table->dropColumn('rv_accessory_percent');
            $table->date('account_closing_date')->change();
        });
    }
}
