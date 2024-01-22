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
            $table->unsignedBigInteger('customer_district_id')->nullable()->after('customer_province_id');
            $table->unsignedBigInteger('customer_subdistrict_id')->nullable()->after('customer_district_id');

            $table->string('payment_method', 20)->nullable()->after('edit_count');
            $table->string('payment_gateway', 20)->nullable()->after('payment_method');
            $table->string('payment_url', 500)->nullable()->after('payment_gateway');
            $table->boolean('is_paid')->default(false)->after('payment_url');
            $table->dateTime('payment_date')->nullable()->after('is_paid');
            $table->text('payment_response_desc')->nullable()->after('payment_date');

            $table->foreign('customer_district_id')->references('id')->on('amphures')->nullOnDelete();
            $table->foreign('customer_subdistrict_id')->references('id')->on('districts')->nullOnDelete();
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
            $table->dropForeign(['customer_district_id']);
            $table->dropForeign(['customer_subdistrict_id']);

            $table->dropForeign(['payment_method']);
            $table->dropForeign(['payment_gateway']);
            $table->dropForeign(['payment_url']);
            $table->dropForeign(['is_paid']);
            $table->dropForeign(['payment_date']);
            $table->dropForeign(['payment_response_desc']);

            $table->dropColumn([
                'customer_district_id',
                'customer_subdistrict_id',
            ]);
        });
    }
};
