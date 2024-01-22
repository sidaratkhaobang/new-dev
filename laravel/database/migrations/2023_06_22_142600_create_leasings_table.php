<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeasingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::dropIfExists('leasings');
        Schema::create('leasings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 20)->nullable();
            $table->string('name', 100);
            $table->text('address')->nullable();
            $table->string('tel', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('contact_name', 100)->nullable();
            $table->string('contact_tel', 20)->nullable();
            $table->string('contact_email', 100)->nullable();
            $table->status();
            $table->userFields();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leasings');
    }
}
