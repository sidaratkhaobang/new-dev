<?php

use App\Enums\InspectionFormEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectionFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspection_forms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('form_type', 20)->nullable();

            $table->boolean('is_standard')->default(false);

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
        Schema::dropIfExists('inspection_forms');
    }
}
