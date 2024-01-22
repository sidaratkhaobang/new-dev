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
        Schema::table('customer_billing_addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('district_id')->nullable()->after('province_id');
            $table->unsignedBigInteger('subdistrict_id')->nullable()->after('district_id');

            $table->foreign('district_id')->references('id')->on('amphures')->nullOnDelete();
            $table->foreign('subdistrict_id')->references('id')->on('districts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_billing_addresses', function (Blueprint $table) {
            $table->dropForeign(['district_id']);
            $table->dropForeign(['subdistrict_id']);

            $table->dropColumn([
                'district_id',
                'subdistrict_id',
            ]);
        });
    }
};
