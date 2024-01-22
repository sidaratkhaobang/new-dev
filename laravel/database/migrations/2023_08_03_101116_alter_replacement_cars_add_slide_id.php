<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterReplacementCarsAddSlideId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('replacement_cars', function (Blueprint $table) {
            $table->tinyInteger('is_cust_receive_replace')->nullable()->after('status');
            $table->uuid('slide_id')->nullable()->after('is_cust_receive_replace');
            $table->foreign('slide_id')->references('id')->on('slides')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('replacement_cars', function (Blueprint $table) {
            $table->dropColumn(['is_cust_receive_replace']);
            $table->dropForeign(['slide_id']);
            $table->dropColumn(['slide_id']);
        });
    }
}
