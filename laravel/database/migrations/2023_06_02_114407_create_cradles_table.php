<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCradlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cradles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('cradle_type')->nullable();
            $table->string('cradle_email')->nullable();
            $table->string('cradle_tel')->nullable();
            $table->boolean('emcs')->nullable();
            $table->boolean('is_onsite_service')->nullable();
            $table->text('address')->nullable();
            $table->string('region')->nullable();
            $table->unsignedBigInteger('province')->nullable();
            $table->unsignedBigInteger('district')->nullable();
            $table->unsignedBigInteger('subdistrict')->nullable();
            $table->text('remark')->nullable();
            $table->string('id_line')->nullable();
            $table->string('website')->nullable();
            $table->string('coordinator_name')->nullable();
            $table->string('coordinator_tel')->nullable();
            $table->string('coordinator_email')->nullable();
            $table->string('status')->nullable();
            $table->userFields();

            $table->foreign('province')->references('id')->on('provinces')->cascadeOnDelete();
            $table->foreign('district')->references('id')->on('amphures')->cascadeOnDelete();
            $table->foreign('subdistrict')->references('id')->on('districts')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cradles');
    }
}
