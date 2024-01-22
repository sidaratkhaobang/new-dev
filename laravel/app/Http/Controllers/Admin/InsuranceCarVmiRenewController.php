<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\InsuranceCarStatusEnum;
use App\Enums\InsuranceCarEnum;
use App\Enums\InsuranceRegistrationEnum;
use App\Enums\InsuranceStatusEnum;
use App\Enums\Resources;
use App\Exports\ExportVMI;
use App\Http\Controllers\Controller;
use App\Jobs\EffectiveVmiJob;
use App\Models\Car;
use App\Models\ImportCarLine;
use App\Models\InsuranceCompanies;
use App\Models\InsurancePackage;
use App\Models\PurchaseOrder;
use App\Models\VMI;
use App\Traits\InsuranceTrait;
use Carbon\Carbon;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class InsuranceCarVmiRenewController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::InsuranceCarVmiRenew);
        $vmi_id = $request->vmi_id;
        $status = $request->status;
        $lot = $request->lot;
        $vmi_worksheet_no = null;
        if ($vmi_id) {
            $vmi = VMI::find($vmi_id);
            $vmi_worksheet_no = $vmi?->worksheet_no ?? null;
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
        $list = VMI::with(['insurer', 'car'])->select('*')
            ->sortable(['worksheet_no' => 'desc'])
            ->whereNotnull('status_vmi')
            ->where('type', InsuranceRegistrationEnum::RENEW)
            ->when($vmi_id, function ($query) use ($vmi_id) {
                $query->where('id', $vmi_id);
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
        $page_title = "ต่ออายุประกัน";
        $type = InsuranceRegistrationEnum::RENEW;
        return view('admin.insurance-car-vmi-renew.index', [
            'page_title' => $page_title,
            'list' => $list,
            's' => $request->s,
            'vmi_id' => $vmi_id,
            'vmi_worksheet_no' => $vmi_worksheet_no,
            'license_plate' => $license_plate,
            'license_plate_text' => $license_plate_text,
            'insurer_id' => $insurer_id,
            'insurer_name' => $insurer_name,
            'status' => $status,
            'status_list' => $status_list,
            'po_id' => $po_id,
            'po_no' => $po_no,
            'lot' => $lot,
            'type' => $type,
        ]);
    }

    public function edit(VMI $insurance_vmi_renew)
    {
        $this->authorize(Actions::Manage . '_' . Resources::InsuranceCarVmiRenew);
        $car = Car::find($insurance_vmi_renew->car_id);
        if ($car) {
            $import_car_line = ImportCarLine::find($insurance_vmi_renew->car_id);
            $car->receive_date = null;
            if ($import_car_line) {
                $car->receive_date = $import_car_line->delivery_date;
                $car->registration_type = $import_car_line->registration_type;
            }
            $car->class_name = $car->carClass?->full_name;
        }
        $po = null;
        if (strcmp($insurance_vmi_renew->job_type, PurchaseOrder::class) === 0) {
            $po = PurchaseOrder::find($insurance_vmi_renew->job_id);
        }
        $package_name = null;
        if ($insurance_vmi_renew->insurance_package_id) {
            $package = InsurancePackage::find($insurance_vmi_renew->insurance_package_id);
            $package_name = $package?->name ?? null;
        }

        $type_vmi_list = InsuranceTrait::getTypeVMIList();
        $type_cmi_list = InsuranceTrait::getTypeCMIList();
        $car_class_insurance_list = InsuranceTrait::getCarClassInsranceList();
        $insurer_list = InsuranceTrait::getInsurerList();
        $premium_status_list = InsuranceTrait::getPremiumPaymentStatus();
        $insurance_type_list = InsuranceTrait::getInsuranceTypeList();
        $leasing_list = InsuranceTrait::getLeasingList();
        $rental = InsuranceTrait::getRentalDetail($insurance_vmi_renew);
        $premium_summary = InsuranceTrait::summaryPremium($insurance_vmi_renew->premium, $insurance_vmi_renew->discount, $insurance_vmi_renew->stamp_duty, $insurance_vmi_renew->tax);
        $insurance_vmi_renew->sum_insured_total = number_format($insurance_vmi_renew->sum_insured_car + $insurance_vmi_renew->sum_insured_accessory, 2, '.', ',');
        $page_title = __('lang.edit') . __('insurance_car.renew_insurance');
        return view('admin.insurance-car-vmi-renew.form', [
            'mode' => MODE_UPDATE,
            'page_title' => $page_title,
            'd' => $insurance_vmi_renew,
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
        ]);
    }

    public function show(VMI $insurance_vmi_renew)
    {
        $this->authorize(Actions::View . '_' . Resources::InsuranceCarVmiRenew);
        $car = Car::find($insurance_vmi_renew->car_id);
        if ($car) {
            $import_car_line = ImportCarLine::find($insurance_vmi_renew->car_id);
            $car->receive_date = null;
            if ($import_car_line) {
                $car->receive_date = $import_car_line->delivery_date;
                $car->registration_type = $import_car_line->registration_type;
            }
            $car->class_name = $car->carClass?->full_name;
        }
        $po = null;
        if (strcmp($insurance_vmi_renew->job_type, PurchaseOrder::class) === 0) {
            $po = PurchaseOrder::find($insurance_vmi_renew->job_id);
        }

        $package_name = null;
        if ($insurance_vmi_renew->insurance_package_id) {
            $package = InsurancePackage::find($insurance_vmi_renew->insurance_package_id);
            $package_name = $package?->name ?? null;
        }
        $type_vmi_list = InsuranceTrait::getTypeVMIList();
        $type_cmi_list = InsuranceTrait::getTypeCMIList();
        $car_class_insurance_list = InsuranceTrait::getCarClassInsranceList();
        $insurer_list = InsuranceTrait::getInsurerList();
        $premium_status_list = InsuranceTrait::getPremiumPaymentStatus();
        $insurance_type_list = InsuranceTrait::getInsuranceTypeList();
        $leasing_list = InsuranceTrait::getLeasingList();
        $rental = InsuranceTrait::getRentalDetail($insurance_vmi_renew);
        $premium_summary = InsuranceTrait::summaryPremium($insurance_vmi_renew->premium, $insurance_vmi_renew->discount, $insurance_vmi_renew->stamp_duty, $insurance_vmi_renew->tax);
        $insurance_vmi_renew->sum_insured_total = number_format($insurance_vmi_renew->sum_insured_car + $insurance_vmi_renew->sum_insured_accessory, 2, '.', ',');
        $page_title =   __('insurance_car.renew_insurance');
        return view('admin.insurance-car-vmi-renew.form', [
            'mode' => MODE_VIEW,
            'page_title' => $page_title,
            'd' => $insurance_vmi_renew,
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
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::InsuranceCarVmiRenew);
        $vmi = VMI::findOrFail($request->id);
        $vmi_status = $vmi->status;
        $validate_data = [
            'car_class_insurance_id' => 'required',
            'type_vmi' => 'required',
            'type_cmi' => 'required',
            'sum_insured_car' => 'required | string | max:10',
            'sum_insured_accessory' => 'required | string | max:10',
            // 'send_date' => 'required | date',
            // 'term_start_date' => 'required | date',
            // 'term_end_date' => 'required | date',
            'insurance_type' => 'required',
            'insurer_id' => 'required',
            'beneficiary_id' => 'required',
            'insurance_package_id' => 'required',
        ];

        if (strcmp($vmi_status, InsuranceStatusEnum::IN_PROCESS) === 0) {
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
            'car_class_insurance_id' => __('cmi_cars.insurance_class'),
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
            'premium' => __('cmi_cars.premium_net'),
            'discount' => __('cmi_cars.discount'),
            'stamp_duty' => __('cmi_cars.stamp_duty'),
            'tax' => __('cmi_cars.tax'),
            'premium_total' => __('cmi_cars.premium_total'),
            'withholding_tax' => __('cmi_cars.withholding_tax_1'),
            'insurance_type' => __('vmi_cars.insurance_type'),
            'insurance_package_id' => __('vmi_cars.condition'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $vmi->car_class_insurance_id = $request->car_class_insurance_id;
        $vmi->type_vmi = $request->type_vmi;
        $vmi->type_cmi = $request->type_cmi;
        $vmi->sum_insured_car = transform_float($request->sum_insured_car);
        $vmi->sum_insured_accessory = transform_float($request->sum_insured_accessory);
        $vmi->insurer_id = $request->insurer_id;
        $vmi->insurance_type = $request->insurance_type;
        $vmi->insurance_package_id = $request->insurance_package_id;
        $vmi->beneficiary_id = $request->beneficiary_id;
        $vmi->remark = $request->remark;
        $vmi->send_date = $request->send_date;

        $vmi->receive_date = $request->receive_date;
        $vmi->check_date = $request->check_date;
        $vmi->policy_reference_child_vmi = $request->policy_reference_child_vmi;
        $vmi->policy_reference_vmi = $request->policy_reference_vmi;
        $vmi->endorse_vmi = $request->endorse_vmi;

        // $vmi->term_start_date = $request->term_start_date;
        // $vmi->term_end_date = $request->term_end_date;
        $vmi->term_start_date = $request->term_start_date . " 16:30:00";
        $vmi->term_end_date = $request->term_end_date . " 16:30:00";

        $vmi->pa = transform_float($request->pa);
        $vmi->pa_and_bb = transform_float($request->pa_and_bb);
        $vmi->pa_per_endorsement = transform_float($request->pa_per_endorsement);
        $vmi->pa_total_premium = transform_float($request->pa_total_premium);
        $vmi->id_deductible = transform_float($request->id_deductible);
        $vmi->discount_deductible = transform_float($request->discount_deductible);
        $vmi->fit_discount = transform_float($request->fit_discount);
        $vmi->fleet_discount = transform_float($request->fleet_discount);
        $vmi->ncb = transform_float($request->ncb);
        $vmi->good_vmi = transform_float($request->good_vmi);
        $vmi->bad_vmi = transform_float($request->bad_vmi);

        $vmi->other_discount_percent = transform_float($request->other_discount_percent);
        $vmi->other_discount = transform_float($request->other_discount);
        $vmi->gps_discount = transform_float($request->gps_discount);
        $vmi->total_discount = transform_float($request->total_discount);
        $vmi->net_discount = transform_float($request->net_discount);
        $vmi->cct = transform_float($request->cct);
        $vmi->gross = transform_float($request->gross);

        $vmi->tpbi_person = transform_float($request->tpbi_person);
        $vmi->tpbi_aggregate = transform_float($request->tpbi_aggregate);
        $vmi->tppd_aggregate = transform_float($request->tppd_aggregate);
        $vmi->deductible = transform_float($request->deductible);
        $vmi->own_damage = transform_float($request->own_damage);
        $vmi->fire_and_theft = transform_float($request->fire_and_theft);
        $vmi->deductible_car = transform_float($request->deductible_car);
        $vmi->pa_driver = transform_float($request->pa_driver);
        $vmi->pa_passenger = transform_float($request->pa_passenger);
        $vmi->medical_exp = transform_float($request->medical_exp);
        $vmi->bail_bond = transform_float($request->bail_bond);

        if (strcmp($vmi_status, InsuranceStatusEnum::IN_PROCESS) === 0) {
            $premium = transform_float($request->premium);
            $discount = transform_float($request->discount);
            $stamp_duty = transform_float($request->stamp_duty);
            $tax = transform_float($request->tax);
            if ($premium <= 0 || $stamp_duty <= 0 || $tax <= 0) {
                return $this->responseWithCode(false,  __('cmi_cars.premium_gt_zero'), null, 422);
            }
            $vmi->premium = $premium;
            $vmi->discount = $discount;
            $vmi->stamp_duty = $stamp_duty;
            $vmi->tax = $tax;
        }

        $vmi->statement_no = $request->statement_no;
        $vmi->tax_invoice_no = $request->tax_invoice_no;
        $vmi->statement_date = $request->statement_date;
        $vmi->account_submission_date = $request->account_submission_date;
        $vmi->operated_date = $request->operated_date;
        $vmi->status_pay_premium = $request->status_pay_premium;
        if (strcmp($vmi_status, InsuranceStatusEnum::IN_PROCESS) === 0) {
            if (!empty($vmi->policy_reference_child_vmi) && !empty($vmi->policy_reference_vmi) && !empty($vmi->endorse_vmi)) {
                $vmi->status = InsuranceStatusEnum::COMPLETE;
                $current_date = Carbon::now()->format('Y-m-d H:i:s');
                $term_start_date = Carbon::parse($request->term_start_date)->format('Y-m-d H:i:s');
                $dateTime = Carbon::parse($request->term_start_date);
                if ($term_start_date <= $current_date) {
                    $vmi->status_vmi = InsuranceCarStatusEnum::UNDER_POLICY;
                } else {
                    $vmi_status_update = new EffectiveVmiJob($vmi);
                    dispatch($vmi_status_update)->delay($dateTime);
                }
            }
        }
        $vmi->save();

        $redirect_route = route('admin.insurance-vmi-renew.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function makeInProcessVMIs(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::InsuranceCarVmiRenew);
        $vmi_list = $request->vmi_list;
        $term_start_date = $request->term_start_date;
        $term_end_date = $request->term_end_date;
        foreach ($vmi_list as $vmi) {
            try {
                $vmi = VMI::findOrFail($vmi['id']);
                $vmi->term_start_date = $term_start_date;
                $vmi->term_end_date = $term_end_date;
                $vmi->status = InsuranceStatusEnum::IN_PROCESS;
                $vmi->save();
            } catch (ErrorException $exception) {
                return $this->responseWithCode(false, __('lang.store_error_title'), null, 422);
            }
        }
        return $this->responseWithCode(true, DATA_SUCCESS, null, 200);
    }

    public function getInsurancePackageDetail(Request $request)
    {
        $insurance_package_id = $request->id;
        if (!$insurance_package_id) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }

        $insurance_package = InsurancePackage::find($insurance_package_id);
        if (!$insurance_package) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }
        $insurance_package->tpbi_person = number_format(floatval($insurance_package->tpbi_person), 2, '.', ',');
        $insurance_package->tpbi_aggregate = number_format(floatval($insurance_package->tpbi_aggregate), 2, '.', ',');
        $insurance_package->tppd_aggregate = number_format(floatval($insurance_package->tppd_aggregate), 2, '.', ',');
        $insurance_package->pa_driver = number_format(floatval($insurance_package->pa_driver), 2, '.', ',');
        $insurance_package->pa_passenger = number_format(floatval($insurance_package->pa_passenger), 2, '.', ',');
        $insurance_package->medical_exp = number_format(floatval($insurance_package->medical_exp), 2, '.', ',');
        $insurance_package->baibond = number_format(floatval($insurance_package->baibond), 2, '.', ',');
        $insurance_package->deductible = number_format(floatval($insurance_package->deductible), 2, '.', ',');
        return response()->json($insurance_package);
    }

    public function exportVMIs(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::InsuranceCarVmiRenew);
        $ids = $request->ids;
        if (!is_array($ids)) {
            return $this->responseWithCode(false, __('lang.store_error_title'), null, 422);
        }
        $vmi_list = VMI::whereIn('id', $ids)->get();
        if (sizeof($vmi_list) <= 0) {
            return $this->responseWithCode(false, __('lang.store_error_title'), null, 422);
        }
        return Excel::download(new ExportVMI($vmi_list), 'template.xlsx');
    }

    public function importVMIs(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::InsuranceCarVmiRenew);
        $file = $request->file('file');
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
            $chassis_no = $row[1];
            $insurance_company = $row[2];
            $license_plate = $row[3];
            $sum_total = $row[4];
            $type_of_repair = $row[5];
            $remark = $row[6];
            $tpbi_person = $row[7];
            $tpbi_aggregate = $row[8];
            $tppd_aggregate = $row[9];
            $pa_driver = $row[10];
            $pa_passenger = $row[11];
            $medical_exp = $row[12];
            $bail_bond = $row[13];
            $deductible = $row[14];
            $policy_reference_child_vmi = $row[15];
            $term_start_date = $row[16];
            $term_end_date = $row[17];
            $premium = $row[18];
            $stamp_duty = $row[19];
            $tax = $row[20];
            $statement_no = $row[23];
            $tax_invoice_no = $row[24];
            $statement_date = $row[25];
            $account_submission_date = $row[26];
            $operated_date = $row[27];

            if (InsuranceTrait::containsOnlyNull($row)) {
                continue;
            }
            if (empty($chassis_no)) {
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

            $vmi = VMI::where('car_id', $car->id)
                ->first();

            if (!$vmi) {
                $log[] = 'ข้อมูลลำดับที่ ' . $no . ' ไม่พบประกัน ไม่สามารถบันทึกได้';
                continue;
            }
            if (!strcmp($vmi->status, InsuranceStatusEnum::IN_PROCESS) === 0) {
                $log[] = 'ข้อมูลลำดับที่ ' . $no . ' สถานะไม่ถูกต้อง ไม่สามารถบันทึกได้';
                continue;
            }
            $vmi->type_of_repair = $type_of_repair;
            $vmi->remark = $remark;
            $vmi->tpbi_person = transform_float($tpbi_person) ?? null;
            $vmi->tpbi_aggregate = transform_float($tpbi_aggregate) ?? null;
            $vmi->tppd_aggregate = transform_float($tppd_aggregate) ?? null;
            $vmi->deductible = transform_float($deductible) ?? null;
            $vmi->pa_driver = transform_float($pa_driver) ?? null;
            $vmi->pa_passenger = transform_float($pa_passenger) ?? null;
            $vmi->medical_exp = transform_float($medical_exp) ?? null;
            $vmi->bail_bond = transform_float($bail_bond) ?? null;
            $vmi->bail_bond = transform_float($bail_bond) ?? null;

            $vmi->policy_reference_child_vmi = $policy_reference_child_vmi;
            $vmi->term_start_date = $term_start_date ? InsuranceTrait::formatDateToDefault($term_start_date) : null;
            $vmi->term_end_date = $term_end_date ? InsuranceTrait::formatDateToDefault($term_end_date) : null;

            $vmi->premium = transform_float($premium) ?? null;
            $vmi->stamp_duty = transform_float($stamp_duty) ?? null;
            $vmi->tax = transform_float($tax) ?? null;
            $vmi->statement_no = $statement_no;
            $vmi->tax_invoice_no = $tax_invoice_no;
            $vmi->statement_date = $statement_date ? InsuranceTrait::formatDateToDefault($statement_date) : null;
            $vmi->account_submission_date = $account_submission_date ? InsuranceTrait::formatDateToDefault($account_submission_date) : null;
            $vmi->operated_date = $operated_date ? InsuranceTrait::formatDateToDefault($operated_date) : null;
            $vmi->save();
        }
        return $this->responseWithCode(true, DATA_SUCCESS, $log, 200);
    }
}
