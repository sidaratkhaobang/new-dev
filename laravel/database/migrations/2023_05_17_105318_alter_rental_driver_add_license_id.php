<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRentalDriverAddLicenseId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rental_drivers', function (Blueprint $table) {
            $table->string('license_id', 20)->nullable()->after('citizen_id');
            $table->date('license_exp_date')->nullable()->after('license_id');
            $table->boolean('is_check_dup')->default(false)->after('license_exp_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rental_drivers', function (Blueprint $table) {
            $table->dropColumn(['license_id','license_exp_date','is_check_dup']);
        });
    }
}
