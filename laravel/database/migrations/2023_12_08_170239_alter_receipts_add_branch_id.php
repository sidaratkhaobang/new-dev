<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->uuid('branch_id')->nullable()->after('id');
            $table->boolean('is_withholding_tax')->default(false)->after('customer_tax_no');
            $table->tinyInteger('withholding_tax_value')->default(0)->after('is_withholding_tax');
            $table->boolean('is_show_wt')->default(false)->after('withholding_tax_value');
            $table->string('payment_medtod', 50)->nullable()->after('parent_id');
            $table->uuid('bank_id')->nullable()->after('payment_medtod');
            $table->string('bank_account_number', 50)->nullable()->after('bank_id');
            $table->string('bank_branch', 200)->nullable()->after('bank_account_number');
            $table->string('credit_note_no', 100)->nullable()->after('bank_branch');
            $table->string('withholding_tax_no', 100)->nullable()->after('credit_note_no');
            $table->date('receipt_date')->nullable()->after('withholding_tax_no');

            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            $table->foreign('bank_id')->references('id')->on('banks')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['bank_id']);

            $table->dropColumn([
                'branch_id',
                'is_withholding_tax',
                'withholding_tax_value',
                'is_show_wt',
                'payment_medtod',
                'bank_id',
                'bank_account_number',
                'bank_branch',
                'credit_note_no',
                'withholding_tax_no',
                'receipt_date'
            ]);
        });
    }
};
