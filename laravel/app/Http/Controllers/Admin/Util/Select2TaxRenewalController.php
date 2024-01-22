<?php

namespace App\Http\Controllers\Admin\Util;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarClass;
use App\Models\ChangeRegistration;
use App\Models\InsuranceLot;
use App\Models\OwnershipTransfer;
use App\Models\Register;
use App\Models\TaxRenewal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Select2TaxRenewalController extends Controller
{

    function getCarLicensePlate(Request $request)
    {
        $list = TaxRenewal::leftJoin('cars', 'cars.id', '=', 'tax_renewals.car_id')
            ->select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('cars.license_plate', 'like', '%' . $request->s . '%');
                    $query->orWhere('cars.engine_no', 'like', '%' . $request->s . '%');
                    $query->orWhere('cars.chassis_no', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                if ($item->license_plate) {
                    $text = $item->license_plate;
                } else if ($item->engine_no) {
                    $text = __('inspection_cars.engine_no') . ' ' . $item->engine_no;
                } else if ($item->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
                }
                $item->id = $item->id;
                $item->text = $text;
                return $item;
            });

        return response()->json($list);
    }

    function getCarClasses(Request $request)
    {
        $car_class = TaxRenewal::leftJoin('cars', 'cars.id', '=', 'tax_renewals.car_id')
            ->leftJoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->select('car_classes.id', 'car_classes.name', 'car_classes.full_name')
            // ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('car_classes.name', 'like', '%' . $request->s . '%');
                    $query->orWhere('car_classes.full_name', 'like', '%' . $request->s . '%');
                }
            })
            // ->orderBy('seq')
            ->orderBy('name')
            ->distinct('car_classes.id')
            ->limit(30)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->full_name . ' - ' . $item->name
                ];
            });
        return response()->json($car_class);
    }
}
