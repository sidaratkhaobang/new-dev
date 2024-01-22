<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AlterCarsChangeLeasingIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('cars')->update(['leasing_id' => null]);

        Schema::table('cars', function (Blueprint $table) {
            $table->dropForeign(['leasing_id']);

            $table->foreign('leasing_id')->references('id')->on('creditors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
