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
        Schema::table('compensation_negotiations', function (Blueprint $table) {
            $table->string('negotiator', 100)->nullable()->after('type');
            $table->date('report_date')->nullable()->after('negotiator');
            $table->renameColumn('results', 'result');
            $table->renameColumn('channel_results', 'channel_result');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('compensation_negotiations', function (Blueprint $table) {
            $table->dropColumn(['negotiator', 'report_date']);
            $table->renameColumn('result', 'results');
            $table->renameColumn('channel_result', 'channel_results');

        });
    }
};
