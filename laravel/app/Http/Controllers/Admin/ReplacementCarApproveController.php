<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\ReplacementCar;
use App\Traits\ReplacementCarTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use ReplacementCarStatusEnum;
use App\Traits\HistoryTrait;

class ReplacementCarApproveController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ReplacementCarApprove);
        $s = $request->s;
        $s = $request->s;
        $worksheet_id = $request->worksheet_id;
        $worksheet_name = null;
        $main_car_id = $request->main_car_id;
        $replacement_car_id = $request->replacement_car_id;
        $main_car_license_plate = null;
        $replacement_car_license_plate = null;

        if ($worksheet_id) {
            $replacement_car = ReplacementCar::find($worksheet_id);
            $worksheet_name = $replacement_car ? $replacement_car->worksheet_no : '';
        }

        if ($main_car_id) {
            $car = Car::find($main_car_id);
            $main_car_license_plate = $car ? $car->license_plate : '';
        }
        if ($replacement_car_id) {
            $car = Car::find($replacement_car_id);
            $replacement_car_license_plate = $car ? $car->license_plate : '';
        }

        $list = ReplacementCar::search($s, $request)
            ->whereIn('status', [ReplacementCarStatusEnum::PENDING_REVIEW, ReplacementCarStatusEnum::REJECT])
            ->sortable(['created_at' => 'desc'])
            ->paginate(PER_PAGE);
        $replacement_type_list = ReplacementCarTrait::getReplacementTypeList();
        $replacement_job_type_list = ReplacementCarTrait::getReplacementJobTypeList();
        $page_title = __('replacement_cars.page_title_approve');
        return view('admin.replacement-car-approves.index', [
            's' => $s,
            'replacement_type' => $request->replacement_type,
            'job_type' => $request->job_type,
            'worksheet_id' => $request->worksheet_id,
            'worksheet_name' => $worksheet_name,
            'main_car_id' => $main_car_id,
            'main_car_license_plate' => $main_car_license_plate,
            'replacement_car_id' => $replacement_car_id,
            'replacement_car_license_plate' => $replacement_car_license_plate,
            'list' => $list,
            'page_title' => $page_title,
            'replacement_type_list' => $replacement_type_list,
            'replacement_job_type_list' => $replacement_job_type_list,
        ]);
    }

    public function show(ReplacementCar $replacement_car_approve)
    {
        $this->authorize(Actions::View . '_' . Resources::ReplacementCarApprove);
        $replacement_type_list = ReplacementCarTrait::getReplacementTypeList();
        $replacement_job_type_list = ReplacementCarTrait::getReplacementJobTypeList();
        $is_need_driver_list = ReplacementCarTrait::getIsNeedDriverList();
        $is_need_slide_list = ReplacementCarTrait::getIsNeedSlideList();
        $replacement_car_files = $replacement_car_approve->getMedia('replacement_car_documents');
        $replacement_car_files = get_medias_detail($replacement_car_files);
        $main_car = ReplacementCarTrait::getCarInfo($replacement_car_approve->main_car_id);
        $replace_car = ReplacementCarTrait::getCarInfo($replacement_car_approve->replacement_car_id);
        $required_lower_spec = $replacement_car_approve->is_spec_low;

        // $approve_line_management = new StepApproveManagement();
        // $approve_return = $approve_line_management->logApprove(ReplacementCar::class, $replacement_car_approve->id);
        // $approve = $approve_return['approve'];
        // $approve_line_list = $approve_return['approve_line_list'];

        // // can approve or super user
        // $approve_line_owner = $approve_line_management->checkCanApprove($approve);
        $approve_line = HistoryTrait::getHistory(ReplacementCar::class, $replacement_car_approve->id);
        $approve_line_list = $approve_line['approve_line_list'];
        $approve = $approve_line['approve'];
        $approve_line_logs = $approve_line['approve_line_logs'];
        if (!is_null($approve_line_list)) {
            // can approve or super user
            $approve_line_owner = new StepApproveManagement();
            // $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
            $approve_line_owner = $approve_line_owner->checkCanApprove(ReplacementCar::class, $replacement_car_approve->id);
        } else {
            $approve_line_owner = null;
        }
        $available_replacement_car = [];
        $route_uri = route('admin.replacement-car-approves.update-status');
        $page_title = __('lang.view') . __('replacement_cars.page_title');
        return view('admin.replacement-car-approves.form', [
            'd' => $replacement_car_approve,
            'page_title' => $page_title,
            'replacement_type_list' => $replacement_type_list,
            'replacement_job_type_list' => $replacement_job_type_list,
            'is_need_driver_list' => $is_need_driver_list,
            'is_need_slide_list' => $is_need_slide_list,
            'replacement_car_files' => $replacement_car_files,
            'main_car' => $main_car,
            'replacement_car' => $replace_car,
            'route_uri' => $route_uri,
            'mode' => MODE_VIEW,
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
            'approve_line_owner' => $approve_line_owner,
            'required_lower_spec' => $required_lower_spec,
            'approve_line_logs' => $approve_line_logs,
            'available_replacement_car' => $available_replacement_car
        ]);
    }


    public function updateStatus(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ReplacementCarApprove);
        $replacement_car = REplacementCar::find($request->id);
        if (!$replacement_car) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }

        if (strcmp($replacement_car->status, ReplacementCarStatusEnum::PENDING_REVIEW) != 0) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }

        $validator = Validator::make($request->all(), [
            'status_update' => 'required',
            'reject_reason' => 'required_if:status_update,REJECT'
        ], [], [
                'status_update' => __('lang.status'),
                'reject_reason' => __('replacement_cars.reject_reason')
            ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        // update approve step
        $approve_update = new StepApproveManagement();
        // $approve_update = $approve_update->updateApprove($request, $replacement_car, $request->status_update, ReplacementCar::class);
        $approve_update = $approve_update->updateApprove(ReplacementCar::class, $replacement_car->id, $request->status_update,null,null);

        if (strcmp($approve_update, 'CONFIRM') === 0) {
            $approve_update = ReplacementCarStatusEnum::PENDING;
        }
        $replacement_car->status = $approve_update;
        $replacement_car->save();

        if (strcmp($replacement_car->status, ReplacementCarStatusEnum::PENDING) === 0) {
            // if ($replacement_car->is_need_driver) {
            //     ReplacementCarTrait::createDrivingJobByReplacementType($replacement_car);
            // }
            ReplacementCarTrait::createDrivingJobByReplacementType($replacement_car);
            ReplacementCarTrait::createInspectionJobByReplacementType($replacement_car);
        }

        $redirect_route = route('admin.replacement-car-approves.index');
        return $this->responseValidateSuccess($redirect_route);
    }
}