<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Enums\Actions;
use App\Enums\BorrowCarEnum;
use App\Enums\BorrowTypeEnum;
use App\Enums\CarEnum;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\DrivingJobTypeStatusEnum;
use App\Enums\InspectionTypeEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\Resources;
use App\Enums\SelfDriveTypeEnum;
use App\Enums\TransferCarEnum;
use App\Http\Controllers\Controller;
use App\Models\BorrowCar;
use App\Models\Branch;
use App\Models\Car;
use App\Models\CarParkTransfer;
use App\Models\DrivingJob;
use App\Models\InspectionFlow;
use App\Models\InspectionJob;
use App\Models\InspectionStep;
use App\Models\TransferCar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\HistoryTrait;
use App\Factories\DrivingJobFactory;
use App\Factories\CarparkTransferFactory;
use App\Factories\InspectionJobFactory;

class BorrowCarConfirmApproveController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::BorrowCarConfirmApprove);
        $list = BorrowCar::with('car')->sortable(['worksheet_no' => 'desc'])->search($request)->paginate(PER_PAGE);
        $list->map(function ($item) {
            $item->can_edit = false;
            if ((in_array($item->status, [BorrowCarEnum::CONFIRM, BorrowCarEnum::PENDING_DELIVERY])) || ((strcmp($item->borrow_type, BorrowTypeEnum::BORROW_OTHER) === 0) && ($item->status == BorrowCarEnum::IN_PROCESS))) {
                $item->can_edit = true;
            }
            return $item;
        });
        $page_title = __('borrow_cars.borrow_sheet');
        $transfer_car_report = true;

        $license_plate_list = BorrowCar::leftjoin('cars', 'cars.id', 'borrow_cars.car_id')
            ->where('cars.status', CarEnum::READY_TO_USE)
            ->select('cars.id as car_id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
            ->get();
        $license_plate_list->map(function ($item) {
            if ($item->license_plate) {
                $text = $item->license_plate;
            } else if ($item->chassis_no) {
                $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
            }
            $item->id = $item->car_id;
            $item->name = $text;
            return $item;
        });

        $branch_lists = Branch::all();
        $status_lists = $this->getStatusBorrowCarList();
        $car_id = $request->car_id;
        $status = $request->status;
        $worksheet_no_list = BorrowCar::select('id', 'worksheet_no as name')->get();
        $borrow_type_list = $this->getBorrowTypeList();
        $worksheet_no = $request->worksheet_no;
        $borrow_type = $request->borrow_type;
        $pickup_date_start = $request->pickup_date_start;
        $pickup_date_end = $request->pickup_date_end;
        $return_date_start = $request->return_date_start;
        $return_date_end = $request->return_date_end;

        return view('admin.borrow-car-confirm-approves.index', [
            'list' => $list,
            's' => $request->s,
            'page_title' => $page_title,
            'transfer_car_report' => $transfer_car_report,
            'license_plate_list' => $license_plate_list,
            'car_id' => $car_id,
            'status' => $status,
            'branch_lists' => $branch_lists,
            'status_lists' => $status_lists,
            'worksheet_no_list' => $worksheet_no_list,
            'borrow_type_list' => $borrow_type_list,
            'pickup_date_start' => $pickup_date_start,
            'pickup_date_end' => $pickup_date_end,
            'return_date_start' => $return_date_start,
            'return_date_end' => $return_date_end,
            'worksheet_no' => $worksheet_no,
            'borrow_type' => $borrow_type,
        ]);
    }

    public function store(Request $request)
    {
        $borrow_car = BorrowCar::find($request->id);

        if ($borrow_car) {
            if (strcmp($borrow_car->status, BorrowCarEnum::CONFIRM) === 0) {
                $validator = Validator::make($request->all(), [
                    'car_id' => [
                        'required',
                    ],

                    'pickup_place' => [Rule::when(($borrow_car->is_driver == STATUS_ACTIVE), ['required'])],


                ], [], [
                    'pickup_place' => __('borrow_cars.pickup_place'),
                    'car_id' => __('transfer_cars.license_plate_chassis'),
                ]);

                if ($validator->stopOnFirstFailure()->fails()) {
                    return $this->responseValidateFailed($validator);
                }

                $borrow_car->car_id = $request->car_id;

                if ($borrow_car->is_driver == STATUS_ACTIVE) { // ต้องการ
                    $create_model_send = $this->createAutoModel($borrow_car, $request, SelfDriveTypeEnum::SEND, STATUS_INACTIVE); // สร้างใบตรวจขาส่งมอบ , ใบนำรถเข้าออก , ใบงานคนขับ
                    if (!is_null($request->return_place) && !is_null($request->end_date)) {
                        $create_model_pickup = $this->createAutoModel($borrow_car, $request, SelfDriveTypeEnum::PICKUP, STATUS_ACTIVE); // สร้างใบตรวจขารับเข้า , ใบนำรถเข้าออก , ใบงานคนขับ
                    }
                } else if ($borrow_car->is_driver == STATUS_INACTIVE) { // ไม่ต้องการ
                    $create_model_send = $this->createAutoModel($borrow_car, $request, SelfDriveTypeEnum::SELF_DRIVE, null);
                }
            }

            if (strcmp($borrow_car->status, BorrowCarEnum::PENDING_DELIVERY) === 0) { // รอส่งมอบ
                if ($borrow_car->is_driver == STATUS_ACTIVE) {
                    if (((!is_null($request->return_place)) && (!is_null($request->end_date))) && (is_null($borrow_car->return_place) || is_null($borrow_car->end_date))) {
                        $create_model_pickup = $this->createAutoModel($borrow_car, $request, SelfDriveTypeEnum::PICKUP, STATUS_ACTIVE); // สร้างใบตรวจขารับเข้า , ใบนำรถเข้าออก , ใบงานคนขับ
                    }
                    if (strcmp($borrow_car->borrow_type, BorrowTypeEnum::BORROW_OTHER) === 0) {
                        $borrow_car->end_date = $request->end_date;
                    }
                }
            }

            if (strcmp($borrow_car->status, BorrowCarEnum::IN_PROCESS) === 0) { // ระหว่างการยืม
                $validator = Validator::make($request->all(), [
                    'end_date' => [
                        'required',
                    ],

                    'return_place' => [Rule::when(($borrow_car->is_driver == STATUS_ACTIVE), ['required'])],


                ], [], [
                    'end_date' => __('borrow_cars.end_date'),
                    'return_place' => __('borrow_cars.return_place'),
                ]);

                if ($validator->stopOnFirstFailure()->fails()) {
                    return $this->responseValidateFailed($validator);
                }

                if ($borrow_car->is_driver == STATUS_ACTIVE) {
                    if (((!is_null($request->return_place)) && (!is_null($request->end_date))) && (is_null($borrow_car->return_place) || is_null($borrow_car->end_date))) {
                        $create_model_pickup = $this->createAutoModel($borrow_car, $request, SelfDriveTypeEnum::PICKUP, STATUS_ACTIVE); // สร้างใบตรวจขารับเข้า , ใบนำรถเข้าออก , ใบงานคนขับ
                    }
                    if (strcmp($borrow_car->borrow_type, BorrowTypeEnum::BORROW_OTHER) === 0) {
                        $borrow_car->end_date = $request->end_date;
                    }
                }
            }

            $borrow_car->pickup_place = $request->pickup_place;
            $borrow_car->return_place = $request->return_place;

            if (strcmp($borrow_car->status, BorrowCarEnum::CONFIRM) === 0) {
                $borrow_car->status = BorrowCarEnum::PENDING_DELIVERY;
            }

            $borrow_car->save();
        }

        $redirect_route = route('admin.borrow-car-confirm-approves.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(BorrowCar $borrow_car_confirm_approve)
    {
        $this->authorize(Actions::View . '_' . Resources::BorrowCarConfirmApprove);
        $optional_borrow_files = [];
        $branch_list = Branch::select('name', 'id')->get();
        $car_lists = Car::select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
            // ->where('cars.branch_id', Auth::user()->branch_id)
            ->get()->map(function ($item) {
                if ($item->license_plate) {
                    $text = $item->license_plate;
                } else if ($item->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
                }
                $item->id = $item->id;
                $item->name = $text;
                return $item;
            });

        $driving_job_send = DrivingJob::where('job_type', BorrowCar::class)->where('job_id', $borrow_car_confirm_approve->id)->whereIn('self_drive_type', [SelfDriveTypeEnum::SEND, SelfDriveTypeEnum::SELF_DRIVE])->first();
        $car_park_transfer_send = null;
        if ($driving_job_send) {
            $car_park_transfer_send = CarParkTransfer::where('driving_job_id', $driving_job_send->id)->first();
        }
        $driving_job_pickup = DrivingJob::where('job_type', BorrowCar::class)->where('job_id', $borrow_car_confirm_approve->id)->where('self_drive_type', SelfDriveTypeEnum::PICKUP)->first();
        $car_park_transfer_pickup = null;
        if ($driving_job_pickup) {
            $car_park_transfer_pickup = CarParkTransfer::where('driving_job_id', $driving_job_pickup->id)->first();
        }

        $inspection_pickup = InspectionJob::where('item_type', BorrowCar::class)->where('item_id', $borrow_car_confirm_approve->id)->where('transfer_type', STATUS_INACTIVE)->first();
        $inspection_return = InspectionJob::where('item_type', BorrowCar::class)->where('item_id', $borrow_car_confirm_approve->id)->where('transfer_type', STATUS_ACTIVE)->first();

        $optional_borrow_files = $borrow_car_confirm_approve->getMedia('optional_borrow_files');
        $optional_borrow_files = get_medias_detail($optional_borrow_files);
        $page_title = __('borrow_cars.view_borrow_sheet');
        $status_list = $this->getStatusList();
        $need_driver_list = $this->getNeedDriverList();
        $borrow_type_list = $this->getBorrowTypeList();
        $url = 'admin.borrow-car-confirm-approves.index';

        $approve_line = HistoryTrait::getHistory(BorrowCar::class, $borrow_car_confirm_approve->id);
        $approve_line_list = $approve_line['approve_line_list'];
        $approve = $approve_line['approve'];
        $approve_line_logs = $approve_line['approve_line_logs'];
        if (!is_null($approve_line_list)) {
            // can approve or super user
            $approve_line_owner = new StepApproveManagement();
            // $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
            $approve_line_owner = $approve_line_owner->checkCanApprove(BorrowCar::class, $borrow_car_confirm_approve->id, ConfigApproveTypeEnum::BORROW_CAR);
        } else {
            $approve_line_owner = null;
        }

        return view('admin.borrow-car-confirm-approves.form',  [
            'd' => $borrow_car_confirm_approve,
            'page_title' => $page_title,
            'optional_borrow_files' => $optional_borrow_files,
            'car_lists' => $car_lists,
            'branch_list' => $branch_list,
            'status_list' => $status_list,
            'need_driver_list' => $need_driver_list,
            'url' => $url,
            'borrow_type_list' => $borrow_type_list,
            'view' => true,
            'driving_job_send' => $driving_job_send,
            'car_park_transfer_send' => $car_park_transfer_send,
            'driving_job_pickup' => $driving_job_pickup,
            'car_park_transfer_pickup' => $car_park_transfer_pickup,
            'inspection_pickup' => $inspection_pickup,
            'inspection_return' => $inspection_return,
            'approve_line_logs' => $approve_line_logs,
        ]);
    }

    public function edit(BorrowCar $borrow_car_confirm_approve)
    {
        $this->authorize(Actions::Manage . '_' . Resources::BorrowCarConfirmApprove);
        $optional_borrow_files = [];
        $branch_list = Branch::select('name', 'id')->get();
        $start_date = Carbon::parse($borrow_car_confirm_approve->start_date)->format('Y-m-d');
        $end_date = Carbon::parse($borrow_car_confirm_approve->end_date)->format('Y-m-d');

        $car_lists = Car::select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
            ->when($borrow_car_confirm_approve->status == BorrowCarEnum::CONFIRM, function ($query) use ($start_date, $end_date) {
                $query->whereNotIn(
                    'id',
                    BorrowCar::select(['car_id'])
                        ->whereIn('status', [BorrowCarEnum::CONFIRM, BorrowCarEnum::PENDING_DELIVERY, BorrowCarEnum::IN_PROCESS])
                        ->whereNotNull('car_id')
                        ->where(function ($query) use ($start_date,  $end_date) {
                            $query->where(function ($query2) use ($start_date,  $end_date) {
                                $query2->where('start_date', '<=', $start_date);
                                $query2->where('end_date', '>=', $start_date);
                            });

                            $query->orWhere(function ($query2) use ($start_date, $end_date) {
                                $query2->where('start_date', '<=', $end_date);
                                $query2->where('end_date', '>=', $end_date);
                            });
                            $query->orWhere(function ($query2) use ($start_date, $end_date) {
                                $query2->where('start_date', '>=', $start_date);
                                $query2->where('end_date', '<=', $end_date);
                            });
                            $query->orWhere(function ($query2) use ($start_date, $end_date) {
                                $query2->where('start_date', '<=', $start_date);
                                $query2->where('end_date', '>=', $end_date);
                            });
                        })
                );
            })
            ->where('cars.branch_id', $borrow_car_confirm_approve->borrow_branch_id)
            ->where('cars.rental_type', RentalTypeEnum::BORROW)
            ->get()->map(function ($item) {
                if ($item->license_plate) {
                    $text = $item->license_plate;
                } else if ($item->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
                }
                $item->id = $item->id;
                $item->name = $text;
                return $item;
            });

        $driving_job_send = DrivingJob::where('job_type', BorrowCar::class)->where('job_id', $borrow_car_confirm_approve->id)->whereIn('self_drive_type', [SelfDriveTypeEnum::SEND, SelfDriveTypeEnum::SELF_DRIVE])->first();
        $car_park_transfer_send = null;
        if ($driving_job_send) {
            $car_park_transfer_send = CarParkTransfer::where('driving_job_id', $driving_job_send->id)->first();
        }
        $driving_job_pickup = DrivingJob::where('job_type', BorrowCar::class)->where('job_id', $borrow_car_confirm_approve->id)->where('self_drive_type', SelfDriveTypeEnum::PICKUP)->first();
        $car_park_transfer_pickup = null;
        if ($driving_job_pickup) {
            $car_park_transfer_pickup = CarParkTransfer::where('driving_job_id', $driving_job_pickup->id)->first();
        }

        $inspection_pickup = InspectionJob::where('item_type', BorrowCar::class)->where('item_id', $borrow_car_confirm_approve->id)->where('transfer_type', STATUS_INACTIVE)->first();
        $inspection_return = InspectionJob::where('item_type', BorrowCar::class)->where('item_id', $borrow_car_confirm_approve->id)->where('transfer_type', STATUS_ACTIVE)->first();

        $optional_borrow_files = $borrow_car_confirm_approve->getMedia('optional_borrow_files');
        $optional_borrow_files = get_medias_detail($optional_borrow_files);
        $page_title = __('borrow_cars.edit_borrow_sheet');
        $status_list = $this->getStatusList();
        $need_driver_list = $this->getNeedDriverList();
        $borrow_type_list = $this->getBorrowTypeList();
        $url = 'admin.borrow-car-confirm-approves.index';

        $approve_line = HistoryTrait::getHistory(BorrowCar::class, $borrow_car_confirm_approve->id);
        $approve_line_list = $approve_line['approve_line_list'];
        $approve = $approve_line['approve'];
        $approve_line_logs = $approve_line['approve_line_logs'];
        if (!is_null($approve_line_list)) {
            // can approve or super user
            $approve_line_owner = new StepApproveManagement();
            $approve_line_owner = $approve_line_owner->checkCanApprove(BorrowCar::class, $borrow_car_confirm_approve->id, ConfigApproveTypeEnum::BORROW_CAR);
        } else {
            $approve_line_owner = null;
        }
        return view('admin.borrow-car-confirm-approves.form',  [
            'd' => $borrow_car_confirm_approve,
            'page_title' => $page_title,
            'optional_borrow_files' => $optional_borrow_files,
            'car_lists' => $car_lists,
            'branch_list' => $branch_list,
            'status_list' => $status_list,
            'need_driver_list' => $need_driver_list,
            'url' => $url,
            'borrow_type_list' => $borrow_type_list,
            'driving_job_send' => $driving_job_send,
            'car_park_transfer_send' => $car_park_transfer_send,
            'driving_job_pickup' => $driving_job_pickup,
            'car_park_transfer_pickup' => $car_park_transfer_pickup,
            'inspection_pickup' => $inspection_pickup,
            'inspection_return' => $inspection_return,
            'approve_line_logs' => $approve_line_logs,
        ]);
    }

    private function getStatusList()
    {
        return collect([
            [
                'id' => TransferCarEnum::CONFIRM_RECEIVE,
                'value' => TransferCarEnum::CONFIRM_RECEIVE,
                'name' => __('transfer_cars.status_' . STATUS_ACTIVE),
            ],
            [
                'id' => TransferCarEnum::REJECT_RECEIVE,
                'value' => TransferCarEnum::REJECT_RECEIVE,
                'name' => __('transfer_cars.status_' . STATUS_INACTIVE),
            ],
        ]);
    }

    private function getNeedDriverList()
    {
        return collect([
            [
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => __('transfer_cars.need_driver_' . STATUS_ACTIVE),
            ],
            [
                'id' => STATUS_INACTIVE,
                'value' => STATUS_INACTIVE,
                'name' => __('transfer_cars.need_driver_' . STATUS_INACTIVE),
            ],
        ]);
    }

    private function getBorrowTypeList()
    {
        return collect([
            (object)[
                'id' => BorrowTypeEnum::BORROW_EMPLOYEE,
                'value' => BorrowTypeEnum::BORROW_EMPLOYEE,
                'name' => __('borrow_cars.type_' . BorrowTypeEnum::BORROW_EMPLOYEE),
            ],
            (object)[
                'id' => BorrowTypeEnum::BORROW_OTHER,
                'value' => BorrowTypeEnum::BORROW_OTHER,
                'name' => __('borrow_cars.type_' . BorrowTypeEnum::BORROW_OTHER),
            ],
        ]);
    }

    public function getStatusBorrowCarList()
    {
        return collect([
            (object) [
                'id' => BorrowCarEnum::PENDING_REVIEW,
                'value' => BorrowCarEnum::PENDING_REVIEW,
                'name' => __('borrow_cars.status_' . BorrowCarEnum::PENDING_REVIEW . '_text'),
            ],
            (object) [
                'id' => BorrowCarEnum::CONFIRM,
                'value' => BorrowCarEnum::CONFIRM,
                'name' => __('borrow_cars.status_' . BorrowCarEnum::CONFIRM . '_text'),
            ],
            (object) [
                'id' => BorrowCarEnum::REJECT,
                'value' => BorrowCarEnum::REJECT,
                'name' => __('borrow_cars.status_' . BorrowCarEnum::REJECT . '_text'),
            ],
            (object) [
                'id' => BorrowCarEnum::PENDING_DELIVERY,
                'value' => BorrowCarEnum::PENDING_DELIVERY,
                'name' => __('borrow_cars.status_' . BorrowCarEnum::PENDING_DELIVERY . '_text'),
            ],
            (object) [
                'id' => BorrowCarEnum::IN_PROCESS,
                'value' => BorrowCarEnum::IN_PROCESS,
                'name' => __('borrow_cars.status_' . BorrowCarEnum::IN_PROCESS . '_text'),
            ],
            (object) [
                'id' => BorrowCarEnum::SUCCESS,
                'value' => BorrowCarEnum::SUCCESS,
                'name' => __('borrow_cars.status_' . BorrowCarEnum::SUCCESS . '_text'),
            ],
        ]);
    }

    public static function createAutoModel($borrow_car, $request, $self_drive_type, $transfer_type)
    {
        if ($borrow_car) {
            $djf = new DrivingJobFactory(BorrowCar::class, $borrow_car->id, $borrow_car->car_id, [
                'self_drive_type' => $self_drive_type,
                //'start_date' => null,
                //'end_date' => null,
                'origin' => $request->return_place,
                'destination' => $request->pickup_place,
                'driver_name' => ($borrow_car->is_driver == STATUS_ACTIVE ? $borrow_car->contact : null),
                'remark' => $request->remark,
            ]);
            $driving_job = $djf->create();

            $ctf = new CarparkTransferFactory($driving_job->id, $borrow_car->car_id);
            $ctf->create();

            $ijf = new InspectionJobFactory(InspectionTypeEnum::BORROWED, BorrowCar::class, $borrow_car->id, $borrow_car->car_id, [
                'transfer_type' => $transfer_type
            ]);
            $ijf->create();
        }

        return true;
    }
}
