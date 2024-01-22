<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRentalLineAddDatetime extends Migration
{
    public function up()
    {
        Schema::table('rental_lines', function (Blueprint $table) {
            $table->dateTime('pickup_date')->nullable()->after('total');
            $table->dateTime('return_date')->nullable()->after('pickup_date');
            $table->uuid('former_car_id')->nullable();
            $table->status();

            $table->foreign('former_car_id')->references('id')->on('cars')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('rental_lines', function (Blueprint $table) {
            $table->dropForeign(['former_car_id']);
            $table->dropColumn([
                'pickup_date',
                'return_date',
                'former_car_id',
                'status',
            ]);
        });
    }
}
