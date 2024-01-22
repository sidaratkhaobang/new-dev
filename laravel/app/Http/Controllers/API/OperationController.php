<?php

namespace App\Http\Controllers\API;

use App\Enums\DrivingJobTypeStatusEnum;
use App\Enums\SelfDriveTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\DrivingJob;
use Illuminate\Http\Request;

class OperationController extends Controller
{
    public function store(Request $request)
    {
        // dd($request->all());
        // 99f0d04b-d798-4bf7-abcf-4b44942d788e
        // 99f0d04b-e161-4efc-8dca-15582754d0eb
        // foreach ($request->id as $index => $data) {
            $driving_job = DrivingJob::find($request->id);
            if ($driving_job) {
                if ($request->self_drive_type != SelfDriveTypeEnum::SEND && $request->driving_job_type == DrivingJobTypeStatusEnum::MAIN_JOB) {
                    $driving_job->actual_end_date = isset($request->actual_end_date) ? $request->actual_end_date : null;
                    $driving_job_id_send_temp_to_pickup = isset($driving_job_id_send_temp) ? $driving_job_id_send_temp : null;
                    $actual_end_date_temp = isset($request->actual_end_date) ? $request->actual_end_date : null;
                }

                if ($request->self_drive_type != SelfDriveTypeEnum::SEND && $request->driving_job_type == DrivingJobTypeStatusEnum::SIDE_JOB) {
                    $driving_job->actual_end_date = isset($request->actual_end_date) ? $request->actual_end_date : null;
                }

                if ($request->self_drive_type != SelfDriveTypeEnum::PICKUP && $request->driving_job_type == DrivingJobTypeStatusEnum::MAIN_JOB) {
                    $driving_job->actual_prepare_date = isset($request->actual_prepare_date) ? $request->actual_prepare_date : null;
                } elseif ($request->self_drive_type == SelfDriveTypeEnum::PICKUP && $request->driving_job_type == DrivingJobTypeStatusEnum::MAIN_JOB) {
                    $driving_job->actual_prepare_date = isset($actual_prepare_date_temp) ? $actual_prepare_date_temp : null;
                }

                if ($request->self_drive_type != SelfDriveTypeEnum::PICKUP && $request->driving_job_type == DrivingJobTypeStatusEnum::SIDE_JOB) {
                    $driving_job->actual_prepare_date = isset($request->actual_prepare_date) ? $request->actual_prepare_date : null;
                }

                // dd($driving_job->actual_prepare_date);
                $driving_job->save();
                return $this->responseWithCode(true, DATA_SUCCESS, $driving_job->id, 200);
            }
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        // }
    }
}
