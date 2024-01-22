<?php

namespace App\Http\Controllers\Admin;

use App\Classes\NotificationManagement;
use App\Enums\Actions;
use App\Enums\CarEnum;
use App\Enums\DepartmentEnum;
use App\Enums\InsuranceCarStatusEnum;
use App\Enums\InsuranceRegistrationEnum;
use App\Enums\InsuranceStatusEnum;
use App\Enums\LongTermRentalTypeAccessoryEnum;
use App\Enums\NotificationScopeEnum;
use App\Enums\RegisterStatusEnum;
use App\Enums\Resources;
use App\Exports\ExportCMI;
use App\Http\Controllers\Controller;
use App\Jobs\EffectiveCmiJob;
use App\Models\Accessories;
use App\Models\Car;
use App\Models\CarClass;
use App\Models\CMI;
use App\Models\ImportCarLine;
use App\Models\InsuranceCompanies;
use App\Models\InsuranceLot;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionLineAccessory;
use App\Models\Register;
use App\Models\VMI;
use App\Traits\FinanceTrait;
use App\Traits\InstallEquipmentTrait;
use App\Traits\InsuranceTrait;
use App\Traits\NotificationTrait;
use Carbon\Carbon;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;


class CMICarController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CMI);
        $cmi_id = $request->cmi_id;
        $status = $request->status;
        $lot = $request->lot;
        $cmi_worksheet_no = null;
        if ($cmi_id) {
            $cmi = CMI::find($cmi_id);
            $cmi_worksheet_no = $cmi?->worksheet_no ?? null;
        }

        $license_plate = $request->license_plate;
        $license_plate_text = null;
        if ($license_plate) {
            $car = Car::find($license_plate);
            $license_plate_text = $car?->license_plate ?? null;
        }

        $insurer_id = $request->insurer_id;
        $insurer_name = null;
        if ($insurer_id) {
            $insurer = InsuranceCompanies::find($insurer_id);
            $insurer_name = $insurer?->insurance_name_th ?? null;
        }

        $po_id = $request->po_id;
        $po_no = null;
        if ($po_id) {
            $po = PurchaseOrder::find($po_id);
            $po_no = $po->po_no ?? null;
        }
        $list = CMI::with('insurer', 'car')->select('*')
            ->where('type', InsuranceRegistrationEnum::REGISTER)
            ->sortable(['worksheet_no' => 'desc'])
            ->when($cmi_id, function ($query) use ($cmi_id) {
                $query->where('id', $cmi_id);
            })
            ->when($license_plate, function ($query) use ($license_plate) {
                $query->where('car_id', $license_plate);
            })
            ->when($insurer_id, function ($query) use ($insurer_id) {
                $query->where('insurer_id', $insurer_id);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($po_id, function ($query) use ($po_id) {
                $query->where('job_id', $po_id);
            })
            ->when($lot, function ($query) use ($lot) {
                $query->where('lot_number', $lot);
            })
            ->paginate(PER_PAGE);
        foreach ($list as $key => $item) {
            $rental = InsuranceTrait::getRentalDetail($item);
            $item->rental_customer = $rental['customer_name'] ?? null;
        }
        $status_list = InsuranceTrait::getInsuranceWorkSheetStatus();
        return view('admin.cmi-cars.index', [
            'list' => $list,
            's' => $request->s,
            'cmi_id' => $cmi_id,
            'cmi_worksheet_no' => $cmi_worksheet_no,
            'license_plate' => $license_plate,
            'license_plate_text' => $license_plate_text,
            'insurer_id' => $insurer_id,
            'insurer_name' => $insurer_name,
            'status' => $status,
            'status_list' => $status_list,
            'po_id' => $po_id,
            'po_no' => $po_no,
            'lot' => $lot
        ]);
    }

    public function edit(CMI $cmi_car)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CMI);
        $car = Car::find($cmi_car->car_id);
        if ($car) {
            $import_car_line = ImportCarLine::find($cmi_car->car_id);
            $car->receive_date = null;
            if ($import_car_line) {
                $car->receive_date = $import_car_line->delivery_date;
                $car->registration_type = $import_car_line->registration_type;
            }
            $car->class_name = $car->carClass?->full_name;
        }
        $po = null;
        $rental = InsuranceTrait::getRentalDetail($cmi_car);
        $type_vmi_list = InsuranceTrait::getTypeVMIList();
        $type_cmi_list = InsuranceTrait::getTypeCMIList();
        $car_class_insurance_list = InsuranceTrait::getCarClassInsranceList();
        $insurer_list = InsuranceTrait::getInsurerList();
        $premium_status_list = InsuranceTrait::getPremiumPaymentStatus();
        $leasing_list = InsuranceTrait::getLeasingList();
        $premium_summary = InsuranceTrait::summaryPremium($cmi_car->premium, $cmi_car->discount, $cmi_car->stamp_duty, $cmi_car->tax);
        $cmi_car->sum_insured_total = number_format($cmi_car->sum_insured_car + $cmi_car->sum_insured_accessory, 2, '.', ',');
        $page_title = __('lang.edit') . __('cmi_cars.page_title');
        return view('admin.cmi-cars.form', [
            'mode' => MODE_UPDATE,
            'page_title' => $page_title,
            'd' => $cmi_car,
            'car' => $car,
            'type_vmi_list' => $type_vmi_list,
            'type_cmi_list' => $type_cmi_list,
            'car_class_insurance_list' => $car_class_insurance_list,
            'insurer_list' => $insurer_list,
            'po' => $po,
            'premium_summary' => $premium_summary,
            'premium_status_list' => $premium_status_list,
            'leasing_list' => $leasing_list,
            'rental' => $rental
        ]);
    }

    public function show(CMI $cmi_car)
    {
        $this->authorize(Actions::View . '_' . Resources::CMI);
        $car = Car::find($cmi_car->car_id);
        if ($car) {
            $import_car_line = ImportCarLine::find($cmi_car->car_id);
            $car->receive_date = null;
            if ($import_car_line) {
                $car->receive_date = $import_car_line->delivery_date;
                $car->registration_type = $import_car_line->registration_type;
            }
            $car->class_name = $car->carClass?->full_name;
        }
        $po = null;
        if (strcmp($cmi_car->job_type, PurchaseOrder::class) === 0) {
            $po = PurchaseOrder::find($cmi_car->job_id);
        }

        $type_vmi_list = InsuranceTrait::getTypeVMIList();
        $type_cmi_list = InsuranceTrait::getTypeCMIList();
        $car_class_insurance_list = InsuranceTrait::getCarClassInsranceList();
        $insurer_list = InsuranceTrait::getInsurerList();
        $premium_status_list = InsuranceTrait::getPremiumPaymentStatus();
        $leasing_list = InsuranceTrait::getLeasingList();
        $rental = InsuranceTrait::getRentalDetail($cmi_car);
        $premium_summary = InsuranceTrait::summaryPremium($cmi_car->premium, $cmi_car->discount, $cmi_car->stamp_duty, $cmi_car->tax);
        $cmi_car->sum_insured_total = number_format($cmi_car->sum_insured_car + $cmi_car->sum_insured_accessory, 2, '.', ',');
        $page_title = __('cmi_cars.page_title');
        return view('admin.cmi-cars.form', [
            'mode' => MODE_VIEW,
            'page_title' => $page_title,
            'd' => $cmi_car,
            'car' => $car,
            'type_vmi_list' => $type_vmi_list,
            'type_cmi_list' => $type_cmi_list,
            'car_class_insurance_list' => $car_class_insurance_list,
            'insurer_list' => $insurer_list,
            'po' => $po,
            'premium_summary' => $premium_summary,
            'premium_status_list' => $premium_status_list,
            'leasing_list' => $leasing_list,
            'rental' => $rental
        ]);
    }

    public function create()
    {
        abort(404);
    }

    public function createCMICarList(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CMI);
        $cars = $request->cars;
        $lot_no = $request->lot_no;
        $leasing_id = $request->leasing_id;
        if (!$lot_no) {
            return $this->responseWithCode(false, 'กรุณากรอก เลข Lot', null, 422);
        }
        if (InsuranceLot::where('lot_no', $lot_no)->exists()) {
            return $this->responseWithCode(false, 'เลข Lot นี้มีอยู่แล้ว', null, 422);
        }

        $lot = new InsuranceLot();
        $lot->lot_no = $lot_no;
        $lot->leasing_id = $leasing_id;
        $lot->year = 1;
        $lot->save();

        $checkCmi = 0;
        $checkVmi = 0;
        $checkAccessory = 0;
        foreach ($cars as $car) {
            $register_check = Register::find($car['id']);
            if (!$register_check) {
                $register = new Register();
                $register->worksheet_no = generate_worksheet_no(Register::class, false);
                $register->lot_id = $lot ? $lot->id : null;
                $register->car_id = $car['id'] ?? null;
                $register->po_id = $car['po_id'] ?? null;
                if ($car['po_id']) {
                    $po = PurchaseOrder::find($car['po_id']);
                    if ($po) {
                        $pr = PurchaseRequisition::find($po->pr_id);
                        $register->job_type = $pr && $pr->reference_type ? $pr->reference_type : null;
                        $register->job_id = $pr && $pr->reference_id ? $pr->reference_type : null;
                    }
                }
                $register->receive_information_date = Carbon::now();
                $register->status = RegisterStatusEnum::PREPARE_REGISTER;
                $register->save();
            }

            $cmi_exist = CMI::where('car_id', $car['id'])
                ->where('job_id', $car['po_id'])
                ->where('type', InsuranceRegistrationEnum::REGISTER)
                ->exists();
            if ($cmi_exist) {
                continue;
            }
            $cmi_count = DB::table('compulsory_motor_insurances')->count() + 1;
            $vmi_count = DB::table('voluntary_motor_insurances')->count() + 1;
            try {
                $import_car_line = ImportCarLine::find($car['id']);
                $import_car_line->lot_id = $lot->id;
                $import_car_line->save();

                $model_insurance = null;
                $_car = Car::find($car['id']);
                if ($_car) {
                    $car_class = CarClass::find($_car->car_class_id);
                    $model_insurance = $car_class ? $car_class->model_insurance : null;
                }
                $dataPo = PurchaseOrder::where('id', $car['po_id'])->first();
                $idPrLine = $dataPo?->purchaseRequisiton?->purchaseRequisitionLine?->first()?->id;
                $cmi = new CMI();
                $prefix = 'CMI-';
                $cmi->worksheet_no = generateRecordNumber($prefix, $cmi_count);
                $cmi->year = 1;
                $cmi->job_id = $car['po_id'] ?? null;
                $cmi->job_type = PurchaseOrder::class;
                $cmi->car_id = $car['id'] ?? null;
                $cmi->car_class_insurance_id = $model_insurance;
                $cmi->registration_type = $car['registration_type'] ?? null;
                $cmi->type = InsuranceRegistrationEnum::REGISTER;
                $cmi->status = InsuranceStatusEnum::PENDING;
                $cmi->lot_id = $lot ? $lot->id : null;
                $cmi->lot_number = $lot ? $lot->lot_no : null;
                if ($cmi->save()) {
                    $checkCmi++;
                }
                $vmi = new VMI();
                $prefix = 'VMI-';
                $vmi->worksheet_no = generateRecordNumber($prefix, $vmi_count);
                $vmi->year = 1;
                $vmi->job_id = $car['po_id'] ?? null;
                $vmi->job_type = PurchaseOrder::class;
                $vmi->car_id = $car['id'] ?? null;
                $vmi->car_class_insurance_id = $model_insurance;
                $vmi->registration_type = $car['registration_type'] ?? null;
                $vmi->type = InsuranceRegistrationEnum::REGISTER;
                $vmi->status = InsuranceStatusEnum::PENDING;
                $vmi->lot_id = $lot ? $lot->id : null;
                $vmi->lot_number = $lot ? $lot->lot_no : null;
                $vmi->save();
                if ($vmi->save()) {
                    $checkVmi++;
                }
                $pr_line_accessory = PurchaseRequisitionLineAccessory::where('purchase_requisition_line_id', $idPrLine)->where('type_accessories', LongTermRentalTypeAccessoryEnum::ADDITIONAL)->get();
                $accessory_arr = [];
                if (count($pr_line_accessory) > 0) {
                    $_car->status = CarEnum::EQUIPMENT; //รถติดตั้งอุปกรณ์
                    $_car->start_date = Carbon::now();
                    $_car->save();
                    foreach ($pr_line_accessory as $pr_line_acc) {
                        $accessory_data = [];
                        $accessory = Accessories::find($pr_line_acc->accessory_id);
                        $accessory_data['id'] = null;
                        $accessory_data['accessory_id'] = $pr_line_acc->accessory_id;
                        $accessory_data['supplier_id'] = $accessory->creditor_id;
                        $accessory_data['amount'] = $pr_line_acc->amount;
                        $accessory_data['price'] = $accessory->price;
                        $accessory_data['remark'] = $pr_line_acc->remark;
                        if ($accessory_data) {
                            $accessory_arr[] = $accessory_data;
                        }
                    }
                    $install_equipments = $accessory_arr;
                    if (!empty($install_equipments)) {
                        $checkAccessory++;
                    }
                    $lot = $lot ? $lot->id : null;
                    InstallEquipmentTrait::createInstallEquipments($car['po_id'], $_car->id, $install_equipments, $remark = '', null, $lot, $lot_no);
                }
            } catch (ErrorException $exception) {
                return $this->responseWithCode(false, __('lang.store_error_title'), null, 422);
            }
        }
//        Create Prepare Finance
        
        if (!empty($lot->id)) {
            FinanceTrait::createHirePurchaseCar($lot->id);
        }

        if (!empty($checkCmi)) {
            $this->sendNotificationCmi($lot->lot_number);
        }
        if (!empty($checkVmi)) {
            $this->sendNotificationVmi($lot->lot_number);
        }
        if (!empty($checkAccessory)) {
            $this->sendNotificationAccessory($lot->lot_number, $checkAccessory);
        }
        if ($checkCmi > 0) {
            $this->sendNotificationRegisterNewCar($lot->lot_number, $checkCmi);
        }
        return $this->responseWithCode(true, DATA_SUCCESS, null, 200);
    }

    public function sendNotificationCmi($noLot)
    {
        $dataDepartment = [
            DepartmentEnum::RMD_INSURANCE,
        ];
        $totalLotCmiCar = CMI::where('lot_number', $noLot)->count();
        $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
        $url = route('admin.cmi-cars.index', ['lot' => $noLot]);
        $notiTypeChange = new NotificationManagement('ทำพรบ.ใหม่ ' . $noLot, $noLot . ' มีรถ ' . $totalLotCmiCar . ' คัน แจ้งทำพรบ. ใหม่ ' . $noLot, $url, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, []);
        $notiTypeChange->send();
    }

    public function sendNotificationVmi($noLot)
    {
        $dataDepartment = [
            DepartmentEnum::RMD_INSURANCE,
        ];
        $totalLotVmiCar = VMI::where('lot_number', $noLot)->count();
        $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
        $url = route('admin.vmi-cars.index', ['lot' => $noLot]);
        $notiTypeChange = new NotificationManagement('ทำประกันรถใหม่ ' . $noLot, $noLot . ' มีรถ ' . $totalLotVmiCar . ' คัน แจ้งทำประกันรถใหม่ ' . $noLot, $url, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, []);
        $notiTypeChange->send();
    }

    public function sendNotificationAccessory($noLot, $totalCar)
    {
        $dataDepartment = [
            DepartmentEnum::RMD_INSURANCE,
        ];
        $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
        $url = route('admin.install-equipments.index', ['lot' => $noLot]);
        $notiTypeChange = new NotificationManagement('ติดตั้งอุปกรณ์ ' . $noLot, $noLot . ' มีรถ ' . $totalCar . ' คัน ต้องติดตั้งอุปกรณ์เสริม ' . $noLot, $url, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, []);
        $notiTypeChange->send();
    }

    public function sendNotificationRegisterNewCar($noLot, $totalCar)
    {
        $dataDepartment = [
            DepartmentEnum::PCD_REGISTRATION,
        ];
        $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
        //        $url = route('admin.install-equipments.index', ['lot' => $noLot]);
        $notiTypeChange = new NotificationManagement('จดทะเบียนรถใหม่ ' . $noLot, $noLot . ' มีรถ ' . $totalCar . ' คัน แจ้งจดทะเบียนรถใหม่', null, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, []);
        $notiTypeChange->send();
    }

    public function getLotNumber()
    {
        $lot_number = InsuranceTrait::getInsuranceLotNumber();
        return $this->responseWithCode(true, DATA_SUCCESS, $lot_number, 200);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CMI);
        $cmi = CMI::findOrFail($request->id);
        $cmi_status = $cmi->status;
        $validate_data = [
            // 'car_class_insurance_id' => 'required',
            // 'type_vmi' => 'required',
            // 'type_cmi' => 'required',
            // 'sum_insured_car' => 'required | string | max:10',
            // 'sum_insured_accessory' => 'required | string | max:10',
            // 'send_date' => 'required | date',
            // 'term_start_date' => 'required | date',
            // 'term_end_date' => 'required | date',
            'insurer_id' => 'required',
            'beneficiary_id' => 'required',
        ];

        if (strcmp($cmi_status, InsuranceStatusEnum::IN_PROCESS) === 0) {
            $request->merge([
                'premium' => transform_float($request->premium),
                'discount' => transform_float($request->discount),
                'stamp_duty' => transform_float($request->stamp_duty),
                'tax' => transform_float($request->tax),
            ]);
            $validate_data['premium'] = 'required | numeric | gt:0 | max:999999999.99';
            $validate_data['discount'] = 'required | numeric | gt:0 | max:999999999.99';
            $validate_data['stamp_duty'] = 'required | numeric | gt:0 | max:999999999.99';
            $validate_data['tax'] = 'required | numeric | gt:0 | max:999999999.99';
        }

        $validator = Validator::make($request->all(), $validate_data, [], [
            // 'car_class_insurance_id' => __('cmi_cars.insurance_class'),
            'type_vmi' => __('cmi_cars.type_vmi'),
            'type_cmi' => __('cmi_cars.type_cmi'),
            'sum_insured_car' => __('cmi_cars.sum_insured_car'),
            'sum_insured_accessory' => __('cmi_cars.sum_insured_accessory'),
            'send_date' => __('cmi_cars.delivery_doc_date'),
            'receive_date' => __('cmi_cars.receive_doc_date'),
            'term_start_date' => __('cmi_cars.policy_start_date'),
            'term_end_date' => __('cmi_cars.policy_end_date'),
            'insurer_id' => __('cmi_cars.insurance_company'),
            'beneficiary_id' => __('cmi_cars.beneficiary'),
            'check_date' => __('cmi_cars.term_end_date'),
            'number_bar_cmi' => __('cmi_cars.cmi_bar_no'),
            'premium' => __('cmi_cars.premium_net'),
            'discount' => __('cmi_cars.discount'),
            'stamp_duty' => __('cmi_cars.stamp_duty'),
            'tax' => __('cmi_cars.tax'),
            'premium_total' => __('cmi_cars.premium_total'),
            'withholding_tax' => __('cmi_cars.withholding_tax_1'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $cmi->car_class_insurance_id = $request->car_class_insurance_id;
        $cmi->type_vmi = $request->type_vmi;
        $cmi->type_cmi = $request->type_cmi;
        $cmi->sum_insured_car = transform_float($request->sum_insured_car);
        $cmi->sum_insured_accessory = transform_float($request->sum_insured_accessory);
        $cmi->insurer_id = $request->insurer_id;
        $cmi->beneficiary_id = $request->beneficiary_id;
        $cmi->remark = $request->remark;
        $cmi->send_date = $request->send_date;

        $cmi->receive_date = $request->receive_date;
        $cmi->check_date = $request->check_date;
        $cmi->number_bar_cmi = $request->number_bar_cmi;
        $cmi->policy_reference_cmi = $request->policy_reference_cmi;
        $cmi->endorse_cmi = $request->endorse_cmi;

        $cmi->term_start_date = $request->term_start_date . " 16:30:00";
        $cmi->term_end_date = $request->term_end_date . " 16:30:00";
        if (strcmp($cmi_status, InsuranceStatusEnum::PENDING) === 0) {
            if ($cmi->term_start_date && $cmi->term_end_date) {
                $cmi->status = InsuranceStatusEnum::IN_PROCESS;
            }
        }

        if (strcmp($cmi_status, InsuranceStatusEnum::IN_PROCESS) === 0) {
            $premium = transform_float($request->premium);
            $discount = transform_float($request->discount);
            $stamp_duty = transform_float($request->stamp_duty);
            $tax = transform_float($request->tax);
            if ($premium <= 0 || $stamp_duty <= 0 || $tax <= 0) {
                return $this->responseWithCode(false, __('cmi_cars.premium_gt_zero'), null, 422);
            }

            $cmi->premium = $premium;
            $cmi->discount = $discount;
            $cmi->stamp_duty = $stamp_duty;
            $cmi->tax = $tax;
        }

        $cmi->statement_no = $request->statement_no;
        $cmi->tax_invoice_no = $request->tax_invoice_no;
        $cmi->statement_date = $request->statement_date;
        $cmi->account_submission_date = $request->account_submission_date;
        $cmi->operated_date = $request->operated_date;
        $cmi->status_pay_premium = $request->status_pay_premium;

        if (strcmp($cmi_status, InsuranceStatusEnum::IN_PROCESS) === 0) {

            if (!empty($cmi->number_bar_cmi) && !empty($cmi->policy_reference_cmi) && !empty($cmi->endorse_cmi)) {
                $cmi->status = InsuranceStatusEnum::COMPLETE;
                $current_date = Carbon::now()->format('Y-m-d H:i:s');
                $term_start_date = Carbon::parse($request->term_start_date)->format('Y-m-d H:i:s');
                $dateTime = Carbon::parse($request->term_start_date);
                if ($term_start_date <= $current_date) {
                    $cmi->status_cmi = InsuranceCarStatusEnum::UNDER_POLICY;
                } else {
                    $cmi_status_update = new EffectiveCmiJob($cmi);
                    dispatch($cmi_status_update)->delay($dateTime);
                }
            }
        }
        $cmi->save();

        $redirect_route = route('admin.cmi-cars.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function makeInProcessCMIs(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CMI);
        $cmi_list = $request->cmi_list;
        $term_start_date = $request->term_start_date;
        $term_end_date = $request->term_end_date;
        foreach ($cmi_list as $cmi) {
            try {
                $cmi = CMI::findOrFail($cmi['id']);
                $cmi->term_start_date = $term_start_date;
                $cmi->term_end_date = $term_end_date;
                $cmi->status = InsuranceStatusEnum::IN_PROCESS;
                $cmi->save();
            } catch (ErrorException $exception) {
                return $this->responseWithCode(false, __('lang.store_error_title'), null, 422);
            }
        }
        return $this->responseWithCode(true, DATA_SUCCESS, null, 200);
    }

    public function exportCMIs(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CMI);
        $ids = $request->ids;
        if (!is_array($ids)) {
            return $this->responseWithCode(false, __('lang.store_error_title'), null, 422);
        }
        $cmi_list = CMI::whereIn('id', $ids)->get();
        if (sizeof($cmi_list) <= 0) {
            return $this->responseWithCode(false, __('lang.store_error_title'), null, 422);
        }
        foreach ($cmi_list as $cmi) {
            $cmi->term_start_date = $cmi->term_start_date ? get_date_time_by_format($cmi->term_start_date, 'd/m/Y H:i:s') : null;
            $cmi->term_end_date = $cmi->term_end_date ? get_date_time_by_format($cmi->term_end_date, 'd/m/Y H:i:s') : null;
            $cmi->statement_date = $cmi->statement_date ? get_date_time_by_format($cmi->statement_date, 'd/m/Y H:i:s') : null;
            $cmi->account_submission_date = $cmi->account_submission_date ? get_date_time_by_format($cmi->account_submission_date, 'd/m/Y H:i:s') : null;
            $cmi->operated_date = $cmi->operated_date ? get_date_time_by_format($cmi->operated_date, 'd/m/Y H:i:s') : null;
        }
        return Excel::download(new ExportCMI($cmi_list), 'template.xlsx');
    }

    public function importCMIs(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CMI);
        $file = $request->file('file');
        if (!file_exists($file)) {
            return $this->responseWithCode(false, __('lang.store_error_title'), null, 422);
        }
        $validator = Validator::make(
            [
                'file' => $request->file,
                'extension' => strtolower($request->file->getClientOriginalExtension()),
            ],
            [
                'file' => 'required',
                'extension' => 'required|in:xlsx,xls',
            ],
            [],
            [
                'extension' => __('cmi_cars.file_type'),
            ]
        );

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $data = Excel::toArray([], $request->file('file'));
        $sheet = $data[0] ?? null;
        if (!($sheet) || sizeof($sheet) <= 0) {
            return $this->responseWithCode(false, __('lang.store_error_title'), null, 422);
        }
        $log = [];
        $header = true;
        foreach ($data[0] as $row) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($row) < 17) {
                continue;
            }
            $no = $row[0];
            $chassis_no = $row[1];
            $insurance_company = $row[2];
            $license_plate = $row[3];
            $worksheet_no = $row[4];
            $term_start_date = $row[5];
            $term_end_date = $row[6];
            $premium = $row[7];
            $discount = $row[8];
            $stamp_duty = $row[9];
            $tax = $row[10];
            $number_bar_cmi = $row[13];
            $statement_date = $row[14];
            $account_submission_date = $row[15];
            $operated_date = $row[16];

            if (InsuranceTrait::containsOnlyNull($row)) {
                continue;
            }
            if (empty($chassis_no) || empty($worksheet_no)) {
                $log[] = 'ข้อมูลลำดับที่ ' . $no . ' ข้อมูลไม่ครบถ้วน ไม่สามารถบันทึกได้';
                continue;
            }
            if (empty($chassis_no) || empty($worksheet_no)) {
                $log[] = 'ข้อมูลลำดับที่ ' . $no . ' ข้อมูลไม่ครบถ้วน ไม่สามารถบันทึกได้';
                continue;
            }

            if ($term_start_date && !InsuranceTrait::validateDateTimeFormat($term_start_date)) {
                $log[] = 'ข้อมูลลำดับที่ ' . $no . ' รูปแบบวันที่ไม่ถูกต้อง ไม่สามารถบันทึกได้';
                continue;
            }
            if ($term_end_date && !InsuranceTrait::validateDateTimeFormat($term_end_date)) {
                $log[] = 'ข้อมูลลำดับที่ ' . $no . ' รูปแบบวันที่ไม่ถูกต้อง ไม่สามารถบันทึกได้';
                continue;
            }
            if ($statement_date && !InsuranceTrait::validateDateTimeFormat($statement_date)) {
                $log[] = 'ข้อมูลลำดับที่ ' . $no . ' รูปแบบวันที่ไม่ถูกต้อง ไม่สามารถบันทึกได้';
                continue;
            }
            if ($account_submission_date && !InsuranceTrait::validateDateTimeFormat($account_submission_date)) {
                $log[] = 'ข้อมูลลำดับที่ ' . $no . ' รูปแบบวันที่ไม่ถูกต้อง ไม่สามารถบันทึกได้';
                continue;
            }
            if ($operated_date && !InsuranceTrait::validateDateTimeFormat($operated_date)) {
                $log[] = 'ข้อมูลลำดับที่ ' . $no . ' รูปแบบวันที่ไม่ถูกต้อง ไม่สามารถบันทึกได้';
                continue;
            }
            $car = Car::where('chassis_no', $chassis_no)->first();
            if (!$car) {
                $log[] = 'ข้อมูลลำดับที่ ' . $no . 'ไม่พบรถที่มีหมายเลขตัวถังนี้ ไม่สามารถบันทึกได้';
                continue;
            }

            $cmi = CMI::where('worksheet_no', $worksheet_no)
                ->where('car_id', $car->id)
                ->first();

            if (!$cmi) {
                $log[] = 'ข้อมูลลำดับที่ ' . $no . ' ไม่พบพรบ. ไม่สามารถบันทึกได้';
                continue;
            }

            if (!strcmp($cmi->status, InsuranceStatusEnum::IN_PROCESS) === 0) {
                $log[] = 'ข้อมูลลำดับที่ ' . $no . ' สถานะไม่ถูกต้อง ไม่สามารถบันทึกได้';
                continue;
            }

            $cmi->term_start_date = $term_start_date ? InsuranceTrait::formatDateToDefault($term_start_date) : null;
            $cmi->term_end_date = $term_end_date ? InsuranceTrait::formatDateToDefault($term_end_date) : null;
            $cmi->premium = transform_float($premium) ?? null;
            $cmi->discount = transform_float($discount) ?? null;
            $cmi->stamp_duty = transform_float($stamp_duty) ?? null;
            $cmi->stamp_duty = transform_float($stamp_duty) ?? null;
            $cmi->tax = transform_float($tax) ?? null;
            $cmi->number_bar_cmi = $number_bar_cmi;
            $cmi->statement_date = $statement_date ? InsuranceTrait::formatDateToDefault($statement_date) : null;
            $cmi->account_submission_date = $account_submission_date ? InsuranceTrait::formatDateToDefault($account_submission_date) : null;
            $cmi->operated_date = $operated_date ? InsuranceTrait::formatDateToDefault($operated_date) : null;
            $cmi->save();
        }
        return $this->responseWithCode(true, DATA_SUCCESS, $log, 200);
    }
}
