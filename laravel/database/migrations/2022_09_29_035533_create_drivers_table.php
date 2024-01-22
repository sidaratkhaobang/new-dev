<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\EmployeeStatusEnum;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code', 10)->nullable();
            $table->string('emp_status', 20)->default(EmployeeStatusEnum::FULL_TIME);
            $table->uuid('position_id')->nullable();
            $table->unsignedBigInteger('province_id')->nullable();
            $table->string('tel', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('citizen_id', 20)->nullable();

            $table->time('start_working_time')->nullable();
            $table->time('end_working_time')->nullable();
            $table->boolean('working_day_mon')->default(false);
            $table->boolean('working_day_tue')->default(false);
            $table->boolean('working_day_wed')->default(false);
            $table->boolean('working_day_thu')->default(false);
            $table->boolean('working_day_fri')->default(false);
            $table->boolean('working_day_sat')->default(false);
            $table->boolean('working_day_sun')->default(false);

            $table->uuid('branch_id')->nullable();

            // wage
            /* $table->decimal('salary')->default(0);
            $table->decimal('working_day_ot')->default(0);
            $table->decimal('holiday_wage')->default(0);
            $table->decimal('holiday_ot')->default(0);
            $table->decimal('trip')->default(0);
            $table->decimal('taxi')->default(0);
            $table->decimal('allowance_upcountry')->default(0);
            $table->decimal('accommodation_upcountry')->default(0);
            $table->decimal('foreign_language')->default(0);
            $table->decimal('extra_driver_license')->default(0);
            $table->decimal('good_service')->default(0);
            $table->decimal('extra_charge_salary')->default(0);
            $table->decimal('extra_charge_ot')->default(0); */

            $table->status();
            $table->userFields();

            $table->refId();

            $table->foreign('position_id')->references('id')->on('positions')->nullOnDelete();
            $table->foreign('province_id')->references('id')->on('provinces')->nullOnDelete();
            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drivers');
    }
}
