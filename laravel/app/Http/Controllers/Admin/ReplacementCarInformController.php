<?php

namespace App\Http\Controllers\Admin;

use Actions;
use App\Enums\ReplacementCarStatusEnum;
use App\Enums\ReplacementJobTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Car;
use App\Models\Repair;
use App\Models\ReplacementCar;
use App\Traits\ReplacementCarTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReplacementCarInformController extends Controller
{
    use ReplacementCarTrait;
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ReplacementCarInform);
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

        $list = ReplacementCar::with(['mainCar', 'replacementCar'])
            ->sortable(['created_at' => 'desc'])
            ->search($s, $request)
            ->paginate(PER_PAGE);

        foreach ($list as $item) {
            $item->job_worksheet_no = null;
            if ($item->job_type === ReplacementJobTypeEnum::REPAIR) {
                $repair = Repair::find($item->job_id);
                $item->job_worksheet_no = $repair ? $repair->worksheet_no : null;
            }
            if ($item->job_type === ReplacementJobTypeEnum::ACCIDENT) {
                $accident = Accident::find($item->job_id);
                $item->job_worksheet_no = $accident ? $accident->worksheet_no : null;
            }
        }

        $replacement_type_list = ReplacementCarTrait::getReplacementTypeList();
        $replacement_job_type_list = ReplacementCarTrait::getReplacementJobTypeList();
        $page_title = __('replacement_cars.page_title_inform');
        return view('admin.replacement-car-informs.index', [
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

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::ReplacementCarInform);
        
        $d = new ReplacementCar();
        $replacement_type_list = ReplacementCarTrait::getReplacementTypeList();
        $replacement_job_type_list = ReplacementCarTrait::getReplacementJobTypeList();
        $is_need_driver_list = ReplacementCarTrait::getIsNeedDriverList();
        $is_need_slide_list = ReplacementCarTrait::getIsNeedSlideList();

        $route_uri = route('admin.replacement-car-informs.store');
        $page_title = __('lang.create') . __('replacement_cars.page_title');
        return view('admin.replacement-car-informs.form', [
            'd' => $d,
            'page_title' => $page_title,
            'replacement_type_list' => $replacement_type_list,
            'replacement_job_type_list' => $replacement_job_type_list,
            'is_need_driver_list' => $is_need_driver_list,
            'is_need_slide_list' => $is_need_slide_list,
            'replacement_car_files' => [],
            'route_uri' => $route_uri,
            'mode' => MODE_CREATE
        ]);
    }

    public function store(Request $request)
    {
        __log($request->all());
        $this->authorize(Actions::Manage . '_' . Resources::ReplacementCarInform);
        $validator = Validator::make($request->all(), [
            'replacement_type' => 'required',
            'job_type' => 'required',
            'job_id' => 'required',
            'main_car_id' => 'required',
            'is_need_driver' => 'required',
            'is_need_slide' => 'required',
            'replacement_expect_place' => 'required',
            'customer_name' => 'required | max:255',
            'tel' => 'required | max:20',
        ], [], [
                'replacement_type' => __('replacement_cars.replacement_type'),
                'job_type' => __('replacement_cars.job_type'),
                'job_id' => __('replacement_cars.job_id'),
                'main_car_id' => __('replacement_cars.main_license_plate'),
                'is_need_driver' => __('replacement_cars.is_need_driver'),
                'is_need_slide' => __('replacement_cars.is_need_slide'),
                'replacement_expect_place' => __('replacement_cars.place'),
                'customer_name' => __('replacement_cars.customer_name'),
                'tel' => __('replacement_cars.tel'),
            ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $user = Auth::user();
        $replacement_car = ReplacementCar::firstOrNew(['id' => $request->id]);
        $replacement_car_count = DB::table('replacement_cars')->count() + 1;
        $prefix = 'RC-';
        $replacement_car->worksheet_no = generateRecordNumber($prefix, $replacement_car_count);
        $replacement_car->replacement_type = $request->replacement_type;
        $replacement_car->job_type = $request->job_type;
        $replacement_car->job_id = $request->job_id;
        $replacement_car->branch_id = $user ? $user->branch_id : null;
        $replacement_car->main_car_id = $request->main_car_id;
        $replacement_car->replacement_expect_date = $request->replacement_expect_date;
        $replacement_car->is_need_driver = filter_var($request->is_need_driver, FILTER_VALIDATE_BOOLEAN);
        $replacement_car->is_need_slide = filter_var($request->is_need_slide, FILTER_VALIDATE_BOOLEAN);
        $replacement_car->is_cust_receive_replace = filter_var($request->is_pickup_at_tls, FILTER_VALIDATE_BOOLEAN);
        $replacement_car->replacement_expect_place = $request->replacement_expect_place;
        $replacement_car->customer_name = $request->customer_name;
        $replacement_car->tel = $request->tel;
        $replacement_car->remark = $request->remark;
        $replacement_car->save();

        if ($request->documents__pending_delete_ids) {
            $pending_delete_ids = $request->documents__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $replacement_car->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                if ($file->isValid()) {
                    $replacement_car->addMedia($file)->toMediaCollection('replacement_car_documents');
                }
            }
        }
        $redirect_route = route('admin.replacement-car-informs.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(ReplacementCar $replacement_car_inform)
    {
        $this->authorize(Actions::View . '_' . Resources::ReplacementCarInform);
        $replacement_type_list = ReplacementCarTrait::getReplacementTypeList();
        $replacement_job_type_list = ReplacementCarTrait::getReplacementJobTypeList();
        $is_need_driver_list = ReplacementCarTrait::getIsNeedDriverList();
        $is_need_slide_list = ReplacementCarTrait::getIsNeedSlideList();
        $replacement_car_files = $replacement_car_inform->getMedia('replacement_car_documents');
        $replacement_car_files = get_medias_detail($replacement_car_files);
        $main_car = ReplacementCarTrait::getCarInfo($replacement_car_inform->main_car_id);
        $replacement_car = ReplacementCarTrait::getCarInfo($replacement_car_inform->replacement_car_id);
        $required_lower_spec = $replacement_car_inform->is_spec_low;
        $available_replacement_car = [];
        $route_uri = route('admin.replacement-car-informs.store');
        $page_title = __('lang.view') . __('replacement_cars.page_title');
        return view('admin.replacement-car-informs.form', [
            'd' => $replacement_car_inform,
            'page_title' => $page_title,
            'replacement_type_list' => $replacement_type_list,
            'replacement_job_type_list' => $replacement_job_type_list,
            'is_need_driver_list' => $is_need_driver_list,
            'is_need_slide_list' => $is_need_slide_list,
            'replacement_car_files' => $replacement_car_files,
            'main_car' => $main_car,
            'replacement_car' => $replacement_car,
            'mode' => MODE_VIEW,
            'route_uri' => $route_uri,
            'required_lower_spec' => $required_lower_spec,
            'available_replacement_car' => $available_replacement_car,
            'view' => true
        ]);
    }

    public function edit(ReplacementCar $replacement_car_inform)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ReplacementCarInform);
        if (!in_array($replacement_car_inform->status, [ReplacementCarStatusEnum::PENDING_INSPECT])) {
            abort(404);
        }
        $replacement_type_list = ReplacementCarTrait::getReplacementTypeList();
        $replacement_job_type_list = ReplacementCarTrait::getReplacementJobTypeList();
        $is_need_driver_list = ReplacementCarTrait::getIsNeedDriverList();
        $is_need_slide_list = ReplacementCarTrait::getIsNeedSlideList();
        $replacement_car_files = $replacement_car_inform->getMedia('replacement_car_documents');
        $replacement_car_files = get_medias_detail($replacement_car_files);
        $main_car = ReplacementCarTrait::getCarInfo($replacement_car_inform->main_car_id);
        $replace_car = ReplacementCarTrait::getCarInfo($replacement_car_inform->replacement_car_id);
        $required_lower_spec = $replacement_car_inform->is_spec_low;
        $route_uri = route('admin.replacement-car-informs.store');
        $page_title = __('lang.edit') . __('replacement_cars.page_title');
        return view('admin.replacement-car-informs.form', [
            'd' => $replacement_car_inform,
            'page_title' => $page_title,
            'replacement_type_list' => $replacement_type_list,
            'replacement_job_type_list' => $replacement_job_type_list,
            'is_need_driver_list' => $is_need_driver_list,
            'is_need_slide_list' => $is_need_slide_list,
            'replacement_car_files' => $replacement_car_files,
            'main_car' => $main_car,
            'replacement_car' => $replace_car,
            'mode' => MODE_UPDATE,
            'route_uri' => $route_uri,
            'required_lower_spec' => $required_lower_spec
        ]);
    }

    public function update(Request $request, $id)
    {
        abort(404);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ReplacementCarInform);
        $replacement_car = ReplacementCar::find($id);
        $replacement_car->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}