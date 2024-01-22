<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLitigationsAddRedNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('litigations', function (Blueprint $table) {
            $table->string('legal_service_provider', 100)->nullable()->after('responsible_person_id');
            $table->decimal('legal_service_fee', 10, 2)->nullable()->after('legal_service_provider');
            $table->text('legal_note')->nullable()->after('legal_service_fee');
            $table->string('location_name', 200)->nullable()->after('court_filing_date');
            $table->string('black_number', 50)->nullable()->after('location_name');
            $table->string('red_number', 50)->nullable()->after('black_number');
            $table->string('inquiry_official_tel', 20)->nullable()->after('inquiry_official');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('litigations', function (Blueprint $table) {
            $table->dropColumn([
                'legal_service_provider', 
                'legal_service_fees',
                'legal_notes',
                'location_name',
                'black_number',
                'red_number',
                'inquiry_official_tel',
            ]);
        });
    }
}
