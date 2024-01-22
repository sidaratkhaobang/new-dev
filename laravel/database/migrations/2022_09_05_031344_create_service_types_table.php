<?php

use App\Enums\ServiceTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TransportationTypeEnum;

class CreateServiceTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('service_type', 20)->default(ServiceTypeEnum::SELF_DRIVE);
            $table->tinyInteger('transportation_type')->default(TransportationTypeEnum::CAR);
            /* $table->boolean('can_rental_over_days')->default(false);
            $table->boolean('can_add_stopover')->default(false);
            $table->boolean('can_add_driver')->default(false);
            $table->boolean('can_add_products')->default(false);
            $table->boolean('can_add_transport_goods')->default(false);
            $table->boolean('can_add_passengers')->default(false); */

            $table->status();
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
        Schema::dropIfExists('service_types');
    }
}
