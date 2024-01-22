<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\CustomerTypeEnum;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('customer_code', 20)->nullable();
            $table->string('debtor_code', 20)->nullable();
            $table->string('account_code', 50)->nullable();
            $table->string('tax_no', 20)->nullable();
            $table->string('customer_type', 20)->default(CustomerTypeEnum::GOVERNMENT);
            $table->tinyInteger('customer_grade')->default(1)->nullable();
            $table->string('prefixname_th')->nullable();
            $table->string('fullname_th')->nullable();
            $table->string('prefixname_en')->nullable();
            $table->string('fullname_en')->nullable();
            $table->unsignedBigInteger('province_id')->nullable();
            $table->text('address')->nullable();
            $table->string('tel', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('fax', 20)->nullable();
            $table->string('email')->nullable();

            $table->uuid('sale_id')->nullable();
            $table->boolean('is_accept_pdpa')->default(false);

            $table->status();
            $table->userFields();

            $table->refId();

            $table->foreign('province_id')->references('id')->on('provinces')->nullOnDelete();
            $table->foreign('sale_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
