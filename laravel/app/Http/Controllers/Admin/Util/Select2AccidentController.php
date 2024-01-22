<?php

namespace App\Http\Controllers\Admin\Util;

use App\Http\Controllers\Controller;
use App\Models\InstallEquipment;
use App\Models\Rental;
use App\Models\LongTermRental;
use App\Models\DriverWage;
use App\Models\DrivingJob;
use App\Models\ImportCar;
use App\Models\ImportCarLine;
use App\Enums\WageCalType;
use App\Enums\DrivingJobStatusEnum;
use App\Enums\WoundType;
use App\Models\Accident;
use App\Models\AccidentRepairOrder;
use App\Models\Amphure;
use App\Models\Cradle;
use App\Models\District;
use App\Models\Insurer;
use App\Models\Province;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Select2AccidentController extends Controller
{
    function getAccidentList(Request $request)
    {
        // dd($request->all());
        $list = Accident::select('id', 'worksheet_no', 'claim_no')
            ->where('car_id', $request->parent_id)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('worksheet_no', 'like', '%' . $request->s . '%');
                    $query->orWhere('claim_no', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                $accident_no =  $item->claim_no ? $item->worksheet_no . ' / ' . $item->claim_no : $item->worksheet_no;
                return [
                    'id' => $item->id,
                    'text' => $accident_no,
                ];
            });
        return response()->json($list);
    }

    function getAccidentAllList(Request $request)
    {
        $list = Accident::select('id', 'worksheet_no', 'claim_no')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('worksheet_no', 'like', '%' . $request->s . '%');
                    $query->orWhere('claim_no', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                // $accident_no =  $item->claim_no ? $item->worksheet_no . ' / ' . $item->claim_no : $item->worksheet_no;
                return [
                    'id' => $item->id,
                    'text' => $item->worksheet_no,
                ];
            });
        return response()->json($list);
    }

    function getUserList(Request $request)
    {
        $list = User::select('id', 'name')
        ->where(function ($query) use ($request) {
            if (!empty($request->s)) {
                $query->where('name', 'like', '%' . $request->s . '%');
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

    function getInsurerList(Request $request)
    {
        $list = Insurer::select('id', 'insurance_name_th as name')
        ->where(function ($query) use ($request) {
            if (!empty($request->s)) {
                $query->where('insurance_name_th', 'like', '%' . $request->s . '%');
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
    

    function getGarageList(Request $request)
    {
        // dd($request->all());
        $list = Cradle::select('id', 'name')
            // ->where('car_id', $request->parent_id)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name', 'like', '%' . $request->s . '%');
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

    function getWoundList(Request $request)
    {
        // dd($request->all());
        return collect([
            (object)[
                'id' => WoundType::A,
                'value' => WoundType::A,
                'text' => WoundType::A,
            ],
            (object)[
                'id' => WoundType::B,
                'value' => WoundType::B,
                'text' => WoundType::B,
            ],
            (object)[
                'id' => WoundType::C,
                'value' => WoundType::C,
                'text' => WoundType::C,
            ],
            (object)[
                'id' => WoundType::D,
                'value' => WoundType::D,
                'text' => WoundType::D,
            ],
            (object)[
                'id' => WoundType::OLD_SPARE_PART,
                'value' => WoundType::OLD_SPARE_PART,
                'text' => __('accident_informs.wound_type_' . WoundType::OLD_SPARE_PART),
            ],
            (object)[
                'id' => WoundType::REPAIR_SPARE_PART,
                'value' => WoundType::REPAIR_SPARE_PART,
                'text' => __('accident_informs.wound_type_' . WoundType::REPAIR_SPARE_PART),
            ],
            (object)[
                'id' => WoundType::NO_CHANGE_SPARE_PART,
                'value' => WoundType::NO_CHANGE_SPARE_PART,
                'text' => __('accident_informs.wound_type_' . WoundType::NO_CHANGE_SPARE_PART),
            ],

        ]);
    }

    function getWorksheetList(Request $request)
    {
        $list = AccidentRepairOrder::select('id', 'worksheet_no')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('worksheet_no', 'like', '%' . $request->s . '%');
                }
            })
            ->distinct('id')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->worksheet_no,
                ];
            });
        return response()->json($list);
    }

    function getLicensePlateList(Request $request)
    {
        $list = AccidentRepairOrder::select('id', 'worksheet_no')
            ->leftJoin('accidents', 'accidents.id', '=', 'accident_repair_orders.accident_id')
            ->leftJoin('cradles', 'cradles.id', '=', 'accident_repair_orders.cradle_id')
            ->leftJoin('cars', 'cars.id', '=', 'accidents.car_id')
            ->distinct('cars.id')
            ->select('cars.id', 'cars.license_plate' , 'cars.engine_no' , 'cars.chassis_no')
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
                return [
                    'id' => $item->id,
                    'text' => $text,
                ];
            });
        return response()->json($list);
    }

    function getAccidentWorksheetList(Request $request)
    {
        $list = AccidentRepairOrder::select('id', 'worksheet_no')
            ->leftJoin('accidents', 'accidents.id', '=', 'accident_repair_orders.accident_id')
            // ->leftJoin('cradles', 'cradles.id', '=', 'accident_repair_orders.cradle_id')
            // ->leftJoin('cars', 'cars.id', '=', 'accidents.car_id')
            ->distinct('accidents.id')
            ->select('accidents.id', 'accidents.worksheet_no')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('accidents.worksheet_no', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->worksheet_no,
                ];
            });
        return response()->json($list);
    }
}
