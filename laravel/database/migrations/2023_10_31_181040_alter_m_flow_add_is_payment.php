<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_flows', function (Blueprint $table) {
            $table->boolean('is_payment')->default(true)->nullable()->after('payment_date');
            $table->decimal('maximum_fine', 10, 2)->nullable()->after('is_payment');
            $table->text('remark')->nullable()->after('maximum_fine');

            $table->dropForeign(['m_flow_station_id']);
            $table->dropColumn('m_flow_station_id');

            $table->uuid('expressway_id')->nullable()->after('car_id');
            $table->foreign('expressway_id')->references('id')->on('expressways')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m_flows', function (Blueprint $table) {
            $table->dropColumn(['is_payment', 'maximum_fine', 'remark']);
            $table->dropForeign(['expressway_id']);
            $table->dropColumn('expressway_id');
        });
    }
};
