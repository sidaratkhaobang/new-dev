<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigApproveLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_approve_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('config_approve_id');
            $table->integer('seq')->default(1)->nullable();
            $table->uuid('department_id')->nullable();
            $table->boolean('is_all_department')->default(false);
            $table->boolean('is_all_role')->default(false);
            $table->boolean('is_super_user')->default(false);
            $table->uuid('role_id')->nullable();
            $table->uuid('user_id')->nullable();
            $table->userFields();

            $table->foreign('config_approve_id')->references('id')->on('config_approves')->cascadeOnDelete();
            $table->foreign('department_id')->references('id')->on('user_departments')->nullOnDelete();
            $table->foreign('role_id')->references('id')->on('roles')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('config_approve_lines');
    }
}
