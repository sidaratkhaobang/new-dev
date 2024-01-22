<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAccidentsAddDeduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accidents', function (Blueprint $table) {
            $table->dateTime('date_deductible')->nullable()->after('deductible');
            $table->string('doc_deductible',100)->nullable()->after('date_deductible');
            $table->decimal('total_damages',10,2)->nullable()->after('doc_deductible');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accidents', function (Blueprint $table) {
            $table->dropColumn(['date_deductible']);
            $table->dropColumn(['doc_deductible']);
            $table->dropColumn(['total_damages']);
        });
    }
}
