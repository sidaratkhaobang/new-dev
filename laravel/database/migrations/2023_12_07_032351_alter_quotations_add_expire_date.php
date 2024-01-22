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
            $table->dateTime('payment_expire_date')->nullable()->after('payment_url');
            $table->string('payment_ref_id', 30)->nullable()->after('payment_date');
            $table->string('payment_ref_id2', 30)->nullable()->after('payment_ref_id');
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
            $table->dropColumn(['payment_expire_date', 'payment_ref_id', 'payment_ref_id2']);
        });
    }
};
