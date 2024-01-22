<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRentalAddWithholdingTax extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->boolean('is_withholding_tax')->default(false)->after('promotion_code_id');
            $table->smallInteger('withholding_tax_value')->default(0)->after('is_withholding_tax');
            $table->decimal('withholding_tax', 10, 2)->default(0)->after('vat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn(['is_withholding_tax', 'withholding_tax_value', 'withholding_tax']);
        });
    }
}
