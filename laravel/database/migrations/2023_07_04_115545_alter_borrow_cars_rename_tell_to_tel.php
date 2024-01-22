<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBorrowCarsRenameTellToTel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('borrow_cars', function (Blueprint $table) {
            $table->renameColumn('tell', 'tel');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('borrow_cars', function (Blueprint $table) {
            $table->renameColumn('tel', 'tell');
        });
    }
}
