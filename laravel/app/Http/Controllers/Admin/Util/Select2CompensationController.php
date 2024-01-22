<?php

namespace App\Http\Controllers\Admin\Util;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Car;
use App\Models\Compensation;
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

class Select2CompensationController extends Controller
{
    function getWorksheetList(Request $request)
    {
        $list = Compensation::select('id', 'worksheet_no')
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

    function getAccidentList(Request $request)
    {
        $list = Accident::join('compensations', 'compensations.accident_id', '=', 'accidents.id')
            ->select('accidents.id', 'accidents.worksheet_no')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('accidents.worksheet_no', 'like', '%' . $request->s . '%');
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
}