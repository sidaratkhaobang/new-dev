<?php

use App\Enums\LongTermRentalPriceStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLtRentalAddField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lt_rentals', function (Blueprint $table) {
            $table->boolean('check_spec')->default(true)->after('remark');
            $table->text('reject_spec_reason')->nullable()->after('check_spec');
            $table->uuid('lt_rental_type_id')->nullable()->after('worksheet_no');
            $table->uuid('bom_id')->nullable()->after('creditor_id');
            $table->string('rental_price_status', 20)->nullable()->after('comparison_price_status');

            $table->foreign('lt_rental_type_id')->references('id')->on('lt_rental_types')->cascadeOnDelete();
            $table->foreign('bom_id')->references('id')->on('lt_rentals')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lt_rentals', function (Blueprint $table) {
            $table->dropForeign(['lt_rental_type_id']);
            $table->dropForeign(['bom_id']);
            $table->dropColumn(['check_spec', 'reject_spec_reason', 'lt_rental_type_id', 'bom_id', 'rental_price_status']);
        });
    }
}
