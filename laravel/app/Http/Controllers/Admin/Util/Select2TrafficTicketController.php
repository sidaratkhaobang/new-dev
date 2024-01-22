<?php

namespace App\Http\Controllers\Admin\Util;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\InstallEquipment;
use App\Models\Litigation;
use App\Models\PoliceStation;
use App\Models\Rental;
use App\Models\LongTermRental;
use App\Models\DriverWage;
use App\Models\DrivingJob;
use App\Models\ImportCar;
use App\Models\ImportCarLine;
use App\Enums\WageCalType;
use App\Enums\DrivingJobStatusEnum;
use App\Models\Amphure;
use App\Models\Cradle;
use App\Models\District;
use App\Models\Province;
use App\Models\TrafficTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Select2TrafficTicketController extends Controller
{
    function getWorksheetList(Request $request)
    {
        $list = TrafficTicket::select('id', 'traffic_ticket_no')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('traffic_ticket_no', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->traffic_ticket_no
                ];
            });
        return response()->json($list);
    }

    function getTrafficTicketCarList(Request $request)
    {
        $list = Car::join('traffic_tickets', 'traffic_tickets.car_id', '=', 'cars.id')
            ->select('cars.id', 'cars.license_plate')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('cars.license_plate', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->license_plate
                ];
            });
        return response()->json($list);
    }

    function getPoliceStationList(Request $request)
    {
        $list = PoliceStation::select('id', 'code', 'name')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('code', 'like', '%' . $request->s . '%');
                    $query->orWhere('name', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => ($item->code ?? "-") . " / " . ($item->name ?? "-")
                ];
            });
        return response()->json($list);
    }

}