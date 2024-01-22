<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code', 10)->nullable();
            $table->text('address');
            $table->string('tel')->nullable();
            $table->string('fax')->nullable();
            $table->string('tax')->nullable();
            $table->boolean('is_main_data')->default(0);
            $table->unsignedBigInteger('province_id')->nullable();

            $table->status();
            $table->userFields();

            $table->refId();

            $table->foreign('province_id')->references('id')->on('provinces')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
