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
        Schema::table('rentals', function (Blueprint $table) {
            $table->string('customer_tax_no', 20)->nullable()->after('customer_email');
            $table->unsignedBigInteger('customer_district_id')->nullable()->after('customer_province_id');
            $table->unsignedBigInteger('customer_subdistrict_id')->nullable()->after('customer_district_id');
            $table->string('customer_billing_name')->nullable()->after('customer_subdistrict_id');
            $table->text('customer_billing_address')->nullable()->after('customer_billing_name');
            $table->string('customer_billing_tel', 20)->nullable()->after('customer_billing_address');
            $table->string('customer_billing_email')->nullable()->after('customer_billing_tel');
            $table->string('customer_billing_tax_no', 20)->nullable()->after('customer_billing_email');
            $table->unsignedBigInteger('customer_billing_province_id')->nullable()->after('customer_billing_tax_no');
            $table->unsignedBigInteger('customer_billing_district_id')->nullable()->after('customer_billing_province_id');
            $table->unsignedBigInteger('customer_billing_subdistrict_id')->nullable()->after('customer_billing_district_id');
            $table->string('payment_url', 500)->nullable()->after('payment_gateway');
            $table->boolean('check_customer_address')->default(true)->after('customer_billing_subdistrict_id');

            $table->foreign('customer_district_id')->references('id')->on('amphures')->nullOnDelete();
            $table->foreign('customer_subdistrict_id')->references('id')->on('districts')->nullOnDelete();

            $table->foreign('customer_billing_province_id')->references('id')->on('provinces')->nullOnDelete();
            $table->foreign('customer_billing_district_id')->references('id')->on('amphures')->nullOnDelete();
            $table->foreign('customer_billing_subdistrict_id')->references('id')->on('districts')->nullOnDelete();
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
            $table->dropForeign(['customer_billing_subdistrict_id']);
            $table->dropForeign(['customer_billing_district_id']);
            $table->dropForeign(['customer_billing_province_id']);
            $table->dropForeign(['customer_subdistrict_id']);
            $table->dropForeign(['customer_district_id']);

            $table->dropColumn([
                'customer_tax_no',
                'customer_district_id',
                'customer_subdistrict_id',
                'customer_billing_name',
                'customer_billing_address',
                'customer_billing_tel',
                'customer_billing_email',
                'customer_billing_tax_no',
                'customer_billing_province_id',
                'customer_billing_district_id',
                'customer_billing_subdistrict_id',
                'payment_url',
                'check_customer_address',
            ]);
        });
    }
};
