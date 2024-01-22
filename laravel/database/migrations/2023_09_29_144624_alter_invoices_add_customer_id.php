<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInvoicesAddCustomerId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->uuid('customer_id')->nullable()->after('invoice_no');
            $table->integer('installment_amount_total')->nullable()->after('status_debt_collection');
            $table->integer('installment_amount_current')->nullable()->after('installment_amount_total');
            $table->string('type_format', 50)->nullable()->after('installment_amount_current');
            $table->boolean('type_create_invoice')->nullable()->after('type_format');
            $table->string('account_code', 100)->nullable()->after('type_create_invoice');
            $table->uuid('gl_account_id')->nullable()->after('account_code');
            $table->date('period_start_date')->nullable()->after('gl_account_id');
            $table->date('period_end_date')->nullable()->after('period_start_date');

            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('gl_account_id')->references('id')->on('gl_accounts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
            $table->dropColumn('installment_amount_total');
            $table->dropColumn('installment_amount_current');
            $table->dropColumn('type_format');
            $table->dropColumn('type_create_invoice');
            $table->dropColumn('account_code');
            $table->dropForeign(['gl_account_id']);
            $table->dropColumn('gl_account_id');
            $table->dropColumn('period_start_date');
            $table->dropColumn('period_end_date');
        });
    }
}
