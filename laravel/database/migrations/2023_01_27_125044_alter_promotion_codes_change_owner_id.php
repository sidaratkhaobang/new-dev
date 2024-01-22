<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPromotionCodesChangeOwnerId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promotion_codes', function (Blueprint $table) {
            $table->renameColumn('owner_id', 'buyer_id');
        });

        Schema::table('promotion_codes', function (Blueprint $table) {
            $table->dateTime('transfer_date')->nullable()->after('buyer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promotion_codes', function (Blueprint $table) {
            $table->dropColumn('transfer_date');
        });

        Schema::table('promotion_codes', function (Blueprint $table) {
            $table->renameColumn('buyer_id', 'owner_id');
        });
    }
}
