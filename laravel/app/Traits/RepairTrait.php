<?php

namespace App\Traits;

use App\Enums\AccidentRepairStatusEnum;
use App\Models\Slide;
use App\Models\User;
use Illuminate\Http\Request;
use App\Enums\RepairEnum;
use App\Enums\RepairTypeEnum;
use App\Enums\CheckDistanceTypeEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\RentalStatusEnum;
use App\Enums\LongTermRentalStatusEnum;
use App\Enums\ReplacementJobTypeEnum;
use App\Enums\CreditorTypeEnum;
use App\Enums\DrivingJobTypeStatusEnum;
use App\Enums\InspectionTypeEnum;
use App\Enums\RepairStatusEnum;
use App\Enums\ReplacementTypeEnum;
use App\Enums\SelfDriveTypeEnum;
use App\Models\Car;
use App\Models\CarParkTransfer;
use App\Models\Rental;
use App\Models\Contracts;
use App\Models\Creditor;
use App\Models\CreditorType;
use App\Models\DrivingJob;
use App\Models\InspectionFlow;
use App\Models\InspectionFormSection;
use App\Models\InspectionJob;
use App\Models\InspectionJobChecklist;
use App\Models\InspectionJobStep;
use App\Models\InspectionStep;
use App\Models\LongTermRentalLine;
use App\Models\LongTermRental;
use App\Models\RentalLine;
use App\Models\ReplacementCar;
use App\Models\Quotation;
use App\Models\QuotationForm;
use App\Models\QuotationFormChecklist;
use App\Models\RepairList;
use App\Models\RepairOrder;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Factories\DrivingJobFactory;
use App\Factories\CarparkTransferFactory;

trait RepairTrait
{
    public static function getStatus()
    {
        return collect([
            (object) [
                'id' => RepairStatusEnum::WAIT_OPEN_REPAIR_ORDER,
                'name' => __('repairs.repair_text_' . RepairStatusEnum::WAIT_OPEN_REPAIR_ORDER),
                'value' => RepairStatusEnum::WAIT_OPEN_REPAIR_ORDER,
            ],
            (object) [
                'id' => RepairStatusEnum::PENDING_REPAIR,
                'name' => __('repairs.repair_text_' . RepairStatusEnum::PENDING_REPAIR),
                'value' => RepairStatusEnum::PENDING_REPAIR,
            ],
            (object) [
                'id' => RepairStatusEnum::WAIT_APPROVE_QUOTATION,
                'name' => __('repairs.repair_text_' . RepairStatusEnum::WAIT_APPROVE_QUOTATION),
                'value' => RepairStatusEnum::WAIT_APPROVE_QUOTATION,
            ],
            (object) [
                'id' => RepairStatusEnum::REJECT_QUOTATION,
                'name' => __('repairs.repair_text_' . RepairStatusEnum::REJECT_QUOTATION),
                'value' => RepairStatusEnum::REJECT_QUOTATION,
            ],
            (object) [
                'id' => RepairStatusEnum::IN_PROCESS,
                'name' => __('repairs.repair_text_' . RepairStatusEnum::IN_PROCESS),
                'value' => RepairStatusEnum::IN_PROCESS,
            ],
            (object) [
                'id' => RepairStatusEnum::COMPLETED,
                'name' => __('repairs.repair_text_' . RepairStatusEnum::COMPLETED),
                'value' => RepairStatusEnum::COMPLETED,
            ],
            (object) [
                'id' => RepairStatusEnum::CANCEL,
                'name' => __('repairs.repair_text_' . RepairStatusEnum::CANCEL),
                'value' => RepairStatusEnum::CANCEL,
            ],
            (object) [
                'id' => RepairStatusEnum::EXPIRED,
                'name' => __('repairs.repair_text_' . RepairStatusEnum::EXPIRED),
                'value' => RepairStatusEnum::EXPIRED,
            ],
        ]);
    }

    static function getRepairType()
    {
        return collect([
            (object) [
                'id' => RepairTypeEnum::CHECK_DISTANCE,
                'name' => __('repairs.repair_type_' . RepairTypeEnum::CHECK_DISTANCE),
                'value' => RepairTypeEnum::CHECK_DISTANCE,
            ],
            (object) [
                'id' => RepairTypeEnum::GENERAL_REPAIR,
                'name' => __('repairs.repair_type_' . RepairTypeEnum::GENERAL_REPAIR),
                'value' => RepairTypeEnum::GENERAL_REPAIR,
            ],
            (object) [
                'id' => RepairTypeEnum::CHECK_AND_REPAIR,
                'name' => __('repairs.repair_type_' . RepairTypeEnum::CHECK_AND_REPAIR),
                'value' => RepairTypeEnum::CHECK_AND_REPAIR,
            ],
        ]);
    }

    static function getInformer()
    {
        return collect([
            (object) [
                'id' => RepairEnum::CUSTOMER,
                'name' => __('repairs.informer_' . RepairEnum::CUSTOMER),
                'value' => RepairEnum::CUSTOMER,
            ],
            (object) [
                'id' => RepairEnum::TLS,
                'name' => __('repairs.informer_' . RepairEnum::TLS),
                'value' => RepairEnum::TLS,
            ],
        ]);
    }

    static function getServiceCenter()
    {
        return collect([
            [
                'id' => 1,
                'value' => 1,
                'name' => __('repairs.is_customer'),
            ],
            [
                'id' => 0,
                'value' => 0,
                'name' => __('repairs.is_tls'),
            ],
        ]);
    }

    static function getIsNeedDriver()
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

    static function getCheckList()
    {
        return collect([
            (object) [
                'id' => CheckDistanceTypeEnum::REPAIR,
                'name' => __('check_distances.type_text_' . CheckDistanceTypeEnum::REPAIR),
                'value' => CheckDistanceTypeEnum::REPAIR,
            ],
            (object) [
                'id' => CheckDistanceTypeEnum::CHANGE,
                'name' => __('check_distances.type_text_' . CheckDistanceTypeEnum::CHANGE),
                'value' => CheckDistanceTypeEnum::CHANGE,
            ],
            (object) [
                'id' => CheckDistanceTypeEnum::SERVICE_CHARGE,
                'name' => __('check_distances.type_text_' . CheckDistanceTypeEnum::SERVICE_CHARGE),
                'value' => CheckDistanceTypeEnum::SERVICE_CHARGE,
            ],
            (object) [
                'id' => CheckDistanceTypeEnum::CHECK,
                'name' => __('check_distances.type_text_' . CheckDistanceTypeEnum::CHECK),
                'value' => CheckDistanceTypeEnum::CHECK,
            ],
            (object) [
                'id' => CheckDistanceTypeEnum::ADJUST,
                'name' => __('check_distances.type_text_' . CheckDistanceTypeEnum::ADJUST),
                'value' => CheckDistanceTypeEnum::ADJUST,
            ],
            (object) [
                'id' => CheckDistanceTypeEnum::CLEAN,
                'name' => __('check_distances.type_text_' . CheckDistanceTypeEnum::CLEAN),
                'value' => CheckDistanceTypeEnum::CLEAN,
            ],
            (object) [
                'id' => CheckDistanceTypeEnum::MODIFY,
                'name' => __('check_distances.type_text_' . CheckDistanceTypeEnum::MODIFY),
                'value' => CheckDistanceTypeEnum::MODIFY,
            ],
            (object) [
                'id' => CheckDistanceTypeEnum::PUTTER_OUT,
                'name' => __('check_distances.type_text_' . CheckDistanceTypeEnum::PUTTER_OUT),
                'value' => CheckDistanceTypeEnum::PUTTER_OUT,
            ],
            (object) [
                'id' => CheckDistanceTypeEnum::RECAP,
                'name' => __('check_distances.type_text_' . CheckDistanceTypeEnum::RECAP),
                'value' => CheckDistanceTypeEnum::RECAP,
            ],
            (object) [
                'id' => CheckDistanceTypeEnum::FREE_SERVICE,
                'name' => __('check_distances.type_text_' . CheckDistanceTypeEnum::FREE_SERVICE),
                'value' => CheckDistanceTypeEnum::FREE_SERVICE,
            ],
            (object) [
                'id' => CheckDistanceTypeEnum::FREE_WAGE,
                'name' => __('check_distances.type_text_' . CheckDistanceTypeEnum::FREE_WAGE),
                'value' => CheckDistanceTypeEnum::FREE_WAGE,
            ],
        ]);
    }

    public static function getHaveExpensesList()
    {
        return [
            [
                'id' => 1,
                'value' => 1,
                'name' => __('lang.have'),
            ],
            [
                'id' => 0,
                'value' => 0,
                'name' => __('lang.no_have'),
            ],
        ];
    }

    public static function getDistrict()
    {
        $data = [];
        $data = DB::table('geographies')->select('id', 'name')
            ->whereNotIn('id', ['7'])
            ->orderBy('id')->get()->map(function ($item) {
                $item->id = $item->id;
                $item->name = $item->name;
                return $item;
            });
        return $data;
    }

    public static function getRepairListId()
    {
        $data = [];
        $data = RepairList::select('id', 'code', 'name', 'price')
            ->where('status', STATUS_ACTIVE)
            ->limit(30)
            ->get()->map(function ($item) {
                $item->id = $item->id;
                $item->name = $item->code . ' - ' . $item->name;
                return $item;
            });
        return $data;
    }

    public static function getReplacementTypeList()
    {
        $replacementtype_arr = [
            ReplacementTypeEnum::SEND_REPLACE_RECEIVE_MAIN,
            ReplacementTypeEnum::SEND_REPLACE,
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

    public static function getCenterList()
    {
        $data = [];
        $creditor_type_id = CreditorType::where('type', CreditorTypeEnum::SERVICE)->pluck('id')->first();
        $data = Creditor::leftJoin('creditors_types_relation', 'creditors_types_relation.creditor_id', '=', 'creditors.id')
            ->where('creditors_types_relation.creditor_type_id', $creditor_type_id)
            ->select('creditors.id', 'creditors.name')
            ->where('status', STATUS_ACTIVE)
            ->get()->map(function ($item) {
                $item->id = $item->id;
                $item->name = $item->name;
                return $item;
            });
        return $data;
    }

    static function getDataCar($car_id = null)
    {
        $car_id = $car_id;
        // $data = ;
        $car = Car::find($car_id);
        if ($car) {
            $car->car_class_name = ($car && $car->carClass) ? $car->carClass->full_name : null;
            $car->chassis_no = ($car) ? $car->chassis_no : null;
            $car->license_plate = ($car) ? $car->license_plate : null;
            $car->current_mileage = ($car) ? $car->current_mileage : null;
            $car->car_status = ($car) ? $car->status : null;
            $car->rental = 0;
            $car->contract = 0;
            $rental = null;
            $rental_type = null;
            if ($car) {
                if ($car->license_plate) {
                    $car->car_license = $car->license_plate;
                } else if ($car->engine_no) {
                    $car->car_license = __('inspection_cars.engine_no') . ' ' . $car->engine_no;
                } else if ($car->chassis_no) {
                    $car->car_license = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
                }
            }

            if (strcmp($car->rental_type, RentalTypeEnum::SHORT) == 0) {
                $rental = RentalLine::leftJoin('rentals', 'rentals.id', '=', 'rental_lines.rental_id')
                    ->where('rental_lines.car_id', $car->id)
                    ->where('rentals.status', RentalStatusEnum::PAID)
                    ->select('rentals.id', 'rentals.worksheet_no', 'rentals.customer_name')
                    ->first();
                if ($rental) {
                    $rental_type = Rental::class;
                    $contract = Contracts::leftJoin('contract_lines', 'contract_lines.contract_id', '=', 'contracts.id')
                        ->where('contracts.job_type', Rental::class)
                        ->where('contracts.job_id', $rental->id)
                        ->where('contract_lines.car_id', $car->id)
                        ->select('contracts.id', 'contracts.worksheet_no', 'contract_lines.pick_up_date', 'contract_lines.return_date')
                        ->first();
                }
            } else if (strcmp($car->rental_type, RentalTypeEnum::LONG) == 0) {
                $rental = LongTermRentalLine::leftJoin('lt_rentals', 'lt_rentals.id', '=', 'lt_rental_lines.lt_rental_id')
                    ->where('lt_rental_lines.car_class_id', $car->car_class_id)
                    ->where('lt_rentals.status', LongTermRentalStatusEnum::COMPLETE)
                    ->select('lt_rentals.id', 'lt_rentals.worksheet_no', 'lt_rentals.customer_name')
                    ->first();
                if ($rental) {
                    $rental_type = LongTermRental::class;
                    $contract = Contracts::leftJoin('contract_lines', 'contract_lines.contract_id', '=', 'contracts.id')
                        ->where('contracts.job_type', LongTermRental::class)
                        ->where('contracts.job_id', $rental->id)
                        ->where('contract_lines.car_id', $car->id)
                        ->select('contracts.id', 'contracts.worksheet_no', 'contract_lines.pick_up_date', 'contract_lines.return_date')
                        ->first();
                }
            }

            $car->rental_type = $rental_type;
            if ($rental) {
                $car->rental = 1;
                $car->rental_id = ($rental) ? $rental->id : null;
                $car->rental_worksheet_no = ($rental) ? $rental->worksheet_no : null;
                $car->rental_customer_name = ($rental) ? $rental->customer_name : null;

                if ($contract) {
                    $car->contract = 1;
                    $car->contract_worksheet_no = ($contract) ? $contract->worksheet_no : null;
                    $car->contract_pick_up_date = ($contract) ? date('d/m/Y H:i', strtotime($contract->pick_up_date)) : null;
                    $car->contract_return_date = ($contract) ? date('d/m/Y H:i', strtotime($contract->return_date)) : null;
                }
            }
        }

        return $car;
    }

    public static function createReplacementCar($repair_order_id, $repair)
    {
        $replacement_car_old = ReplacementCar::where('job_type', ReplacementJobTypeEnum::REPAIR)
            ->where('job_id', $repair_order_id)->count();
        if ($replacement_car_old <= 0) {
            $user = Auth::user();
            $replacement_car = new ReplacementCar;
            $replacement_car_count = DB::table('replacement_cars')->count() + 1;
            $prefix = 'RC-';
            $replacement_car->worksheet_no = generateRecordNumber($prefix, $replacement_car_count);
            $replacement_car->replacement_type = $repair->replacement_type;
            $replacement_car->job_type = ReplacementJobTypeEnum::REPAIR;
            $replacement_car->job_id = $repair_order_id;
            $replacement_car->branch_id = $user ? $user->branch_id : null;
            $replacement_car->main_car_id = $repair->car_id;
            $replacement_car->replacement_expect_date = $repair->replacement_date;
            $replacement_car->replacement_expect_place = $repair->replacement_place;
            $replacement_car->save();
        }
        return true;
    }

    public static function createDrivingJob($repair_order_id, $self_drive_type, $car_id)
    {
        $driving_job_old = DrivingJob::where('job_type', RepairOrder::class)
            ->where('job_id', $repair_order_id)->where('self_drive_type', $self_drive_type)->count();
        if ($driving_job_old <= 0) {
            $djf = new DrivingJobFactory(RepairOrder::class, $repair_order_id, $car_id, [
                'self_drive_type' => $self_drive_type,
            ]);
            $driving_job = $djf->create();

            $ctf = new CarparkTransferFactory($driving_job->id, $car_id);
            $ctf->create();
        }

        return true;
    }

    public static function createInspectionJobs($repair_order_id, $inspection_type, $transfer_type, $car_id, $repair_date)
    {
        $inspection_job_old = InspectionJob::where('item_id', $repair_order_id)
            ->where('item_type', RepairOrder::class)->where('transfer_type', $transfer_type)->count();
        if ($inspection_job_old <= 0) {
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
                    $inspection_job->item_type = RepairOrder::class;
                    $inspection_job->item_id = $repair_order_id;
                    $inspection_job->open_date = $open_date;
                    $inspection_job->transfer_type = $step_form_new->transfer_type;
                    $inspection_job->inspection_flow_id = $inspection_flow->id;
                    $inspection_job->inspection_type = $inspection_flow->inspection_type;
                    $inspection_job->transfer_reason = $inspection_step->transfer_reason;
                    $inspection_job->is_need_customer_sign_in = $inspection_flow->is_need_customer_sign_in;
                    $inspection_job->is_need_customer_sign_out = $inspection_flow->is_need_customer_sign_out;
                    $inspection_job->car_id = $car_id;
                    $inspection_job->inspection_must_date = $repair_date;
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
        return true;
    }


    public static function getConditionQuotation($repair)
    {
        $quotation = Quotation::where('reference_type', $repair->job_type)
            ->where('reference_id', $repair->job_id)->first();
        $condotion_lt_rental = [];
        if ($quotation) {
            $condotion_lt_rental = QuotationForm::where('quotation_id', $quotation->id)->orderBy('seq', 'asc')->get();
            $list2 = $condotion_lt_rental->pluck('id')->toArray();
            $quotation_form_checklists = QuotationFormChecklist::whereIn('quotation_form_id', $list2)->orderBy('seq', 'asc')->get();
            $quotation_form_checklists->map(function ($item) {
                $item->quotation_form_checklist_status  = $item->status == STATUS_INACTIVE ? false : true;
                $item->quotation_form_checklist_seq  = $item->seq;
                $item->quotation_form_checklist_name  = $item->name;
                $item->quotation_form_checklist_id  = $item->id;
                return $item;
            });
            $condotion_lt_rental->map(function ($item) use ($quotation_form_checklists) {
                $quotation_form_checklist = $quotation_form_checklists->where('quotation_form_id', $item->id)->values();
                $item->sub_quotation_form_checklist  = $quotation_form_checklist;
                $item->quotation_form_status  = $item->status == STATUS_INACTIVE ? false : true;
                $item->quotation_form_id  = $item->id;
                return $item;
            });
        }


        return $condotion_lt_rental;
    }

    // public static function ReplacementCar($repair)
    // {
    //     $replacement_car = ReplacementCar::where('job_id', $repair->id)
    //         ->where('job_type', ReplacementJobTypeEnum::REPAIR)
    //         ->where('main_car_id', $repair->car_id)
    //         ->select(
    //             'id',
    //             'worksheet_no',
    //             'replacement_type',
    //             'replacement_expect_date',
    //             'replacement_expect_place'
    //         )
    //         ->first();
    //         dd($replacement_car);
    // }

    public static function getReplacementList($repair_id)
    {
        if (!$repair_id) {
            return [];
        }
        $replacement_list = ReplacementCar::where('job_type', ReplacementJobTypeEnum::REPAIR)
            ->where('job_id', $repair_id)
            ->get()
            ->map(function ($item) {
                $item->job_type_id = $item->replacement_type;
                $item->job_type_text = __('replacement_cars.type_' . $item->replacement_type);
                $item->send_pickup_date = $item->replacement_expect_date;
                $item->is_pickup_at_tls = $item->is_cust_receive_replace;
                $slide_text = null;
                if ($item->slide_id) {
                    $slide = Slide::find($item->slide_id);
                    $slide_text = $slide?->worksheet_no;
                }
                $item->slide_text = $slide_text;
                $item->send_pickup_place = $item->replacement_expect_place;
                $main_license_plate = null;
                if ($item->main_car_id) {
                    $main_car = Car::find($item->main_car_id);
                    $main_license_plate = $main_car?->license_plate;
                }
                $item->main_license_plate = $main_license_plate;
                $replacement_license_plate = null;
                if ($item->replacement_car_id) {
                    $replacement_car = Car::find($item->replacement_car_id);
                    $replacement_license_plate = $replacement_car?->license_plate;
                }
                $item->replacement_license_plate = $replacement_license_plate;

                $medias = $item->getMedia('replacement_car_documents');
                $replacement_files = get_medias_detail($medias);
                $replacement_files = collect($replacement_files)->map(function ($item) {
                    $item['formated'] = true;
                    $item['saved'] = true;
                    $item['raw_file'] = null;
                    return $item;
                })->toArray();
                $item->replacement_files = $replacement_files;
                return $item;
            });
        return $replacement_list;
    }

    public static function getDrivingJob($repair_order_id, $self_drive_type)
    {
        $driving_job = DrivingJob::where('job_type', RepairOrder::class)
            ->where('job_id', $repair_order_id)
            ->where('self_drive_type', $self_drive_type)->first();

        return $driving_job;
    }

    public static function getInspectionJob($repair_order_id, $inspection_type)
    {
        $inspection_job = InspectionJob::where('item_type', RepairOrder::class)
            ->where('item_id', $repair_order_id)
            ->where('inspection_type', $inspection_type)->first();

        return $inspection_job;
    }

    static function getGeographieName($geographie_id)
    {
        $geographie_name = null;
        if (!empty($geographie_id)) {
            $geographie_data = DB::table('Geographies')->where('id', $geographie_id)
                ->get();
            if (!empty($geographie_data)) {
                $geographie_name = $geographie_data[0]->name;
            }
        }
        return $geographie_name;
    }

    static function getCreditorName($creditor_id)
    {
        $creditor_name = null;
        if (!empty($creditor_id)) {
            $creditor_data = Creditor::where('id', $creditor_id)->first();
            if (!empty($creditor_data)) {
                $creditor_name = $creditor_data->name;
            }
        }
        return $creditor_name;
    }

    static function getBillRecipientName($bill_recipient_id)
    {
        $bill_recipient_name = null;
        if (!empty($bill_recipient_id)) {
            $bill_recipient_data = User::where('id', $bill_recipient_id)->first();
            if (!empty($bill_recipient_data)) {
                $bill_recipient_name = $bill_recipient_data->name;
            }
        }
        return $bill_recipient_name;
    }
}
