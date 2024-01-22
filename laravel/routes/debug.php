<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DebugController;

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

Route::get('promotions', [DebugController::class, 'promotions'])->name('debug.promotions');
Route::get('soap', [DebugController::class, 'soap'])->name('debug.soap');
Route::get('soap2', [DebugController::class, 'soap2'])->name('debug.soap2');
Route::get('soap3', [DebugController::class, 'soap3'])->name('debug.soap3');
Route::get('soap4', [DebugController::class, 'soap4'])->name('debug.soap4');
Route::get('soap5', [DebugController::class, 'soap5'])->name('debug.soap5');
Route::get('quickpay', [DebugController::class, 'quickpay'])->name('debug.quickpay');
Route::get('quickpay-check', [DebugController::class, 'quickpayCheck'])->name('debug.quickpay-check');
Route::get('upload', [DebugController::class, 'upload'])->name('debug.upload');
Route::post('upload-check', [DebugController::class, 'uploadCheck'])->name('debug.upload-check');
Route::get('upload-test', [DebugController::class, 'uploadTest'])->name('debug.upload-test');
Route::get('fake-car', [DebugController::class, 'getFakeCars'])->name('debug.fake-car');
Route::get('fake-replacement-car', [DebugController::class, 'getFakeReplacementCars'])->name('debug.fake-replacment-car');
Route::get('scb-payment', [DebugController::class, 'scbPayment'])->name('debug.scb-payment');
Route::get('car-park', [DebugController::class, 'carPark'])->name('debug.car-park');
Route::get('sap', [DebugController::class, 'sap'])->name('debug.sap');
Route::get('sap2', [DebugController::class, 'sap2'])->name('debug.sap2');
Route::get('date', [DebugController::class, 'date'])->name('debug.date');
Route::get('notification', [DebugController::class, 'notification'])->name('debug.notification');
Route::get('notification-alert', [DebugController::class, 'notificationAlert'])->name('debug.notification-alert');
Route::get('design-system', [DebugController::class, 'designSystem'])->name('debug.design-system');
Route::get('env', [DebugController::class, 'env'])->name('debug.env');
Route::get('abort-403', [DebugController::class, 'abort403'])->name('debug.abort-403');
Route::get('abort-404', [DebugController::class, 'abort404'])->name('debug.abort-404');
Route::get('abort-500', [DebugController::class, 'abort500'])->name('debug.abort-500');
Route::get('get-image', [DebugController::class, 'getImage'])->name('debug.get-image');
Route::get('api-gps', [DebugController::class, 'apiGPS'])->name('debug.api-gps');
Route::get('gantt-chart', [DebugController::class, 'ganttChart'])->name('debug.gantt-chart');
Route::get('worksheetno', [DebugController::class, 'worksheetno'])->name('debug.worksheetno');
Route::get('calculate-rental', [DebugController::class, 'calculateRental'])->name('debug.calculate-rentals');
Route::get('rental', [DebugController::class, 'rental'])->name('debug.rental');
