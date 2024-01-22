<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLtRentalsAddDeliverInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lt_rentals', function (Blueprint $table) {
            $table->timestamp('date_delivery')->nullable()->after('reason_delivery');
            $table->text('location_delivery')->nullable()->after('date_delivery');
            $table->string('name_user_receive')->nullable()->after('location_delivery');
            $table->string('phone_user_receive', 20)->nullable()->after('name_user_receive');
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
            $table->dropColumn(['date_delivery', 'location_delivery', 'name_user_receive', 'phone_user_receive']);
        });
    }
}
