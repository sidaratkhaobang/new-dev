<?php

namespace App\Http\Controllers\API\CarPark\V1;

use App\Http\Controllers\Controller;
use App\Models\DrivingJob;
use Illuminate\Http\Request;

class DrivingJobController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;
        $list = DrivingJob::select([
            'driving_jobs.id',
            'driving_jobs.worksheet_no',
            'driving_jobs.start_date',
            'driving_jobs.end_date',
            'driving_jobs.actual_start_date',
            'driving_jobs.actual_end_date',
            'driving_jobs.origin',
            'driving_jobs.destination',
            'driving_jobs.driver_id',
            'driving_jobs.driver_name',
            'driving_jobs.car_id',
            'cars.license_plate',
        ])
            ->leftjoin('cars', 'cars.id', '=', 'driving_jobs.car_id')
            ->when(!empty($s), function ($query) use ($s) {
                $query->where('driving_jobs.worksheet_no', 'like', '%' . $s . '%');
                $query->orWhere('cars.license_plate', 'like', '%' . $s . '%');
            })
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read($id)
    {
        $data = DrivingJob::select([
            'driving_jobs.id',
            'driving_jobs.worksheet_no',
            'driving_jobs.start_date',
            'driving_jobs.end_date',
            'driving_jobs.actual_start_date',
            'driving_jobs.actual_end_date',

            'driving_jobs.estimate_prepare_date',
            'driving_jobs.estimate_start_date',
            'driving_jobs.estimate_end_job_date',
            'driving_jobs.estimate_arrive_date',
            'driving_jobs.estimate_end_date',
            'driving_jobs.estimate_rented_date',

            'driving_jobs.actual_prepare_date',
            'driving_jobs.actual_end_job_date',
            'driving_jobs.actual_arrive_date',
            'driving_jobs.actual_rented_date',

            'driving_jobs.origin',
            'driving_jobs.destination',
            'driving_jobs.driver_id',
            'driving_jobs.driver_name',
            'driving_jobs.atk_check',
            'driving_jobs.alcohol_check',
            'driving_jobs.alcohol',
            'driving_jobs.remark',
            'driving_jobs.driving_job_type',
            'driving_jobs.self_drive_type',
            'driving_jobs.status',
            'driving_jobs.parent_id',
            'driving_jobs.car_id',
            'cars.license_plate',
        ])
            ->leftjoin('cars', 'cars.id', '=', 'driving_jobs.car_id')
            ->where('driving_jobs.id', $id)
            ->first();
        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }
}
