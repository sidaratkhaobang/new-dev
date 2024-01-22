<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGpsHistoricalDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gps_historical_datas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no', 100)->nullable();
            $table->text('purpose')->nullable();
            $table->string('type_file')->nullable();
            $table->string('link')->nullable();
            $table->text('remark')->nullable();
            $table->text('reason')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('gps_historical_datas');
    }
}
