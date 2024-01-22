<?php

use App\Enums\InspectionTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectionFlowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspection_flows', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('inspection_type')->default(InspectionTypeEnum::SELF_DRIVE);
            $table->boolean('is_need_customer_sign_in')->default(false);
            $table->boolean('is_need_customer_sign_out')->default(false);

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
        Schema::dropIfExists('inspection_flows');
    }
}
