<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRentalProductTransportsAddProductType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rental_product_transports', function (Blueprint $table) {
            $table->string('product_type', 50)->nullable()->after('transfer_type');
            $table->string('column_1', 100)->nullable()->after('product_type');
            $table->string('column_2', 100)->nullable()->after('column_1');
            $table->string('column_3', 100)->nullable()->after('column_2');
            $table->string('column_4', 100)->nullable()->after('column_3');
            $table->string('column_5', 100)->nullable()->after('column_4');
            $table->string('column_6', 100)->nullable()->after('column_5');
            $table->string('column_7', 100)->nullable()->after('column_6');
            $table->string('column_8', 100)->nullable()->after('column_7');
            $table->string('column_9', 100)->nullable()->after('column_8');
            $table->string('column_10', 100)->nullable()->after('column_9');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rental_product_transports', function (Blueprint $table) {
            $table->dropColumn(['product_type']);
            $table->dropColumn(['column_1']);
            $table->dropColumn(['column_2']);
            $table->dropColumn(['column_3']);
            $table->dropColumn(['column_4']);
            $table->dropColumn(['column_5']);
            $table->dropColumn(['column_6']);
            $table->dropColumn(['column_7']);
            $table->dropColumn(['column_8']);
            $table->dropColumn(['column_9']);
            $table->dropColumn(['column_10']);
        });
    }
}
