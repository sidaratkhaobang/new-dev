<?php

use App\Enums\LongTermRentalJobType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLtRentalTypesAddJobType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lt_rental_types', function (Blueprint $table) {
            $table->string('job_type', 20)->default(LongTermRentalJobType::OTHER)->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lt_rental_types', function (Blueprint $table) {
            $table->dropColumn(['job_type']);
        });
    }
}
