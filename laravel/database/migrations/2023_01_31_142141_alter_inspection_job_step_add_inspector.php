<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInspectionJobStepAddInspector extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inspection_job_steps', function (Blueprint $table) {
            $table->nullableUuidMorphs('inspector');
            $table->string('inspector_fullname')->nullable()->after('inspector_id');
            $table->tinyInteger('dpf_solution')->nullable()->after('oil_quantity');
            $table->integer('mileage')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inspection_job_steps', function (Blueprint $table) {
            //
        });
    }
}
