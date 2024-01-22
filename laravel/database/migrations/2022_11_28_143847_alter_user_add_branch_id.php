<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserAddBranchId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('branch_id')->nullable()->after('user_department_id');

            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->string('ref_1', 20)->nullable()->after('customer_province_id');
            $table->string('ref_2', 20)->nullable()->after('ref_1');
            $table->uuid('rental_bill_id')->nullable()->after('reference_id');

            $table->foreign('rental_bill_id')->references('id')->on('rental_bills')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });

        Schema::table('quotations', function (Blueprint $table) {
            //
        });
    }
}
