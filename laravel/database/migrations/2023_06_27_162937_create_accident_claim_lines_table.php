<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccidentClaimLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accident_claim_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('accident_id')->nullable();
            $table->uuid('accident_claim_list_id')->nullable();
            $table->string('wound_characteristics')->nullable();
            $table->boolean('is_withdraw_true')->default(1);
            $table->decimal('cost',10,2)->nullable();
            $table->boolean('supplier')->nullable();
            $table->userFields();

            $table->foreign('accident_id')->references('id')->on('accidents')->cascadeOnDelete();
            $table->foreign('accident_claim_list_id')->references('id')->on('claim_lists')->cascadeOnDelete();        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accident_claim_lines');
    }
}
