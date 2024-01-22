<?php

namespace App\Http\Controllers\Admin\Util;

use App\Enums\FaceSheetTypeEnum;
use App\Enums\RegisterStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\CarClass;
use App\Models\InsuranceLot;
use App\Models\Register;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Select2RegisterController extends Controller
{

    function getLotList(Request $request)
    {
        $list = Register::leftjoin('insurance_lots', 'insurance_lots.id', '=', 'registereds.lot_id')->select('insurance_lots.id', 'insurance_lots.lot_no as name')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('lot_no', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name,
                ];
            });
        return response()->json($list);
    }

    function getCarClasses(Request $request)
    {
        $car_class = Register::leftJoin('cars', 'cars.id', '=', 'registereds.car_id')
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

    function getLicensePlateList(Request $request)
    {
        $list = Register::leftJoin('cars', 'cars.id', '=', 'registereds.car_id')
            ->distinct('cars.id')
            ->select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    // $query->where('cars.license_plate', 'like', '%' . $request->s . '%');
                    $query->orWhere('cars.engine_no', 'like', '%' . $request->s . '%');
                    $query->orWhere('cars.chassis_no', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                if ($item->engine_no) {
                    $text = __('inspection_cars.engine_no') . ' ' . $item->engine_no;
                } else if ($item->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
                }
                return [
                    'id' => $item->id,
                    'text' => $text,
                ];
            });
        return response()->json($list);
    }

    public function getStatusRegisteredList(Request $request)
    {
        $s = $request->s;

        $register_statuses = collect([
            (object)[
                'id' => RegisterStatusEnum::PREPARE_REGISTER,
                'value' => RegisterStatusEnum::PREPARE_REGISTER,
                'text' => __('registers.status_' . RegisterStatusEnum::PREPARE_REGISTER . '_text'),
            ],
            (object)[
                'id' => RegisterStatusEnum::REGISTERING,
                'value' => RegisterStatusEnum::REGISTERING,
                'text' => __('registers.status_' . RegisterStatusEnum::REGISTERING . '_text'),
            ],
            (object)[
                'id' => RegisterStatusEnum::REGISTERED,
                'value' => RegisterStatusEnum::REGISTERED,
                'text' => __('registers.status_' . RegisterStatusEnum::REGISTERED . '_text'),
            ],
        ]);

        if ($s) {
            $register_statuses = $register_statuses->filter(function ($item) use ($s) {
                return str_contains($item->text, $s);
            })->values();
        }

        return $register_statuses;
    }


    public function getStatusFaceSheetList(Request $request)
    {
        $s = $request->s;

        $facesheet_types = collect([
            (object)[
                'id' => FaceSheetTypeEnum::REGISTER_NEW_CAR,
                'value' => FaceSheetTypeEnum::REGISTER_NEW_CAR,
                'text' => __('registers.type_face_sheet_' . FaceSheetTypeEnum::REGISTER_NEW_CAR),
            ],
            (object)[
                'id' => FaceSheetTypeEnum::RETURN_LEASING,
                'value' => FaceSheetTypeEnum::RETURN_LEASING,
                'text' => __('registers.type_face_sheet_' . FaceSheetTypeEnum::RETURN_LEASING),
            ],
        ]);

        if ($s) {
            $facesheet_types = $facesheet_types->filter(function ($item) use ($s) {
                return str_contains($item->text, $s);
            })->values();
        }

        return $facesheet_types;
    }
}
