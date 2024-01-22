<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersAndRolesAddSectionId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('department_id')->nullable()->after('user_department_id');
            $table->uuid('section_id')->nullable()->after('department_id');

            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
            $table->foreign('section_id')->references('id')->on('sections')->nullOnDelete();
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->uuid('department_id')->nullable()->after('name');
            $table->uuid('section_id')->nullable()->after('department_id');

            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
            $table->foreign('section_id')->references('id')->on('sections')->nullOnDelete();
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
