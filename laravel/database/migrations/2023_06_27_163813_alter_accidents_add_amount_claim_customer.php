<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAccidentsAddAmountClaimCustomer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accidents', function (Blueprint $table) {
            $table->string('amount_claim_customer')->nullable()->after('is_driver_replacement');
            $table->string('amount_claim_tls')->nullable()->after('amount_claim_customer');
            $table->decimal('compensation',10,2)->nullable()->after('amount_claim_tls');
            $table->string('repair_type')->nullable()->after('compensation');
            $table->string('responsible')->nullable()->after('repair_type');
            $table->string('is_except_deductible')->nullable()->after('responsible');
            $table->text('reason_except_deductible')->nullable()->after('is_except_deductible');
            $table->string('deductible')->nullable()->after('reason_except_deductible');
            $table->string('claim_no')->nullable()->after('deductible');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accidents', function (Blueprint $table) {
            $table->dropColumn('amount_claim_customer');
            $table->dropColumn('amount_claim_tls');
            $table->dropColumn('compensation');
            $table->dropColumn('repair_type');
            $table->dropColumn('responsible');
            $table->dropColumn('is_except_deductible');
            $table->dropColumn('reason_except_deductible');
            $table->dropColumn('deductible');
            $table->dropColumn('claim_no');
        });
    }
}
