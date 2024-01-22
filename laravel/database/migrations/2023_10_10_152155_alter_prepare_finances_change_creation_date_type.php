<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPrepareFinancesChangeCreationDateType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prepare_finances', function (Blueprint $table) {
            $table->date('creation_date')->nullable()->change();
            $table->date('billing_date')->nullable()->change();
            $table->date('payment_date')->nullable()->change();
            $table->string('contact', 100)->nullable()->change();
            $table->string('tel', 20)->nullable()->change();
            $table->string('status', 50)->nullable()->change();
            $table->text('remark')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
