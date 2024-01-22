<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\InsuranceCarEnum;
use App\Enums\InsuranceCarStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\ImportCarLine;
use App\Models\InsurancePackage;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Models\VMI;
use App\Traits\InsuranceTrait;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;

class InsuranceCarVmiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $response_status = $this->responseWithCode(false, 'ไม่สามารถส่งคำร้องแก้ไขได้', null, 404);
        $type = $request?->request_type;
        $vmi_id = $request->id;
        $vmi_data = VMI::findOrFail($vmi_id);
        if (!empty($request?->request_type) && !empty($vmi_data)) {

            //
            if ($type == InsuranceCarEnum::BENEFITS && !empty($vmi_data)) {

                $old_data = [
                    'beneficiary_id' => $vmi_data->beneficiary_id
                ];
                $new_data = [
                    'beneficiary_id' => $request->beneficiary_id
                ];
                $status = $this->saveAuditRequestChange($vmi_data, $old_data, $new_data, $request?->userAgent(), $request?->ip(), $request->url(), $type);
            }
            if ($type == InsuranceCarEnum::INSURANCE_FUNDS) {
                $old_data = [
                    'sum_insured_car' => $vmi_data->sum_insured_car,
                    'sum_insured_accessory' => $vmi_data->sum_insured_accessory,
                ];
                $new_data = [
                    'sum_insured_car' => $request->sum_insured_car,
                    'sum_insured_accessory' => $request->sum_insured_accessory
                ];
                $status = $this->saveAuditRequestChange($vmi_data, $old_data, $new_data, $request?->userAgent(), $request?->ip(), $request->url());
            }
            //
            if ($type == InsuranceCarEnum::BUY_PROTECTION) {
                $old_data = [
                    'tpbi_person' => $vmi_data->tpbi_person,
                    'tpbi_aggregate' => $vmi_data->tpbi_aggregate,
                    'tppd_aggregate' => $vmi_data->tppd_aggregate,
                    'deductible' => $vmi_data->deductible,
                    'own_damage' => $vmi_data->own_damage,
                    'fire_and_theft' => $vmi_data->fire_and_theft,
                    'pa_driver' => $vmi_data->pa_driver,
                    'pa_passenger' => $vmi_data->pa_passenger,
                    'medical_exp' => $vmi_data->medical_exp,
                    'bail_bond' => $vmi_data->bail_bond,
                ];
                $new_data = [
                    'tpbi_person' => $request->tpbi_person,
                    'tpbi_aggregate' => $request->tpbi_aggregate,
                    'tppd_aggregate' => $request->tppd_aggregate,
                    'deductible' => $request->deductible,
                    'own_damage' => $request->own_damage,
                    'fire_and_theft' => $request->fire_and_theft,
                    'pa_driver' => $request->pa_driver,
                    'pa_passenger' => $request->pa_passenger,
                    'medical_exp' => $request->medical_exp,
                    'bail_bond' => $request->bail_bond,
                ];
                $status = $this->saveAuditRequestChange($vmi_data, $old_data, $new_data, $request?->userAgent(), $request?->ip(), $request->url());
            }
            if ($status == true) {
                $response_status = $this->responseWithCode(true, 'ส่งขำร้องขอแก้ไขสำเร็จ', null, 200);
            }
        }
        return $response_status;
    }

    public function saveAuditRequestChange($model, $old_data, $new_data, $user_agent, $ip, $url, $type = null)
    {
        $status = false;
        try {
            $old_data_json = json_encode($old_data);
            $new_data_json = json_encode($new_data);
            $data_audit = [
                'user_type' => User::class,
                'user_id' => auth()?->user()?->id,
                'event' => 'request',
                'auditable_type' => VMI::class,
                'auditable_id' => $model->id,
                'old_values' => $old_data_json,
                'new_values' => $new_data_json,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_agent' => $user_agent,
                'ip_address' => $ip,
                'url' => $url,
            ];
            //            save new data
            foreach ($new_data as $key => $value) {
                if ($type == InsuranceCarEnum::BENEFITS) {
                    $model->$key = $value;
                } else {
                    $model->$key = transform_float($value);
                }
            }
            $model->save();
            $save_audit = DB::table('audits')->insert($data_audit);
            if ($save_audit) {
                $status = true;
            }
        } catch (Exception $e) {
            $status = false;
        }

        return $status;
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(VMI $insurance_car_vmi)
    {
        $car = Car::find($insurance_car_vmi->car_id);
        if ($car) {
            $import_car_line = ImportCarLine::find($insurance_car_vmi->car_id);
            $car->receive_date = null;
            if ($import_car_line) {
                $car->receive_date = $import_car_line->delivery_date;
                $car->registration_type = $import_car_line->registration_type;
            }
            $car->class_name = $car->carClass?->full_name;
        }
        $po = null;
        if (strcmp($insurance_car_vmi->job_type, PurchaseOrder::class) === 0) {
            $po = PurchaseOrder::find($insurance_car_vmi->job_id);
        }
        $package_name = null;
        if ($insurance_car_vmi->insurance_package_id) {
            $package = InsurancePackage::find($insurance_car_vmi->insurance_package_id);
            $package_name = $package?->name ?? null;
        }
        $insurance_car_vmi->end_three_month_term_status = $this->checkEndTermVmi($insurance_car_vmi);
        $insurance_car_vmi->renew_status = 0;
        $type_vmi_list = InsuranceTrait::getTypeVMIList();
        $type_cmi_list = InsuranceTrait::getTypeCMIList();
        $car_class_insurance_list = InsuranceTrait::getCarClassInsranceList();
        $insurer_list = InsuranceTrait::getInsurerList();
        $premium_status_list = InsuranceTrait::getPremiumPaymentStatus();
        $insurance_type_list = InsuranceTrait::getInsuranceTypeList();
        $leasing_list = InsuranceTrait::getLeasingList();
        $rental = InsuranceTrait::getRentalDetail($insurance_car_vmi);
        $premium_summary = InsuranceTrait::summaryPremium($insurance_car_vmi->premium, $insurance_car_vmi->discount, $insurance_car_vmi->stamp_duty, $insurance_car_vmi->tax);
        $insurance_car_vmi->sum_insured_total = number_format($insurance_car_vmi->sum_insured_car + $insurance_car_vmi->sum_insured_accessory, 2, '.', ',');
        $page_title = __('insurance_car.sheet_protection');
        $insurance_job_type_list = $this->getCmiInsuranceJobType();
        $year_renew = $insurance_car_vmi->year + 1;
        return view('admin.insurance-car-vmi.form', [
            'mode' => MODE_VIEW,
            'page_title' => $page_title,
            'd' => $insurance_car_vmi,
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
            'type' => InsuranceCarEnum::VMI,
            'insurance_job_type_list' => $insurance_job_type_list,
            'year_renew' => $year_renew,
        ]);
    }

    public function checkEndTermVmi($vmi_data)
    {
        $less_than_three_month_status = false;
        if (!empty($vmi_data)) {
            try {
                $term_end_date = $vmi_data?->term_end_date;
                $term_status = $vmi_data?->status_cmi;
                $specificDate = Carbon::createFromFormat('Y-m-d H:i:s', $term_end_date);
                $currentDate = Carbon::now();

                $monthsDifference = $currentDate->diffInMonths($specificDate);
                if ($specificDate < $currentDate) {
                    $monthsDifference = -$monthsDifference;
                }
                $policy_status = [
                    InsuranceCarStatusEnum::END_POLICY,
                    InsuranceCarStatusEnum::CANCEL_POLICY,
                ];
                if ($monthsDifference <= 3 && $monthsDifference >= 0 && !in_array($term_status, $policy_status)) {
                    $less_than_three_month_status = true;
                }
            } catch (Throwable $t) {
                $less_than_three_month_status = false;
            }
        } else {
            $less_than_three_month_status = false;
        }
        return $less_than_three_month_status;
    }

    public function getCmiInsuranceJobType()
    {
        $car_statues = collect([
            (object)[
                'id' => 'RenewCmi',
                'name' => 'ต่ออายุ',
                'value' => 'ต่ออายุ',
            ],
        ]);
        return $car_statues;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(VMI $insurance_car_vmi)
    {
        //        dd($insurance_car_vmi->audits()->where('event','request')->get()[0]->new_values);
        $car = Car::find($insurance_car_vmi->car_id);
        if ($car) {
            $import_car_line = ImportCarLine::find($insurance_car_vmi->car_id);
            $car->receive_date = null;
            if ($import_car_line) {
                $car->receive_date = $import_car_line->delivery_date;
                $car->registration_type = $import_car_line->registration_type;
            }
            $car->class_name = $car->carClass?->full_name;
        }
        $po = null;
        if (strcmp($insurance_car_vmi->job_type, PurchaseOrder::class) === 0) {
            $po = PurchaseOrder::find($insurance_car_vmi->job_id);
        }
        $package_name = null;
        if ($insurance_car_vmi->insurance_package_id) {
            $package = InsurancePackage::find($insurance_car_vmi->insurance_package_id);
            $package_name = $package?->name ?? null;
        }
        $insurance_car_vmi->end_three_month_term_status = $this->checkEndTermVmi($insurance_car_vmi);
        $insurance_car_vmi->renew_status = $this->checkRenewStatus($insurance_car_vmi);

        $type_vmi_list = InsuranceTrait::getTypeVMIList();
        $type_cmi_list = InsuranceTrait::getTypeCMIList();
        $car_class_insurance_list = InsuranceTrait::getCarClassInsranceList();
        $insurer_list = InsuranceTrait::getInsurerList();
        $premium_status_list = InsuranceTrait::getPremiumPaymentStatus();
        $insurance_type_list = InsuranceTrait::getInsuranceTypeList();
        $leasing_list = InsuranceTrait::getLeasingList();
        $rental = InsuranceTrait::getRentalDetail($insurance_car_vmi);
        $premium_summary = InsuranceTrait::summaryPremium($insurance_car_vmi->premium, $insurance_car_vmi->discount, $insurance_car_vmi->stamp_duty, $insurance_car_vmi->tax);
        $insurance_car_vmi->sum_insured_total = number_format($insurance_car_vmi->sum_insured_car + $insurance_car_vmi->sum_insured_accessory, 2, '.', ',');
        $page_title = __('insurance_car.sheet_protection');
        $insurance_job_type_list = $this->getCmiInsuranceJobType();
        $year_renew = $insurance_car_vmi->year + 1;
        return view('admin.insurance-car-vmi.form', [
            'mode' => MODE_UPDATE,
            'page_title' => $page_title,
            'd' => $insurance_car_vmi,
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
            'type' => InsuranceCarEnum::VMI,
            'insurance_job_type_list' => $insurance_job_type_list,
            'year_renew' => $year_renew
        ]);
    }

    public function checkRenewStatus($vmi_data)
    {
        $renew_status = 0;
        if (!empty($vmi_data)) {
            $total_renew = VMI::where('car_id', $vmi_data->car_id)
                ->where('status_vmi', InsuranceCarStatusEnum::RENEW_POLICY)
                ->count();
            if ($total_renew == 0 && $vmi_data->status_vmi == InsuranceCarStatusEnum::UNDER_POLICY) {
                $renew_status = 1;
            } else {
                $renew_status = 0;
            }
        }
        return $renew_status;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(VMI $insurance_car_vmi, Request $request)
    {
    }

    public function requestCancelInsurance(VMI $insurance_car_vmi, Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::InsuranceCar);
        if ($insurance_car_vmi->status_vmi != InsuranceCarStatusEnum::UNDER_POLICY) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }
        try {
            DB::transaction(function () use ($insurance_car_vmi, $request) {
                $insurance_car_vmi->status_vmi = InsuranceCarStatusEnum::REQUEST_CANCEL;
                $insurance_car_vmi->save();

                $cancel_date = $request?->insurance_cancel_date;
                $cancel_reason = $request?->insurance_cancel_reason;
                $success = InsuranceTrait::createCancelInsurance($insurance_car_vmi->id, VMI::class, $cancel_date, $cancel_reason);
                if (!$success) {
                    throw new Exception(__('lang.store_error_title'), 0);
                }
            });
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $redirect_route = route('admin.insurance-car.index');
        return $this->responseValidateSuccess($redirect_route);
    }
}
