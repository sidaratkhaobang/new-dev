<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CarPark\V1\CarController;
use App\Http\Controllers\API\CarPark\V1\DriverController;
use App\Http\Controllers\API\CarPark\V1\DrivingJobController;
use App\Http\Controllers\API\CarPark\V1\RentalController;
use App\Http\Controllers\API\CarParkLogAPIController;

Route::post('auth/login', [AuthController::class, 'loginUser']);

Route::middleware(['auth:sanctum', 'abilities:car-park'])->group(function () {
    Route::post('check-abilities', function (Request $request) {
        return $request->user();
    });

    Route::post('/car-park-log', [CarParkLogAPIController::class, 'carParkLog']);

    Route::get('drivers', [DriverController::class, 'index']);
    Route::get('drivers/{id}', [DriverController::class, 'read']);

    Route::get('cars', [CarController::class, 'index']);
    Route::get('cars/{id}', [CarController::class, 'read']);

    Route::get('driving-jobs', [DrivingJobController::class, 'index']);
    Route::get('driving-jobs/{id}', [DrivingJobController::class, 'read']);

    Route::get('rentals', [RentalController::class, 'index']);
    Route::get('rentals/{id}', [RentalController::class, 'read']);
});
