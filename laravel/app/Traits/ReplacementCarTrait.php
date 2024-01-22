<?php

namespace App\Traits;

use App\Enums\ReplacementCarStatusEnum;
use App\Enums\ReplacementJobTypeEnum;
use App\Enums\ReplacementTypeEnum;
use App\Enums\TransferTypeEnum;
use App\Models\Car;
use App\Models\CarParkTransfer;
use App\Models\DrivingJob;
use App\Models\InspectionFlow;
use App\Models\InspectionFormSection;
use App\Models\InspectionJob;
use App\Models\InspectionJobChecklist;
use App\Models\InspectionJobStep;
use App\Models\InspectionStep;
use App\Models\ReplacementCar;
use DateTime;
use App\Enums\DrivingJobTypeStatusEnum;
use App\Enums\InspectionTypeEnum;
use App\Enums\SelfDriveTypeEnum;
use App\Enums\GPSStatusEnum;
use App\Models\GpsCheckSignal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Factories\DrivingJobFactory;
use App\Factories\CarparkTransferFactory;

trait ReplacementCarTrait
{
    public static function getReplacementTypeList()
    {
        $replacementtype_arr = [
            ReplacementTypeEnum::SEND_REPLACE_RECEIVE_MAIN,
            ReplacementTypeEnum::SEND_MAIN_RECEIVE_REPLACE,
            ReplacementTypeEnum::RECEIVE_MAIN,
            ReplacementTypeEnum::SEND_MAIN,
            ReplacementTypeEnum::SEND_REPLACE,
            ReplacementTypeEnum::RECEIVE_REPLACE,
            // ReplacementTypeEnum::CHANGE_REPLACE,
        ];
        $replacement_type_list = collect([]);
        foreach ($replacementtype_arr as $replacementtype) {
            $replacementtype_obj = (object) [
                'id' => $replacementtype,
                'value' => $replacementtype,
                'name' => __('replacement_cars.type_' . $replacementtype),
            ];
            $replacement_type_list[] = $replacementtype_obj;
        }
        return $replacement_type_list;
    }

    public static function getReplacementJobTypeList()
    {
        return collect([
            (object) [
                'id' => ReplacementJobTypeEnum::ACCIDENT,
                'value' => ReplacementJobTypeEnum::ACCIDENT,
                'name' => __('replacement_cars.job_type_' . ReplacementJobTypeEnum::ACCIDENT),
            ],
            (object) [
                'id' => ReplacementJobTypeEnum::REPAIR,
                'value' => ReplacementJobTypeEnum::REPAIR,
                'name' => __('replacement_cars.job_type_' . ReplacementJobTypeEnum::REPAIR),
            ],
        ]);
    }

    public static function getReplacementUpdateStatusList()
    {
        return collect([
            (object) [
                'id' => ReplacementCarStatusEnum::IN_PROCESS,
                'value' => ReplacementCarStatusEnum::IN_PROCESS,
                'name' => __('replacement_cars.status_' . ReplacementCarStatusEnum::IN_PROCESS),
            ],
            (object) [
                'id' => ReplacementCarStatusEnum::COMPLETE,
                'value' => ReplacementCarStatusEnum::COMPLETE,
                'name' => __('replacement_cars.status_' . ReplacementCarStatusEnum::COMPLETE),
            ],
        ]);
    }

    public static function getIsNeedDriverList()
    {
        return collect([
            [
                'id' => 1,
                'value' => 1,
                'name' => __('lang.wanted'),
            ],
            [
                'id' => 0,
                'value' => 0,
                'name' => __('lang.unwanted'),
            ],
        ]);
    }

    public static function getIsNeedSlideList()
    {
        return collect([
            [
                'id' => 1,
                'value' => 1,
                'name' => __('lang.wanted'),
            ],
            [
                'id' => 0,
                'value' => 0,
                'name' => __('lang.unwanted'),
            ],
        ]);
    }

    public static function getCarInfo($car_id)
    {
        $car = Car::find($car_id);
        if (!$car) {
            return null;
        }
        $car->name = $car->license_plate;
        $car->class_name = $car->carClass?->full_name;
        $car_images_files = $car->getMedia('car_images');
        $car_images_files = get_medias_detail($car_images_files);
        $car->image = $car_images_files[0] ?? null;
        $car->plicy_number = 'TEST/00840734';
        $car->policy_start_date = '10/1/2022 16:30';
        $car->policy_end_date = '14/1/2022 16:30';
        $car->insurance_no = 'TEST-58108';
        $car->insurance_company = 'test จำกัด';
        $car->insurance_start_date = '14/1/2022 16:30';
        $car->insurance_start_date = '14/1/2025 16:30';
        return $car;
    }

    public static function createDrivingJobByReplacementType($replacement_car)
    {
        if (strcmp($replacement_car->replacement_type, ReplacementTypeEnum::SEND_REPLACE_RECEIVE_MAIN) === 0) {
            ReplacementCarTrait::createDrivingJob($replacement_car->id, SelfDriveTypeEnum::SEND, $replacement_car->replacement_car_id, '', $replacement_car->replacement_place);
            ReplacementCarTrait::createDrivingJob($replacement_car->id, SelfDriveTypeEnum::PICKUP, $replacement_car->main_car_id, $replacement_car->replacement_place, '');
        }

        if (strcmp($replacement_car->replacement_type, ReplacementTypeEnum::SEND_MAIN_RECEIVE_REPLACE) === 0) {
            ReplacementCarTrait::createDrivingJob($replacement_car->id, SelfDriveTypeEnum::SEND, $replacement_car->main_car_id, '', $replacement_car->replacement_place);
            ReplacementCarTrait::createDrivingJob($replacement_car->id, SelfDriveTypeEnum::PICKUP, $replacement_car->replacement_car_id, $replacement_car->replacement_place, '');
        }

        if (strcmp($replacement_car->replacement_type, ReplacementTypeEnum::RECEIVE_MAIN) === 0) {
            ReplacementCarTrait::createDrivingJob($replacement_car->id, SelfDriveTypeEnum::PICKUP, $replacement_car->main_car_id, $replacement_car->replacement_place, '');
        }

        if (strcmp($replacement_car->replacement_type, ReplacementTypeEnum::SEND_MAIN) === 0) {
            ReplacementCarTrait::createDrivingJob($replacement_car->id, SelfDriveTypeEnum::SEND, $replacement_car->main_car_id, '', $replacement_car->replacement_place);
        }

        if (strcmp($replacement_car->replacement_type, ReplacementTypeEnum::SEND_REPLACE) === 0) {
            ReplacementCarTrait::createDrivingJob($replacement_car->id, SelfDriveTypeEnum::SEND, $replacement_car->replacement_car_id, '', $replacement_car->replacement_place);
        }

        if (strcmp($replacement_car->replacement_type, ReplacementTypeEnum::RECEIVE_REPLACE) === 0) {
            ReplacementCarTrait::createDrivingJob($replacement_car->id, SelfDriveTypeEnum::PICKUP, $replacement_car->replacement_car_id, $replacement_car->replacement_place, '');
        }
    }

    public static function createInspectionJobByReplacementType($replacement_car)
    {
        if (strcmp($replacement_car->replacement_type, ReplacementTypeEnum::SEND_REPLACE_RECEIVE_MAIN) === 0) {
            ReplacementCarTrait::createInspectionJobs($replacement_car, InspectionTypeEnum::REPLACEMENT, TransferTypeEnum::OUT, $replacement_car->replacement_car_id);
            $inspection_type = (strcmp($replacement_car->job_type, ReplacementJobTypeEnum::ACCIDENT) === 0) ? InspectionTypeEnum::ACCIDENT_RC : InspectionTypeEnum::MAINTENANCE_RC;
            ReplacementCarTrait::createInspectionJobs($replacement_car, $inspection_type, TransferTypeEnum::IN, $replacement_car->main_car_id);
        }

        if (strcmp($replacement_car->replacement_type, ReplacementTypeEnum::SEND_MAIN_RECEIVE_REPLACE) === 0) {
            $inspection_type = (strcmp($replacement_car->job_type, ReplacementJobTypeEnum::ACCIDENT) === 0) ? InspectionTypeEnum::ACCIDENT_DC : InspectionTypeEnum::MAINTENANCE_DC;
            ReplacementCarTrait::createInspectionJobs($replacement_car, $inspection_type, TransferTypeEnum::OUT, $replacement_car->main_car_id);
            ReplacementCarTrait::createInspectionJobs($replacement_car, InspectionTypeEnum::REPLACEMENT, TransferTypeEnum::IN, $replacement_car->replacement_car_id);
        }

        if (strcmp($replacement_car->replacement_type, ReplacementTypeEnum::RECEIVE_MAIN) === 0) {
            $inspection_type = (strcmp($replacement_car->job_type, ReplacementJobTypeEnum::ACCIDENT) === 0) ? InspectionTypeEnum::ACCIDENT_RC : InspectionTypeEnum::MAINTENANCE_RC;
            ReplacementCarTrait::createInspectionJobs($replacement_car, $inspection_type, TransferTypeEnum::IN, $replacement_car->main_car_id);
        }

        if (strcmp($replacement_car->replacement_type, ReplacementTypeEnum::SEND_MAIN) === 0) {
            $inspection_type = (strcmp($replacement_car->job_type, ReplacementJobTypeEnum::ACCIDENT) === 0) ? InspectionTypeEnum::ACCIDENT_DC : InspectionTypeEnum::MAINTENANCE_DC;
            ReplacementCarTrait::createInspectionJobs($replacement_car, $inspection_type, TransferTypeEnum::OUT, $replacement_car->main_car_id);
        }

        if (strcmp($replacement_car->replacement_type, ReplacementTypeEnum::SEND_REPLACE) === 0) {
            ReplacementCarTrait::createInspectionJobs($replacement_car, InspectionTypeEnum::REPLACEMENT, TransferTypeEnum::OUT, $replacement_car->replacement_car_id);
        }

        if (strcmp($replacement_car->replacement_type, ReplacementTypeEnum::RECEIVE_REPLACE) === 0) {
            ReplacementCarTrait::createInspectionJobs($replacement_car, InspectionTypeEnum::REPLACEMENT, TransferTypeEnum::IN, $replacement_car->replacement_car_id);
        }
    }

    public static function createDrivingJob($replacement_car_id, $self_drive_type, $car_id, $origin, $destination)
    {
        $djf = new DrivingJobFactory(ReplacementCar::class, $replacement_car_id, $car_id, [
            'self_drive_type' => $self_drive_type,
            'destination' => $destination,
            'origin' => $origin
        ]);
        $driving_job = $djf->create();

        $ctf = new CarparkTransferFactory($driving_job->id, $car_id);
        $ctf->create();
    }

    public static function createInspectionJobs($replacement_car, $inspection_type, $transfer_type, $car_id)
    {
        if (!$replacement_car) {
            return false;
        }
        $date = new DateTime();
        $open_date = $date->format('Y-m-d H:i:s');
        $inspection_flow = InspectionFlow::where('inspection_type', $inspection_type)->first();
        $step_form = InspectionFlow::leftjoin('inspection_steps', 'inspection_steps.inspection_flow_id', '=', 'inspection_flows.id')
            ->select('inspection_steps.transfer_type', 'inspection_flows.id')
            ->groupBy('inspection_steps.transfer_type', 'inspection_flows.id')
            ->orderBy('inspection_steps.transfer_type', 'DESC')
            ->where('inspection_flows.inspection_type', $inspection_type)
            ->where('inspection_steps.transfer_type', $transfer_type)
            ->get();
        foreach ($step_form as $step_form_new) {
            if ($step_form_new->transfer_type) {
                $inspection_count = DB::table('inspection_jobs')->count() + 1;
                $prefix = 'QA';
                $inspection_step = InspectionStep::where('inspection_flow_id', $step_form_new->id)
                    ->where('transfer_type', $step_form_new->transfer_type)
                    ->select('transfer_reason')
                    ->first();

                $inspection_job = new InspectionJob();
                $inspection_job->worksheet_no = generateRecordNumber($prefix, $inspection_count);
                $inspection_job->item_type = ReplacementCar::class;
                $inspection_job->item_id = $replacement_car->id;
                $inspection_job->open_date = $open_date;
                $inspection_job->transfer_type = $step_form_new->transfer_type;
                $inspection_job->inspection_flow_id = $inspection_flow->id;
                $inspection_job->inspection_type = $inspection_flow->inspection_type;
                $inspection_job->transfer_reason = $inspection_step->transfer_reason; // transfer_reason first
                $inspection_job->is_need_customer_sign_in = $inspection_flow->is_need_customer_sign_in;
                $inspection_job->is_need_customer_sign_out = $inspection_flow->is_need_customer_sign_out;
                $inspection_job->car_id = $car_id;
                $inspection_job->inspection_must_date = $replacement_car->replacement_date;
                $inspection_job->save();

                $step_form_detail = InspectionFlow::leftjoin('inspection_steps', 'inspection_steps.inspection_flow_id', '=', 'inspection_flows.id')
                    ->leftjoin('inspection_forms', 'inspection_forms.id', '=', 'inspection_steps.inspection_form_id')
                    ->leftjoin('inspection_jobs', 'inspection_jobs.inspection_flow_id', '=', 'inspection_flows.id')
                    ->where('inspection_jobs.id', $inspection_job->id)
                    ->where('inspection_steps.transfer_type', $step_form_new->transfer_type)
                    ->select(
                        'inspection_steps.id as inspection_step_id',
                        'inspection_steps.inspection_form_id',
                        'inspection_steps.transfer_type',
                        'inspection_steps.transfer_reason',
                        'inspection_steps.inspection_department_id',
                        'inspection_forms.form_type',
                        'inspection_steps.is_need_images',
                        'inspection_steps.is_need_inspector_sign',
                        'inspection_steps.is_need_send_mobile',
                        'inspection_steps.inspection_role_id',
                    )
                    ->get();
                foreach ($step_form_detail as $step_form_data) {
                    $inspection_job_step = new InspectionJobStep();
                    $inspection_job_step->inspection_job_id = $inspection_job->id;
                    $inspection_job_step->inspection_step_id = $step_form_data->inspection_step_id;
                    $inspection_job_step->inspection_form_id = $step_form_data->inspection_form_id;
                    $inspection_job_step->transfer_type = $step_form_data->transfer_type;
                    $inspection_job_step->transfer_reason = $step_form_data->transfer_reason;
                    $inspection_job_step->inspection_department_id = $step_form_data->inspection_department_id;
                    $inspection_job_step->is_need_images = $step_form_data->is_need_images;
                    $inspection_job_step->is_need_inspector_sign = $step_form_data->is_need_inspector_sign;
                    $inspection_job_step->is_need_send_mobile = $step_form_data->is_need_send_mobile;
                    $inspection_job_step->inspection_role_id = $step_form_data->inspection_role_id;
                    $inspection_job_step->inspector_type = $step_form_data->is_need_send_mobile == STATUS_ACTIVE ? Driver::class : User::class;
                    $inspection_job_step->inspector_id = '';
                    $inspection_job_step->save();

                    $inspection_section_data = InspectionFormSection::leftjoin('inspection_form_checklists', 'inspection_form_checklists.inspection_form_section_id', '=', 'inspection_form_sections.id')
                        ->where('inspection_form_sections.inspection_form_id', $inspection_job_step->inspection_form_id)
                        ->select(
                            'inspection_form_sections.*',
                            'inspection_form_checklists.id as checklist_id',
                            'inspection_form_checklists.name as checklist_name',
                            'inspection_form_checklists.car_part as checklist_car_part',
                        )
                        ->get();
                    foreach ($inspection_section_data as $inspection_section_key => $inspection_section_data) {
                        $inspection_job_checklist = new InspectionJobChecklist();
                        $inspection_job_checklist->inspection_job_step_id = $inspection_job_step->id;
                        $inspection_job_checklist->inspection_form_section_id = $inspection_section_data->id;
                        $inspection_job_checklist->inspection_form_section_name = $inspection_section_data->name;
                        $inspection_job_checklist->inspection_form_checklist_id = $inspection_section_data->checklist_id;
                        $inspection_job_checklist->inspection_form_checklist_name = $inspection_section_data->checklist_name;
                        $inspection_job_checklist->save();
                    }
                }
            }
        }
    }

    public static function createGPSCheckSignalByReplacementType($replacement_car)
    {
        if (in_array($replacement_car->replacement_type, [ReplacementTypeEnum::SEND_MAIN_RECEIVE_REPLACE, ReplacementTypeEnum::SEND_MAIN])) {
            ReplacementCarTrait::createGPSCheckSignal($replacement_car->id, $replacement_car->main_car_id, $replacement_car->replacement_date);
        }

        if (in_array($replacement_car->replacement_type, [ReplacementTypeEnum::SEND_REPLACE_RECEIVE_MAIN, ReplacementTypeEnum::SEND_REPLACE])) {
            ReplacementCarTrait::createGPSCheckSignal($replacement_car->id, $replacement_car->replacement_car_id, $replacement_car->replacement_date);
        }
    }

    public static function createGPSCheckSignal($replacement_car_id, $car_id, $must_check_date)
    {
        $user = Auth::user();
        $must_check_date_new = Carbon::parse($must_check_date)->subDays(1);
        $gps_check_signal = new GpsCheckSignal();
        $gps_check_signal->car_id = $car_id;
        $gps_check_signal->job_type = ReplacementCar::class;
        $gps_check_signal->job_id = $replacement_car_id;
        $gps_check_signal->must_check_date = $must_check_date_new;
        $gps_check_signal->branch_id = $user ? $user->branch_id : null;
        $gps_check_signal->status = GPSStatusEnum::PENDING;
        $gps_check_signal->save();
    }
}
