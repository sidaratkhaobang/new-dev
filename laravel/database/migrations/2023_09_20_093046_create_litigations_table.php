<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLitigationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('litigations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no', 20);
            $table->string('title', 100)->nullable();
            $table->string('case', 100)->nullable();
            $table->string('case_type', 50);
            $table->string('tls_type', 50);
            $table->string('accuser_defendant', 100);
            $table->date('incident_date')->nullable();
            $table->string('consultant', 100)->nullable();
            $table->decimal('fund', 10, 2)->nullable();
            $table->uuid('responsible_person_id')->nullable();
            $table->string('location_case', 100)->nullable();
            $table->text('details')->nullable();
            $table->date('request_date')->nullable();
            $table->date('receive_date')->nullable();
            $table->date('court_filing_date')->nullable();
            $table->date('appointment_date')->nullable();
            $table->string('inquiry_official', 100)->nullable();
            $table->integer('age')->nullable();
            $table->text('remark')->nullable();
            $table->date('due_date')->nullable();
            $table->boolean('is_expenses')->nullable();
            $table->string('status', 50)->nullable();
            $table->userFields();

            $table->foreign('responsible_person_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('litigations');
    }
}