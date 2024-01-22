<?php

namespace App\Http\Controllers\Admin\Util;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarClass;
use Illuminate\Http\Request;

class Select2CarController extends Controller
{
    function getCarsByClass(Request $request)
    {

        $list = Car::
            // where('status', STATUS_ACTIVE)
            where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('license_plate', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('car_class_id', $request->parent_id);
                }
            })
            ->select('id', 'license_plate')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->license_plate
                ];
            });
        return response()->json($list);
    }

    function getCarClassesByRentalCategory(Request $request)
    {
        $car_class = CarClass::leftjoin('cars', 'cars.car_class_id', '=', 'car_classes.id')
            ->leftjoin('cars_rental_categories', 'cars_rental_categories.car_id', '=', 'cars.id')
            ->select('car_classes.id', 'car_classes.name', 'car_classes.full_name')
            // ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('car_classes.name', 'like', '%' . $request->s . '%');
                    $query->orWhere('car_classes.full_name', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('cars_rental_categories.rental_category_id', $request->parent_id);
                }
            })
            ->orderBy('car_classes.name')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->full_name . ' - ' . $item->name
                ];
            });
        return response()->json($car_class);
    }

    function getCarByClassAndRentalCategory(Request $request)
    {
        if (empty($request->parent_id)) {
            return response()->json([]);
        }
        $car_list = Car::leftjoin('cars_rental_categories', 'cars_rental_categories.car_id', '=', 'cars.id')
            ->select('cars.id', 'cars.license_plate')
            // ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('license_plate', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('cars_rental_categories.rental_category_id', $request->parent_id);
                }
            })
            ->orderBy('cars.license_plate')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->license_plate
                ];
            });
        return response()->json($car_list);
    }

    function getCarsByCarCode(Request $request)
    {
        $list = Car::where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('code', 'like', '%' . $request->s . '%');
                }
            })
            ->select('id', 'code')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->code
                ];
            });
        return response()->json($list);
    }

    function getCarsByLicensePlate(Request $request)
    {
        $list = Car::where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('license_plate', 'like', '%' . $request->s . '%');
                }
            })
            ->select('id', 'license_plate')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->license_plate
                ];
            });
        __log($list);
        return response()->json($list);
    }

}
