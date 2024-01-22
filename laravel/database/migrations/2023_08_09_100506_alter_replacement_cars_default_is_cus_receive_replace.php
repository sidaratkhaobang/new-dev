<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterReplacementCarsDefaultIsCusReceiveReplace extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('replacement_cars', function (Blueprint $table) {
            $table->boolean('is_cust_receive_replace')->default(0)->change();
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
            // $table->tinyInteger('is_cust_receive_replace')->nullable()->change();
        });
    }
}
