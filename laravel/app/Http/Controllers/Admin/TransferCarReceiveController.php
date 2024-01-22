<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Enums\TransferCarEnum;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Car;
use App\Models\CarParkTransfer;
use App\Models\DrivingJob;
use App\Models\InspectionJob;
use App\Models\TransferCar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransferCarReceiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::TransferCarReceive);
        $list = TransferCar::sortable(['worksheet_no' => 'desc'])->TransferBranchFilter()->search($request)->paginate(PER_PAGE);

        $list->map(function ($item) {
            $item->can_edit_receive = false;
            if (in_array($item->status, [TransferCarEnum::WAITING_RECEIVE, TransferCarEnum::IN_PROCESS])) {
                $item->can_edit_receive = true;
            }
            return $item;
        });

        $page_title = __('transfer_cars.tranfer_car_receive');
        $branch_lists = Branch::all();
        $status_lists = $this->getStatusTransferCarList();
        $car_id = $request->car_id;
        $status_id = $request->status;
        $from_branch_id = $request->from_branch_id;
        $to_branch_id = $request->to_branch_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $license_plate_list = Car::select('id', 'license_plate', 'engine_no', 'chassis_no')->get();

        return view('admin.transfer-cars.index', [
            'list' => $list,
            's' => $request->s,
            'page_title' => $page_title,
            'license_plate_list' => $license_plate_list,
            'car_id' => $car_id,
            'status_id' => $status_id,
            'from_branch_id' => $from_branch_id,
            'to_branch_id' => $to_branch_id,
            'branch_lists' => $branch_lists,
            'status_lists' => $status_lists,
            'from_date' => $from_date,
            'to_date' => $to_date,
        ]);
    }

    public function show(TransferCar $transfer_car_receife)
    {
        $this->authorize(Actions::View . '_' . Resources::TransferCarReceive);
        $optional_files = [];
        $branch_list = Branch::select('name', 'id')->get();
        $car_lists = Car::select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
            // ->where('cars.branch_id', Auth::user()->branch_id)
            ->get()->map(function ($item) {
                if ($item->license_plate) {
                    $text = $item->license_plate;
                } else if ($item->engine_no) {
                    $text = __('inspection_cars.engine_no') . ' ' . $item->engine_no;
                } else if ($item->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
                }
                $item->id = $item->id;
                $item->name = $text;
                return $item;
            });
        $page_title = __('transfer_cars.view_recieve');
        $status_list = $this->getStatusList();
        $need_driver_list = $this->getNeedDriverList();
        $optional_files = $transfer_car_receife->getMedia('optional_files');
        $optional_files = get_medias_detail($optional_files);
        $url = 'admin.transfer-car-receives.index';
        $driving_job = null;
        $car_park_transfer = null;
        $driving_job = DrivingJob::where('job_type', TransferCar::class)->where('job_id', $transfer_car_receife->id)->first();
        if ($driving_job) {
            $car_park_transfer = CarParkTransfer::where('driving_job_id', $driving_job->id)->first();
        }

        if($transfer_car_receife->is_driver == STATUS_ACTIVE){
            $transfer_car_receife->is_driver = TransferCarEnum::CONFIRM_RECEIVE;
        }else{
            $transfer_car_receife->is_driver = TransferCarEnum::REJECT_RECEIVE;
        }

        $inspection_pickup = InspectionJob::where('item_type', TransferCar::class)->where('item_id', $transfer_car_receife->id)->where('transfer_type',STATUS_INACTIVE)->first();
        $inspection_return = InspectionJob::where('item_type', TransferCar::class)->where('item_id', $transfer_car_receife->id)->where('transfer_type',STATUS_ACTIVE)->first();

        return view('admin.transfer-cars.form',  [
            'd' => $transfer_car_receife,
            'page_title' => $page_title,
            'optional_files' => $optional_files,
            'car_lists' => $car_lists,
            'branch_list' => $branch_list,
            'status_list' => $status_list,
            'need_driver_list' => $need_driver_list,
            'optional_files' => $optional_files,
            'view' => true,
            'url' => $url,
            'driving_job' => $driving_job,
            'car_park_transfer' => $car_park_transfer,
            'inspection_pickup' => $inspection_pickup,
            'inspection_return' => $inspection_return,
        ]);
    }

    public function edit(TransferCar $transfer_car_receife)
    {
        $this->authorize(Actions::Manage . '_' . Resources::TransferCarReceive);
        $optional_files = [];
        $branch_list = Branch::select('name', 'id')->get();
        $car_lists = Car::select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
            // ->where('cars.branch_id', Auth::user()->branch_id)
            ->get()->map(function ($item) {
                if ($item->license_plate) {
                    $text = $item->license_plate;
                } else if ($item->engine_no) {
                    $text = __('inspection_cars.engine_no') . ' ' . $item->engine_no;
                } else if ($item->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
                }
                $item->id = $item->id;
                $item->name = $text;
                return $item;
            });
        $page_title = __('transfer_cars.edit_recieve');
        $status_list = $this->getStatusList();
        $need_driver_list = $this->getNeedDriverList();
        $optional_files = $transfer_car_receife->getMedia('optional_files');
        $optional_files = get_medias_detail($optional_files);
        $status_confirm = $transfer_car_receife->status;
        $url = 'admin.transfer-car-receives.index';
        $driving_job = null;
        $car_park_transfer = null;
        $driving_job = DrivingJob::where('job_type', TransferCar::class)->where('job_id', $transfer_car_receife->id)->first();
        if ($driving_job) {
            $car_park_transfer = CarParkTransfer::where('driving_job_id', $driving_job->id)->first();
        }

        if($transfer_car_receife->is_driver == STATUS_ACTIVE){
            $transfer_car_receife->is_driver = TransferCarEnum::CONFIRM_RECEIVE;
        }else{
            $transfer_car_receife->is_driver = TransferCarEnum::REJECT_RECEIVE;
        }

        $inspection_pickup = InspectionJob::where('item_type', TransferCar::class)->where('item_id', $transfer_car_receife->id)->where('transfer_type',STATUS_INACTIVE)->first();
        $inspection_return = InspectionJob::where('item_type', TransferCar::class)->where('item_id', $transfer_car_receife->id)->where('transfer_type',STATUS_ACTIVE)->first();

        return view('admin.transfer-cars.form',  [
            'd' => $transfer_car_receife,
            'page_title' => $page_title,
            'optional_files' => $optional_files,
            'car_lists' => $car_lists,
            'branch_list' => $branch_list,
            'status_list' => $status_list,
            'need_driver_list' => $need_driver_list,
            'optional_files' => $optional_files,
            'status_confirm' => $status_confirm,
            'url' => $url,
            'driving_job' => $driving_job,
            'car_park_transfer' => $car_park_transfer,
            'inspection_pickup' => $inspection_pickup,
            'inspection_return' => $inspection_return,
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
                'id' => TransferCarEnum::CONFIRM_RECEIVE,
                'value' => TransferCarEnum::CONFIRM_RECEIVE,
                'name' => __('transfer_cars.need_driver_' . STATUS_ACTIVE),
            ],
            [
                'id' => TransferCarEnum::REJECT_RECEIVE,
                'value' => TransferCarEnum::REJECT_RECEIVE,
                'name' => __('transfer_cars.need_driver_' . STATUS_INACTIVE),
            ],
        ]);
    }

    public function getStatusTransferCarList()
    {
        return collect([
            (object) [
                'id' => TransferCarEnum::WAITING_RECEIVE,
                'value' => TransferCarEnum::WAITING_RECEIVE,
                'name' => __('transfer_cars.status_' . TransferCarEnum::WAITING_RECEIVE . '_text'),
            ],
            (object) [
                'id' => TransferCarEnum::CONFIRM_RECEIVE,
                'value' => TransferCarEnum::CONFIRM_RECEIVE,
                'name' => __('transfer_cars.status_' . TransferCarEnum::CONFIRM_RECEIVE . '_text'),
            ],
            (object) [
                'id' => TransferCarEnum::REJECT_RECEIVE,
                'value' => TransferCarEnum::REJECT_RECEIVE,
                'name' => __('transfer_cars.status_' . TransferCarEnum::REJECT_RECEIVE . '_text'),
            ],
            (object) [
                'id' => TransferCarEnum::IN_PROCESS,
                'value' => TransferCarEnum::IN_PROCESS,
                'name' => __('transfer_cars.status_' . TransferCarEnum::IN_PROCESS . '_text'),
            ],
            (object) [
                'id' => TransferCarEnum::SUCCESS,
                'value' => TransferCarEnum::SUCCESS,
                'name' => __('transfer_cars.status_' . TransferCarEnum::SUCCESS . '_text'),
            ],
        ]);
    }
}
