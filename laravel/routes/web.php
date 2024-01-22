<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Admin\DebugController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ImportCarVendorController;
use App\Http\Controllers\LongTermRentalSpecVendorController;
use App\Http\Controllers\QuickPayCallbackController;
use App\Http\Controllers\SCBBillPaymentController;
use App\Http\Controllers\ReceiptController;

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

Route::get('/', function () {
    return redirect()->route('admin.home');
});

Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('login-check', [LoginController::class, 'login'])->name('auth.check');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/element', function () {
    return view('element');
})->name('element');

Route::resource('import-car-dealers', ImportCarVendorController::class);
Route::get('import-car-dealers/{import_car_dealer}/export-template', [ImportCarVendorController::class, 'export'])->name('import-car-dealers.export-template');

Route::get('/debug-sentry', function () {
    throw new Exception('My first Sentry error!');
});

Route::get('/debug-sentry-2', function () {
    Log::channel('sentry')->error('test cron ' . date('Y-m-d H:i:s'), [
        'time' => date('Y-m-d H:i:s')
    ]);
});

//SCB Bill Payment
Route::post('/callback/scb-billpayment-verify', [SCBBillPaymentController::class, 'verify']);
Route::post('/callback/scb-billpayment-confirm', [SCBBillPaymentController::class, 'confirm']);


Route::resource('long-term-rental-spec-dealers', LongTermRentalSpecVendorController::class);
Route::get('long-term-rental-vendor/specs/edit/{rental}/{dealer}', [LongTermRentalSpecVendorController::class, 'edit'])->name('long-term-rental-vendor.specs.edit');

// Receipt
Route::get('receipt-pdfs/{id}', [ReceiptController::class, 'print'])->name('receipt-pdf');;
