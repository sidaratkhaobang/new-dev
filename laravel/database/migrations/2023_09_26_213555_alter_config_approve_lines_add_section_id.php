<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterConfigApproveLinesAddSectionId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('config_approve_lines', function (Blueprint $table) {
            $table->uuid('section_id')->nullable()->after('is_all_department');
            $table->boolean('is_all_section')->default(false)->after('section_id');
            $table->uuid('branch_id')->nullable()->after('is_all_section');

            $table->foreign('section_id')->references('id')->on('sections')->nullOnDelete();
            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
        });

        Schema::table('approve_lines', function (Blueprint $table) {
            $table->uuid('section_id')->nullable()->after('is_all_department');
            $table->boolean('is_all_section')->default(false)->after('section_id');
            $table->uuid('branch_id')->nullable()->after('is_all_section');

            $table->foreign('section_id')->references('id')->on('sections')->nullOnDelete();
            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
        });

        Schema::table('inspection_steps', function (Blueprint $table) {
            $table->uuid('inspection_section_id')->nullable()->after('inspection_department_id');

            $table->foreign('inspection_section_id')->references('id')->on('sections')->nullOnDelete();
        });

        Schema::table('inspection_jobs', function (Blueprint $table) {
            $table->uuid('inspection_section_id')->nullable()->after('inspection_department_id');

            $table->foreign('inspection_section_id')->references('id')->on('sections')->nullOnDelete();
        });

        Schema::table('inspection_job_steps', function (Blueprint $table) {
            $table->uuid('inspection_section_id')->nullable()->after('inspection_department_id');

            $table->foreign('inspection_section_id')->references('id')->on('sections')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('config_approve_lines', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropColumn(['section_id']);
            $table->dropColumn(['is_all_section']);
        });

        Schema::table('approve_lines', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropColumn(['section_id']);
            $table->dropColumn(['is_all_section']);
        });

        Schema::table('inspection_steps', function (Blueprint $table) {
            $table->dropForeign(['inspection_section_id']);
            $table->dropColumn(['inspection_section_id']);
        });

        Schema::table('inspection_jobs', function (Blueprint $table) {
            $table->dropForeign(['inspection_section_id']);
            $table->dropColumn(['inspection_section_id']);
        });

        Schema::table('inspection_job_steps', function (Blueprint $table) {
            $table->dropForeign(['inspection_section_id']);
            $table->dropColumn(['inspection_section_id']);
        });
    }
}
