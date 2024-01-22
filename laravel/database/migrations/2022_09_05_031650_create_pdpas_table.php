<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\ConsentType;

class CreatePdpasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdpas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('version', 10)->nullable();
            $table->string('consent_type', 20)->default(ConsentType::PRIVACY);
            $table->longText('description_th')->nullable();
            $table->longText('description_en')->nullable();

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
        Schema::dropIfExists('pdpas');
    }
}
