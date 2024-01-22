<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLtRentalLineAddFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lt_rental_lines', function (Blueprint $table) {
            $table->string('remark')->nullable()->after('rental_price');
            $table->boolean('have_accessories')->default(false)->after('remark');
            $table->decimal('purchase_options', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lt_rental_lines', function (Blueprint $table) {
            $table->dropColumn(['remark', 'have_accessories', 'purchase_options']);
        });
    }
}
