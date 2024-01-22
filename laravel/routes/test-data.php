<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Test\TestDataController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('checkenv')->group(function () {
    /* Route::get('clear-rentals', [TestDataController::class, 'clearRentals'])->name('debug.clear-rentals');
    Route::get('generate-driving-jobs', [TestDataController::class, 'generateDrivingJobs'])->name('generate-driving-jobs'); */
});
