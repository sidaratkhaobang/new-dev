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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Select2DriverController extends Controller
{
    public function getDefaultJobByID(Request $request)
    {
        $job_type = $request->parent_id;
        $data = [];
        if (strcmp($job_type, Rental::class) == 0) {
            $data = Rental::select('id', 'worksheet_no')
                ->orderBy('worksheet_no')->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'text' => $item->worksheet_no
                    ];
                });
        }
        if (strcmp($job_type, LongTermRental::class) == 0) {
            $data = LongTermRental::select('id', 'worksheet_no')
                ->orderBy('worksheet_no')->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'text' => $item->worksheet_no
                    ];
                });
        }
        if (strcmp($job_type, ImportCarLine::class) == 0) {
            
            $data = ImportCar::select('import_cars.id', 'purchase_orders.po_no as po_no')
                ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'import_cars.po_id')
                ->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'text' => $item->po_no
                    ];
                });
        }
        if (strcmp($job_type, InstallEquipment::class) == 0) {
            $data = InstallEquipment::select('id', 'worksheet_no')
            // ->orderBy('created_at', 'desc')
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
        }
        return response()->json($data);
    }

    public function getDriverWageNotMonth(Request $request)
    {
        $data = DriverWage::select('id', 'name')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name', 'like', '%' . $request->s . '%');
                }
            })
            ->whereNotIn('wage_cal_type', [WageCalType::PER_MONTH])->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($data);
    }

    function getParentDrivinfJob(Request $request)
    {
        $list = DrivingJob::select('id', 'worksheet_no')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('worksheet_no', 'like', '%' . $request->s . '%');
                }
            })
            ->orderBy('worksheet_no')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->worksheet_no
                ];
            });
        return response()->json($list);
    }

    public function getDrivinfJobStatusList(Request $request)
    {
        $list = collect([
            [
                'id' => DrivingJobStatusEnum::COMPLETE,
                'value' => DrivingJobStatusEnum::COMPLETE,
                'text' => __('driving_jobs.status_' . DrivingJobStatusEnum::COMPLETE . '_text'),
            ],
            [
                'id' => DrivingJobStatusEnum::CANCEL,
                'value' => DrivingJobStatusEnum::CANCEL,
                'text' => __('driving_jobs.status_' . DrivingJobStatusEnum::CANCEL . '_text'),
            ],
        ]);
        return $list;
    }
}
