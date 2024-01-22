<?php

use App\Http\Controllers\API\ExpenseCashController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CarParkLogAPIController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BranchController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\CarBrandController;
use App\Http\Controllers\API\CarTypeController;
use App\Http\Controllers\API\CarClassController;
use App\Http\Controllers\API\CarController;
use App\Http\Controllers\API\CustomerBillingAddressController;
use App\Http\Controllers\API\CustomerDriverController;
use App\Http\Controllers\API\DriverController;
use App\Http\Controllers\API\PositionController;
use App\Http\Controllers\API\DrivingJobController;
use App\Http\Controllers\API\ServiceTypeController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ProvinceController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\LocationGroupController;
use App\Http\Controllers\API\PDPAManagementController;
use App\Http\Controllers\API\PromotionController;
use App\Http\Controllers\API\PromotionCodeController;
use App\Http\Controllers\API\RentalCategoryController;
use App\Http\Controllers\API\RentalServiceController;
use App\Http\Controllers\API\ShortTermRentalBillController;
use App\Http\Controllers\API\ShortTermRentalController;
use App\Http\Controllers\API\InspectionJobController;
use App\Http\Controllers\API\InspectionJobStepController;
use App\Models\RentalCategory;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->post('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);

Route::middleware(['auth:sanctum', 'abilities:customer-application'])->group(function () {
    Route::post('check-abilities', function (Request $request) {
        return $request->user();
    });

    // Branch
    Route::get('/branches', [BranchController::class, 'index']);
    Route::get('/branches/{id}', [BranchController::class, 'read']);

    // Car
    Route::get('/cars', [CarController::class, 'index']);
    Route::get('/cars/{id}', [CarController::class, 'read']);

    // Car Brand
    Route::get('/car-brands', [CarBrandController::class, 'index']);
    Route::get('/car-brands/{id}', [CarBrandController::class, 'read']);

    // Car Type
    Route::get('/car-types', [CarTypeController::class, 'index']);
    Route::get('/car-types/{id}', [CarTypeController::class, 'read']);

    // Car Class
    Route::get('/car-classes', [CarClassController::class, 'index']);
    Route::get('/car-classes/{id}', [CarClassController::class, 'read']);

    // Customer
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/{id}', [CustomerController::class, 'read']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::post('/customers/consent', [CustomerController::class, 'consent']);
    Route::put('/customers', [CustomerController::class, 'update']);
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);

    // Customer Billing Address
    Route::get('/customer/{customer_id}/billing-addresses', [CustomerBillingAddressController::class, 'index']);
    Route::get('/customer/{customer_id}/billing-addresses/{id}', [CustomerBillingAddressController::class, 'read']);
    Route::post('/customer/{customer_id}/billing-addresses', [CustomerBillingAddressController::class, 'store']);
    Route::put('/customer/{customer_id}/billing-addresses', [CustomerBillingAddressController::class, 'update']);
    Route::delete('/customer/{customer_id}/billing-addresses/{id}', [CustomerBillingAddressController::class, 'destroy']);

    // Customer Driver
    Route::get('/customer/{customer_id}/drivers', [CustomerDriverController::class, 'index']);
    Route::get('/customer/{customer_id}/drivers/{id}', [CustomerDriverController::class, 'read']);
    Route::post('/customer/{customer_id}/drivers', [CustomerDriverController::class, 'store']);
    Route::put('/customer/{customer_id}/drivers', [CustomerDriverController::class, 'update']);
    Route::delete('/customer/{customer_id}/drivers/{id}', [CustomerDriverController::class, 'destroy']);

    // Location
    Route::get('/locations', [LocationController::class, 'index']);
    Route::get('/locations/{id}', [LocationController::class, 'read']);

    // Location Group
    Route::get('/location-groups', [LocationGroupController::class, 'index']);
    Route::get('/location-groups/{id}', [LocationGroupController::class, 'read']);

    // PDPA
    Route::get('/pdpa', [PDPAManagementController::class, 'index']);
    Route::get('/pdpa/type/{consent_type}', [PDPAManagementController::class, 'indexByType']);
    Route::get('/pdpa/{id}', [PDPAManagementController::class, 'read']);

    // Product
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'read']);

    // Province
    Route::get('/provinces', [ProvinceController::class, 'index']);

    // Service Type
    Route::get('/service-types', [ServiceTypeController::class, 'index']);
    Route::get('/service-types/{id}', [ServiceTypeController::class, 'read']);

    // Rental category
    Route::get('/rental-categories', [RentalCategoryController::class, 'index']);
    Route::get('/rental-categories/{id}', [RentalCategoryController::class, 'read']);

    // Promotion
    Route::get('/promotions', [PromotionController::class, 'index']);
    Route::get('/promotions/{id}', [PromotionController::class, 'read']);

    // Promotion Code
    Route::get('/promotion/{promotion_id}/codes', [PromotionCodeController::class, 'index']);
    Route::get('/promotion/{promotion_id}/codes/{id}', [PromotionCodeController::class, 'read']);
    Route::post('/promotion-code/buy', [PromotionCodeController::class, 'buy']);
    Route::post('/promotion-code/transfer', [PromotionCodeController::class, 'transfer']);

    // Position
    Route::get('/positions', [PositionController::class, 'index']);
    Route::get('/positions/{id}', [PositionController::class, 'read']);

    // Driver
    Route::get('/drivers', [DriverController::class, 'index']);
    Route::get('/drivers/{id}', [DriverController::class, 'read']);
    Route::post('/drivers', [DriverController::class, 'store']);
    Route::put('/drivers', [DriverController::class, 'update']);
    Route::delete('/drivers/{id}', [DriverController::class, 'destroy']);

    // Driving Job
    Route::get('/driving-jobs', [DrivingJobController::class, 'index']);
    Route::get('/driving-jobs/job-list', [DrivingJobController::class, 'getJobList']);
    Route::get('/driving-jobs/status-list', [DrivingJobController::class, 'getStatusList']);
    Route::post('/driving-jobs/start-job', [DrivingJobController::class, 'startJob']);
    Route::post('/driving-jobs/end-job', [DrivingJobController::class, 'endJob']);
    Route::post('/driving-jobs/rented-job', [DrivingJobController::class, 'rentedJob']);
    Route::post('/driving-jobs/arrived', [DrivingJobController::class, 'arrived']);
    Route::post('/driving-jobs/checkin', [DrivingJobController::class, 'checkin']);
    Route::get('/driving-jobs/{id}/status', [DrivingJobController::class, 'getStatus']);
    Route::get('/driving-jobs/{id}/rental-status', [DrivingJobController::class, 'getRentalStatus']);
    Route::get('/driving-jobs/{id}/rental-origin', [DrivingJobController::class, 'getRentalOrigin']);
    Route::get('/driving-jobs/{id}/rental-car-classes', [DrivingJobController::class, 'getRentalCarClasses']);
    Route::get('/driving-jobs/{id}/rental-cars', [DrivingJobController::class, 'getRentalCars']);
    Route::get('/driving-jobs/{id}/zone', [DrivingJobController::class, 'getZone']);
    Route::get('/driving-jobs/{id}/qr', [DrivingJobController::class, 'qr']);
    Route::get('/driving-jobs/{id}', [DrivingJobController::class, 'read']);
    Route::put('/driving-jobs', [DrivingJobController::class, 'update']);

    // Rental Service
    Route::get('/rental-service/available-pickup-date', [RentalServiceController::class, 'availablePickupDate']);
    Route::get('/rental-service/available-return-date', [RentalServiceController::class, 'availableReturnDate']);
    Route::get('/rental-service/available-pickup-time', [RentalServiceController::class, 'availablePickupTime']);
    Route::get('/rental-service/available-return-time', [RentalServiceController::class, 'availableReturnTime']);
    Route::get('/rental-service/available-cars', [RentalServiceController::class, 'availableCars']);

    //Short Term Rental
    Route::get('/rentals', [ShortTermRentalController::class, 'index']);
    Route::post('/rentals', [ShortTermRentalController::class, 'booking']);
    Route::put('/rentals', [ShortTermRentalController::class, 'update']);
    Route::post('/rentals/check-driver', [ShortTermRentalController::class, 'checkDriver']);
    Route::post('/rentals/check-in', [ShortTermRentalController::class, 'rentalCheckIn']);
    Route::post('/rentals/update-delivery-success', [ShortTermRentalController::class, 'updateDeliverySuccess']);
    Route::get('/rentals/{id}', [ShortTermRentalController::class, 'read']);
    Route::get('/rental-bills', [ShortTermRentalBillController::class, 'index']);
    Route::get('/rental-bills/{id}', [ShortTermRentalBillController::class, 'read']);
    Route::post('/rental-bills', [ShortTermRentalBillController::class, 'store']);
    Route::put('/rental-bills', [ShortTermRentalBillController::class, 'update']);
    // Route::get('/rentals/cars', [ShortTermRentalController::class, 'getAvailbleCarList']);

    // Inspection Job
    Route::get('/inspection-jobs', [InspectionJobController::class, 'index']);
    Route::get('/inspection-jobs/{id}', [InspectionJobController::class, 'read']);
    Route::post('/inspection-jobs/sign', [InspectionJobController::class, 'sign']);

    // Inspection Job Step
    Route::get('/inspection-job-steps', [InspectionJobStepController::class, 'index']);
    Route::get('/inspection-job-steps/{id}', [InspectionJobStepController::class, 'read']);
    Route::post('/inspection-job-steps', [InspectionJobStepController::class, 'update']);

    // Expense Petty Cash
    Route::get('/expense-cashes', [ExpenseCashController::class, 'index']);
    Route::get('/expense-cashes/{id}', [ExpenseCashController::class, 'read']);
    Route::post('/expense-cashes', [ExpenseCashController::class, 'store']);
    Route::delete('/expense-cashes/{id}', [ExpenseCashController::class, 'destroy']);
    Route::get('/expense-types', [ExpenseCashController::class, 'getExpenseTypeList']);
});
