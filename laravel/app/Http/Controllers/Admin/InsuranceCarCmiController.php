<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\CMIStatusEnum;
use App\Enums\CMITypeEnum;
use App\Enums\InsuranceCarEnum;
use App\Enums\InsuranceCarStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CMI;
use App\Models\ImportCarLine;
use App\Models\PurchaseOrder;
use App\Traits\InsuranceTrait;
use Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InsuranceCarCmiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(CMI $insurance_car_cmi)
    {
        $this->authorize(Actions::View . '_' . Resources::InsuranceCar);
        $this->authorize(Actions::Manage . '_' . Resources::InsuranceCar);
        $route_edit_redirect = route('admin.insurance-car-cmi.edit', [$insurance_car_cmi?->id]);
        $route_remark_redirect = route('admin.insurance-car-cmi.remark', [$insurance_car_cmi?->id]);
        $car = Car::find($insurance_car_cmi->car_id);
        if ($car) {
            $import_car_line = ImportCarLine::find($insurance_car_cmi->car_id);
            $car->receive_date = null;
            if ($import_car_line) {
                $car->receive_date = $import_car_line->delivery_date;
                $car->registration_type = $import_car_line->registration_type;
            }
            $car->class_name = $car->carClass?->full_name;
        }
        $po = null;
        if (strcmp($insurance_car_cmi->job_type, PurchaseOrder::class) === 0) {
            $po = PurchaseOrder::find($insurance_car_cmi->job_id);
        }
        $insurance_car_cmi->end_three_month_term_status = $this->checkEndTermCmi($insurance_car_cmi);
        $insurance_car_cmi->renew_status = 0;

        $type_vmi_list = InsuranceTrait::getTypeVMIList();
        $type_cmi_list = InsuranceTrait::getTypeCMIList();
        $car_class_insurance_list = InsuranceTrait::getCarClassInsranceList();
        $insurer_list = InsuranceTrait::getInsurerList();
        $premium_status_list = InsuranceTrait::getPremiumPaymentStatus();
        $premium_summary = InsuranceTrait::summaryPremium($insurance_car_cmi->premium, $insurance_car_cmi->discount, $insurance_car_cmi->stamp_duty, $insurance_car_cmi->tax);
        $insurance_car_cmi->sum_insured_total = number_format($insurance_car_cmi->sum_insured_car + $insurance_car_cmi->sum_insured_accessory, 2, '.', ',');
        $insurance_job_type_list = $this->getCmiInsuranceJobType();
        $rental = InsuranceTrait::getRentalDetail($insurance_car_cmi);
        $leasing_list = InsuranceTrait::getLeasingList();
        $page_title = __('insurance_car.sheet_policy');
        $year_renew = $insurance_car_cmi->year + 1;
        return view('admin.insurance-car-cmi.form', [
            'page_title' => $page_title,
            'route_edit_redirect' => $route_edit_redirect,
            'route_remark_redirect' => $route_remark_redirect,
            'mode' => MODE_UPDATE,
            'd' => $insurance_car_cmi,
            'car' => $car,
            'type_vmi_list' => $type_vmi_list,
            'type_cmi_list' => $type_cmi_list,
            'car_class_insurance_list' => $car_class_insurance_list,
            'insurer_list' => $insurer_list,
            'insurance_job_type_list' => $insurance_job_type_list,
            'po' => $po,
            'premium_summary' => $premium_summary,
            'premium_status_list' => $premium_status_list,
            'rental' => $rental,
            'leasing_list' => $leasing_list,
            'type' => InsuranceCarEnum::CMI,
            'year_renew' => $year_renew
        ]);
    }

    public function checkEndTermCmi($cmi_data)
    {
        $less_than_three_month_status = false;
        if (!empty($cmi_data)) {
            try {
                $term_end_date = $cmi_data?->term_end_date;
                $term_status = $cmi_data?->status_cmi;
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
    public function edit(CMI $insurance_car_cmi)
    {
        $this->authorize(Actions::Manage . '_' . Resources::InsuranceCar);
        $route_edit_redirect = route('admin.insurance-car-cmi.edit', [$insurance_car_cmi?->id]);
        $route_remark_redirect = route('admin.insurance-car-cmi.remark', [$insurance_car_cmi?->id]);
        $car = Car::find($insurance_car_cmi->car_id);
        if ($car) {
            $import_car_line = ImportCarLine::find($insurance_car_cmi->car_id);
            $car->receive_date = null;
            if ($import_car_line) {
                $car->receive_date = $import_car_line->delivery_date;
                $car->registration_type = $import_car_line->registration_type;
            }
            $car->class_name = $car->carClass?->full_name;
        }
        $po = null;
        if (strcmp($insurance_car_cmi->job_type, PurchaseOrder::class) === 0) {
            $po = PurchaseOrder::find($insurance_car_cmi->job_id);
        }
        $insurance_car_cmi->end_three_month_term_status = $this->checkEndTermCmi($insurance_car_cmi);
        $insurance_car_cmi->renew_status = $this->checkRenewStatus($insurance_car_cmi);
        $type_vmi_list = InsuranceTrait::getTypeVMIList();
        $type_cmi_list = InsuranceTrait::getTypeCMIList();
        $car_class_insurance_list = InsuranceTrait::getCarClassInsranceList();
        $insurer_list = InsuranceTrait::getInsurerList();
        $premium_status_list = InsuranceTrait::getPremiumPaymentStatus();
        $premium_summary = InsuranceTrait::summaryPremium($insurance_car_cmi->premium, $insurance_car_cmi->discount, $insurance_car_cmi->stamp_duty, $insurance_car_cmi->tax);
        $insurance_car_cmi->sum_insured_total = number_format($insurance_car_cmi->sum_insured_car + $insurance_car_cmi->sum_insured_accessory, 2, '.', ',');
        $insurance_job_type_list = $this->getCmiInsuranceJobType();
        $rental = InsuranceTrait::getRentalDetail($insurance_car_cmi);
        $leasing_list = InsuranceTrait::getLeasingList();
        $page_title = __('insurance_car.sheet_policy');
        //        $year_list = $this->getYearList();
        $year_renew = $insurance_car_cmi->year + 1;
        return view('admin.insurance-car-cmi.form', [
            'page_title' => $page_title,
            'route_edit_redirect' => $route_edit_redirect,
            'route_remark_redirect' => $route_remark_redirect,
            'mode' => MODE_UPDATE,
            'd' => $insurance_car_cmi,
            'car' => $car,
            'type_vmi_list' => $type_vmi_list,
            'type_cmi_list' => $type_cmi_list,
            'car_class_insurance_list' => $car_class_insurance_list,
            'insurer_list' => $insurer_list,
            'insurance_job_type_list' => $insurance_job_type_list,
            'po' => $po,
            'premium_summary' => $premium_summary,
            'premium_status_list' => $premium_status_list,
            'rental' => $rental,
            'leasing_list' => $leasing_list,
            'type' => InsuranceCarEnum::CMI,
            'year_renew' => $year_renew
        ]);
    }

    public function checkRenewStatus($cmi_data)
    {
        $renew_status = 0;
        if (!empty($cmi_data)) {
            $total_renew = CMI::where('car_id', $cmi_data->car_id)
                ->where('status_cmi', InsuranceCarStatusEnum::RENEW_POLICY)
                ->count();
            if ($total_renew == 0 && $cmi_data->status_cmi == InsuranceCarStatusEnum::UNDER_POLICY) {
                $renew_status = 1;
            } else {
                $renew_status = 0;
            }
        }
        return $renew_status;
    }

    public function getYearList()
    {
        $year_list = collect([
            (object)[
                'id' => '1',
                'name' => '1ปี',
                'value' => '1',
            ],
        ]);
        return $year_list;
    }

    //    Remark View

    public function remark(CMI $insurance_car_cmi)
    {
        $this->authorize(Actions::View . '_' . Resources::InsuranceCar);
        $route_edit_redirect = route('admin.insurance-car-cmi.edit', [$insurance_car_cmi->id]);
        $route_remark_redirect = route('admin.insurance-car-cmi.remark', [$insurance_car_cmi->id]);
        return view('admin.insurance-car-cmi.form-remark', [
            'route_edit_redirect' => $route_edit_redirect,
            'route_remark_redirect' => $route_remark_redirect,
        ]);
    }

    public function getCarAccessoryListData(Request $request)
    {
        $cmi_data = CMI::where('id', $request?->cmi_id)->first();
        $car_accessory = $cmi_data?->car?->accessories?->map(function ($item) {
            $item->name = $item?->carAccessory?->name;
            $item->price = $item?->carAccessory?->price;
            return $item;
        });
        return $car_accessory;
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
    public function destroy(CMI $insurance_car_cmi, Request $request)
    {
    }

    public function requestCancelInsurance(CMI $insurance_car_cmi, Request $request)
    {
        if ($insurance_car_cmi->status_cmi != InsuranceCarStatusEnum::UNDER_POLICY) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }
        $this->authorize(Actions::Manage . '_' . Resources::InsuranceCar);
        try {
            DB::transaction(function () use ($insurance_car_cmi, $request) {
                $insurance_car_cmi->status_cmi = InsuranceCarStatusEnum::REQUEST_CANCEL;
                $insurance_car_cmi->save();
                $cancel_date = $request?->insurance_cancel_date;
                $cancel_reason = $request?->insurance_cancel_reason;
                $success = InsuranceTrait::createCancelInsurance($insurance_car_cmi->id, CMI::class, $cancel_date, $cancel_reason);
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
