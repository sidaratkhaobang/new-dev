<?php

use App\Enums\AmountTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDriverWagesRelationAddAmountType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_wages_relation', function (Blueprint $table) {
            $table->string('amount_type',20)->default(AmountTypeEnum::BAHT);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_wages_relation', function (Blueprint $table) {
            $table->dropColumn(['amount_type']);
        });
    }
}
