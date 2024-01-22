<?php

use App\Events\TestNotification;
use App\Http\Controllers\Admin;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\HomeController;
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
//Home
Route::get('home', [HomeController::class, 'index'])->name('home');

// Route::get('/test-event', function () {
//     return view('test-event');
// });

// Route::post('/test-event', function () {
//     $name = request()->name;
//     event(new TestNotification($name));
//     return view('test-event');
// });

Route::get('debug', [DebugController::class, 'index'])->name('debug');
Route::get('export-data-dictionary', [DebugController::class, 'exportDataDictionary'])->name('export-data-dictionary');
Route::get('design-system', [DebugController::class, 'designSystem'])->name('design-system');


//master data Car
Route::resource('accessories', Admin\AccessorieController::class);
Route::resource('car-batteries', Admin\CarBatteryController::class);
Route::resource('car-brands', Admin\CarBrandController::class);
Route::resource('car-colors', Admin\CarColorController::class);
Route::resource('car-categories', Admin\CarCategoryController::class);
Route::resource('car-classes', Admin\CarClassController::class);
Route::resource('car-groups', Admin\CarGroupController::class);
Route::resource('car-tires', Admin\CarTireController::class);
//Route::get('car-types/default-car-group', [Admin\CarTypeController::class, 'getDefaultCarGroup'])->name('car-types.default-car-group');
Route::resource('car-types', Admin\CarTypeController::class);
Route::resource('car-wipers', Admin\CarWiperController::class);
Route::resource('creditors', Admin\CreditorController::class);
Route::resource('locations', Admin\LocationController::class);
Route::resource('location-groups', Admin\LocationGroupController::class);
Route::resource('service-types', Admin\ServiceTypeController::class);
Route::resource('branches', Admin\BranchController::class);
Route::resource('product-additionals', Admin\ProductAdditionalController::class);
Route::resource('products', Admin\ProductController::class);
Route::resource('product-prices', Admin\ProductPriceController::class);
Route::resource('rental-categories', Admin\RentalCategoryController::class);
Route::resource('car-rental-categories', Admin\CarRentalCategoryController::class);
Route::resource('car-service-types', Admin\CarServiceTypeController::class);
Route::resource('driving-skills', Admin\DrivingSkillController::class);
Route::resource('driver-wage-categories', Admin\DriverWageCategoryController::class);
Route::resource('driver-wages', Admin\DriverWageController::class);
Route::resource('positions', Admin\PositionController::class);
Route::resource('auction-reject-reasons', Admin\AuctionRejectReasonController::class);
Route::resource('sap-interfaces', Admin\SAPInterfaceController::class);

// Call Center Follow Up Repair
Route::resource('call-center-follow-up-repairs', Admin\CallCenterFollowUpRepairController::class);


// Accident Follow Up Repair
Route::resource('accident-follow-up-repairs', Admin\AccidentFollowUpRepairController::class);

// Accident inform sheet
Route::post('accident-inform-sheets/save-replacment-accident', [Admin\AccidentInformSheetController::class, 'createCarReplacementEdit'])->name('accident-inform-sheets.save-replacment-accident');
Route::post('accident-inform-sheets/save-slide-accident', [Admin\AccidentInformSheetController::class, 'saveSlideAccident'])->name('accident-inform-sheets.save-slide-accident');
Route::post('accident-inform-sheets/claim/store', [Admin\AccidentInformSheetController::class, 'storeEditClaim'])->name('accident-inform-sheets.store-edit-claim');
Route::get('accident-inform-sheets/{accident_inform_sheet}/show-claim', [Admin\AccidentInformSheetController::class, 'showClaim'])->name('accident-inform-sheets.show-claim');
Route::get('accident-inform-sheets/{accident_inform_sheet}/edit-claim', [Admin\AccidentInformSheetController::class, 'editClaim'])->name('accident-inform-sheets.edit-claim');
Route::post('accident-inform-sheets/store', [Admin\AccidentInformSheetController::class, 'storeEditAccident'])->name('accident-inform-sheets.store-edit-accident');
Route::get('accident-inform-sheets/default-car-license', [Admin\AccidentInformSheetController::class, 'getDefaultCarByLicensePlate'])->name('accident-inform-sheets.default-car-license');
Route::resource('accident-inform-sheets', Admin\AccidentInformSheetController::class);

// Accident Inform
Route::post('accident-informs/save-replacment-accident', [Admin\AccidentInformController::class, 'createCarReplacementEdit'])->name('accident-informs.save-replacment-accident');
Route::post('accident-informs/save-slide-accident', [Admin\AccidentInformController::class, 'saveSlideAccident'])->name('accident-informs.save-slide-accident');
Route::post('accident-informs/claim/store', [Admin\AccidentInformController::class, 'storeEditClaim'])->name('accident-informs.store-edit-claim');
Route::get('accident-informs/{accident_inform}/show-claim', [Admin\AccidentInformController::class, 'showClaim'])->name('accident-informs.show-claim');
Route::get('accident-informs/{accident_inform}/edit-claim', [Admin\AccidentInformController::class, 'editClaim'])->name('accident-informs.edit-claim');
Route::post('accident-informs/store', [Admin\AccidentInformController::class, 'storeEditAccident'])->name('accident-informs.store-edit-accident');
Route::get('accident-informs/default-car-license', [Admin\AccidentInformController::class, 'getDefaultCarByLicensePlate'])->name('accident-informs.default-car-license');
Route::resource('accident-informs', Admin\AccidentInformController::class);

// Accident Order
Route::get('accident-orders/accident-order-pdf', [Admin\AccidentOrderController::class, 'printAccidentOrderPdf'])->name('accident-orders.accident-order-pdf');
Route::post('accident-orders/save-replacment-accident', [Admin\AccidentOrderController::class, 'createCarReplacementEdit'])->name('accident-orders.save-replacment-accident');
Route::get('accident-orders/default-user', [Admin\AccidentOrderController::class, 'getDefaultUser'])->name('accident-orders.default-user');
Route::get('accident-orders/default-insurer', [Admin\AccidentOrderController::class, 'getDefaultInsurer'])->name('accident-orders.default-insurer');
Route::get('accident-orders/send-mail', [Admin\AccidentOrderController::class, 'sendMail'])->name('accident-orders.send-mail');
Route::get('accident-orders/{accident_order}/edit-repair-price', [Admin\AccidentOrderController::class, 'editRepairPrice'])->name('accident-orders.edit-repair-price');
Route::get('accident-orders/{accident_order}/show-repair-price', [Admin\AccidentOrderController::class, 'showRepairPrice'])->name('accident-orders.show-repair-price');
Route::get('accident-orders/{accident_order}/edit-claim', [Admin\AccidentOrderController::class, 'editClaim'])->name('accident-orders.edit-claim');
Route::get('accident-orders/{accident_order}/show-claim', [Admin\AccidentOrderController::class, 'showClaim'])->name('accident-orders.show-claim');
Route::post('accident-orders/repair-price/store', [Admin\AccidentOrderController::class, 'storeEditRepairPrice'])->name('accident-orders.store-edit-repair-price');
Route::post('accident-orders/accident/store', [Admin\AccidentOrderController::class, 'storeEditAccident'])->name('accident-orders.store-edit-accident');
Route::post('accident-orders/claim/store', [Admin\AccidentOrderController::class, 'storeEditClaim'])->name('accident-orders.store-edit-claim');
Route::get('accident-orders/get-accident-list', [Admin\AccidentOrderController::class, 'getAccidentList'])->name('accident-orders.get-accident-list');
Route::get('accident-orders/data-car', [Admin\AccidentOrderController::class, 'getDataCar'])->name('accident-orders.data-car');
Route::get('accident-orders/data-car-accident', [Admin\AccidentOrderController::class, 'getDataCarAccident'])->name('accident-orders.data-car-accident');
Route::get('accident-orders/default-car-id', [Admin\AccidentOrderController::class, 'getDefaultCarID'])->name('accident-orders.default-car-id');
Route::resource('accident-orders', Admin\AccidentOrderController::class);

// Accident Order Approve
Route::get('accident-order-approves/{accident_order_approve}/show-claim', [Admin\AccidentOrderApproveController::class, 'showClaim'])->name('accident-order-approves.show-claim');
Route::post('accident-order-approves/update-status', [Admin\AccidentOrderApproveController::class, 'updateAccidentRepairStatus'])->name('accident-order-approves.update-status');
Route::resource('accident-order-approves', Admin\AccidentOrderApproveController::class);

// Accident Order Sheet Approve
Route::post('accident-order-sheet-approves/update-status', [Admin\AccidentOrderSheetApproveController::class, 'updateAccidentRepairStatus'])->name('accident-order-sheet-approves.update-status');
Route::resource('accident-order-sheet-approves', Admin\AccidentOrderSheetApproveController::class);

// Accident Order Sheet TTL Approve
Route::post('accident-order-sheet-ttl-approves/update-status', [Admin\AccidentOrderSheetTTLApproveController::class, 'updateAccidentRepairStatus'])->name('accident-order-sheet-ttl-approves.update-status');
Route::resource('accident-order-sheet-ttl-approves', Admin\AccidentOrderSheetTTLApproveController::class);

Route::get('garages/zip-code', [Admin\GarageController::class, 'getZipCode'])->name('garages.zip-code');
Route::resource('garages', Admin\GarageController::class);

//master data Customer
Route::resource('customer-groups', Admin\CustomerGroupController::class);
Route::resource('customers', Admin\CustomerController::class);

//master data gl account
Route::resource('general-ledger-accounts', Admin\GeneralLedgerAccountController::class);

//master data Promotion
Route::get('promotions/select-type', [Admin\PromotionController::class, 'selectType'])->name('promotions.select-type');
Route::post('promotions/create-promotion', [Admin\PromotionController::class, 'createPromotion'])->name('promotions.create-promotion');
Route::resource('promotions', Admin\PromotionController::class);
Route::post('promotion-codes/promotion-code-update', [Admin\PromotionCodeController::class, 'updatePromotionCode'])->name('promotion-codes.promotion-code-update');
Route::resource('promotion-codes', Admin\PromotionCodeController::class);

//master data Employee
Route::get('drivers/default-driver-wage', [Admin\DriverController::class, 'getDefaultDriverWage'])->name('drivers.default-driver-wage');
Route::resource('drivers', Admin\DriverController::class);
Route::get('driving-jobs/default-car-license', [Admin\DrivingJobController::class, 'getDefaultCarByLicensePlate'])->name('driving-jobs.default-car-license');
Route::get('driving-jobs/default-service-type-rental', [Admin\DrivingJobController::class, 'getDefaultServiceTypeRental'])->name('driving-jobs.default-service-type-rental');
Route::get('driving-jobs/default-driver-wage-job', [Admin\DrivingJobController::class, 'getDefaultDriverWageJob'])->name('driving-jobs.default-driver-wage-job');
Route::post('driving-jobs/update-status', [Admin\DrivingJobController::class, 'updateStatusJob'])->name('driving-jobs.update-status');
Route::get('driving-jobs/calendar-ajax', [Admin\DrivingJobController::class, 'getCalendar'])->name('driving-jobs.calendar-ajax');
Route::get('driving-jobs/calendar', [Admin\DrivingJobController::class, 'showCalendar'])->name('driving-jobs.calendar');
Route::resource('driving-jobs', Admin\DrivingJobController::class);

Route::get('driver-report/{driver_id}/{driving_id}/edit', [Admin\DriverReportController::class, 'edit'])->name('driver-report-wage.edit');
Route::get('driver-report/{driver_id}/{driving_id}/show', [Admin\DriverReportController::class, 'show'])->name('driver-report-wage.show');
Route::resource('driver-report', Admin\DriverReportController::class);

//User
Route::resource('users', Admin\UserController::class);
Route::resource('departments', Admin\DepartmentController::class);
Route::resource('sections', Admin\SectionController::class);
Route::resource('roles', Admin\RoleController::class);
Route::resource('permissions', Admin\PermissionController::class);

// Config Approve
/* Route::get('config-approves/get-role', [Admin\ConfigApproveController::class, 'getRole'])->name('config-approves.get-role'); */
Route::post('config-approves/add-row', [Admin\ConfigApproveController::class, 'addRow'])->name('config-approves.add-row');
Route::get('config-approves/index-branch', [Admin\ConfigApproveController::class, 'indexBranch'])->name('config-approves.index-branch');
Route::resource('config-approves', Admin\ConfigApproveController::class);

// Car Inspection
Route::get('car-inspections/copy-form', [Admin\InspectionController::class, 'copyForm'])->name('car-inspections.copyForm');
Route::resource('car-inspections', Admin\InspectionController::class);

// Inspection Job
Route::post('inspection-jobs/get-data-inspection-type', [Admin\InspectionJobController::class, 'getDataInspectionType'])->name('inspection-jobs.get-data-inspection-type');
Route::get('inspection-jobs/default-car', [Admin\InspectionJobController::class, 'getDefaultCar'])->name('inspection-jobs.default-car');
Route::get('inspection-jobs/default-car-license', [Admin\InspectionJobController::class, 'getDefaultCarByLicensePlate'])->name('inspection-jobs.default-car-license');
Route::get('inspection-jobs/worksheet-no', [Admin\InspectionJobController::class, 'getWorksheet'])->name('inspection-jobs.worksheet-no');
Route::get('inspection-jobs/select-option-car', [Admin\InspectionJobController::class, 'getSelectOptionCars'])->name('inspection-jobs.select-option-car');
Route::resource('inspection-jobs', Admin\InspectionJobController::class);


// Inspection Job Step
Route::post('inspection-job-steps/get-data-inspection-type', [Admin\InspectionJobStepController::class, 'getDataInspectionType'])->name('inspection-job-steps.get-data-inspection-type');
Route::get('inspection-job-steps/default-car', [Admin\InspectionJobStepController::class, 'getDefaultCar'])->name('inspection-job-steps.default-car');
Route::resource('inspection-job-steps', Admin\InspectionJobStepController::class);

// Inspection Car Detail Form
Route::get('inspection-job-step-forms/pdf', [Admin\InspectionJobStepFormController::class, 'printPdf'])->name('inspection-job-step-forms.pdf');
Route::resource('inspection-job-step-forms', Admin\InspectionJobStepFormController::class);

// Car Inspection Type
Route::get('car-inspections/copy-form', [Admin\InspectionController::class, 'copyForm'])->name('car-inspections.copyForm');
Route::resource('car-inspection-types', Admin\InspectionTypeController::class);

//Operation
Route::resource('operations', Admin\OperationController::class);

// Install Equipment
Route::get('install-equipments/accessory-detail', [Admin\InstallEquipmentController::class, 'getAccessoryDetail'])->name('install-equipments.accessory-detail');
Route::get('install-equipments/accessory-car-list', [Admin\InstallEquipmentController::class, 'getAccessoriesPOCarList'])->name('install-equipments.accessory-car-list');
Route::get('install-equipments/bom-accessories', [Admin\InstallEquipmentController::class, 'getBOMAcessories'])->name('install-equipments.bom-accessories');
Route::get('install-equipments/car-detail', [Admin\InstallEquipmentController::class, 'getCarDetail'])->name('install-equipments.car-detail');
Route::get('install-equipments/group-detail', [Admin\InstallEquipmentController::class, 'getGroupDetail'])->name('install-equipments.group-detail');
Route::get('install-equipments/print-pdf', [Admin\InstallEquipmentController::class, 'printInstallEquipmentPdf'])->name('install-equipments.pdf');
Route::get('install-equipments/install-equipment-detail', [Admin\InstallEquipmentController::class, 'getInstallEquipmentDetail'])->name('install-equipments.install-equipment-detail');
Route::get('install-equipments/export-excel', [Admin\InstallEquipmentController::class, 'exportExcel'])->name('install-equipments.export-excel');
Route::post('install-equipments/create-inspection', [Admin\InstallEquipmentController::class, 'createInspection'])->name('install-equipments.create-inspection');
Route::post('install-equipments/store-init', [Admin\InstallEquipmentController::class, 'storeInitial'])->name('install-equipments.store-inital');
Route::get('install-equipment-purchase-orders/print-pdf', [Admin\InstallEquipmentPOController::class, 'printInstallEquipmentPOPdf'])->name('install-equipment-purchase-orders.pdf');
Route::post('install-equipment-po-approves/update-status', [Admin\InstallEquipmentPOApproveController::class, 'updateStatus'])->name('install-equipment-po-approves.update-status');
Route::resource('install-equipments', Admin\InstallEquipmentController::class);
Route::resource('install-equipment-purchase-orders', Admin\InstallEquipmentPOController::class);
Route::resource('install-equipment-po-approves', Admin\InstallEquipmentPOApproveController::class);

//PDPA
Route::resource('pdpa-managements', Admin\PDPAManagementController::class);

//PR
Route::get('purchase-requisition/default-car-class-accessories', [Admin\PurchaseRequisitionController::class, 'getDefaultCarClassAccessories'])->name('purchase-requisition.default-car-class-accessories');
Route::post('purchase-requisition/update-status', [Admin\PurchaseRequisitionController::class, 'updateStatusReview'])->name('purchase-requisition.update-status');
Route::get('purchase-requisition/rental-type-by-id', [Admin\PurchaseRequisitionController::class, 'getRentalTypeById'])->name('purchase-requisition.rental-type-by-id');
Route::get('purchase-requisition/rental-type-data', [Admin\PurchaseRequisitionController::class, 'getRentalTypeData'])->name('purchase-requisition.rental-type-data');
Route::get('purchase-requisition/lt-rental-data', [Admin\PurchaseRequisitionController::class, 'getLongtermRentalData'])->name('purchase-requisition.lt-rental-data');
Route::get('purchase-requisition/pdf', [Admin\PurchaseRequisitionController::class, 'printPdf'])->name('purchase-requisition.pdf');
Route::get('purchase-requisitions/edit-draft/{purchase_requisition}', [Admin\PurchaseRequisitionController::class, 'editDraft'])->name('purchase-requisitions.edit-draft');
Route::post('purchase-requisition/save-form-dealer', [Admin\PurchaseRequisitionController::class, 'storeComparePriceAndDealer'])->name('purchase-requisition.save-form-dealer');
Route::get('purchase-requisition/duplicate', [Admin\PurchaseRequisitionController::class, 'duplicatePurchaseRequisition'])->name('purchase-requisition.duplicate');
Route::resource('purchase-requisitions', Admin\PurchaseRequisitionController::class);

//PR Approve
Route::post('purchase-requisition-approve/update-status', [Admin\PurchaseRequisitionApproveController::class, 'updatePurchaseRequisitionStatus'])->name('purchase-requisition-approve.update-status');
Route::resource('purchase-requisition-approve', Admin\PurchaseRequisitionApproveController::class);

//PO
Route::post('purchase-orders/update-status', [Admin\PurchaseOrderController::class, 'updatePurchaseOrderStatus'])->name('purchase-orders.update-status');
Route::get('purchase-orders/print-pdf', [Admin\PurchaseOrderController::class, 'printPdf'])->name('purchase-orders.print-pdf');
Route::resource('purchase-orders', Admin\PurchaseOrderController::class);
Route::resource('purchase-order-open', Admin\PurchaseOrderOpenController::class);
Route::resource('purchase-order-approve', Admin\PurchaseOrderApproveController::class);

//Import Car
Route::get('import-cars/{import_car}/export-template', [Admin\ImportCarController::class, 'export'])->name('import-cars.export-template');
Route::get('import-cars/{import_car}/updateStatus', [Admin\ImportCarController::class, 'updateStatus'])->name('import-cars.updateStatus');
Route::get('import-cars/send-email', [Admin\ImportCarController::class, 'sendMail'])->name('import-cars.send-email'); //send mail
Route::resource('import-cars', Admin\ImportCarController::class);


//Car Warehouse
Route::get('cars/sale-car', [Admin\CarController::class, 'getSaleCar'])->name('cars.sale-car');
Route::get('cars/car-cmis', [Admin\CarController::class, 'getCarCMIList'])->name('cars.car-cmis');
Route::get('cars/car-vmis', [Admin\CarController::class, 'getCarVMIList'])->name('cars.car-vmis');
Route::get('cars/car-rentals', [Admin\CarController::class, 'getShortTermRentalList'])->name('cars.car-rentals');
Route::get('cars/car-install-equipments', [Admin\CarController::class, 'getInstallEquipmentList'])->name('cars.car-install-equipments');
Route::get('cars/car-accidents', [Admin\CarController::class, 'getAccidentList'])->name('cars.car-accidents');
Route::get('cars/car-repairs', [Admin\CarController::class, 'getRepairList'])->name('cars.car-repairs');

Route::post('cars/update-status', [Admin\CarController::class, 'updateStatusSale'])->name('cars.update-status');
Route::post('cars/update-type', [Admin\CarController::class, 'updateCarType'])->name('cars.update-type');
Route::resource('cars', Admin\CarController::class);

// Car Park
Route::post('car-park/add-car-to-parking', [Admin\CarParkController::class, 'addCarToParking'])->name('car-park.add-car-to-parking');
Route::post('car-park/remove-car-from-parking', [Admin\CarParkController::class, 'removeCarFromParking'])->name('car-park.remove-car-from-parking');

//Parking Lot Manage
Route::post('parking-lots/shift-car-area', [Admin\ParkingLotController::class, 'shiftCarArea'])->name('parking-lots.shift-car-area');
Route::get('parking-lots/car-park-area-detail', [Admin\ParkingLotController::class, 'getCarParkAreaDetail'])->name('parking-lots.car-park-area-detail');
Route::get('parking-lots/show-shift-cars', [Admin\ParkingLotController::class, 'showShiftCars'])->name('parking-lots.show-shift-cars');
Route::post('parking-lots/update-status-car-park-area', [Admin\ParkingLotController::class, 'updateCarParkAreaStatus'])->name('parking-lots.update-status-car-park-area');
Route::resource('parking-lots', Admin\ParkingLotController::class);
Route::resource('car-park-transfer-logs', Admin\CarParkTransferLogController::class);
Route::get('car-park-areas', [Admin\CarParkAreaController::class, 'index'])->name('car-park-areas.index');
Route::get('car-park-areas/view-parking-history/{car_park_id}', [Admin\CarParkAreaController::class, 'viewAllParkingHistory'])->name('car-park-areas.view-parking-history');
Route::post('car-park-areas/update-car-park-status', [Admin\CarParkAreaController::class, 'updateCarParkStatus'])->name('car-park-areas.update-car-park-status');
Route::post('car-park-areas/update-car-park-disable-date', [Admin\CarParkAreaController::class, 'updateCarParkDisableDate'])->name('car-park-areas.update-car-park-disable-date');

//Car Park Transfer
Route::get('car-park-transfers/default-car-zone', [Admin\CarParkTransferController::class, 'getDefaultCarZone'])->name('car-park-transfers.default-car-zone');
Route::get('car-park-transfers/default-car', [Admin\CarParkTransferController::class, 'getDefaultCar'])->name('car-park-transfers.default-car');
Route::get('car-park-transfers/default-driving-job', [Admin\CarParkTransferController::class, 'getDefaultDrivingJob'])->name('car-park-transfers.default-driving-job');
Route::post('car-park-transfers/update-status', [Admin\CarParkTransferController::class, 'updateStatus'])->name('car-park-transfers.update-status');
Route::resource('car-park-transfers', Admin\CarParkTransferController::class);
// Route::get('dropdownlist/getEngineNo/{id}',[Admin\CarParkTransferLogController::class, 'getEngineNo']);
// Route::get('dropdownlist/getChassisNo/{id}',[Admin\CarParkTransferLogController::class, 'getChassisNo']);

//Prepare New Car
Route::get('prepare-new-cars/get-car-data', [Admin\PrepareNewCarController::class, 'getPrepareCarData'])->name('prepare-new-cars.get-car-data');
Route::resource('prepare-new-cars', Admin\PrepareNewCarController::class);
Route::post('prepare-new-cars/getDataModal', [Admin\PrepareNewCarController::class, 'getDataModal'])->name('prepare-new-cars.getDataModal');
Route::post('prepare-new-cars/update-car-detail', [Admin\PrepareNewCarController::class, 'updateCarDetail'])->name('prepare-new-cars.update-car-detail');


//Replacement Car
Route::get('replacement-cars/replacement-car-detail', [Admin\ReplacementCarController::class, 'getReplacementCarDetail'])->name('replacement-cars.replacement-car-detail');
Route::get('replacement-cars/get-replacement-cars', [Admin\ReplacementCarController::class, 'getReplacementCars'])->name('replacement-cars.get-replacement-cars');
Route::get('replacement-cars/print-pdf', [Admin\ReplacementCarController::class, 'printPdf'])->name('replacement-cars.print-pdf');
Route::post('replacement-car-approves/update-status', [Admin\ReplacementCarApproveController::class, 'updateStatus'])->name('replacement-car-approves.update-status');
Route::resource('replacement-car-informs', Admin\ReplacementCarInformController::class);
Route::resource('replacement-cars', Admin\ReplacementCarController::class);
Route::resource('replacement-type-cars', Admin\ReplacementTypeCarController::class);
Route::resource('replacement-car-approves', Admin\ReplacementCarApproveController::class);

//Short Term Rentals
//// Service Type
Route::get('short-term-rental/service-types/create', [Admin\ShortTermRentalServiceTypeController::class, 'create'])->name('short-term-rental.service-types.create');
Route::get('short-term-rental/service-types/edit/{rental_id}', [Admin\ShortTermRentalServiceTypeController::class, 'edit'])->name('short-term-rental.service-types.edit');
Route::post('short-term-rental/service-types/store', [Admin\ShortTermRentalServiceTypeController::class, 'store'])->name('short-term-rental.service-types.store');
//// Channel
Route::resource('short-term-rental-channel', Admin\ShortTermRentalChannelController::class);
//// Info
Route::get('short-term-rental/info/default-data-customer-billing-address', [Admin\ShortTermRentalInfoController::class, 'getDataCustomerBillingAddress'])->name('short-term-rental.info.default-data-customer-billing-address');
Route::post('short-term-rental/info/store-customer-billing', [Admin\ShortTermRentalInfoController::class, 'storeCustomerBilling'])->name('short-term-rental.info.store-customer-billing');
Route::get('short-term-rental/info/edit/{rental_id}', [Admin\ShortTermRentalInfoController::class, 'edit'])->name('short-term-rental.info.edit');
Route::post('short-term-rental/info/store', [Admin\ShortTermRentalInfoController::class, 'store'])->name('short-term-rental.info.store');
Route::post('short-term-rental/info/product-data', [Admin\ShortTermRentalInfoController::class, 'getDataProduct'])->name('short-term-rental.info.product-data');

//// Asset
Route::get('short-term-rental/asset/edit/{rental_id}', [Admin\ShortTermRentalAssetController::class, 'edit'])->name('short-term-rental.asset.edit');
Route::post('short-term-rental/asset/store', [Admin\ShortTermRentalAssetController::class, 'store'])->name('short-term-rental.asset.store');
Route::get('short-term-rental/asset/back/{rental_id}', [Admin\ShortTermRentalAssetController::class, 'back'])->name('short-term-rental.asset.back');

//// Driver
Route::get('short-term-rental/driver/default-data-driver', [Admin\ShortTermRentalDriverController::class, 'getDataDriver'])->name('short-term-rental.driver.default-data-driver');
Route::get('short-term-rental/driver/edit/{rental_id}', [Admin\ShortTermRentalDriverController::class, 'edit'])->name('short-term-rental.driver.edit');
Route::post('short-term-rental/driver/store', [Admin\ShortTermRentalDriverController::class, 'store'])->name('short-term-rental.driver.store');
//Route::post('short-term-rental/driver/get-data-rental-product-additional', [Admin\ShortTermRentalDriverController::class, 'getDataRentalProductAdditional'])->name('short-term-rental.driver.data-rental-product-additional');


//// Promotion
Route::get('short-term-rental/promotion/edit/{rental_id}', [Admin\ShortTermRentalPromotionController::class, 'edit'])->name('short-term-rental.promotion.edit');
Route::post('short-term-rental/promotion/store', [Admin\ShortTermRentalPromotionController::class, 'store'])->name('short-term-rental.promotion.store');
Route::post('short-term-rental/promotion/promotion-data', [Admin\ShortTermRentalPromotionController::class, 'getPromotionData'])->name('short-term-rental.promotion.promotion-data');
Route::get('short-term-rental/promotion/promotion-coupon', [Admin\ShortTermRentalPromotionController::class, 'getPromotionCoupon'])->name('short-term-rental.promotion.promotion-coupon');
Route::get('short-term-rental/promotion/promotion-voucher', [Admin\ShortTermRentalPromotionController::class, 'getPromotionVoucher'])->name('short-term-rental.promotion.promotion-voucher');

//// Bill
Route::get('short-term-rental/bill/edit/{rental_id}', [Admin\ShortTermRentalBillSummaryController::class, 'edit'])->name('short-term-rental.bill.edit');

//// Summary
Route::get('short-term-rental/summary/edit/{rental_id}', [Admin\ShortTermRentalSummaryController::class, 'edit'])->name('short-term-rental.summary.edit');
Route::post('short-term-rental/summary/store', [Admin\ShortTermRentalSummaryController::class, 'store'])->name('short-term-rental.summary.store');
/* Route::post('short-term-rental/summary/update-payment', [Admin\ShortTermRentalSummaryController::class, 'updatePayment'])->name('short-term-rental.summary.update-payment'); */
Route::post('short-term-rental/summary/update-rental-car', [Admin\ShortTermRentalSummaryController::class, 'updateRentalCar'])->name('short-term-rental.summary.update-rental-car');
Route::post('short-term-rental/summary/update-rental-extra', [Admin\ShortTermRentalSummaryController::class, 'updateRentalExtra'])->name('short-term-rental.summary.update-rental-extra');
Route::post('short-term-rental/summary/update-rental-product-additional', [Admin\ShortTermRentalSummaryController::class, 'updateRentalProductAdditional'])->name('short-term-rental.summary.update-rental-product-additional');
Route::post('short-term-rental/summary/update-withholding-tax', [Admin\ShortTermRentalSummaryController::class, 'updateWithholdingTax'])->name('short-term-rental.summary.update-withholding-tax');

//// Alter
Route::get('short-term-rental/alter/edit/{rental_id}', [Admin\ShortTermRentalAlterInfoController::class, 'edit'])->name('short-term-rental.alter.edit');
Route::get('short-term-rental/alter/view/{rental_id}', [Admin\ShortTermRentalAlterInfoController::class, 'show'])->name('short-term-rental.alter.view');
Route::get('short-term-rental/alter/edit-driver/{rental_id}', [Admin\ShortTermRentalAlterDriverController::class, 'edit'])->name('short-term-rental.alter.edit-driver');
Route::get('short-term-rental/alter/view-driver/{rental_id}', [Admin\ShortTermRentalAlterDriverController::class, 'show'])->name('short-term-rental.alter.view-driver');
Route::get('short-term-rental/alter/edit-bill/{rental_id}', [Admin\ShortTermRentalAlterBillController::class, 'index'])->name('short-term-rental.alter.edit-bill');
Route::get('short-term-rental/alter/view-bill/{rental_id}', [Admin\ShortTermRentalAlterBillController::class, 'show'])->name('short-term-rental.alter.view-bill');
Route::get('short-term-rental/alter/bill-summary/{rental_bill_id}', [Admin\ShortTermRentalAlterBillController::class, 'edit'])->name('short-term-rental.alter.bill-summary');
Route::get('short-term-rental/alter/view-bill-summary/{rental_bill_id}', [Admin\ShortTermRentalAlterBillController::class, 'showBill'])->name('short-term-rental.alter.view-bill-summary');
Route::post('short-term-rental/alter/store-info', [Admin\ShortTermRentalAlterInfoController::class, 'storeInfo'])->name('short-term-rental.alter.store-info');
Route::post('short-term-rental/alter/store-driver', [Admin\ShortTermRentalAlterDriverController::class, 'store'])->name('short-term-rental.alter.store-driver');
Route::post('short-term-rental/alter/store-bill', [Admin\ShortTermRentalAlterBillController::class, 'store'])->name('short-term-rental.alter.store-bill');
Route::post('short-term-rental/alter/store-rental-bill', [Admin\ShortTermRentalAlterBillController::class, 'storeRentalBill'])->name('short-term-rental.alter.store-rental-bill');

Route::get('short-term-rentals/promotion-detail', [Admin\ShortTermRentalController::class, 'getPromotionDetail'])->name('short-term-rentals.promotion-detail');
Route::post('short-term-rentals/update-status', [Admin\ShortTermRentalController::class, 'updateStatus'])->name('short-term-rentals.update-status');
Route::get('short-term-rentals/asset-cars', [Admin\ShortTermRentalController::class, 'getAssetCars'])->name('short-term-rentals.asset-cars');
Route::get('short-term-rentals/available-cars', [Admin\ShortTermRentalController::class, 'getAvailableCars'])->name('short-term-rentals.available-cars');
Route::get('short-term-rentals/available-car-spares', [Admin\ShortTermRentalController::class, 'getAvailableCarSpares'])->name('short-term-rentals.available-car-spares');
Route::get('short-term-rentals/gen-2c2p-link', [Admin\ShortTermRentalController::class, 'gen2c2pPaymentLink'])->name('short-term-rentals.gen-2c2p-link');
Route::get('short-term-rentals/calendar-ajax', [Admin\ShortTermRentalController::class, 'getCalendar'])->name('short-term-rentals.calendar-ajax');
Route::get('short-term-rentals/calendar', [Admin\ShortTermRentalController::class, 'showCalendar'])->name('short-term-rentals.calendar');
Route::post('short-term-rentals/get-timelines', [Admin\ShortTermRentalController::class, 'getCarRentalsByMonthYear'])->name('short-term-rentals.get-timelines');
Route::resource('short-term-rentals', Admin\ShortTermRentalController::class);

//Long Term Retal
//// Compare Price
Route::get('long-term-rental/compare-price/index', [Admin\LongTermRentalComparePriceController::class, 'index'])->name('long-term-rental.compare-price.index');
Route::get('long-term-rental/compare-price/edit/{rental}', [Admin\LongTermRentalComparePriceController::class, 'edit'])->name('long-term-rental.compare-price.edit');
Route::get('long-term-rental/compare-price/{rental}', [Admin\LongTermRentalComparePriceController::class, 'show'])->name('long-term-rental.compare-price.show');
Route::post('long-term-rental/compare-price/store', [Admin\LongTermRentalComparePriceController::class, 'store'])->name('long-term-rental.compare-price.store');
//// Compare Price Approve
Route::get('long-term-rental/compare-price-approve/index', [Admin\LongTermRentalComparePriceApproveController::class, 'index'])->name('long-term-rental.compare-price-approve.index');
Route::get('long-term-rental/compare-price-approve/{rental}', [Admin\LongTermRentalComparePriceApproveController::class, 'show'])->name('long-term-rental.compare-price-approve.show');
Route::post('long-term-rental/compare-price-approve/update-compare-rental-status', [Admin\LongTermRentalComparePriceApproveController::class, 'updateComparePriceStatus'])->name('long-term-rental.compare-price-approve.update-compare-rental-status');
////Specs
Route::get('long-term-rental/specs/default-bom-car', [Admin\LongTermRentalSpecController::class, 'getDefaultBomCar'])->name('long-term-rental.specs.default-bom-car');
Route::post('long-term-rental/specs/store-bom-car', [Admin\LongTermRentalSpecController::class, 'storeBomCar'])->name('long-term-rental.specs.store-bom-car');
Route::get('long-term-rental/specs/default-tor-line', [Admin\LongTermRentalSpecController::class, 'getDefaultTorLine'])->name('long-term-rental.specs.default-tor-line');
Route::get('long-term-rental/specs/default-tor-line-accessory', [Admin\LongTermRentalSpecController::class, 'getDefaultTorLineAccessory'])->name('long-term-rental.specs.default-tor-line-accessory');
Route::get('long-term-rental/specs/default-bom-accessory', [Admin\LongTermRentalSpecController::class, 'getDefaultBomAccessory'])->name('long-term-rental.specs.default-bom-accessory');
Route::post('long-term-rental/specs/store-tor-accessory', [Admin\LongTermRentalSpecController::class, 'storeTorAccessory'])->name('long-term-rental.specs.store-tor-accessory');
Route::post('long-term-rental/specs/store-dealer', [Admin\LongTermRentalSpecController::class, 'storeDealer'])->name('long-term-rental.specs.store-dealer');
Route::get('long-term-rental/specs/send-email', [Admin\LongTermRentalSpecController::class, 'sendMail'])->name('long-term-rental.specs.send-email'); //send mail
Route::post('long-term-rental/specs/delete-tor', [Admin\LongTermRentalSpecController::class, 'destroyTor'])->name('long-term-rental.specs.delete-tor');
Route::get('long-term-rental/specs/index', [Admin\LongTermRentalSpecController::class, 'index'])->name('long-term-rental.specs.index');
Route::get('long-term-rental/specs/edit/{rental}', [Admin\LongTermRentalSpecController::class, 'edit'])->name('long-term-rental.specs.edit');
Route::get('long-term-rental/specs/{rental}', [Admin\LongTermRentalSpecController::class, 'show'])->name('long-term-rental.specs.show');
Route::post('long-term-rental/specs/store', [Admin\LongTermRentalSpecController::class, 'store'])->name('long-term-rental.specs.store');
Route::post('long-term-rental/specs/tor/get-data-accessory-type', [Admin\LongTermRentalSpecTorController::class, 'getDataBomAccessory'])->name('long-term-rental.specs.tors.get-data-accessory-type');
Route::get('long-term-rental/specs/tor/{rental}/create', [Admin\LongTermRentalSpecTorController::class, 'create'])->name('long-term-rental.specs.tor.create');
Route::get('long-term-rental/specs/tor/{rental}/edit-car/{lt_rental_tor_id}', [Admin\LongTermRentalSpecTorController::class, 'editCar'])->name('long-term-rental.specs.tor.edit-car');
Route::get('long-term-rental/specs/tor/{rental}/show-car/{lt_rental_tor_id}', [Admin\LongTermRentalSpecTorController::class, 'showCar'])->name('long-term-rental.specs.tor.show-car');
Route::get('long-term-rental/specs/tor/{rental}/edit/{lt_rental_tor_id}', [Admin\LongTermRentalSpecTorController::class, 'edit'])->name('long-term-rental.specs.tor.edit');
Route::get('long-term-rental/specs/tor/{rental}/view/{lt_rental_tor_id}', [Admin\LongTermRentalSpecTorController::class, 'show'])->name('long-term-rental.specs.tor.show');
Route::post('long-term-rental/specs/tor/store', [Admin\LongTermRentalSpecTorController::class, 'store'])->name('long-term-rental.specs.tor.store');
Route::post('long-term-rental/specs/tor/update-accessory', [Admin\LongTermRentalSpecTorController::class, 'updateAccessory'])->name('long-term-rental.specs.tor.update-accessory');
Route::get('long-term-rental/specs/accessories/index', [Admin\LongTermRentalSpecAccessoryController::class, 'index'])->name('long-term-rental.specs.accessories.index');
Route::get('long-term-rental/specs/accessories/edit/{rental}', [Admin\LongTermRentalSpecAccessoryController::class, 'edit'])->name('long-term-rental.specs.accessories.edit');

// Spec Check Car
Route::get('long-term-rental/spec-check-cars/default-car-by-brand', [Admin\LongTermRentalSpecCheckCarController::class, 'getDefaultCarByBrand'])->name('long-term-rental.spec-check-cars.default-car-by-brand');
Route::post('long-term-rental/spec-check-cars/store-dealer', [Admin\LongTermRentalSpecCheckCarController::class, 'storeDealer'])->name('long-term-rental.spec-check-cars.store-dealer');
Route::get('long-term-rental/spec-check-cars/index', [Admin\LongTermRentalSpecCheckCarController::class, 'index'])->name('long-term-rental.spec-check-cars.index');
Route::get('long-term-rental/spec-check-cars/{rental}', [Admin\LongTermRentalSpecCheckCarController::class, 'show'])->name('long-term-rental.spec-check-cars.show');
Route::get('long-term-rental/spec-check-cars/edit/{rental}', [Admin\LongTermRentalSpecCheckCarController::class, 'edit'])->name('long-term-rental.spec-check-cars.edit');
Route::post('long-term-rental/spec-check-cars/store', [Admin\LongTermRentalSpecCheckCarController::class, 'store'])->name('long-term-rental.spec-check-cars.store');

////Spec- ApproveE
Route::get('long-term-rental/specs-approve/index', [Admin\LongTermRentalSpecApproveController::class, 'index'])->name('long-term-rental.specs-approve.index');
Route::get('long-term-rental/specs-approve/{rental}', [Admin\LongTermRentalSpecApproveController::class, 'show'])->name('long-term-rental.specs-approve.show');
Route::get('long-term-rental/specs-approve/tor/{rental}/view/{lt_rental_tor_id}', [Admin\LongTermRentalSpecApproveController::class, 'showTor'])->name('long-term-rental.specs-approve.show-tor');
Route::post('long-term-rental/specs-approve/update-status', [Admin\LongTermRentalSpecApproveController::class, 'updateSpecStatus'])->name('long-term-rental.specs-approve.update-status');
////Quotation
Route::get('long-term-rental/quotations/index', [Admin\LongTermRentalQuotationController::class, 'index'])->name('long-term-rental.quotations.index');
Route::get('long-term-rental/quotations/edit/{rental}', [Admin\LongTermRentalQuotationController::class, 'edit'])->name('long-term-rental.quotations.edit');
Route::get('long-term-rental/quotations/{rental}', [Admin\LongTermRentalQuotationController::class, 'show'])->name('long-term-rental.quotations.show');
Route::post('long-term-rental/quotations/store', [Admin\LongTermRentalQuotationController::class, 'store'])->name('long-term-rental.quotations.store');

// PR Line
Route::get('long-term-rental/pr-lines/{long_term_rental}/edit', [Admin\LongTernRentalPRController::class, 'editPRLongTermRentalLine'])->name('long-term-rentals.pr-lines.edit');
Route::get('long-term-rental/pr-lines/{long_term_rental}', [Admin\LongTernRentalPRController::class, 'showPRLongTermRentalLine'])->name('long-term-rentals.pr-lines.show');
Route::post('long-term-rental/pr-lines/store', [Admin\LongTernRentalPRController::class, 'storePRLongTermRentalLine'])->name('long-term-rentals.pr-lines.store');

// Car Contract
//Route::get('long-term-rental/car-info-and-deliver/{long_term_rental}/edit', [Admin\LongTermRentalCarInfoAndDeliverController::class, 'editCarInfoAndDeliver'])->name('long-term-rentals.car-info-and-deliver.edit');
Route::get('long-term-rental/car-info-and-deliver/{long_term_rental}', [Admin\LongTermRentalCarInfoAndDeliverController::class, 'showCarInfoAndDeliver'])->name('long-term-rentals.car-info-and-deliver.show');
Route::post('long-term-rental/car-info-and-deliver/store', [Admin\LongTermRentalCarInfoAndDeliverController::class, 'storeCarInfoAndDeliver'])->name('long-term-rentals.car-info-and-deliver.store');
Route::post('long-term-rental/car-info-and-deliver/create-contract', [Admin\LongTermRentalCarInfoAndDeliverController::class, 'createContractLongTermRental'])->name('long-term-rentals.car-info-and-deliver.create-contract');

////Long Term
Route::get('long-term-rentals/check-rental-type', [Admin\LongTermRentalController::class, 'checkRentalType'])->name('long-term-rentals.check-rental-type');
Route::get('long-term-rentals/generate-last-date', [Admin\LongTermRentalController::class, 'generateLastDateLongTerm'])->name('long-term-rentals.generate-last-date');
Route::get('long-term-rentals/rental-requisition-pdf', [Admin\LongTermRentalController::class, 'printRentalRequisitionPdf'])->name('long-term-rentals.rental-requisition-pdf');
Route::get('long-term-rentals/check-count-month', [Admin\LongTermRentalController::class, 'checkCountMonth'])->name('long-term-rentals.check-count-month');
Route::get('long-term-rentals/print-rental-requisition', [Admin\LongTermRentalController::class, 'printRentalPDFbyConfig'])->name('long-term-rentals.print-rental-requisition');
Route::resource('long-term-rentals', Admin\LongTermRentalController::class);

Route::resource('long-term-rental-types', Admin\LongTermRentalTypeController::class);
Route::resource('long-term-rental-boms', Admin\LongTermRentalBomController::class);

//Quotation
Route::get('quotations/condition', [Admin\QuotationController::class, 'getCondition'])->name('quotations.condition');
Route::get('quotations/pdf', [Admin\QuotationController::class, 'pdf'])->name('quotations.pdf');
Route::get('quotations/short-term-rental-pdf', [Admin\QuotationController::class, 'printShortTermRentalPdf'])->name('quotations.short-term-rental-pdf');
Route::get('quotations/short-term-rental-payment-pdf', [Admin\QuotationController::class, 'printShortTermRentalPaymentPdf'])->name('quotations.short-term-rental-payment-pdf');
Route::get('quotations/long-term-rental-pdf', [Admin\QuotationController::class, 'printLongTermRentalPdf'])->name('quotations.long-term-rental-pdf');
Route::post('quotations/update-status', [Admin\QuotationController::class, 'updateQuotationStatus'])->name('quotations.update-status');
Route::resource('quotations', Admin\QuotationController::class);

Route::post('quotation-approves/update-status', [Admin\QuotationApproveController::class, 'updateQuotationStatus'])->name('quotation-approves.update-status');
Route::resource('quotation-approves', Admin\QuotationApproveController::class);

//Master Data Condition Quotation
Route::resource('condition-quotation-long-terms', Admin\ConditionQuotationLongTermRentalController::class);
Route::resource('condition-quotation-short-terms', Admin\ConditionQuotationShortTermRentalController::class);
Route::get('receipts/pdf', [Admin\ReceiptController::class, 'printPdf'])->name('receipts.pdf');
Route::resource('receipts', Admin\ReceiptController::class);

Route::resource('request-receipts', Admin\RequestReceiptController::class);

//GPS Management
Route::get('gps-check-signals/default-job-id', [Admin\GpsCheckSignalAlertController::class, 'getDefaultJobID'])->name('gps-check-signals.default-job-id');
Route::get('gps-check-signals/default-data-job', [Admin\GpsCheckSignalAlertController::class, 'getDefaultDataJob'])->name('gps-check-signals.default-data-job');
Route::get('gps-check-signals/default-car-id', [Admin\GpsCheckSignalAlertController::class, 'getDefaultCarID'])->name('gps-check-signals.default-car-id');
Route::get('gps-check-signals/default-data-car', [Admin\GpsCheckSignalAlertController::class, 'getDefaultDataCar'])->name('gps-check-signals.default-data-car');
Route::get('gps-cars/export-excel', [Admin\GpsCarController::class, 'exportExcel'])->name('gps-cars.export-excel');
Route::post('gps-cars/veh-last-location', [Admin\GpsCarController::class, 'updateVehLastLocation'])->name('gps-cars.veh-last-location');
Route::resource('gps-cars', Admin\GpsCarController::class);
Route::resource('gps-check-signal-alerts', Admin\GpsCheckSignalAlertController::class);
/* GPS checksignal job short term */
Route::post('gps-check-signal-jobs/send-check-job', [Admin\GpsCheckSignalJobController::class, 'sendCheckJob'])->name('gps-check-signal-jobs.send-check-job');
Route::post('gps-check-signal-jobs/send-branch-job', [Admin\GpsCheckSignalJobController::class, 'sendBranchJob'])->name('gps-check-signal-jobs.send-branch-job');
Route::resource('gps-check-signal-jobs', Admin\GpsCheckSignalJobController::class);
/* GPS checksignal job long term branch */
Route::resource('gps-check-signal-job-long-term', Admin\GpsCheckSignalJobLongTermController::class);
/* GPS checksignal job replacement car */
Route::resource('gps-check-signal-job-replaces', Admin\GpsCheckSignalJobReplacementController::class);
/* GPS checksignal job short term branch */
Route::resource('gps-check-signal-job-branch', Admin\GpsCheckSignalJobBranchController::class);
/* GPS checksignal job kratos tracking */
Route::resource('gps-check-signal-job-kratos', Admin\GpsCheckSignalJobKratosController::class);
/* GPS remove stop alert */
Route::get('gps-remove-stop-signal-alerts/car-license-plate', [Admin\GpsRemoveStopSignalAlertController::class, 'getCarLicensePlate'])->name('gps-remove-stop-signal-alerts.car-license-plate');
Route::get('gps-remove-stop-signal-alerts/car-vid', [Admin\GpsRemoveStopSignalAlertController::class, 'getCarVid'])->name('gps-remove-stop-signal-alerts.car-vid');
Route::get('gps-remove-stop-signal-alerts/car-engine-no', [Admin\GpsRemoveStopSignalAlertController::class, 'getCarEngineNo'])->name('gps-remove-stop-signal-alerts.car-engine-no');
Route::get('gps-remove-stop-signal-alerts/car-chassis-no', [Admin\GpsRemoveStopSignalAlertController::class, 'getCarChassisNo'])->name('gps-remove-stop-signal-alerts.car-chassis-no');
Route::get('gps-remove-stop-signal-alerts/default-car', [Admin\GpsRemoveStopSignalAlertController::class, 'getDefaultCar'])->name('gps-remove-stop-signal-alerts.default-car');
Route::resource('gps-remove-stop-signal-alerts', Admin\GpsRemoveStopSignalAlertController::class);
/* GPS remove stop job */
Route::post('gps-remove-stop-signal-jobs/send-remove-job', [Admin\GpsRemoveStopSignalJobController::class, 'sendRemoveJob'])->name('gps-remove-stop-signal-jobs.send-remove-job');
Route::post('gps-remove-stop-signal-jobs/send-stop-job', [Admin\GpsRemoveStopSignalJobController::class, 'sendStopJob'])->name('gps-remove-stop-signal-jobs.send-stop-job');
Route::resource('gps-remove-stop-signal-jobs', Admin\GpsRemoveStopSignalJobController::class);
/* GPS remove  job */
Route::post('gps-remove-signal-jobs/update-remove-job', [Admin\GpsRemoveSignalJobController::class, 'updateRemoveJob'])->name('gps-remove-signal-jobs.update-remove-job');
Route::resource('gps-remove-signal-jobs', Admin\GpsRemoveSignalJobController::class);
/* GPS stop job */
Route::post('gps-stop-signal-jobs/update-stop-job', [Admin\GpsStopSignalJobController::class, 'updateStopJob'])->name('gps-stop-signal-jobs.update-stop-job');
Route::resource('gps-stop-signal-jobs', Admin\GpsStopSignalJobController::class);
/* GPS historical data alert */
Route::get('gps-historical-data-alerts/upload-excel', [Admin\GpsHistoricalDataAlertController::class, 'uploadExcel'])->name('gps-historical-data-alerts.upload-excel');
Route::resource('gps-historical-data-alerts', Admin\GpsHistoricalDataAlertController::class);
/* GPS historical data job */
Route::get('gps-historical-data-jobs/export-excel', [Admin\GpsHistoricalDataJobController::class, 'exportExcel'])->name('gps-historical-data-jobs.export-excel');
Route::resource('gps-historical-data-jobs', Admin\GpsHistoricalDataJobController::class);
/* GPS alert */
Route::resource('gps-alerts', Admin\GpsAlertController::class);
/* GPS service charges */
Route::get('gps-service-charges/index-service', [Admin\GpsServiceChargeController::class, 'indexService'])->name('gps-service-charges.index-service');
Route::resource('gps-service-charges', Admin\GpsServiceChargeController::class);

Route::resource('check-credit-new-customers', Admin\CheckCreditNewCustomer::class);
Route::resource('check-credit-approves', Admin\CheckCreditApproveController::class);

Route::resource('contract-category', Admin\ContractCategoryController::class);

Route::get('contracts-mockup/{id}', [Admin\ContractsController::class, 'mockupContract']);
Route::get('contracts/print-pdf', [Admin\ContractsController::class, 'printContractPdf'])->name('contracts.print-pdf');
Route::post('contract/media_file', [Admin\ContractsController::class, 'getContractMediaFile'])->name('contract.media_file');
Route::post('contract/log-and-media-file', [Admin\ContractsController::class, 'getContractLogAndMedia'])->name('contract.log_and_media_file');
Route::post('contract/update-approve-request', [Admin\ContractsController::class, 'updateApproveStatusContractLog'])->name('contract.update-approve-request');
Route::resource('contracts', Admin\ContractsController::class);
Route::resource('contract-check-and-edit', Admin\ContractCheckAndEditController::class);

//Car Transfer
Route::resource('car-transfers', Admin\CarTransferController::class);

//Transfer Car
Route::get('transfer-cars/print-pdf', [Admin\TransferCarController::class, 'printPdf'])->name('transfer-cars.print-pdf');
Route::post('transfer-cars/update-status', [Admin\TransferCarController::class, 'updateTransferCarStatus'])->name('transfer-cars.update-status');
Route::resource('transfer-cars', Admin\TransferCarController::class);
Route::resource('transfer-car-receives', Admin\TransferCarReceiveController::class);

// Borrow Car
Route::get('borrow-cars/print-pdf', [Admin\BorrowCarController::class, 'printPdf'])->name('borrow-cars.print-pdf');
Route::resource('borrow-cars', Admin\BorrowCarController::class);

// Borrow Car List
Route::resource('borrow-car-lists', Admin\BorrowCarListController::class);

// Borrow Car Approve
Route::post('borrow-car-approves/update-status', [Admin\BorrowCarApproveController::class, 'updateBorrowCarStatus'])->name('borrow-car-approves.update-status');
Route::resource('borrow-car-approves', Admin\BorrowCarApproveController::class);

// Borrow Car Confirm Approve
Route::resource('borrow-car-confirm-approves', Admin\BorrowCarConfirmApproveController::class);

//Repair + Master Data
Route::get('check-distances/select_car-class', [Admin\CheckDistanceController::class, 'selectCarClass'])->name('check-distances.select_car-class');
Route::post('check-distances/copy-check-distance', [Admin\CheckDistanceController::class, 'copyCheckDistance'])->name('check-distances.copy-check-distance');
Route::resource('check-distances', Admin\CheckDistanceController::class);
Route::resource('repair-lists', Admin\RepairListController::class);
Route::resource('condition-repair-services', Admin\ConditionRepairServiceController::class);
Route::get('repairs/default-car-id', [Admin\RepairController::class, 'getDefaultCarID'])->name('repairs.default-car-id');
Route::get('repairs/data-car', [Admin\RepairController::class, 'getDataCar'])->name('repairs.data-car');
Route::get('repair-orders/data-repair', [Admin\RepairOrderController::class, 'getDataRepair'])->name('repair-orders.data-repair');
Route::get('repair-orders/price-repair', [Admin\RepairOrderController::class, 'getPriceRepair'])->name('repair-orders.price-repair');
Route::get('repair-orders/data-center', [Admin\RepairOrderController::class, 'getDataCenter'])->name('repair-orders.data-center');
Route::get('repair-orders/select-repair', [Admin\RepairOrderController::class, 'selectRepair'])->name('repair-orders.select-repair');
Route::get('repair-orders/select-distance', [Admin\RepairOrderController::class, 'selectDistance'])->name('repair-orders.select-distance');
Route::get('repair-orders/get-default-distance-line', [Admin\RepairOrderController::class, 'getDefaultDistanceLine'])->name('repair-orders.get-default-distance-line');
Route::post('repair-orders/update-status', [Admin\RepairOrderController::class, 'updateStatus'])->name('repair-orders.update-status');
Route::get('repair-orders/print-pdf', [Admin\RepairOrderController::class, 'printPdf'])->name('repair-orders.print-pdf');
Route::get('repair-orders/send-mail', [Admin\RepairOrderController::class, 'sendMail'])->name('repair-orders.send-mail');
Route::get('repair-orders/print-summary-pdf', [Admin\RepairOrderController::class, 'printSummaryPdf'])->name('repair-orders.print-summary-pdf');
Route::get('repair-orders/print-daily-summary-pdf', [Admin\RepairOrderController::class, 'printDailySummaryPdf'])->name('repair-orders.print-daily-summary-pdf');
Route::resource('repairs', Admin\RepairController::class);
Route::resource('call-center-repairs', Admin\CallCenterRepairController::class);
Route::resource('repair-orders', Admin\RepairOrderController::class);
Route::resource('call-center-repair-orders', Admin\CallCenterRepairOrderController::class);
Route::resource('repair-order-conditions', Admin\RepairOrderConditionController::class);
Route::resource('repair-quotation-approves', Admin\RepairQuotationApproveController::class);
Route::resource('check-distance-notices', Admin\CheckDistanceNoticeController::class);
//RepairBill
Route::get('repair-bills/print-pdf/{repair_bill_id}', [Admin\RepairBillController::class, 'printPdf'])->name('repair-bills.print-pdf');
Route::resource('repair-bills', Admin\RepairBillController::class);
// CMI
Route::get('cmi-cars/lot-number', [Admin\CMICarController::class, 'getLotNumber'])->name('cmi-cars.lot-number');
Route::get('cmi-cars/export-cmis', [Admin\CMICarController::class, 'exportCMIs'])->name('cmi-cars.export-cmis');
Route::post('cmi-cars/import-cmis', [Admin\CMICarController::class, 'importCMIs'])->name('cmi-cars.import-cmis');
Route::post('cmi-cars/create-cmi-cars', [Admin\CMICarController::class, 'createCMICarList'])->name('cmi-cars.create-cmi-cars');
Route::post('cmi-cars/make-in-process-cmis', [Admin\CMICarController::class, 'makeInProcessCMIs'])->name('cmi-cars.make-in-process-cmis');
Route::resource('cmi-cars', Admin\CMICarController::class);


// CANCEL CMI
Route::get('cancel-cmi-cars/export-cancel-insurances', [Admin\CancelCMIController::class, 'exportCancelInsurances'])->name('cancel-cmi-cars.export-cancel-insurances');
Route::post('cancel-cmi-cars/import-cancel-insurances', [Admin\CancelCMIController::class, 'importCancelInsurances'])->name('cancel-cmi-cars.import-cancel-insurances');
Route::resource('cancel-cmi-cars', Admin\CancelCMIController::class);

// CANCEL VMI
Route::resource('cancel-vmi-cars', Admin\CancelVMIController::class);


// VMI
Route::get('vmi-cars/insurance-packsage-detail', [Admin\VMICarController::class, 'getInsurancePackageDetail'])->name('vmi-cars.insurance-package-detail');
Route::get('vmi-cars/export-vmis', [Admin\VMICarController::class, 'exportVMIs'])->name('vmi-cars.export-vmis');
Route::post('vmi-cars/import-vmis', [Admin\VMICarController::class, 'importVMIs'])->name('vmi-cars.import-vmis');
Route::post('vmi-cars/make-in-process-vmis', [Admin\VMICarController::class, 'makeInProcessVMIs'])->name('vmi-cars.make-in-process-vmis');
Route::resource('vmi-cars', Admin\VMICarController::class);

//InsuranceCompanies Master Data
Route::resource('insurances-companies', Admin\InsuranceCompaniesController::class);
//insurance-car
Route::resource('insurance-car', Admin\InsuranceCarController::class);
Route::post('/insurance-car/get-car-data', [Admin\InsuranceCarController::class, 'getCarData'])->name('insurance-car.car-data');
Route::post('/insurance-car/get-car-accessory-data', [Admin\InsuranceCarController::class, 'getCarAccessoryListData'])->name('insurance-car.car-accessory-list-data');
Route::post('/insurance-car/insurance-car-renew', [Admin\InsuranceCarController::class, 'InsuranceCarRenew'])->name('insurance-car.car-renew');
Route::post('/insurance-car/get-lot', [Admin\InsuranceCarController::class, 'getLot'])->name('insurance-car.get-lot');


//Insurance CarCMI
Route::resource('insurance-car-cmi', Admin\InsuranceCarCmiController::class);
Route::get('/insurance-car-cmi/{insurance_car_cmi}/remark', [Admin\InsuranceCarCmiController::class, 'remark'])->name('insurance-car-cmi.remark');
Route::post('/insurance-car-cmi/{insurance_car_cmi}/cancel', [Admin\InsuranceCarCmiController::class, 'requestCancelInsurance'])->name('insurance-car-cmi.request-cancel-insurance');

//Insurance Car CMI Renew
Route::get('insurance-cmi-renew/lot-number', [Admin\InsuranceCarCmiRenewController::class, 'getLotNumber'])->name('insurance-cmi-renew.lot-number');
Route::get('insurance-cmi-renew/export-cmis', [Admin\InsuranceCarCmiRenewController::class, 'exportCMIs'])->name('insurance-cmi-renew.export-cmis');
Route::post('insurance-cmi-renew/import-cmis', [Admin\InsuranceCarCmiRenewController::class, 'importCMIs'])->name('insurance-cmi-renew.import-cmis');
Route::post('insurance-cmi-renew/create-insurance-cmi-renew', [Admin\InsuranceCarCmiRenewController::class, 'createCMICarList'])->name('insurance-cmi-renew.create-insurance-cmi-renew');
Route::post('insurance-cmi-renew/make-in-process-cmis', [Admin\InsuranceCarCmiRenewController::class, 'makeInProcessCMIs'])->name('insurance-cmi-renew.make-in-process-cmis');
Route::resource('insurance-cmi-renew', Admin\InsuranceCarCmiRenewController::class);

//InsuranceCarVmi
Route::resource('insurance-car-vmi', Admin\InsuranceCarVmiController::class);
Route::post('/insurance-car-vmi/{insurance_car_vmi}/cancel', [Admin\InsuranceCarVmiController::class, 'requestCancelInsurance'])->name('insurance-car-vmi.request-cancel-insurance');

//InsuranceCarVmiRenew
Route::get('insurance-vmi-renew/insurance-packsage-detail', [Admin\InsuranceCarVmiRenewController::class, 'getInsurancePackageDetail'])->name('insurance-vmi-renew.insurance-package-detail');
Route::get('insurance-vmi-renew/export-vmis', [Admin\InsuranceCarVmiRenewController::class, 'exportVMIs'])->name('insurance-vmi-renew.export-vmis');
Route::post('insurance-vmi-renew/import-vmis', [Admin\InsuranceCarVmiRenewController::class, 'importVMIs'])->name('insurance-vmi-renew.import-vmis');
Route::post('insurance-vmi-renew/make-in-process-vmis', [Admin\InsuranceCarVmiRenewController::class, 'makeInProcessVMIs'])->name('insurance-vmi-renew.make-in-process-vmis');
Route::resource('insurance-vmi-renew', Admin\InsuranceCarVmiRenewController::class);

//InsurancePremium
Route::resource('request-premium', Admin\RequestPremiumController::class);
Route::post('request-premium/accessory-list', [Admin\RequestPremiumController::class, 'getAccessoryList'])->name('request-premium-accessory-list');

//InsuranceDeduct
Route::resource('insurance-deducts', Admin\InsuranceDeductController::class);

//InsuranceLossRatio
Route::resource('insurance-loss-ratios', Admin\InsuranceLossRatioController::class);
//Route::get('insurance-loss-ratios/loss-ratio-pdf', [Admin\InsuranceLossRatioController::class, 'generatePdf'])->name('insurance-loss-ratios.pdf');
Route::get('insurance-loss-ratios-pdf', [Admin\InsuranceLossRatioController::class, 'generatePdf'])->name('insurance-loss-ratios.pdf');

//Register
Route::post('registers/store-import-excel', [Admin\RegisterController::class, 'storeImportExcel'])->name('registers.store-import-excel');
Route::get('registers/import-excel', [Admin\RegisterController::class, 'importExcel'])->name('registers.import-excel');
Route::get('registers/export-excel-avance', [Admin\RegisterController::class, 'exportExcelAvance'])->name('registers.export-excel-avance');
Route::get('registers/export-excel-face-sheet', [Admin\RegisterController::class, 'exportExcelFaceSheet'])->name('registers.export-excel-face-sheet');
Route::get('registers/export-excel-template', [Admin\RegisterController::class, 'exportExcelTemplate'])->name('registers.export-excel-template');
Route::get('registers/check-car', [Admin\RegisterController::class, 'checkCar'])->name('registers.check-car');
Route::get('registers/select-avance', [Admin\RegisterController::class, 'selectAvance'])->name('registers.select-avance');
Route::get('registers/select-face-sheet', [Admin\RegisterController::class, 'selectFaceSheet'])->name('registers.select-face-sheet');
Route::get('registers/{register}/edit-registered', [Admin\RegisterController::class, 'editRegistered'])->name('registers.edit-registered');
Route::get('registers/{register}/show-registered', [Admin\RegisterController::class, 'showRegistered'])->name('registers.show-registered');
Route::post('registers/store-registered', [Admin\RegisterController::class, 'storeRegistered'])->name('registers.store-registered');
Route::resource('registers', Admin\RegisterController::class);


//Ownership Transfer
Route::get('ownership-transfers/export-pdf-transfer', [Admin\OwnershipTransferController::class, 'exportTransferPdf'])->name('ownership-transfers.export-pdf-transfer');
Route::get('ownership-transfers/export-pdf-attorney', [Admin\OwnershipTransferController::class, 'exportPowerAttorneyPdf'])->name('ownership-transfers.export-pdf-attorney');
Route::get('ownership-transfers/import-excel', [Admin\OwnershipTransferController::class, 'importExcel'])->name('ownership-transfers.import-excel');
Route::post('ownership-transfers/store-import-excel', [Admin\OwnershipTransferController::class, 'storeImportExcel'])->name('ownership-transfers.store-import-excel');
Route::get('ownership-transfers/export-excel-template', [Admin\OwnershipTransferController::class, 'exportExcelTemplate'])->name('ownership-transfers.export-excel-template');
Route::get('ownership-transfers/export-excel-face-sheet', [Admin\OwnershipTransferController::class, 'exportExcelFaceSheet'])->name('ownership-transfers.export-excel-face-sheet');
Route::get('ownership-transfers/export-excel-avance', [Admin\OwnershipTransferController::class, 'exportExcelAvance'])->name('ownership-transfers.export-excel-avance');
Route::get('ownership-transfers/check-car', [Admin\OwnershipTransferController::class, 'checkCarTransfer'])->name('ownership-transfers.check-car-transfer');
Route::get('ownership-transfers/check-car-transfer', [Admin\OwnershipTransferController::class, 'checkCar'])->name('ownership-transfers.check-car');
Route::get('ownership-transfers/{ownership_transfer}/show-waiting-transfer', [Admin\OwnershipTransferController::class, 'showWaitingTransfer'])->name('ownership-transfers.show-waiting-transfer');
Route::get('ownership-transfers/{ownership_transfer}/edit-waiting-transfer', [Admin\OwnershipTransferController::class, 'editWaitingTransfer'])->name('ownership-transfers.edit-waiting-transfer');
Route::get('ownership-transfers/{ownership_transfer}/show-transfering', [Admin\OwnershipTransferController::class, 'showTransfering'])->name('ownership-transfers.show-transfering');
Route::get('ownership-transfers/{ownership_transfer}/edit-transfering', [Admin\OwnershipTransferController::class, 'editTransfering'])->name('ownership-transfers.edit-transfering');
Route::post('ownership-transfers/store-transfering', [Admin\OwnershipTransferController::class, 'storeTransfering'])->name('ownership-transfers.store-transfering');
Route::post('ownership-transfers/store-waiting-transfer', [Admin\OwnershipTransferController::class, 'storeWaitingTransfer'])->name('ownership-transfers.store-waiting-transfer');
Route::resource('ownership-transfers', Admin\OwnershipTransferController::class);

//Request Change Registration
Route::get('request-change-registrations/get-user-detail', [Admin\RequestChangeRegistrationController::class, 'getUserDetail'])->name('request-change-registrations.get-user-detail');
// Route::get('request-change-registrations/default-data-car', [Admin\RequestChangeRegistrationController::class, 'getDefaultDataCar'])->name('request-change-registrations.default-data-car');
Route::resource('request-change-registrations', Admin\RequestChangeRegistrationController::class);

//Change Registration
Route::resource('change-registrations', Admin\ChangeRegistrationController::class);

//Tax Renewal
Route::get('tax-renewals/{tax_renewal}/show-waiting-send-tax-register-book', [Admin\TaxRenewalController::class, 'showWaitingSendTaxRegisterBook'])->name('tax-renewals.show-waiting-send-tax-register-book');
Route::get('tax-renewals/{tax_renewal}/edit-waiting-send-tax-register-book', [Admin\TaxRenewalController::class, 'editWaitingSendTaxRegisterBook'])->name('tax-renewals.edit-waiting-send-tax-register-book');
Route::get('tax-renewals/{tax_renewal}/show-taxing', [Admin\TaxRenewalController::class, 'showTaxing'])->name('tax-renewals.show-taxing');
Route::get('tax-renewals/{tax_renewal}/edit-taxing', [Admin\TaxRenewalController::class, 'editTaxing'])->name('tax-renewals.edit-taxing');
Route::get('tax-renewals/{tax_renewal}/show-waiting-send-tax', [Admin\TaxRenewalController::class, 'showWaitingSendTaxRenew'])->name('tax-renewals.show-waiting-send-tax');
Route::get('tax-renewals/{tax_renewal}/edit-waiting-send-tax', [Admin\TaxRenewalController::class, 'editWaitingSendTaxRenew'])->name('tax-renewals.edit-waiting-send-tax');
Route::post('tax-renewals/store-waiting-send-tax', [Admin\TaxRenewalController::class, 'storeWaitingSendTax'])->name('tax-renewals.store-waiting-send-tax');
Route::post('tax-renewals/store-taxing', [Admin\TaxRenewalController::class, 'storeTaxing'])->name('tax-renewals.store-taxing');
Route::post('tax-renewals/store-waiting-send-tax-register-book', [Admin\TaxRenewalController::class, 'storeWaitingSendTaxRegisterBook'])->name('tax-renewals.store-waiting-send-tax-register-book');
Route::resource('tax-renewals', Admin\TaxRenewalController::class);

//Sign Yellow Ticket
Route::get('sign-yellow-tickets/driving-job', [Admin\SignYellowTicketController::class, 'getDrivingJob'])->name('sign-yellow-tickets.driving-job');
Route::get('sign-yellow-tickets/default-data-car', [Admin\SignYellowTicketController::class, 'getDefaultDataCar'])->name('sign-yellow-tickets.default-data-car');
Route::post('sign-yellow-tickets/store-mistake', [Admin\SignYellowTicketController::class, 'storeMistake'])->name('sign-yellow-tickets.store-mistake');
Route::post('sign-yellow-tickets/store-paid-fine', [Admin\SignYellowTicketController::class, 'storePaidFine'])->name('sign-yellow-tickets.store-paid-fine');
Route::post('sign-yellow-tickets/store-paid', [Admin\SignYellowTicketController::class, 'storePaid'])->name('sign-yellow-tickets.store-paid');
Route::resource('sign-yellow-tickets', Admin\SignYellowTicketController::class);


//Car Auction + Master Data
Route::resource('auction-places', Admin\AuctionPlaceController::class);
Route::post('selling-prices/sale-price', [Admin\SellingPriceController::class, 'saveSalePrice'])->name('selling-prices.sale-price');
Route::post('selling-prices/request-approve', [Admin\SellingPriceController::class, 'saveRequestApprove'])->name('selling-prices.request-approve');
Route::post('selling-prices/sale-price-new', [Admin\SellingPriceController::class, 'saveSalePriceNew'])->name('selling-prices.sale-price-new');
Route::resource('selling-prices', Admin\SellingPriceController::class);
Route::resource('selling-price-approves', Admin\SellingPriceApproveController::class);
Route::resource('selling-cars', Admin\SellingCarController::class);
Route::post('car-auctions/cancel-cmi-vmi', [Admin\CarAuctionController::class, 'saveStatusCancel'])->name('car-auctions.cancel-cmi-vmi');
Route::post('car-auctions/save-key', [Admin\CarAuctionController::class, 'saveStatusKey'])->name('car-auctions.save-key');
Route::post('car-auctions/send-auction', [Admin\CarAuctionController::class, 'saveSendAuction'])->name('car-auctions.send-auction');
Route::post('car-auctions/save-book', [Admin\CarAuctionController::class, 'saveStatusBook'])->name('car-auctions.save-book');
Route::post('car-auctions/change-auction', [Admin\CarAuctionController::class, 'saveChangeAuction'])->name('car-auctions.change-auction');
Route::get('car-auctions/attorney-pdf', [Admin\CarAuctionController::class, 'printPowerAttorneyPdf'])->name('car-auctions.attorney-pdf');
Route::get('car-auctions/sale-confirm-pdf', [Admin\CarAuctionController::class, 'printSaleConfirmPdf'])->name('car-auctions.sale-confirm-pdf');
Route::get('car-auctions/sale-summary-pdf', [Admin\CarAuctionController::class, 'printSaleSummaryPdf'])->name('car-auctions.sale-summary-pdf');
Route::get('car-auctions/download-excel', [Admin\CarAuctionController::class, 'exportDownloadExcel'])->name('car-auctions.download-excel');
Route::resource('car-auctions', Admin\CarAuctionController::class);

//Finance
Route::resource('finance', Admin\FinanceController::class);
//Finance Request
Route::get('finance-request/finance-request-excel', [Admin\FinanceRequestController::class, 'indexExcel'])->name('finance-request.finance-request-excel');
Route::get('finance-request/finance-request-car-detail/{finance_request_id}', [Admin\FinanceRequestController::class, 'showCarDetail'])->name('finance-request.finance-request-car-detail.show');
Route::get('finance-request/finance-request-export-excel', [Admin\FinanceRequestController::class, 'exportExcelFinanceRequest'])->name('finance-request.finance-request-export-excel');
Route::get('finance-request/finance-request-export-dealer-excel', [Admin\FinanceRequestController::class, 'exportExcelDealerFinanceRequest'])->name('finance-request.finance-request-export-dealer-excel');


Route::resource('finance-request', Admin\FinanceRequestController::class);

//Finance Request Approve
Route::get('finance-request-approve/finance-request-car-detail/{finance_request_id}', [Admin\FinanceRequestApproveController::class, 'showCarDetail'])->name('finance-request-approve.finance-request-car-detail.show');
Route::post('finance-request-approve/finance-request-update-status', [Admin\FinanceRequestApproveController::class, 'updateStatus'])->name('finance-request-approve.finance-request-update-status');
Route::resource('finance-request-approve', Admin\FinanceRequestApproveController::class);
//Finance Request Contract
Route::get('finance-contract/finance-contract-excel', [Admin\FinanceContractController::class, 'indexExcel'])->name('finance-contract.finance-contract-excel');
Route::get('finance-contract/finance-contract-export-excel', [Admin\FinanceContractController::class, 'exportExcel'])->name('finance-contract.finance-contract-export-excel');
Route::resource('finance-contract', Admin\FinanceContractController::class);

Route::get('maintenance-cost/maintenance-cost-excel-data', [Admin\MaintenanceCostController::class, 'getExcelData'])->name('maintenance-cost.excel-data');
Route::get('maintenance-cost/maintenance-cost-export-excel', [Admin\MaintenanceCostController::class, 'exportExcel'])->name('maintenance-cost.export-excel');
Route::resource('maintenance-cost', Admin\MaintenanceCostController::class);

//SAP Interfaces
Route::post('sap-interfaces/export', [Admin\SAPInterfaceController::class, 'exportSAPInterface'])->name('sap-interfaces.export');
Route::resource('sap-interfaces', Admin\SAPInterfaceController::class);

// Read notification
Route::post('read-notification', [Admin\NotificationController::class, 'read'])->name('read-notification');

// Litigation
Route::resource('litigations', Admin\LitigationController::class);

// Litigation Approve
Route::post('ligations-approves/update-status', [Admin\LitigationApproveController::class, 'updateStatus'])->name('litigation-approves.update-status');
Route::resource('litigation-approves', Admin\LitigationApproveController::class);

// Traffic Tickets
Route::resource('traffic-tickets', Admin\TrafficTicketController::class);

// M-Flow
Route::get('m-flows/car-data', [Admin\MFlowController::class, 'getCarData'])->name('m-flows.car-data');
Route::post('m-flows/store-close', [Admin\MFlowController::class, 'storeClose'])->name('m-flows.store-close');
Route::resource('m-flows', Admin\MFlowController::class);

// Compensation
Route::resource('compensations', Admin\CompensationController::class);
Route::post('compensation-approves/update-status', [Admin\CompensationApproveController::class, 'updateStatus'])->name('compensation-approves.update-status');
Route::resource('compensation-approves', Admin\CompensationApproveController::class);

// AR
Route::get('income-accounts/income-list', [Admin\IncomeAccountController::class, 'getIncomeList'])->name('income-accounts.income-list');
Route::post('income-accounts/export', [Admin\IncomeAccountController::class, 'exportSAPInterface'])->name('income-accounts.export');
Route::resource('income-accounts', Admin\IncomeAccountController::class);

// AP
Route::resource('expense-accounts', Admin\ExpenseAccountController::class);
Route::resource('pay-premiums', Admin\PayPremiumController::class);
Route::resource('record-petty-cashes', Admin\RecordPettyCashController::class);
Route::resource('check-petty-cashes', Admin\CheckPettyCashController::class);
Route::resource('record-other-expenses', Admin\RecordOtherExpenseController::class);

// Invoice
Route::resource('invoice-lt-rentals', Admin\InvoiceLTRentalController::class);
Route::resource('invoice-st-rentals', Admin\InvoiceSTRentalController::class);
Route::resource('invoice-others', Admin\InvoiceOtherController::class);

// Credit Note
Route::resource('credit-notes', Admin\CreditNoteController::class);

//Debt Collection
// Route::get('debt-collections/pdf-invoice', [Admin\DebtCollectionController::class, 'printPdfInvoice'])->name('debt-collections.pdf-invoice');
Route::resource('debt-collections', Admin\DebtCollectionController::class);

//Check Billing Date
Route::resource('check-billings', Admin\CheckBillingController::class);

//Asset Car
Route::get('asset-cars/asset-excel-list', [Admin\AssetCarController::class, 'getAssetExcelList'])->name('asset-cars.asset-excel-list');
Route::post('asset-cars/export', [Admin\AssetCarController::class, 'exportAssetCar'])->name('asset-cars.export');
Route::resource('asset-cars', Admin\AssetCarController::class);

//Util Select2
Route::name('util.')->prefix('util')->group(function () {
    // User
    Route::get('select2/departments', [Admin\Util\Select2Controller::class, 'getDepartments'])->name('select2.departments');
    Route::get('select2/sections', [Admin\Util\Select2Controller::class, 'getSections'])->name('select2.sections');
    Route::get('select2/roles', [Admin\Util\Select2Controller::class, 'getRoles'])->name('select2.roles');
    Route::get('select2/users', [Admin\Util\Select2Controller::class, 'getUsers'])->name('select2.users');

    // Helper
    Route::get('helper/car-detail', [Admin\Util\HelperController::class, 'carDetail'])->name('helper.car-detail');

    // Car
    Route::get('select2/car-brand', [Admin\Util\Select2Controller::class, 'getCarBrand'])->name('select2.car-brand');
    Route::get('select2/car-type', [Admin\Util\Select2Controller::class, 'getCarType'])->name('select2.car-type');
    Route::get('select2/car-category', [Admin\Util\Select2Controller::class, 'getCarCategory'])->name('select2.car-category');
    Route::get('select2/car-category-by-car-class', [Admin\Util\Select2Controller::class, 'getCarCategoryByCarClasses'])->name('select2.car-category-by-car-class');
    Route::get('select2/car-group', [Admin\Util\Select2Controller::class, 'getCarGroups'])->name('select2.car-groups');
    Route::get('select2/car-colors', [Admin\Util\Select2Controller::class, 'getCarColors'])->name('select2.car-colors');
    Route::get('select2/accessories', [Admin\Util\Select2Controller::class, 'getAccessories'])->name('select2.accessories');
    Route::get('select2/accessories-type-accessory', [Admin\Util\Select2Controller::class, 'getAccessoriesTypeAccessory'])->name('select2.accessories-type-accessory');
    Route::get('select2/accessories-bom', [Admin\Util\Select2Controller::class, 'getAccessoriesBom'])->name('select2.accessories-bom');
    Route::get('select2/accessory-versions', [Admin\Util\Select2Controller::class, 'getAccessoryVersions'])->name('select2.accessory-versions');
    Route::get('select2/dealers', [Admin\Util\Select2Controller::class, 'getDealers'])->name('select2.dealers');
    Route::get('select2/pr-parent', [Admin\Util\Select2Controller::class, 'getPRParent'])->name('select2.pr-parent');
    Route::get('select2/car-class', [Admin\Util\Select2Controller::class, 'getCarClasses'])->name('select2.car-class');
    Route::get('select2/car-class-by-car-brand', [Admin\Util\Select2Controller::class, 'getCarClassesByCarBrand'])->name('select2.car-class-by-car-brand');
    Route::get('select2/car-class-colors', [Admin\Util\Select2Controller::class, 'getCarClassColor'])->name('select2.car-class-colors');
    Route::get('select2/location-groups', [Admin\Util\Select2Controller::class, 'getLocationGroups'])->name('select2.location-groups');
    Route::get('select2/locations', [Admin\Util\Select2Controller::class, 'getLocations'])->name('select2.locations');
    Route::get('select2/service-types', [Admin\Util\Select2Controller::class, 'getServiceTypes'])->name('select2.service-types');
    Route::get('select2/products', [Admin\Util\Select2Controller::class, 'getProducts'])->name('select2.products');
    Route::get('select2/product-skus', [Admin\Util\Select2Controller::class, 'getProductSkus'])->name('select2.product-skus');
    Route::get('select2/product-additionals', [Admin\Util\Select2Controller::class, 'getProductAdditionals'])->name('select2.product-additionals');
    Route::get('select2/car-license-plate', [Admin\Util\Select2Controller::class, 'getCarLicensePlate'])->name('select2.car-license-plate');
    Route::get('select2/car-license-plate-by-contract', [Admin\Util\Select2Controller::class, 'getCarLicensePlateByContract'])->name('select2.car-license-plate-by-contract');
    Route::get('select2/car-license-plate-rental', [Admin\Util\Select2Controller::class, 'getCarLicensePlateShortRental'])->name('select2.car-license-plate-rental');
    Route::get('select2/car-engine-no', [Admin\Util\Select2Controller::class, 'getCarEngineNo'])->name('select2.car-engine-no');
    Route::get('select2/car-engine-no-rental-type', [Admin\Util\Select2Controller::class, 'getCarEngineNoRentalType'])->name('select2.car-engine-no-rental-type');
    Route::get('select2/car-chassis-no', [Admin\Util\Select2Controller::class, 'getCarChassisNo'])->name('select2.car-chassis-no');
    Route::get('select2/car-chassis-no-rental-type', [Admin\Util\Select2Controller::class, 'getCarChassisNoRentalType'])->name('select2.car-chassis-no-rental-type');
    Route::get('select2/car-park-zone-code-name', [Admin\Util\Select2Controller::class, 'getCarParkZoneCodeName'])->name('select2.car-park-zone-code-name');
    Route::get('select2/car-park-area-number', [Admin\Util\Select2Controller::class, 'getCarParkAreaNumber'])->name('select2.car-park-area-number');
    Route::get('select2/car-park-zone', [Admin\Util\Select2Controller::class, 'getCarParkZone'])->name('select2.car-park-zone');
    Route::get('select2/car-park', [Admin\Util\Select2Controller::class, 'getCarPark'])->name('select2.car-park');
    Route::get('select2/car-park-free', [Admin\Util\Select2Controller::class, 'getCarParkFree'])->name('select2.car-park-free');
    Route::get('select2/car-park-car-in-parking', [Admin\Util\Select2Controller::class, 'getCarParkCarInParking'])->name('select2.car-park-car-in-parking');
    Route::get('select2/car-park-car-outside-parking', [Admin\Util\Select2Controller::class, 'getCarParkCarOutsideParking'])->name('select2.car-park-car-outside-parking');
    Route::get('select2/branches', [Admin\Util\Select2Controller::class, 'getBranch'])->name('select2.branches');
    Route::get('select2/products-by-branch', [Admin\Util\Select2Controller::class, 'getProductByBranch'])->name('select2.products-by-branch');
    Route::get('select2/origins-by-branch', [Admin\Util\Select2Controller::class, 'getOriginByBranch'])->name('select2.origins-by-branch');
    Route::get('select2/destinations-by-branch', [Admin\Util\Select2Controller::class, 'getDestinationByBranch'])->name('select2.destinations-by-branch');
    Route::get('select2/driving-skill', [Admin\Util\Select2Controller::class, 'getDrivingSkill'])->name('select2.driving-skill');
    Route::get('select2/driver-wage', [Admin\Util\Select2Controller::class, 'getDriverWage'])->name('select2.driver-wage');
    Route::get('select2/driver', [Admin\Util\Select2Controller::class, 'getDriver'])->name('select2.driver');
    Route::get('select2/zone-type', [Admin\Util\Select2Controller::class, 'getZoneType'])->name('select2.zone-types');
    Route::get('select2/product-additional-detail', [Admin\Util\Select2Controller::class, 'getProductAdditionalDetail'])->name('select2.product-additional-detail');
    Route::get('select2/product-detail', [Admin\Util\Select2Controller::class, 'getProductDetail'])->name('select2.product-detail');
    Route::get('select2/car-license', [Admin\Util\Select2Controller::class, 'getCarLicense'])->name('select2.car-license');

    // Customer
    Route::get('select2-customer/customer-codes', [Admin\Util\Select2CustomerController::class, 'getCustomerCode'])->name('select2-customer.customer-codes');
    Route::get('select2-customer/customer-detail', [Admin\Util\Select2CustomerController::class, 'getCustomerDetail'])->name('select2-customer.customer-detail');
    Route::get('select2-customer/customers', [Admin\Util\Select2CustomerController::class, 'getCustomers'])->name('select2-customer.customers');
    Route::get('select2-customer/customer-billing-address', [Admin\Util\Select2CustomerController::class, 'getCustomerBillingAddress'])->name('select2-customer.customer-billing-address');
    Route::get('select2-customer/customer-billing-detail', [Admin\Util\Select2CustomerController::class, 'getCustomerBillingDetail'])->name('select2-customer.customer-billing-detail');

    // Promotion
    Route::get('select2/promotion', [Admin\Util\Select2Controller::class, 'getPromotion'])->name('select2.promotion');

    //Select2 Car
    Route::get('select2-car/cars-by-class', [Admin\Util\Select2CarController::class, 'getCarsByClass'])->name('select2-car.cars-by-class');
    Route::get('select2-car/cars-by-code', [Admin\Util\Select2CarController::class, 'getCarsByCarCode'])->name('select2-car.cars-by-code');
    Route::get('select2-car/cars-by-license-plate', [Admin\Util\Select2CarController::class, 'getCarsByLicensePlate'])->name('select2-car.cars-by-license-plate');

    Route::get('select2-car/car-classes-by-rental-category', [Admin\Util\Select2CarController::class, 'getCarClassesByRentalCategory'])->name('select2-car.car-classes-by-rental-category');
    Route::get('select2-car/cars-by-class-and-rental-category', [Admin\Util\Select2CarController::class, 'getCarByClassAndRentalCategory'])->name('select2-car.cars-by-class-and-rental-category');
    Route::get('select2-car/car-parks', [Admin\Util\Select2Controller::class, 'getCarParkNumber'])->name('select2-car.car-parks');

    //Select2 Driver
    Route::get('select2-driver/default-job', [Admin\Util\Select2DriverController::class, 'getDefaultJobByID'])->name('select2-driver.default-job');
    Route::get('select2-driver/driver-wage-not-month', [Admin\Util\Select2DriverController::class, 'getDriverWageNotMonth'])->name('select2-driver.driver-wage-not-month');
    Route::get('select2/parent-driving-job', [Admin\Util\Select2DriverController::class, 'getParentDrivinfJob'])->name('select2-driver.parent-driving-job');
    Route::get('select2-driver/driving-job-status', [Admin\Util\Select2DriverController::class, 'getDrivinfJobStatusList'])->name('select2-driver.driving-job-status');

    // Provinces & Geographies
    Route::get('select2/provinces', [Admin\Util\Select2Controller::class, 'getProvinces'])->name('select2.provinces');
    Route::get('select2/districts', [Admin\Util\Select2Controller::class, 'getDistricts'])->name('select2.districts');
    Route::get('select2/subdistricts', [Admin\Util\Select2Controller::class, 'getSubdistricts'])->name('select2.subdistricts');
    Route::get('select2/geographies', [Admin\Util\Select2Controller::class, 'getGeographies'])->name('select2.geographies');

    // Rental
    Route::get('select2-rental/lt-rental-by-compare-price-status', [Admin\Util\Select2RentalController::class, 'getLongTermRentalsAllComparePriceStatus'])->name('select2-rental.lt-rental-by-compare-status');
    Route::get('select2-rental/lt-rental-for-compare-price-approve', [Admin\Util\Select2RentalController::class, 'getLongTermRentalsForComparePriceApprove'])->name('select2-rental.lt-rental-for-compare-price-approve');
    Route::get('select2-rental/lt-rental-line-car-classes', [Admin\Util\Select2RentalController::class, 'getCarClassLongTermRentalLines'])->name('select2-rental.lt-rental-line-car-classes');
    Route::get('select2-rental/lt-rental-months', [Admin\Util\Select2RentalController::class, 'getLongTermRentalMonths'])->name('select2-rental.lt-rental-months');
    Route::get('select2-rental/lt-rental-line-car-amount', [Admin\Util\Select2RentalController::class, 'getLongTermRentalLineCarAmount'])->name('select2-rental.lt-rental-line-car-amount');
    Route::get('select2-rental/worksheet-by-flow', [Admin\Util\Select2RentalController::class, 'getWorkSheetByInspectionFlow'])->name('select2-rental.worksheet-by-flow');
    Route::get('select2-rental/lt-rental-by-bom', [Admin\Util\Select2RentalController::class, 'getLongTermRentalByBom'])->name('select2-rental.lt-rental-by-bom');
    Route::get('select2-rental/car-class', [Admin\Util\Select2RentalController::class, 'getCarClasses'])->name('select2-rental.car-class');
    Route::get('select2-rental/accessories-type-accessory', [Admin\Util\Select2RentalController::class, 'getAccessoriesTypeAccessory'])->name('select2-rental.accessories-type-accessory');

    // Install Equipment
    Route::get('select2-install-equipment/accessories', [Admin\Util\Select2InstallEquipmentController::class, 'getAccessoryList'])->name('select2-install-equipment.accessories');
    Route::get('select2-install-equipment/cars-by-po', [Admin\Util\Select2InstallEquipmentController::class, 'getCarInPurchaseOrderList'])->name('select2-install-equipment.cars-by-po');
    // Route::get('select2-install-equipment/accessory-codes', [Admin\Util\Select2InstallEquipmentController::class, 'getAccessoryCodeList'])->name('select2-install-equipment.accessory-codes');
    Route::get('select2-install-equipment/suppliers', [Admin\Util\Select2InstallEquipmentController::class, 'getAccessorySupplierList'])->name('select2-install-equipment.suppliers');
    Route::get('select2-install-equipment/purchase-orders', [Admin\Util\Select2InstallEquipmentController::class, 'getPurchaseOrderList'])->name('select2-install-equipment.purchase-orders');
    Route::get('select2-install-equipment/car-exist-purchase-orders', [Admin\Util\Select2InstallEquipmentController::class, 'getPurchaseOrderCarExistedList'])->name('select2-install-equipment.car-exist-purchase-orders');
    Route::get('select2-install-equipment/supplier-install-equipment', [Admin\Util\Select2InstallEquipmentController::class, 'getSupplierList'])->name('select2-install-equipment.supplier-install-equipment');
    Route::get('select2-install-equipment/install-equipment-by-supplier', [Admin\Util\Select2InstallEquipmentController::class, 'getInstallEquipmentBySupplierList'])->name('select2-install-equipment.install-equipment-by-supplier');
    Route::get('select2-install-equipment/install-equipment-po-by-supplier', [Admin\Util\Select2InstallEquipmentController::class, 'getInstallEquipmentPOBySupplierList'])->name('select2-install-equipment.install-equipment-po-by-supplier');
    Route::get('select2-install-equipment/accessory-boms', [Admin\Util\Select2InstallEquipmentController::class, 'getAccessoryBOMList'])->name('select2-install-equipment.accessory-boms');
    Route::get('select2-install-equipment/lot-no', [Admin\Util\Select2InstallEquipmentController::class, 'getLotNumber'])->name('select2-install-equipment.lot-no');
    Route::get('select2-install-equipment/all-install-equipments', [Admin\Util\Select2InstallEquipmentController::class, 'getAllInstallEquipmentList'])->name('select2-install-equipment.all-install-equipments');
    Route::get('select2-install-equipment/po-of-install-equipments', [Admin\Util\Select2InstallEquipmentController::class, 'getPOAlreadyCreatedInstallEquipmentList'])->name('select2-install-equipment.po-of-install-equipments');
    Route::get('select2-install-equipment/chassis-install-equipments', [Admin\Util\Select2InstallEquipmentController::class, 'getChassisInstallEquipmentList'])->name('select2-install-equipment.chassis-install-equipments');
    Route::get('select2-install-equipment/license-plate-install-equipments', [Admin\Util\Select2InstallEquipmentController::class, 'getLicensePlateInstallEquipmentList'])->name('select2-install-equipment.license-plate-install-equipments');

    // Replacement Car
    Route::get('select2-replacement-car/jobs', [Admin\Util\Select2ReplacementCarController::class, 'getReplacementJob'])->name('select2-replacement-car.jobs');
    Route::get('select2-replacement-car/replacement-cars', [Admin\Util\Select2ReplacementCarController::class, 'getReplacementCars'])->name('select2-replacement-car.replacement-cars');
    Route::get('select2-replacement-car/replacement-main-cars', [Admin\Util\Select2ReplacementCarController::class, 'getReplacementMainCars'])->name('select2-replacement-car.replacement-main-cars');
    Route::get('select2-replacement-car/replacement-replace-cars', [Admin\Util\Select2ReplacementCarController::class, 'getReplacementReplaceCars'])->name('select2-replacement-car.replacement-replace-cars');

    Route::get('select2-replacement-car/job-detail', [Admin\Util\Select2ReplacementCarController::class, 'getReplacementJobDetail'])->name('select2-replacement-car.job-detail');
    Route::get('select2-replacement-car/available-replacement-cars', [Admin\Util\Select2ReplacementCarController::class, 'getAvailableReplacementCars'])->name('select2-replacement-car.available-replacement-cars');

    // Garage
    Route::get('select2-garage/province', [Admin\Util\Select2GarageController::class, 'getProvince'])->name('select2-garage.province');
    Route::get('select2-garage/amphure', [Admin\Util\Select2GarageController::class, 'getAmphureByProvince'])->name('select2-garage.amphure');
    Route::get('select2-garage/district', [Admin\Util\Select2GarageController::class, 'getDistrictByAmphure'])->name('select2-garage.district');

    // Request Receipt
    Route::get('select2-request-receipt/province', [Admin\Util\Select2RequestReceiptController::class, 'getProvince'])->name('select2-request-receipt.province');
    Route::get('select2-request-receipt/amphure', [Admin\Util\Select2RequestReceiptController::class, 'getAmphureByProvince'])->name('select2-request-receipt.amphure');
    Route::get('select2-request-receipt/district', [Admin\Util\Select2RequestReceiptController::class, 'getDistrictByAmphure'])->name('select2-request-receipt.district');
    Route::get('select2-request-receipt/zipcode', [Admin\Util\Select2RequestReceiptController::class, 'getZipcode'])->name('select2-request-receipt.zipcode');

    Route::get('select2-garage/cradle', [Admin\Util\Select2GarageController::class, 'getCradleByProvince'])->name('select2-garage.cradle');

    // CMI
    Route::get('select2-cmi/cmi-worksheets', [Admin\Util\Select2CMIController::class, 'getCMIWorksheets'])->name('select2-cmi.cmi-worksheets');
    Route::get('select2-cmi/cmi-license-plates', [Admin\Util\Select2CMIController::class, 'getCMILicensePlates'])->name('select2-cmi.cmi-license-plates');
    Route::get('select2-cmi/cmi-insurers', [Admin\Util\Select2CMIController::class, 'getCMIInsurers'])->name('select2-cmi.cmi-insurers');
    Route::get('select2-cmi/cmi-pos', [Admin\Util\Select2CMIController::class, 'getCMIPOs'])->name('select2-cmi.cmi-pos');
    Route::get('select2-cmi/cmi-lots', [Admin\Util\Select2CMIController::class, 'getCMILots'])->name('select2-cmi.cmi-lots');
    Route::get('select2-cmi/cancel-cmi-worksheets', [Admin\Util\Select2CMIController::class, 'getCancelCMIWorkSheets'])->name('select2-cmi.cancel-cmi-worksheets');
    Route::get('select2-cmi/cancel-cmi-license-plates', [Admin\Util\Select2CMIController::class, 'getCancelCMILicensePlates'])->name('select2-cmi.cancel-cmi-license-plates');
    Route::get('select2-cmi/cancel-cmi-insurers', [Admin\Util\Select2CMIController::class, 'getCancelCMIInsurers'])->name('select2-cmi.cancel-cmi-insurers');

    // VMI
    Route::get('select2-vmi/vmi-worksheets', [Admin\Util\Select2VMIController::class, 'getVMIWorksheets'])->name('select2-vmi.vmi-worksheets');
    Route::get('select2-vmi/vmi-license-plates', [Admin\Util\Select2VMIController::class, 'getVMILicensePlates'])->name('select2-vmi.vmi-license-plates');
    Route::get('select2-vmi/vmi-insurers', [Admin\Util\Select2VMIController::class, 'getVMIInsurers'])->name('select2-vmi.vmi-insurers');
    Route::get('select2-vmi/vmi-pos', [Admin\Util\Select2VMIController::class, 'getVMIPOs'])->name('select2-vmi.vmi-pos');
    Route::get('select2-vmi/vmi-lots', [Admin\Util\Select2VMIController::class, 'getVMILots'])->name('select2-vmi.vmi-lots');
    Route::get('select2-vmi/insurance-packages', [Admin\Util\Select2VMIController::class, 'getInsurancePackages'])->name('select2-vmi.insurance-packages');
    Route::get('select2-cmi/cancel-vmi-worksheets', [Admin\Util\Select2VMIController::class, 'getCancelVMIWorkSheets'])->name('select2-vmi.cancel-vmi-worksheets');
    Route::get('select2-cmi/cancel-vmi-license-plates', [Admin\Util\Select2VMIController::class, 'getCancelVMILicensePlates'])->name('select2-vmi.cancel-vmi-license-plates');
    Route::get('select2-cmi/cancel-vmi-insurers', [Admin\Util\Select2VMIController::class, 'getCancelVMIInsurers'])->name('select2-vmi.cancel-vmi-insurers');


    Route::get('select2-repair/repair-code-list', [Admin\Util\Select2RepairController::class, 'getRepairCodeList'])->name('select2-repair.repair-code-list');
    Route::get('select2-repair/repair-name-list', [Admin\Util\Select2RepairController::class, 'getRepairNameList'])->name('select2-repair.repair-name-list');
    Route::get('select2-repair/slide-list', [Admin\Util\Select2RepairController::class, 'getSlideList'])->name('select2-repair.slide-list');
    Route::get('select2-repair/bill-recipient', [Admin\Util\Select2RepairController::class, 'getBillRecipient'])->name('select2-repair.bill-recipient');
    Route::get('select2-repair/geographie', [Admin\Util\Select2RepairController::class, 'getGeographies'])->name('select2-repair.geographie');
    Route::get('select2-repair/creditor-services', [Admin\Util\Select2RepairController::class, 'getCreditorServices'])->name('select2-repair.creditor-services');
    Route::get('select2-repair/repair-bill-no', [Admin\Util\Select2RepairController::class, 'getRepairBillNoList'])->name('select2-repair.repair-bill-no');
    Route::get('select2-repair/repair-car-list', [Admin\Util\Select2RepairController::class, 'getCarList'])->name('select2-repair.repair-car-list');
    Route::get('select2-repair/repair-worksheet-no', [Admin\Util\Select2RepairController::class, 'getRepairWorksheetNo'])->name('select2-repair.repair-worksheet-no-list');
    Route::get('select2-repair/repair-list-item', [Admin\Util\Select2RepairController::class, 'getRepairListItem'])->name('select2-repair.repair-list-item');
    Route::get('select2-repair/repair-invoice-no', [Admin\Util\Select2RepairController::class, 'getInvoiceNo'])->name('select2-repair.repair-invoice-no');


    // Register
    Route::get('select2-register/get-status-facesheet', [Admin\Util\Select2RegisterController::class, 'getStatusFaceSheetList'])->name('select2-register.get-status-facesheet');
    Route::get('select2-register/get-status', [Admin\Util\Select2RegisterController::class, 'getStatusRegisteredList'])->name('select2-register.get-status');
    Route::get('select2-register/lot', [Admin\Util\Select2RegisterController::class, 'getLotList'])->name('select2-register.lot-list');
    Route::get('select2-register/license-plate', [Admin\Util\Select2RegisterController::class, 'getLicensePlateList'])->name('select2-register.license-plate-list');
    Route::get('select2-register/car-class', [Admin\Util\Select2RegisterController::class, 'getCarClasses'])->name('select2-register.car-class-list');

    //Ownership Transfer
    Route::get('select2-ownership-transfer/get-status-facesheet', [Admin\Util\Select2OwnershipTransferController::class, 'getStatusFaceSheetList'])->name('select2-ownership-transfer.get-status-facesheet');
    Route::get('select2-ownership-transfer/get-status', [Admin\Util\Select2OwnershipTransferController::class, 'getStatusList'])->name('select2-ownership-transfer.get-status');
    Route::get('select2-ownership-transfer/contract-no', [Admin\Util\Select2OwnershipTransferController::class, 'getContractNo'])->name('select2-ownership-transfer.contract-no');
    Route::get('select2-ownership-transfer/leasing-list', [Admin\Util\Select2OwnershipTransferController::class, 'getLeasing'])->name('select2-ownership-transfer.leasing-list');

    // Change Registration
    Route::get('select2-change-registration/car-license-plate', [Admin\Util\Select2ChangeRegistrationController::class, 'getCarLicensePlate'])->name('select2-change-registration.car-license-plate');
    Route::get('select2-change-registration/default-data-car', [Admin\Util\Select2ChangeRegistrationController::class, 'getDefaultDataCar'])->name('select2-change-registration.default-data-car');
    Route::get('select2-change-registration/get-user-detail', [Admin\Util\Select2ChangeRegistrationController::class, 'getUserDetail'])->name('select2-change-registration.get-user-detail');


    // Tax Renewal
    Route::get('select2-tax-renewal/car-license-plate', [Admin\Util\Select2TaxRenewalController::class, 'getCarLicensePlate'])->name('select2-tax-renewal.car-license-plate');
    Route::get('select2-tax-renewal/car-class', [Admin\Util\Select2TaxRenewalController::class, 'getCarClasses'])->name('select2-tax-renewal.car-class');

    // Sign Yellow Ticket
    Route::get('select2-sign-yellow-ticket/payment', [Admin\Util\Select2SignYellowTicketController::class, 'getPayment'])->name('select2-sign-yellow-ticket.payment');
    Route::get('select2-sign-yellow-ticket/responsible', [Admin\Util\Select2SignYellowTicketController::class, 'getResponsible'])->name('select2-sign-yellow-ticket.responsible');
    Route::get('select2-sign-yellow-ticket/training', [Admin\Util\Select2SignYellowTicketController::class, 'getTraining'])->name('select2-sign-yellow-ticket.training');
    Route::get('select2-sign-yellow-ticket/mistake', [Admin\Util\Select2SignYellowTicketController::class, 'getMistake'])->name('select2-sign-yellow-ticket.mistake');

    Route::get('select2-sign-yellow-ticket/province', [Admin\Util\Select2SignYellowTicketController::class, 'getProvince'])->name('select2-sign-yellow-ticket.province');
    Route::get('select2-sign-yellow-ticket/car-license-plate', [Admin\Util\Select2SignYellowTicketController::class, 'getCarLicensePlate'])->name('select2-sign-yellow-ticket.car-license-plate');
    Route::get('select2-sign-yellow-ticket/car-class', [Admin\Util\Select2SignYellowTicketController::class, 'getCarClasses'])->name('select2-sign-yellow-ticket.car-class');

    // Accident
    Route::get('select2-accident/user', [Admin\Util\Select2AccidentController::class, 'getUserList'])->name('select2-accident.user-list');
    Route::get('select2-accident/insurer', [Admin\Util\Select2AccidentController::class, 'getInsurerList'])->name('select2-accident.insurer-list');
    Route::get('select2-accident/accident', [Admin\Util\Select2AccidentController::class, 'getAccidentList'])->name('select2-accident.accident-list');
    Route::get('select2-accident/accident-all', [Admin\Util\Select2AccidentController::class, 'getAccidentAllList'])->name('select2-accident.accident-all-list');
    Route::get('select2-accident/garage', [Admin\Util\Select2AccidentController::class, 'getGarageList'])->name('select2-accident.garage-list');
    Route::get('select2-accident/wound', [Admin\Util\Select2AccidentController::class, 'getWoundList'])->name('select2-accident.wound-list');
    Route::get('select2-accident/worksheet', [Admin\Util\Select2AccidentController::class, 'getWorksheetList'])->name('select2-accident.worksheet-list');
    Route::get('select2-accident/license-plate', [Admin\Util\Select2AccidentController::class, 'getLicensePlateList'])->name('select2-accident.license-plate-list');
    Route::get('select2-accident/accident-worksheet', [Admin\Util\Select2AccidentController::class, 'getAccidentWorksheetList'])->name('select2-accident.accident-worksheet-list');

    // Car Auction + Selling Price Util
    Route::get('select2-car-auction/sale-price-license-plates', [Admin\Util\Select2CarAuctionController::class, 'getSalePriceLicensePlates'])->name('select2-car-auction.sale-price-license-plates');
    Route::get('select2-car-auction/sale-price-car-class', [Admin\Util\Select2CarAuctionController::class, 'getSalePriceCarClass'])->name('select2-car-auction.sale-price-car-class');
    Route::get('select2-car-auction/sale-price-worksheets', [Admin\Util\Select2CarAuctionController::class, 'getSalePriceWorksheets'])->name('select2-car-auction.sale-price-worksheets');
    Route::get('select2-car-auction/sale-price-car-class-year', [Admin\Util\Select2CarAuctionController::class, 'getSalePriceCarClassYear'])->name('select2-car-auction.sale-price-car-class-year');
    Route::get('select2-car-auction/sale-price-car-color', [Admin\Util\Select2CarAuctionController::class, 'getSalePriceCarColor'])->name('select2-car-auction.sale-price-car-color');
    Route::get('select2-car-auction/status', [Admin\Util\Select2CarAuctionController::class, 'getStatusList'])->name('select2-car-auction.status');
    Route::get('select2-car-auction/car-ownership', [Admin\Util\Select2CarAuctionController::class, 'getOwnerShipList'])->name('select2-car-auction.car-ownership');
    Route::get('select2-car-auction/sale-car-license-plates', [Admin\Util\Select2CarAuctionController::class, 'getSaleCarLicensePlates'])->name('select2-car-auction.sale-car-license-plates');
    Route::get('select2-car-auction/sale-car-car-class', [Admin\Util\Select2CarAuctionController::class, 'getSaleCarCarClass'])->name('select2-car-auction.sale-car-car-class');
    Route::get('select2-car-auction/auction-places', [Admin\Util\Select2CarAuctionController::class, 'getAuctionPlace'])->name('select2-car-auction.auction-places');

    //Insurance
    Route::get('select2-insurance/customer', [Admin\Util\Select2InsuranceController::class, 'getDataCustomer'])->name('select2-insurance.customer');
    Route::get('select2-insurance/insurance-companies', [Admin\Util\Select2InsuranceController::class, 'getInsuranceCompanies'])->name('select2-insurance.insurance-companies');
    Route::get('select2-insurance/customer-group', [Admin\Util\Select2InsuranceController::class, 'getDataCusotmerGroup'])->name('select2-insurance.customer-group');

    //InsuranceDeduct
    Route::get('select2-insurance-deduct/license-plates', [Admin\Util\Select2InsuranceController::class, 'getDeductLicensePlates'])->name('select2-insurance-deduct.license-plates');
    Route::get('select2-insurance-deduct/insurance', [Admin\Util\Select2InsuranceController::class, 'getDeductInsuranceList'])->name('select2-insurance-deduct.insurance-list');
    Route::get('select2-insurance-deduct/insurance-policy-reference', [Admin\Util\Select2InsuranceController::class, 'getDeductPolicyReferenceList'])->name('select2-insurance-deduct.insurance-policy-reference-list');

    //    Finance
    Route::get('select2-finance/creditor-leasing-list', [Admin\Util\Select2FinanceController::class, 'getCreditorLeasingList'])->name('select2-finance.creditor-leasing-list');
    Route::get('select2-finance/get-lot', [Admin\Util\Select2FinanceController::class, 'getLotList'])->name('select2-finance.get-lot');
    Route::get('select2-finance/get-car', [Admin\Util\Select2FinanceController::class, 'getCarList'])->name('select2-finance.get-car');
    Route::get('select2-finance/get-contract', [Admin\Util\Select2FinanceController::class, 'getContract'])->name('select2-finance.get-contract');
    Route::get('select2-finance/finance-request-get-excel-data', [Admin\Util\Select2FinanceController::class, 'getFinanceRequestExportData'])->name('select2-finance.finance-request-get-excel-data');
    Route::get('select2-finance/finance-contract-get-excel-data', [Admin\Util\Select2FinanceController::class, 'getFinanceContractExportData'])->name('select2-finance.finance-contract-get-excel-data');

    // Litigation
    Route::get('select2-litigation/worksheets', [Admin\Util\Select2LitigationController::class, 'getLitigationWorksheetList'])->name('select2-litigation.worksheets');
    Route::get('select2-litigation/titles', [Admin\Util\Select2LitigationController::class, 'getLitigationTitleList'])->name('select2-litigation.titles');

    // Traffic Ticket
    Route::get('select2-traffic-ticket/worksheets', [Admin\Util\Select2TrafficTicketController::class, 'getWorksheetList'])->name('select2-traffic-ticket.worksheets');
    Route::get('select2-traffic-ticket/cars', [Admin\Util\Select2TrafficTicketController::class, 'getTrafficTicketCarList'])->name('select2-traffic-ticket.cars');
    Route::get('select2-traffic-ticket/police-stations', [Admin\Util\Select2TrafficTicketController::class, 'getPoliceStationList'])->name('select2-traffic-ticket.police-stations');

    // Compensation
    Route::get('select2-compensation/worksheets', [Admin\Util\Select2CompensationController::class, 'getWorksheetList'])->name('select2-compensation.worksheets');
    Route::get('select2-compensation/accidents', [Admin\Util\Select2CompensationController::class, 'getAccidentList'])->name('select2-compensation.accidents');

    // M FLow
    Route::get('select2-m-flow/worksheets', [Admin\Util\Select2MFlowController::class, 'getMFlowWorksheetList'])->name('select2-m-flow.worksheets');
    Route::get('select2-m-flow/cars', [Admin\Util\Select2MFlowController::class, 'getMFlowCarList'])->name('select2-m-flow.cars');
    Route::get('select2-m-flow/m-flow-stations', [Admin\Util\Select2MFlowController::class, 'getMFlowStationList'])->name('select2-m-flow.m-flow-stations');

    // PrepareNewCar
    Route::get('select2-prepare-new-car/get-po-no-list', [Admin\Util\Select2PrepareNewCarController::class, 'getPoData'])->name('select2-prepare-new-car.po-no');
    Route::get('select2-prepare-new-car/get-car-list', [Admin\Util\Select2PrepareNewCarController::class, 'getCarList'])->name('select2-prepare-new-car.get-car-list');

    // ShortTermRental
    Route::get('select2-short-term-rental/get-customer-type', [Admin\Util\Select2ShortTermRentalController::class, 'getCustomerType'])->name('select2-short-term-rental.get-customer-type');
    Route::get('select2-short-term-rental/get-province', [Admin\Util\Select2ShortTermRentalController::class, 'getProvince'])->name('select2-short-term-rental.get-province');
    Route::get('select2-short-term-rental/get-location', [Admin\Util\Select2ShortTermRentalController::class, 'getLocation'])->name('select2-short-term-rental.get-location');

});
