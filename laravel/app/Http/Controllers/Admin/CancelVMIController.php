<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\InsuranceCarStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\CancelInsurance;
use App\Models\Car;
use App\Models\CMI;
use App\Models\ImportCarLine;
use App\Models\InsuranceCompanies;
use App\Models\InsurancePackage;
use App\Models\PurchaseOrder;
use App\Models\VMI;
use App\Traits\InsuranceTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CancelVMIController extends Controller
{
    use InsuranceTrait;
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CancelVMI);
        $cancel_id = $request->cancel_id;
        $status = $request->status;
        $cancel_worksheet_no = null;
        if ($cancel_id) {
            $cancel_vmi = CancelInsurance::find($cancel_id);
            $cancel_worksheet_no = $cancel_vmi?->worksheet_no ?? null;
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
        $list = CancelInsurance:: with('ref')
            ->select('*')
            // ->sortable(['worksheet_no' => 'desc'])
            ->whereIn('status', [InsuranceCarStatusEnum::REQUEST_CANCEL, InsuranceCarStatusEnum::CANCEL_POLICY])
            ->where('ref_type', VMI::class)
            ->when($cancel_id, function ($query) use ($cancel_id) {
                $query->where('id', $cancel_id);
            })
            ->when($license_plate, function ($query) use ($license_plate) {
                $query->whereHasMorph('ref', [CMI::class, VMI::class], function ($subquery) use ($license_plate) {
                    $subquery->where('car_id', $license_plate);
                });
            })
            ->when($insurer_id, function ($query) use ($insurer_id) {
                $query->whereHasMorph('ref', [CMI::class, VMI::class], function ($subquery) use ($insurer_id) {
                    $subquery->where('insurer_id', $insurer_id);
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->paginate(PER_PAGE);

        $status_list = InsuranceTrait::getCancelInsuranceStatus();
        return view('admin.cancel-vmi-cars.index', [
            'list' => $list,
            's' => $request->s,
            'cancel_id' => $cancel_id,
            'cancel_worksheet_no' => $cancel_worksheet_no,
            'license_plate' => $license_plate,
            'license_plate_text' => $license_plate_text,
            'insurer_id' => $insurer_id,
            'insurer_name' => $insurer_name,
            'status' => $status,
            'status_list' => $status_list,
        ]);
    }

    public function edit(CancelInsurance $cancel_vmi_car)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CancelVMI);
        if (strcmp($cancel_vmi_car->status, InsuranceCarStatusEnum::CANCEL_POLICY) === 0) {
            return redirect()->route('admin.cancel-cmi-cars.index');
        }
        
        $vmi_car = VMI::find($cancel_vmi_car->ref_id);
        if (!$vmi_car) {
            return redirect()->route('admin.cancel-vmis-cars.index');
        }
        $car = Car::find($vmi_car->car_id);
        if ($car) {
            $import_car_line = ImportCarLine::find($vmi_car->car_id);
            $car->receive_date = null;
            if ($import_car_line) {
                $car->receive_date = $import_car_line->delivery_date;
                $car->registration_type = $import_car_line->registration_type;
            }
            $car->class_name = $car->carClass?->full_name;
        }
        $po = null;
        if (strcmp($vmi_car->job_type, PurchaseOrder::class) === 0) {
            $po = PurchaseOrder::find($vmi_car->job_id);
        }
        $package_name = null;
        if ($vmi_car->insurance_package_id) {
            $package = InsurancePackage::find($vmi_car->insurance_package_id);
            $package_name = $package?->name ?? null;
        }

        $type_vmi_list = InsuranceTrait::getTypeVMIList();
        $type_cmi_list = InsuranceTrait::getTypeCMIList();
        $car_class_insurance_list = InsuranceTrait::getCarClassInsranceList();
        $insurer_list = InsuranceTrait::getInsurerList();
        $premium_status_list = InsuranceTrait::getPremiumPaymentStatus();
        $insurance_type_list = InsuranceTrait::getInsuranceTypeList();
        $leasing_list = InsuranceTrait::getLeasingList();
        $rental = InsuranceTrait::getRentalDetail($vmi_car);
        $premium_summary = InsuranceTrait::summaryPremium($vmi_car->premium, $vmi_car->discount, $vmi_car->stamp_duty, $vmi_car->tax);
        $refund_summary = InsuranceTrait::summaryPremium($cancel_vmi_car->refund, 0, $cancel_vmi_car->refund_stamp, $cancel_vmi_car->refund_vat);
        $vmi_car->sum_insured_total = number_format($vmi_car->sum_insured_car + $vmi_car->sum_insured_accessory, 2, '.', ',');
        $page_title = __('lang.edit') . __('vmi_cars.page_title');
        return view('admin.cancel-vmi-cars.form', [
            'mode' => MODE_UPDATE,
            'page_title' => $page_title,
            'd' => $vmi_car,
            'cancel_insurance' => $cancel_vmi_car,
            'car' => $car,
            'type_vmi_list' => $type_vmi_list,
            'type_cmi_list' => $type_cmi_list,
            'car_class_insurance_list' => $car_class_insurance_list,
            'insurer_list' => $insurer_list,
            'po' => $po,
            'premium_summary' => $premium_summary,
            'premium_status_list' => $premium_status_list,
            'insurance_type_list' => $insurance_type_list,
            'package_name' => $package_name,
            'leasing_list' => $leasing_list,
            'rental' => $rental,
            'refund_summary' => $refund_summary
        ]);
    }

    public function show(CancelInsurance $cancel_vmi_car)
    {
        $this->authorize(Actions::View . '_' . Resources::CancelVMI);
        $vmi_car = VMI::find($cancel_vmi_car->ref_id);
        if (!$vmi_car) {
            return redirect()->route('admin.cancel-vmi-cars.index');
        }
        $car = Car::find($vmi_car->car_id);
        if ($car) {
            $import_car_line = ImportCarLine::find($vmi_car->car_id);
            $car->receive_date = null;
            if ($import_car_line) {
                $car->receive_date = $import_car_line->delivery_date;
                $car->registration_type = $import_car_line->registration_type;
            }
            $car->class_name = $car->carClass?->full_name;
        }
        $po = null;
        if (strcmp($vmi_car->job_type, PurchaseOrder::class) === 0) {
            $po = PurchaseOrder::find($vmi_car->job_id);
        }
        $package_name = null;
        if ($vmi_car->insurance_package_id) {
            $package = InsurancePackage::find($vmi_car->insurance_package_id);
            $package_name = $package?->name ?? null;
        }

        $type_vmi_list = InsuranceTrait::getTypeVMIList();
        $type_cmi_list = InsuranceTrait::getTypeCMIList();
        $car_class_insurance_list = InsuranceTrait::getCarClassInsranceList();
        $insurer_list = InsuranceTrait::getInsurerList();
        $premium_status_list = InsuranceTrait::getPremiumPaymentStatus();
        $insurance_type_list = InsuranceTrait::getInsuranceTypeList();
        $leasing_list = InsuranceTrait::getLeasingList();
        $rental = InsuranceTrait::getRentalDetail($vmi_car);
        $premium_summary = InsuranceTrait::summaryPremium($vmi_car->premium, $vmi_car->discount, $vmi_car->stamp_duty, $vmi_car->tax);
        $refund_summary = InsuranceTrait::summaryPremium($cancel_vmi_car->refund, 0, $cancel_vmi_car->refund_stamp, $cancel_vmi_car->refund_vat);
        $vmi_car->sum_insured_total = number_format($vmi_car->sum_insured_car + $vmi_car->sum_insured_accessory, 2, '.', ',');
        $page_title = __('lang.view') . __('vmi_cars.page_title');
        return view('admin.cancel-vmi-cars.form', [
            'mode' => MODE_VIEW,
            'page_title' => $page_title,
            'd' => $vmi_car,
            'cancel_insurance' => $cancel_vmi_car,
            'car' => $car,
            'type_vmi_list' => $type_vmi_list,
            'type_cmi_list' => $type_cmi_list,
            'car_class_insurance_list' => $car_class_insurance_list,
            'insurer_list' => $insurer_list,
            'po' => $po,
            'premium_summary' => $premium_summary,
            'premium_status_list' => $premium_status_list,
            'insurance_type_list' => $insurance_type_list,
            'package_name' => $package_name,
            'leasing_list' => $leasing_list,
            'rental' => $rental,
            'refund_summary' => $refund_summary
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CancelVMI);
        $validate_data = [
            'reason' => 'nullable | string | max:255',
            'actual_cancel_date' => 'nullable | date',
            'credit_note' => 'nullable | string | max:255',
        ];

        $request->merge([
            'refund' => transform_float($request->refund),
            'refund_stamp' => transform_float($request->refund_stamp),
            'refund_tax' => transform_float($request->refund_tax),
        ]);
        $validate_data['refund'] = 'required | numeric | gte:0 | max:999999999.99';
        $validate_data['refund_stamp'] = 'required | numeric | gte:0 | max:999999999.99';
        $validate_data['refund_tax'] = 'required | numeric | gte:0 | max:999999999.99';

        $validator = Validator::make($request->all(), $validate_data, [], [
            'reason' => __('cmi_cars.cancel_reson'),
            'remark' => __('lang.renmark'),
            'actual_cancel_date' => __('cmi_cars.actual_cancel_date'),
            'refund' => __('cmi_cars.refund'),
            'refund_stamp' => __('cmi_cars.refund_stamp'),
            'refund_tax' => __('cmi_cars.refund_tax'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $cancel_cmi = CancelInsurance::findOrFail($request->id);
        $cancel_cmi->actual_cancel_date = $request->actual_cancel_date;
        $cancel_cmi->remark = $request->cancel_remark;
        $refund = transform_float($request->refund);
        $refund_stamp = transform_float($request->refund_stamp);
        $refund_vat = transform_float($request->refund_vat);
        $cancel_cmi->refund = $refund;
        $cancel_cmi->refund_stamp = $refund_stamp;
        $cancel_cmi->refund_vat = $refund_vat;
        $cancel_cmi->credit_note = $request->credit_note;
        $cancel_cmi->credit_note_date = $request->credit_note_date;
        $cancel_cmi->check_date = $request->refund_check_date;
        $cancel_cmi->send_account_date = $request->send_account_date;
        if ($refund > 0 && $refund_stamp > 0 && $refund_vat > 0 && !empty($request->actual_cancel_date)) {
            $cancel_cmi->status = InsuranceCarStatusEnum::CANCEL_POLICY;
        }
        $cancel_cmi->save();
        $redirect_route = route('admin.cancel-vmi-cars.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function createCancelVMI(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CancelCMI);
        $vmi_id = $request->vmi_id;
        $cancel_date = $request->cancel_date;
        $cancel_reason = $request->cancel_reason;
        $vmi = VMI::find($vmi_id);
        if (!$vmi) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }
        $success = InsuranceTrait::createCancelInsurance($vmi->id, VMI::class, $cancel_date, $cancel_reason);
        if (!$success) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }
        return $this->responseWithCode(true, DATA_SUCCESS, null, 200);
    }
}