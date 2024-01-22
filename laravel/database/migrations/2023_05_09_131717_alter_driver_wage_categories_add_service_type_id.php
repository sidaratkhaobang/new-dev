<?php

use App\Models\DriverWage;
use App\Models\DriverWageCategory;
use App\Models\ServiceType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDriverWageCategoriesAddServiceTypeId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_wages', function (Blueprint $table) {
            $table->uuid('service_type_id')->nullable()->after('is_special_wage');
            $table->foreign('service_type_id')->references('id')->on('service_types')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_wages', function (Blueprint $table) {
            $table->dropForeign(['service_type_id']);
            $table->dropColumn(['service_type_id']);
        });
    }
}
