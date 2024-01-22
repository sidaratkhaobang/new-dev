<?php

use App\Models\Branch;
use App\Models\CheckCredits;
use App\Models\CustomerGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_credits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('worksheet_no')->nullable();
            $table->uuid('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on((new Branch())->getTable())->cascadeOnDelete();
            $table->string('customer_code')->nullable();
            $table->string('customer_type')->nullable();
            $table->text('customer_group')->nullable();
            $table->tinyInteger('customer_grade')->nullable();
            $table->string('name')->nullable();
            $table->string('tax_no')->nullable();
            $table->string('prefixname_th')->nullable();
            $table->string('fullname_th')->nullable();
            $table->string('prefixname_en')->nullable();
            $table->string('fullname_en')->nullable();
            $table->string('email')->nullable();
            $table->string('fax', 20)->nullable();
            $table->string('tel', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('status')->nullable();
            $table->decimal('approved_amount', 10, 2)->nullable();
            $table->integer('approved_days')->nullable();
            $table->text('reason')->nullable();
            $table->tinyInteger('is_create_customer')->nullable();
            $table->userFields();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('check_credits');
    }
}
