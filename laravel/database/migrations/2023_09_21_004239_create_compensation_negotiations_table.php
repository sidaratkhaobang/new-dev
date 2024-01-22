<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompensationNegotiationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compensation_negotiations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('compensation_id');
            $table->string('type', 50)->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('results', 50)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('channel_results', 50)->nullable();
            $table->string('person', 100)->nullable();
            $table->string('tel', 20)->nullable();
            $table->text('remark')->nullable();
            $table->string('receipt_no', 50)->nullable();
            $table->string('sss_no', 50)->nullable();
            $table->date('negotiation_date')->nullable();
            $table->userFields();

            $table->foreign('compensation_id')->references('id')->on('compensations')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compensation_negotiations');
    }
}
