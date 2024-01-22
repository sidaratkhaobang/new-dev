<?php

use App\Http\Controllers\QuickPayCallbackController;
use Illuminate\Support\Facades\Route;

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

// 2C2P callback
Route::post('qp/cb/frontend', [QuickPayCallbackController::class, 'frontend'])->name('qp.cb.frontend');
Route::post('qp/cb/backend', [QuickPayCallbackController::class, 'backend'])->name('qp.cb.backend');

