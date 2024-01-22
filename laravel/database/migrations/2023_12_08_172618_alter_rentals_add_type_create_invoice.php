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
            $table->string('type_create_invoice', 20)->nullable()->after('status');
            $table->smallInteger('billing_start_date')->nullable()->after('type_create_invoice');
            $table->smallInteger('billing_end_date')->nullable()->after('billing_start_date');
            $table->text('remark_billing')->nullable()->after('billing_end_date');
            $table->string('biller_name', 100)->nullable()->after('remark_billing');
            $table->string('biller_tel', 20)->nullable()->after('biller_name');
            $table->string('biller_email', 100)->nullable()->after('biller_tel');
            $table->text('biller_address')->nullable()->after('biller_email');
            $table->unsignedBigInteger('biller_province_id')->nullable()->after('biller_address');
            $table->unsignedBigInteger('biller_district_id')->nullable()->after('biller_province_id');
            $table->unsignedBigInteger('biller_subdistrict_id')->nullable()->after('biller_district_id');

            $table->foreign('biller_province_id')->references('id')->on('provinces')->nullOnDelete();
            $table->foreign('biller_district_id')->references('id')->on('amphures')->nullOnDelete();
            $table->foreign('biller_subdistrict_id')->references('id')->on('districts')->nullOnDelete();
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
            $table->dropForeign(['biller_province_id']);
            $table->dropForeign(['biller_district_id']);
            $table->dropForeign(['biller_subdistrict_id']);

            $table->dropColumn([
                'type_create_invoice',
                'billing_start_date',
                'billing_end_date',
                'remark_billing',
                'biller_name',
                'biller_tel',
                'biller_email',
                'biller_address',
                'biller_province_id',
                'biller_district_id',
                'biller_subdistrict_id',
            ]);
        });
    }
};
