<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableAddField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->string('contract_no', 100)->nullable()->after('remark');
            $table->string('receipt_no', 100)->nullable()->after('contract_no');
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->text('keys_address')->nullable()->after('branch_id');
        });

        Schema::table('driving_jobs', function (Blueprint $table) {
            $table->boolean('atk_check')->default(true)->after('is_confirm_wage');
            $table->boolean('alcohol_check')->default(true)->after('atk_check');
            $table->string('alcohol', 20)->nullable()->after('alcohol_check');
            $table->uuid('car_id')->nullable()->after('alcohol');
            $table->string('pick_up_keys', 20)->nullable()->after('car_id');
            $table->text('remark')->nullable()->after('pick_up_keys');

            $table->dateTime('estimate_prepare_date')->nullable()->after('actual_end_date');
            $table->dateTime('estimate_start_date')->nullable()->after('estimate_prepare_date');
            $table->dateTime('estimate_end_job_date')->nullable()->after('estimate_start_date');
            $table->dateTime('estimate_arrive_date')->nullable()->after('estimate_end_job_date');
            $table->dateTime('estimate_end_date')->nullable()->after('estimate_arrive_date');
            $table->dateTime('actual_prepare_date')->nullable()->after('estimate_end_date');
            $table->dateTime('actual_end_job_date')->nullable()->after('actual_prepare_date');
            $table->dateTime('actual_arrive_date')->nullable()->after('actual_end_job_date');


            $table->foreign('car_id')->references('id')->on('cars')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rentals', function (Blueprint $table) {
            //
        });

        Schema::table('cars', function (Blueprint $table) {
            //
        });

        Schema::table('driving_jobs', function (Blueprint $table) {
            //
        });
    }
}
