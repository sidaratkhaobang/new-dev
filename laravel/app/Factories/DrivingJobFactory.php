<?php

namespace App\Factories;

use App\Enums\DrivingJobTypeStatusEnum;
use App\Enums\SelfDriveTypeEnum;
use App\Models\DrivingJob;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Enums\DrivingJobStatusEnum;

class DrivingJobFactory implements FactoryInterface
{
    public $job_type;
    public $job_id;
    public $car_id;
    public $worksheet_no;
    public $driving_job_type;
    public $self_drive_type;
    public $start_date;
    public $end_date;
    public $origin;
    public $destination;
    public $driver_id;
    public $driver_name;
    public $remark;
    public $pick_up_keys;
    public $branch_id;
    public $status;

    public function __construct($job_type, $job_id, $car_id, $optionals = [])
    {
        $this->worksheet_no = null;
        $this->job_type = $job_type;
        $this->job_id = $job_id;
        $this->car_id = $car_id;
        $this->driving_job_type = isset($optionals['driving_job_type']) ? $optionals['driving_job_type'] : DrivingJobTypeStatusEnum::MAIN_JOB;
        $this->self_drive_type = isset($optionals['self_drive_type']) ? $optionals['self_drive_type'] : SelfDriveTypeEnum::SEND;
        $this->start_date = isset($optionals['start_date']) ? $optionals['start_date'] : null;
        $this->end_date = isset($optionals['end_date']) ? $optionals['end_date'] : null;
        $this->origin = isset($optionals['origin']) ? $optionals['origin'] : null;
        $this->destination = isset($optionals['destination']) ? $optionals['destination'] : null;
        $this->driver_id = isset($optionals['driver_id']) ? $optionals['driver_id'] : null;
        $this->driver_name = isset($optionals['driver_name']) ? $optionals['driver_name'] : null;
        $this->remark = isset($optionals['remark']) ? $optionals['remark'] : null;
        $this->pick_up_keys = isset($optionals['pick_up_keys']) ? $optionals['pick_up_keys'] : null;
        $this->branch_id = isset($optionals['branch_id']) ? $optionals['branch_id'] : get_branch_id();
        $this->status = empty($this->driver_id) ? DrivingJobStatusEnum::INITIAL : DrivingJobStatusEnum::PENDING;
    }

    function generateWorkSheetNo()
    {
        $this->worksheet_no = generate_worksheet_no(DrivingJob::class);
    }

    function create()
    {
        $this->generateWorkSheetNo();
        $this->validate();

        $driving_job = new DrivingJob();
        $driving_job->worksheet_no = $this->worksheet_no;
        $driving_job->branch_id = $this->branch_id;
        $driving_job->driving_job_type = $this->driving_job_type;
        $driving_job->job_type = $this->job_type;
        $driving_job->job_id = $this->job_id;
        $driving_job->self_drive_type = $this->self_drive_type;
        $driving_job->car_id = $this->car_id;
        $driving_job->start_date = $this->start_date;
        $driving_job->end_date = $this->end_date;
        $driving_job->driver_id = $this->driver_id;
        $driving_job->driver_name = $this->driver_name;
        $driving_job->origin = $this->origin;
        $driving_job->destination = $this->destination;
        $driving_job->remark = $this->remark;
        $driving_job->status = $this->status;
        $driving_job->save();
        return $driving_job;
    }

    function validate()
    {
        if (empty($this->worksheet_no)) {
            throw new Exception('empty worksheet_no');
        }
    }
}
