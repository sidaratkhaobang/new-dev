<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Enums\Actions;
use App\Enums\BorrowCarEnum;
use App\Enums\BorrowTypeEnum;
use App\Enums\CarEnum;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\Resources;
use App\Enums\TransferCarEnum;
use App\Http\Controllers\Controller;
use App\Models\BorrowCar;
use App\Models\Branch;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Traits\HistoryTrait;

class BorrowCarApproveController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::BorrowCarApprove);
        $list = BorrowCar::with('car')->sortable(['worksheet_no' => 'desc'])
        ->whereIn('borrow_cars.status', [BorrowCarEnum::CONFIRM,BorrowCarEnum::PENDING_REVIEW,BorrowCarEnum::REJECT])->search($request)->paginate(PER_PAGE);
        $list->map(function ($item) {
            $item->can_edit = true;
            if (!in_array($item->status, [BorrowCarEnum::PENDING_REVIEW])) {
                $item->can_edit = false;
            }
            return $item;
        });
        $page_title = __('borrow_cars.approve_sheet');
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

        return view('admin.borrow-car-approves.index', [
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

    public function show(BorrowCar $borrow_car_approve)
    {
        $this->authorize(Actions::View . '_' . Resources::BorrowCarApprove);
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

        // $approve_line_list = new StepApproveManagement();
        // $approve_return = $approve_line_list->logApprove(BorrowCar::class, $borrow_car_approve->id);
        // $approve_line_list = $approve_return['approve_line_list'];
        // $approve = $approve_return['approve'];
        // if (!is_null($approve_line_list)) {
        //     // can approve or super user
        //     $approve_line_owner = new StepApproveManagement();
        //     $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
        // } else {
        //     $approve_line_owner = null;
        // }

        $approve_line = HistoryTrait::getHistory(BorrowCar::class, $borrow_car_approve->id);
        $approve_line_list = $approve_line['approve_line_list'];
        $approve = $approve_line['approve'];
        $approve_line_logs = $approve_line['approve_line_logs'];
        if (!is_null($approve_line_list)) {
            // can approve or super user
            $approve_line_owner = new StepApproveManagement();
            // $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
            $approve_line_owner = $approve_line_owner->checkCanApprove(BorrowCar::class, $borrow_car_approve->id,ConfigApproveTypeEnum::BORROW_CAR);

        } else {
            $approve_line_owner = null;
        }

        $optional_borrow_files = $borrow_car_approve->getMedia('optional_borrow_files');
        $optional_borrow_files = get_medias_detail($optional_borrow_files);
        $page_title = __('borrow_cars.approve_sheet');
        $status_list = $this->getStatusList();
        $need_driver_list = $this->getNeedDriverList();
        $borrow_type_list = $this->getBorrowTypeList();
        $url = 'admin.borrow-cars.index';
        return view('admin.borrow-car-approves.form',  [
            'd' => $borrow_car_approve,
            'page_title' => $page_title,
            'optional_borrow_files' => $optional_borrow_files,
            'car_lists' => $car_lists,
            'branch_list' => $branch_list,
            'status_list' => $status_list,
            'need_driver_list' => $need_driver_list,
            'url' => $url,
            'borrow_type_list' => $borrow_type_list,
            'view' => true,
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
            'approve_line_owner' => $approve_line_owner,
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

    public function updateBorrowCarStatus(Request $request)
    {
        // dd($request->all());
        // TO DO
        if (in_array($request->bc_status, [
            BorrowCarEnum::REJECT,
            // POStatusEnum::CANCEL,
        ])) {
            $validator = Validator::make($request->all(), [
                'reason' => ['required', 'max:255'],
            ], [], [
                'reason' => __('lang.reason')
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        if ($request->borrow_car) {
            $borrow_car = BorrowCar::find($request->borrow_car);
            // update approve step
            $approve_update = new StepApproveManagement();
            // $approve_update = $approve_update->updateApprove($request, $borrow_car, $request->bc_status, BorrowCar::class);
            //fixed
            $approve_update = $approve_update->updateApprove(BorrowCar::class, $borrow_car->id, $request->bc_status,ConfigApproveTypeEnum::BORROW_CAR,$request->reason);


            $borrow_car->status = $approve_update;
            $borrow_car->reason = $request->reason;
            $borrow_car->save();

            return response()->json([
                'success' => true,
                'message' => 'ok',
                'redirect' => ($request->redirect_route) ? $request->redirect_route : route('admin.borrow-car-approves.index')
            ]);
        }
    }
}
