<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InspectionJobStep;
use App\Models\InspectionJob;
use App\Models\InspectionJobChecklist;
use App\Enums\InspectionStatusEnum;
use App\Enums\InspectionRemarkEnum;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InspectionJobStepController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;

        $list = InspectionJobStep::select(
            'id',
            'inspection_job_id',
            'transfer_type',
            'inspection_status',
            'remark',
            'remark_reason',
            'inspection_date',
            'inspector_id',
            'inspector_fullname',
            'inspection_location',
            'inspection_department_id',
            'inspection_role_id',
            'oil_quantity',
            'dpf_solution',
            'mileage',
            'is_need_images',
            'is_need_inspector_sign',
            'is_need_send_mobile',
        )
            ->where(function ($q) use ($request) {
                if (!empty($request->s)) {
                    $q->where('inspector_fullname', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->inspection_job_id)) {
                    $q->where('inspection_job_id', $request->inspection_job_id);
                }
            })->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read(Request $request)
    {
        $data = InspectionJobStep::select(
            'id',
            'inspection_job_id',
            'transfer_type',
            'inspection_status',
            'remark',
            'remark_reason',
            'inspection_date',
            'inspector_id',
            'inspector_fullname',
            'inspection_location',
            'inspection_department_id',
            'inspection_role_id',
            'oil_quantity',
            'dpf_solution',
            'mileage',
            'is_need_images',
            'is_need_inspector_sign',
            'is_need_send_mobile',
        )
            ->where('id', $request->id)
            ->first();

        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        if (strcmp($data->is_need_images, BOOL_TRUE) == 0) {
            $image_1_files = $data->getMedia('front_car_images_out');
            $image_1 = get_medias_detail($image_1_files);
            if (isset($image_1[0]['url'])) {
                $data->image_1 = $image_1[0]['url'];
            }
            $image_2_files = $data->getMedia('back_car_images_out');
            $image_2 = get_medias_detail($image_2_files);
            if (isset($image_2[0]['url'])) {
                $data->image_2 = $image_2[0]['url'];
            }
            $image_3_files = $data->getMedia('right_car_images_out');
            $image_3 = get_medias_detail($image_3_files);
            if (isset($image_3[0]['url'])) {
                $data->image_3 = $image_3[0]['url'];
            }
            $image_4_files = $data->getMedia('left_car_images_out');
            $image_4 = get_medias_detail($image_4_files);
            if (isset($image_4[0]['url'])) {
                $data->image_4 = $image_4[0]['url'];
            }
            $image_5_files = $data->getMedia('top_car_images_out');
            $image_5 = get_medias_detail($image_5_files);
            if (isset($image_5[0]['url'])) {
                $data->image_5 = $image_5[0]['url'];
            }
        }

        if (strcmp($data->is_need_inspector_sign, BOOL_TRUE) == 0) {
            $signature_files = $data->getMedia('signature');
            $image_inspector_sign = get_medias_detail($signature_files);
            if (isset($image_inspector_sign[0]['url'])) {
                $data->image_inspector_sign = $image_inspector_sign[0]['url'];
            }
        }

        $inspection_job_checklists = InspectionJobChecklist::where('inspection_job_step_id', $data->id)
            ->select(
                'id',
                'inspection_form_section_name',
                'inspection_form_checklist_name',
                'is_pass',
                'remark'
            )->get()->toArray();

        if ($inspection_job_checklists) {
            $data->inspection_job_checklists = $inspection_job_checklists;
        }

        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }

    public function store(Request $request)
    {
        //check request
        $validator = Validator::make($request->all(), [
            'id' => ['required'],
            'inspection_status' => ['required', Rule::in([InspectionStatusEnum::DRAFT, InspectionStatusEnum::PASS, InspectionStatusEnum::NOT_PASS])],
            'inspection_date' => ['required'],
            'inspector_id' => ['required', 'exists:users,id'],
            'mileage' => ['required', 'string', 'max:10'],
        ], [], [
            'id' => __('inspection_cars.id'),
            'inspection_status' => __('inspection_cars.inspection_status'),
            'inspection_date' => __('inspection_cars.inspection_date'),
            'inspector_id' => __('inspection_cars.inspector'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        //check remark reason
        if (in_array($request->inspection_status, [InspectionStatusEnum::NOT_PASS])) {
            $validator = Validator::make($request->all(), [
                'remark_reason' => ['required',  Rule::in([InspectionRemarkEnum::CHANGE_CAR, InspectionRemarkEnum::REPEAT_INSPECTION])],
            ], [], [
                'remark_reason' => __('inspection_cars.remark_reason'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        $id = $request->id;
        $inspection_job_step = InspectionJobStep::find($id);

        if (empty($inspection_job_step)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }

        $inspection_job_step->inspection_status = $request->inspection_status;
        $inspection_job_step->remark = $request->remark;
        $inspection_job_step->inspection_date = $request->inspection_date;
        $inspection_job_step->inspector_id = $request->inspector_id;
        $inspection_job_step->remark_reason = $request->remark_reason;
        $inspection_job_step->inspector_fullname = $request->inspector_fullname;
        $inspection_job_step->inspection_location = $request->inspection_location;
        $inspection_job_step->oil_quantity = $request->oil_quantity;
        $inspection_job_step->dpf_solution = $request->dpf_solution;
        $mileage = floatval(str_replace(',', '', $request->mileage));
        $inspection_job_step->mileage = $mileage;
        $inspection_job_step->save();

        //update image
        if (!empty($request->front_car_image)) {
            if ($request->front_car_image->isValid()) {
                $inspection_job_step->clearMediaCollection('front_car_images_out');
                $inspection_job_step->addMedia($request->front_car_image)->toMediaCollection('front_car_images_out');
            }
        }
        if (!empty($request->back_car_image)) {
            if ($request->back_car_image->isValid()) {
                $inspection_job_step->clearMediaCollection('back_car_images_out');
                $inspection_job_step->addMedia($request->back_car_image)->toMediaCollection('back_car_images_out');
            }
        }
        if (!empty($request->right_car_image)) {
            if ($request->right_car_image->isValid()) {
                $inspection_job_step->clearMediaCollection('right_car_images_out');
                $inspection_job_step->addMedia($request->right_car_image)->toMediaCollection('right_car_images_out');
            }
        }
        if (!empty($request->left_car_image)) {
            if ($request->left_car_image->isValid()) {
                $inspection_job_step->clearMediaCollection('left_car_images_out');
                $inspection_job_step->addMedia($request->left_car_image)->toMediaCollection('left_car_images_out');
            }
        }
        if (!empty($request->top_car_image)) {
            if ($request->top_car_image->isValid()) {
                $inspection_job_step->clearMediaCollection('top_car_images_out');
                $inspection_job_step->addMedia($request->top_car_image)->toMediaCollection('top_car_images_out');
            }
        }

        //update signature
        if (!empty($request->image_inspector_sign)) {
            if ($request->image_inspector_sign->isValid()) {
                $inspection_job_step->clearMediaCollection('signature');
                $inspection_job_step->addMedia($request->image_inspector_sign)->toMediaCollection('signature');
            }
        }

        $inspection_job_checklists = $request->inspection_job_checklists;
        if (is_array($inspection_job_checklists) && sizeof($inspection_job_checklists) > 0) {
            foreach ($inspection_job_checklists as $key => $item) {
                if (!isset($item['id'])) {
                    return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
                }
                $inspection_job_checklist = InspectionJobChecklist::find($item['id']);
                if (!$inspection_job_checklist) {
                    return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
                }
                if (isset($item['is_pass'])) {
                    $inspection_job_checklist->is_pass = $item['is_pass'];
                }
                if (isset($item['remark'])) {
                    $inspection_job_checklist->remark = $item['remark'];
                }
                $inspection_job_checklist->save();
            }
        }

        return $this->responseWithCode(true, DATA_SUCCESS, $inspection_job_step->id, 200);
    }
}
