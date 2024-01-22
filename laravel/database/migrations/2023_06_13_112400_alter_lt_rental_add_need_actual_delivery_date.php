<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLtRentalAddNeedActualDeliveryDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lt_rentals', function (Blueprint $table) {
            $table->boolean('need_actual_delivery_date')->nullable()->after('phone_user_receive');
            $table->string('delivery_date_remark')->nullable()->after('need_actual_delivery_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lt_rentals', function (Blueprint $table) {
            $table->dropColumn('delivery_date_remark');
            $table->dropColumn('need_actual_delivery_date');
        });
    }
}
