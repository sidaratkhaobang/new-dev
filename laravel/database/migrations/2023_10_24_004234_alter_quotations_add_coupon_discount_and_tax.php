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
        Schema::table('quotations', function (Blueprint $table) {
            $table->boolean('is_withholding_tax')->default(false)->after('customer_province_id');
            $table->smallInteger('withholding_tax_value')->default(0)->after('is_withholding_tax');
            $table->decimal('withholding_tax', 10, 2)->default(0)->after('vat');
            $table->decimal('coupon_discount', 10, 2)->default(0)->after('discount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['is_withholding_tax', 'withholding_tax_value', 'withholding_tax', 'coupon_discount']);
        });
    }
};
