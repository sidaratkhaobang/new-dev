<?php

namespace App\Http\Controllers\Admin\Util;

use App\Http\Controllers\Controller;
use App\Models\InstallEquipment;
use App\Models\Litigation;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Select2LitigationController extends Controller
{
    function getLitigationWorksheetList(Request $request)
    {
        $list = Litigation::select('id', 'worksheet_no')
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

    function getLitigationTitleList(Request $request)
    {
        $list = Litigation::select('id', 'title')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('title', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->title
                ];
            });
        return response()->json($list);
    }

}