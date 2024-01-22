<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterContractSignersTableAddIsAttorney extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract_signers', function (Blueprint $table) {
            $table->boolean('is_attorney')->default(false)->after('signer_type');
            $table->string('contract_side', 20)->nullable()->after('is_attorney');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract_signers', function (Blueprint $table) {
            $table->dropColumn(['is_attorney', 'contract_side']);
        });
    }
}
