<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApproveLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approve_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('approve_id');
            $table->integer('seq')->nullable();
            $table->uuid('department_id')->nullable();
            $table->boolean('is_all_department')->nullable();
            $table->uuid('role_id')->nullable();
            $table->boolean('is_all_role')->nullable();
            $table->uuid('user_id')->nullable();
            $table->boolean('is_super_user')->nullable(); 
            $table->boolean('is_pass')->nullable(); 
            $table->userFields();

            $table->foreign('approve_id')->references('id')->on('approves')->cascadeOnDelete();
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
        Schema::dropIfExists('approve_lines');
    }
}

