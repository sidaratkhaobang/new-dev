<?php

namespace App\Http\Controllers\Admin\Util;

use App\Enums\MistakeTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\InsuranceCompanies;
use App\Models\Insurer;
use Illuminate\Http\Request;

class Select2InsuranceController extends Controller
{
    public function getDeductLicensePlates(Request $request)
    {
        $searchFilter = $request?->s;
        $searchType = $request?->parent_id;
        $listCar = Accident::select('car_id')
            ->leftJoin('cars', 'cars.id', 'accidents.car_id')
            ->when(!empty($searchFilter), function ($querySearch) use ($searchFilter) {
                $querySearch->where('cars.license_plate', 'like', '%' . $searchFilter . '%');
                $querySearch->orWhere('cars.engine_no', 'like', '%' . $searchFilter . '%');
                $querySearch->orWhere('cars.chassis_no', 'like', '%' . $searchFilter . '%');
            })
            ->when(!empty($searchType), function ($querySearch) {
                $querySearch->where('accidents.wrong_type', MistakeTypeEnum::FALSE);
                $querySearch->orwhere('accidents.wrong_type', MistakeTypeEnum::TRUE);
            })
            ->when(empty($searchType), function ($querySearch) {
                $querySearch->where('accidents.wrong_type', MistakeTypeEnum::FALSE);
            })
            ->distinct('car_id')
            ->get()
            ->map(function ($item) {
                if ($item?->car?->license_plate) {
                    $text = $item?->car?->license_plate;
                } else if ($item?->car?->engine_no) {
                    $text = __('inspection_cars.engine_no') . ' ' . $item?->car?->engine_no;
                } else if ($item?->car?->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $item?->car?->chassis_no;
                }
                $item->id = $item->car->id;
                $item->text = $text;
                return $item;
            });
        return response()->json($listCar);
    }

    public function getDeductInsuranceList(Request $request)
    {
        $searchFilter = $request?->s;
        $listInsurance = Insurer::get()
            ->when(!empty($searchFilter), function ($querySearch) use ($searchFilter) {
                $querySearch->Where('insurance_name_th', 'like', '%' . $searchFilter . '%');
                $querySearch->orWhere('insurance_name_en', 'like', '%' . $searchFilter . '%');
            })
            ->map(function ($item) {
                $text = '-';
                if (!empty($item->insurance_name_th)) {
                    $text = $item->insurance_name_th;
                } else {
                    $text = $item->insurance_name_en;
                }
                $item->id = $item->id;
                $item->text = $text;
                return $item;
            });
        return response()->json($listInsurance);
    }


    public function getDeductPolicyReferenceList(Request $request)
    {
        $searchFilter = $request?->s;
        $searchType = $request?->parent_id;
        $listPolicyReference = Accident::select('policy_reference_vmi')
            ->leftJoin(
                'voluntary_motor_insurances',
                'accidents.car_id',
                'voluntary_motor_insurances.car_id'
            )
            ->when(!empty($searchFilter), function ($querySearch) use ($searchFilter) {
                $querySearch->where('policy_reference_vmi', $searchFilter);
            })
            ->when(!empty($searchType), function ($querySearch) {
                $querySearch->where('accidents.wrong_type', MistakeTypeEnum::TRUE);
                $querySearch->orwhere('accidents.wrong_type', MistakeTypeEnum::FALSE);
            })
            ->when(empty($searchType), function ($querySearch) {
                $querySearch->where('accidents.wrong_type', MistakeTypeEnum::FALSE);
            })
            ->groupBy('policy_reference_vmi')
            ->get()
            ->map(function ($item) {
                $item->id = $item?->policy_reference_vmi;
                $item->text = $item?->policy_reference_vmi;
                return $item;
            });
        return response()->json($listPolicyReference);
    }

    public function getDataCustomer(Request $request)
    {
        $searchFilter = $request?->s;
        $dataCustomer = Customer::limit(30)
            ->when(!empty($searchFilter), function ($querySearch) use ($searchFilter) {
                $querySearch->where('name', 'Like', '%' . $searchFilter . '%');
            })
            ->get()
            ->map(function ($item) {
                $item->id = $item?->id;
                $item->text = $item?->name;
                return $item;
            });
        if (!empty($dataCustomer->count())) {
            return response()->json($dataCustomer);
        } else {
            return [];
        }
    }

    public function getDataCusotmerGroup(Request $request)
    {
        $searchFilter = $request?->s;
        $dataCustomerGroup = CustomerGroup::limit(30)
            ->when(!empty($searchFilter), function ($querySearch) use ($searchFilter) {
                $querySearch->where('name', 'Like', '%' . $searchFilter . '%');
            })
            ->get()
            ->map(function ($item) {
                $item->id = $item?->name;
                $item->text = $item?->name;
                return $item;
            });
        return response()->json($dataCustomerGroup);
    }

    public function getInsuranceCompanies(Request $request)
    {
        $insurance_companies = InsuranceCompanies::select('id', 'insurance_name_th as name')
            ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('insurers.name', 'like', '%' . $request->s . '%');
                }
            })
            ->orderBy('name')
            ->limit(50)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($insurance_companies);
    }
}
