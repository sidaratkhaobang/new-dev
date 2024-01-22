<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInspectionJobAddIsNeedCustomerSignInField extends Migration
{
    /**
     * Run the migrations.inspection_date
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inspection_jobs', function (Blueprint $table) {
            $table->boolean('is_need_customer_sign_in')->default(false)->after('inspection_date');
            $table->boolean('is_need_customer_sign_out')->default(false)->after('is_need_customer_sign_in');
            $table->date('inspection_must_date')->nullable()->after('is_need_customer_sign_out');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inspection_jobs', function (Blueprint $table) {
            $table->dropColumn('is_need_customer_sign_in');
            $table->dropColumn('is_need_customer_sign_out');
            $table->dropColumn('inspection_must_date');
        });
    }
}
