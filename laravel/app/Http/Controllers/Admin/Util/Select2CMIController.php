<?php

namespace App\Http\Controllers\Admin\Util;

use App\Enums\InsuranceRegistrationEnum;
use App\Http\Controllers\Controller;
use App\Models\CancelInsurance;
use App\Models\Car;
use App\Models\CMI;
use App\Models\Customer;
use App\Models\InsuranceCompanies;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Select2CMIController extends Controller
{
    function getCMIWorksheets(Request $request)
    {
        $type = $request?->parent_id;
        $list = CMI::select('id', 'worksheet_no')
            ->where(function ($query) use ($request, $type) {
                if (!empty($request->s)) {
                    $query->where('worksheet_no', 'like', '%' . $request->s . '%');
                }
                if (!empty($type) && $type == InsuranceRegistrationEnum::RENEW) {
                    $query->whereNotnull('status_cmi')
                        ->where('type', InsuranceRegistrationEnum::RENEW);
                }
            })
            ->orderBy('worksheet_no')
            ->limit(30)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->worksheet_no
                ];
            });
        return response()->json($list);
    }

    function getCMILicensePlates(Request $request)
    {
        $type = $request?->parent_id;
        $list = Car::join('compulsory_motor_insurances', 'compulsory_motor_insurances.car_id', '=', 'cars.id')
            ->select('cars.id', 'cars.license_plate')
            ->where(function ($query) use ($request, $type) {
                if (!empty($request->s)) {
                    $query->where('cars.license_plate', 'like', '%' . $request->s . '%');
                }
                if (!empty($type) && $type == InsuranceRegistrationEnum::RENEW) {
                    $query->whereNotnull('compulsory_motor_insurances.status_cmi')
                        ->where('compulsory_motor_insurances.type', InsuranceRegistrationEnum::RENEW);
                }
            })
            ->orderBy('cars.license_plate')
            ->limit(30)
            ->distinct('cars.id')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->license_plate
                ];
            });
        return response()->json($list);
    }

    function getCMIInsurers(Request $request)
    {
        $type = $request?->parent_id;
        $list = InsuranceCompanies::join('compulsory_motor_insurances', 'compulsory_motor_insurances.insurer_id', '=', 'insurers.id')
            ->select('insurers.id', 'insurers.insurance_name_th')
            ->where(function ($query) use ($request, $type) {
                if (!empty($request->s)) {
                    $query->where('insurers.insurance_name_th', 'like', '%' . $request->s . '%');
                }
                if (!empty($type) && $type == InsuranceRegistrationEnum::RENEW) {
                    $query->whereNotnull('status_cmi')
                        ->where('type', InsuranceRegistrationEnum::RENEW);
                }
            })
            ->orderBy('insurers.insurance_name_th')
            ->limit(30)
            ->distinct('insurers.id')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->insurance_name_th
                ];
            });
        return response()->json($list);
    }

    function getCMIPOs(Request $request)
    {
        $type = $request?->parent_id;
        $list = PurchaseOrder::join('compulsory_motor_insurances', 'compulsory_motor_insurances.job_id', '=', 'purchase_orders.id')
            ->select('purchase_orders.id', 'purchase_orders.po_no')
            ->where(function ($query) use ($request, $type) {
                if (!empty($request->s)) {
                    $query->where('purchase_orders.po_no', 'like', '%' . $request->s . '%');
                }
                if (!empty($type) && $type == InsuranceRegistrationEnum::RENEW) {
                    $query->whereNotnull('compulsory_motor_insurances.status_cmi')
                        ->where('compulsory_motor_insurances.type', InsuranceRegistrationEnum::RENEW);
                }
            })
            ->orderBy('purchase_orders.po_no')
            ->limit(30)
            ->distinct('purchase_orders.id')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->po_no
                ];
            });
        return response()->json($list);
    }

    function getCMILots(Request $request)
    {
        $type = $request?->parent_id;
        $list = CMI::select('lot_number')
            ->where(function ($query) use ($request, $type) {
                if (!empty($request->s)) {
                    $query->where('lot_number', 'like', '%' . $request->s . '%');
                }
                if (!empty($type) && $type == InsuranceRegistrationEnum::RENEW) {
                    $query->whereNotnull('status_cmi')
                        ->where('type', InsuranceRegistrationEnum::RENEW);
                }
            })
            ->orderBy('lot_number')
            ->distinct('lot_number')
            ->limit(30)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->lot_number,
                    'text' => $item->lot_number
                ];
            });
        return response()->json($list);
    }

    function getCancelCMIWorkSheets(Request $request)
    {
        $list = CancelInsurance::select('id', 'worksheet_no')
            ->where('ref_type', CMI::class)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('worksheet_no', 'like', '%' . $request->s . '%');
                }
            })
            ->orderBy('worksheet_no')
            ->limit(30)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->worksheet_no
                ];
            });
        return response()->json($list);
    }

    function getCancelCMILicensePlates(Request $request)
    {
        $type = $request?->parent_id;
        $list = Car::join('compulsory_motor_insurances', 'compulsory_motor_insurances.car_id', '=', 'cars.id')
            ->join('cancel_vmi_cmis', 'cancel_vmi_cmis.ref_id', '=', 'compulsory_motor_insurances.id')
            ->where('cancel_vmi_cmis.ref_type', CMI::class)
            ->select('cars.id', 'cars.license_plate')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('cars.license_plate', 'like', '%' . $request->s . '%');
                }

            })
            ->orderBy('cars.license_plate')
            ->limit(30)
            ->distinct('cars.id')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->license_plate
                ];
            });
        return response()->json($list);
    }

    function getCancelCMIInsurers(Request $request)
    {
        $type = $request?->parent_id;
        $list = InsuranceCompanies::join('compulsory_motor_insurances', 'compulsory_motor_insurances.insurer_id', '=', 'insurers.id')
            ->join('cancel_vmi_cmis', 'cancel_vmi_cmis.ref_id', '=', 'compulsory_motor_insurances.id')
            ->where('cancel_vmi_cmis.ref_type', CMI::class)
            ->select('insurers.id', 'insurers.insurance_name_th')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('insurers.insurance_name_th', 'like', '%' . $request->s . '%');
                }
            })
            ->orderBy('insurers.insurance_name_th')
            ->limit(30)
            ->distinct('insurers.id')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->insurance_name_th
                ];
            });
        return response()->json($list);
    }
}