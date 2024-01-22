<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrivingSkillsServiceTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driving_skills_service_types', function (Blueprint $table) {
            $table->uuid('driving_skill_id');
            $table->uuid('service_type_id');

            $table->primary(['driving_skill_id', 'service_type_id'], 'driving_skills_service_types_pk');

            $table->foreign('driving_skill_id')->references('id')->on('driving_skills')->cascadeOnDelete();
            $table->foreign('service_type_id')->references('id')->on('service_types')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driving_skills_service_types');
    }
}
