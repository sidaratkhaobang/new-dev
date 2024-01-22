<?php

namespace App\Http\Controllers\Admin;

use Actions;
use App\Enums\InsuranceCarStatusEnum;
use App\Enums\Resources;
use App\Exports\ExportCancelInsurance;
use App\Http\Controllers\Controller;
use App\Models\CancelInsurance;
use App\Models\Car;
use App\Models\CMI;
use App\Models\ImportCarLine;
use App\Models\InsuranceCompanies;
use App\Models\InsuranceLot;
use App\Models\PurchaseOrder;
use App\Models\VMI;
use App\Traits\InsuranceTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use InsuranceStatusEnum;
use Maatwebsite\Excel\Facades\Excel;

class CancelCMIController extends Controller
{
    use InsuranceTrait;
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CancelCMI);
        $cancel_id = $request->cancel_id;
        $status = $request->status;
        $cancel_worksheet_no = null;
        if ($cancel_id) {
            $cancel_cmi = CancelInsurance::find($cancel_id);
            $cancel_worksheet_no = $cancel_cmi?->worksheet_no ?? null;
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

        $list = CancelInsurance::with('ref')
            ->select('*')
            // ->sortable(['worksheet_no' => 'desc'])
            ->whereIn('status', [InsuranceCarStatusEnum::REQUEST_CANCEL, InsuranceCarStatusEnum::CANCEL_POLICY])
            ->where('ref_type', CMI::class)
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
        return view('admin.cancel-cmi-cars.index', [
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

    public function edit(CancelInsurance $cancel_cmi_car)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CancelCMI);
        if (strcmp($cancel_cmi_car->status, InsuranceCarStatusEnum::CANCEL_POLICY) === 0) {
            return redirect()->route('admin.cancel-cmi-cars.index');
        }
        
        $cmi_car = CMI::find($cancel_cmi_car->ref_id);
        if (!$cmi_car) {
            return redirect()->route('admin.cancel-cmi-cars.index');
        }

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
        $refund_summary = InsuranceTrait::summaryPremium($cancel_cmi_car->refund, 0, $cancel_cmi_car->refund_stamp, $cancel_cmi_car->refund_vat);
        $cmi_car->sum_insured_total = number_format($cmi_car->sum_insured_car + $cmi_car->sum_insured_accessory, 2, '.', ',');
        $page_title = __('lang.edit') . __('cmi_cars.page_title');
        return view('admin.cancel-cmi-cars.form', [
            'mode' => MODE_UPDATE,
            'cancel_insurance' => $cancel_cmi_car,
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
            'rental' => $rental,
            'refund_summary' => $refund_summary
        ]);
    }

    public function show(CancelInsurance $cancel_cmi_car)
    {
        $this->authorize(Actions::View . '_' . Resources::CancelCMI);
        $cmi_car = CMI::find($cancel_cmi_car->ref_id);
        if (!$cmi_car) {
            return redirect()->route('admin.cancel-cmi-cars.index');
        }

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
        $refund_summary = InsuranceTrait::summaryPremium($cancel_cmi_car->refund, 0, $cancel_cmi_car->refund_stamp, $cancel_cmi_car->refund_vat);
        $cmi_car->sum_insured_total = number_format($cmi_car->sum_insured_car + $cmi_car->sum_insured_accessory, 2, '.', ',');
        $page_title = __('lang.view') . __('cmi_cars.page_title');
        return view('admin.cancel-cmi-cars.form', [
            'mode' => MODE_VIEW,
            'cancel_insurance' => $cancel_cmi_car,
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
            'rental' => $rental,
            'refund_summary' => $refund_summary
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CancelCMI);
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
        $redirect_route = route('admin.cancel-cmi-cars.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function createCancelCMI(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CancelCMI);
        $cmi_id = $request->cmi_id;
        $cancel_date = $request->cancel_date;
        $cancel_reason = $request->cancel_reason;
        $cmi = CMI::find($cmi_id);
        if (!$cmi) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }
        $success = InsuranceTrait::createCancelInsurance($cmi->id, CMI::class, $cancel_date, $cancel_reason);
        if (!$success) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }
        return $this->responseWithCode(true, DATA_SUCCESS, null, 200);
    }


    public function exportCancelInsurances(Request $request)
    {
        $ids = $request->ids;
        if (!is_array($ids)) {
            return $this->responseWithCode(false, __('lang.store_error_title'), null, 422);
        }

        $cancel_insurance_list = CancelInsurance::whereIn('id', $ids)->get();
        if (sizeof($cancel_insurance_list) <= 0) {
            return $this->responseWithCode(false, __('lang.store_error_title'), null, 422);
        }

        foreach ($cancel_insurance_list as $cancel_insurance) {
            $refund_summary = InsuranceTrait::summaryPremium($cancel_insurance->refund, 0, $cancel_insurance->refund_stamp, $cancel_insurance->refund_vat);
            $cancel_insurance->refund_total = $refund_summary['premium_total'];
            $cancel_insurance->withholding_tax = $refund_summary['withholding_tax'];
        }
        return Excel::download(new ExportCancelInsurance($cancel_insurance_list, 'CMI'), 'template.xlsx');
    }

    public function importCancelInsurances(Request $request)
    {
        $file = $request->file('file');
        if (!file_exists($file)) {
            return $this->responseWithCode(false, __('lang.store_error_title'), null, 422);
        }
        $insurance_type = $request->insurance_type;
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
            $no = $row[0];
            $reason = $row[1];
            $insurance_company = $row[2];
            $chassis_no = $row[3];
            $license_plate = $row[4];
            $remark = $row[5];
            $policy_no = $row[6];

            $actual_cancel_date = $row[9];
            $refund = $row[10];
            $refund_stamp = $row[11];
            $refund_vat = $row[12];
            $credit_note = $row[15];
            $credit_note_date = $row[16];

            if (InsuranceTrait::containsOnlyNull($row)) {
                continue;
            }
            if (empty($policy_no) || empty($actual_cancel_date)) {
                $log[] = 'ข้อมูลลำดับที่ ' . $no . ' ข้อมูลไม่ครบถ้วน ไม่สามารถบันทึกได้';
                continue;
            }

            if ($actual_cancel_date && !InsuranceTrait::validateDateTimeFormat($actual_cancel_date)) {
                $log[] = 'ข้อมูลลำดับที่ ' . $no . ' รูปแบบวันที่ไม่ถูกต้อง ไม่สามารถบันทึกได้';
                continue;
            }
            if ($credit_note_date && !InsuranceTrait::validateDateTimeFormat($credit_note_date)) {
                $log[] = 'ข้อมูลลำดับที่ ' . $no . ' รูปแบบวันที่ไม่ถูกต้อง ไม่สามารถบันทึกได้';
                continue;
            }

            $car = Car::where('chassis_no', $chassis_no)->first();
            if (!$car) {
                $log[] = 'ข้อมูลลำดับที่ ' . $no . ' ไม่พบรถที่มีหมายเลขตัวถังนี้ ไม่สามารถบันทึกได้';
                continue;
            }

            $insurance = null;
            if (strcmp($insurance_type, 'CMI') === 0) {
                $insurance = CMI::where('policy_reference_cmi', $policy_no)->first();
            }

            if (strcmp($insurance_type, 'VMI') === 0) {
                $insurance = VMI::where('policy_reference_child_vmi', $policy_no)->first();
            }

            if (!$insurance) {
                $log[] = 'ข้อมูลลำดับที่ ' . $no . ' ไม่พบหมายเลขกรมธรรม์นี้';
                continue;
            }

            $cancel_insurance = CancelInsurance::where('ref_id', $insurance->id)->first();
            if (!$cancel_insurance) {
                $log[] = 'ข้อมูลลำดับที่ ' . $no . ' ไม่พบใบงานยกเลิก ไม่สามารถบันทึกได้';
                continue;
            }

            if (strcmp($cancel_insurance->status, InsuranceCarStatusEnum::CANCEL_POLICY) === 0) {
                if (transform_float($refund) <= 0 || transform_float($refund_stamp) <= 0 || transform_float($refund_vat) <= 0) {
                    $log[] = 'ข้อมูลลำดับที่ ' . $no . ' ข้อมูลเงินคืนไม่ครบถ้วน ไม่สามารถบันทึกได้';
                    continue;
                }
            }

            $cancel_insurance->actual_cancel_date = $actual_cancel_date ? InsuranceTrait::formatDateToDefault($actual_cancel_date) : null;
            $cancel_insurance->refund = transform_float($refund) ?? null;
            $cancel_insurance->refund_stamp = transform_float($refund_stamp) ?? null;
            $cancel_insurance->refund_vat = transform_float($refund_vat) ?? null;
            $cancel_insurance->credit_note = $credit_note ?? null;
            $cancel_insurance->credit_note_date = $credit_note_date ? InsuranceTrait::formatDateToDefault($credit_note_date) : null;
            if (strcmp($cancel_insurance->status, InsuranceCarStatusEnum::REQUEST_CANCEL) === 0) {
                if (!empty($actual_cancel_date) && transform_float($refund) > 0 && transform_float($refund_stamp) > 0 && transform_float($refund_vat) > 0) {
                    $cancel_insurance->status = InsuranceCarStatusEnum::CANCEL_POLICY;
                }
            }
            $cancel_insurance->save();
        }
        return $this->responseWithCode(true, DATA_SUCCESS, $log, 200);
    }
}