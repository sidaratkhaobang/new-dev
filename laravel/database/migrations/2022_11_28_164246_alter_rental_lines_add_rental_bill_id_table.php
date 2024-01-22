<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRentalLinesAddRentalBillIdTable extends Migration
{
    public function up()
    {
        Schema::table('rental_lines', function (Blueprint $table) {
            $table->uuid('rental_bill_id')->nullable()->after('rental_id');
            $table->foreign('rental_bill_id')->references('id')->on('rental_bills')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('rental_lines', function (Blueprint $table) {
            $table->dropColumn('rental_bill_id');
        });
    }
}
