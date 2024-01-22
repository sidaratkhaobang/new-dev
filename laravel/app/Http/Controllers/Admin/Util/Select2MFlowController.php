<?php

namespace App\Http\Controllers\Admin\Util;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\ExpressWay;
use App\Models\MFlow;
use Illuminate\Http\Request;

class Select2MFlowController extends Controller
{
    function getMFlowWorksheetList(Request $request)
    {
        $list = MFlow::select('id', 'worksheet_no')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('worksheet_no', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->worksheet_no
                ];
            });
        return response()->json($list);
    }

    function getMFlowStationList(Request $request)
    {
        $list = ExpressWay::select('id', 'name')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name', 'like', '%' . $request->s . '%');
                }
            })
            ->where('is_expressway', BOOL_FALSE)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getMFlowCarList(Request $request)
    {
        $list = Car::join('m_flows', 'm_flows.car_id', '=', 'cars.id')
            ->select('cars.id', 'cars.license_plate')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('cars.license_plate', 'like', '%' . $request->s . '%');
                }
            })
            ->groupBy(
                'cars.id',
                'cars.license_plate'
            )
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->license_plate
                ];
            });
        return response()->json($list);
    }
}
