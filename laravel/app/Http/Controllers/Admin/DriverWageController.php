<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use App\Models\DriverWageCategory;
use App\Models\DriverWage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Enums\WageCalType;
use App\Enums\WageCalDay;
use App\Enums\WageCalTime;
use App\Models\DriverWageMapping;

class DriverWageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::DriverWage);
        $list = DriverWage::select(['driver_wages.id', 'driver_wages.name', 'driver_wage_categories.status', 'driver_wage_categories.name as driver_wage_category_name', 'service_types.name as service_type_name'])
            ->leftjoin('driver_wage_categories', 'driver_wage_categories.id', '=', 'driver_wages.driver_wage_category_id')
            ->leftjoin('service_types', 'service_types.id', '=', 'driver_wages.service_type_id')
            ->sortable('name')
            ->search($request->s)
            ->paginate(PER_PAGE);
        return view('admin.driver-wages.index', [
            'list' => $list,
            's' => $request->s,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::DriverWage);
        $d = new DriverWage();
        $d->is_standard = STATUS_ACTIVE;
        $d->wage_cal_type = WageCalType::PER_MONTH;
        $d->wage_cal_day = WageCalDay::ALL;
        $d->wage_cal_time = WageCalTime::ALL;
        $d->status = STATUS_ACTIVE;
        $d->is_special_wage = STATUS_DEFAULT;
        $wage_category_list = DriverWageCategory::select('name as name', 'id')->where('status', STATUS_ACTIVE)->get();
        $service_type_list = ServiceType::statusActive()->get();
        $wage_list = DriverWage::select('name', 'id')->orderBy('name')->get();
        $listType = $this->getListType();
        $listStatus = $this->getListStatus();
        $listWageType = $this->getListWageType();
        $listWageDay = $this->getListWageDay();
        $listWageTime = $this->getListWageTime();
        $listSpecialWageStatus = $this->getSpecialWageStatus();
        $wage = [];
        $page_title = __('lang.create') . __('driver_wages.page_title');
        return view('admin.driver-wages.form', [
            'd' => $d,
            'page_title' => $page_title,
            'listType' => $listType,
            'listStatus' => $listStatus,
            'listWageType' => $listWageType,
            'listWageDay' => $listWageDay,
            'listWageTime' => $listWageTime,
            'listSpecialWageStatus' => $listSpecialWageStatus,
            'wage_category_list' => $wage_category_list,
            'service_type_list' => $service_type_list,
            'wage_list' => $wage_list,
            'wage' => $wage,
        ]);
    }

    private function getSpecialWageStatus()
    {
        return collect([
            [
                'id' => STATUS_DEFAULT,
                'value' => STATUS_DEFAULT,
                'name' => __('driver_wages.status_no_' . STATUS_DEFAULT),
            ],
            [
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => __('driver_wages.status_yes_' . STATUS_ACTIVE),
            ],
        ]);
    }

    private function getListType()
    {
        return collect([
            [
                'id' => 'active',
                'value' => 1,
                'name' => __('driver_wages.status_' . STATUS_ACTIVE),
            ],
            [
                'id' => 'inactive',
                'value' => STATUS_DEFAULT,
                'name' => __('driver_wages.status_' . STATUS_DEFAULT),
            ],
        ]);
    }

    private function getListWageType()
    {
        return collect([
            [
                'id' => WageCalType::PER_MONTH,
                'value' => WageCalType::PER_MONTH,
                'name' => __('driver_wages.status_' . WageCalType::PER_MONTH),
            ],
            [
                'id' => WageCalType::PER_DAY,
                'value' => WageCalType::PER_DAY,
                'name' => __('driver_wages.status_' . WageCalType::PER_DAY),
            ],
            [
                'id' => WageCalType::PER_HOUR,
                'value' => WageCalType::PER_HOUR,
                'name' => __('driver_wages.status_' . WageCalType::PER_HOUR),
            ],
            [
                'id' => WageCalType::PER_TRIP,
                'value' => WageCalType::PER_TRIP,
                'name' => __('driver_wages.status_' . WageCalType::PER_TRIP),
            ],
        ]);
    }

    private function getListWageDay()
    {
        return collect([
            [
                'id' => WageCalDay::ALL,
                'value' => WageCalDay::ALL,
                'name' => __('driver_wages.status_' . WageCalDay::ALL),
            ],
            [
                'id' => WageCalDay::WORK_DAY,
                'value' => WageCalDay::WORK_DAY,
                'name' => __('driver_wages.status_' . WageCalDay::WORK_DAY),
            ],
            [
                'id' => WageCalDay::HOLIDAY,
                'value' => WageCalDay::HOLIDAY,
                'name' => __('driver_wages.status_' . WageCalDay::HOLIDAY),
            ],

        ]);
    }

    private function getListWageTime()
    {
        return collect([
            [
                'id' => WageCalTime::ALL,
                'value' => WageCalTime::ALL,
                'name' => __('driver_wages.status_' . WageCalTime::ALL),
            ],
            [
                'id' => WageCalTime::WORK_TIME,
                'value' => WageCalTime::WORK_TIME,
                'name' => __('driver_wages.status_' . WageCalTime::WORK_TIME),
            ],
            [
                'id' => WageCalTime::OUT_OF_WORK_TIME,
                'value' => WageCalTime::OUT_OF_WORK_TIME,
                'name' => __('driver_wages.status_' . WageCalTime::OUT_OF_WORK_TIME),
            ],

        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::DriverWage);
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('driver_wages', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'driver_wage_category_id' => [
                'required'
            ],
            'seq' => [
                'required'
            ],
            'is_standard' => [
                'required'
            ],
            'wage_cal_type' => [
                'required'
            ],
            'wage_cal_day' => [
                'required'
            ],
            'wage_cal_time' => [
                'required'
            ],
            'status' => [
                'required'
            ],
            'is_special_wage' => [
                'required'
            ],
            'wage_list' => [
                Rule::when($request->is_special_wage == STATUS_ACTIVE, ['required']),
            ],


        ], [], [
            'name' => __('driver_wages.name'),
            'driver_wage_category_id' => __('driver_wages.wage_type'),
            'seq' => __('driver_wages.seq'),
            'is_standard' => __('driver_wages.type'),
            'wage_cal_type' => __('driver_wages.wage_type_cal'),
            'wage_cal_day' => __('driver_wages.wage_day_cal'),
            'wage_cal_time' => __('driver_wages.wage_time_cal'),
            'status' => __('driver_wages.status'),
            'is_special_wage' => __('driver_wages.special_wage'),
            'wage_list' => __('driver_wages.special_wage_cal'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $driver_wages = DriverWage::firstOrNew(['id' => $request->id]);
        $driver_wages->name = $request->name;
        $driver_wages->service_type_id = $request->service_type_id;
        $driver_wages->driver_wage_category_id = $request->driver_wage_category_id;
        $driver_wages->seq = $request->seq;
        $driver_wages->is_standard = $request->is_standard;
        $driver_wages->wage_cal_type = $request->wage_cal_type;
        $driver_wages->wage_cal_day = $request->wage_cal_day;
        $driver_wages->wage_cal_time = $request->wage_cal_time;
        $driver_wages->status = $request->status;
        $driver_wages->is_special_wage = $request->is_special_wage;
        // $driver_wages->wage_category = $request->wage_category;
        $driver_wages->save();

        if ($driver_wages->id) {
            $driver_wages_relation = $this->saveDriverWageRelation($request, $driver_wages->id);
        }

        $redirect_route = route('admin.driver-wages.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    private function saveDriverWageRelation($request, $driver_wage_id)
    {
        DriverWageMapping::where('driver_wage_id', $driver_wage_id)->delete();
        if (!empty($request->wage_list)) {
            foreach ($request->wage_list as $wage_list) {
                $driving_skill_relation = new DriverWageMapping();
                $driving_skill_relation->driver_wage_id = $driver_wage_id;
                $driving_skill_relation->driver_wage_map_id = $wage_list;
                $driving_skill_relation->save();
            }
        }
        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(DriverWage $driver_wage)
    {
        $this->authorize(Actions::View . '_' . Resources::DriverWage);
        $page_title = __('lang.view') . __('driver_wages.page_title');
        $listStatus = $this->getListStatus();
        $wage_category_list = DriverWageCategory::select('name as name', 'id')->get();
        $service_type_list = ServiceType::statusActive()->get();
        $wage_list = DriverWage::select('name', 'id')->orderBy('name')->get();
        $listType = $this->getListType();
        $listWageType = $this->getListWageType();
        $listWageDay = $this->getListWageDay();
        $listWageTime = $this->getListWageTime();
        $listSpecialWageStatus = $this->getSpecialWageStatus();
        $wage = $this->getDriverWageArray($driver_wage->id);
        //        dd($driver_wage,$listSpecialWageStatus);
        return view('admin.driver-wages.form', [
            'd' => $driver_wage,
            'view' => true,
            'page_title' => $page_title,
            'listStatus' => $listStatus,
            'listType' => $listType,
            'listWageType' => $listWageType,
            'listWageDay' => $listWageDay,
            'listWageTime' => $listWageTime,
            'listSpecialWageStatus' => $listSpecialWageStatus,
            'wage_category_list' => $wage_category_list,
            'service_type_list' => $service_type_list,
            'wage_list' => $wage_list,
            'wage' => $wage

        ]);
    }

    public function getDriverWageArray($driver_wage_id)
    {
        return DriverWageMapping::leftJoin('driver_wages', 'driver_wages.id', '=', 'driver_wages_mapping.driver_wage_map_id')
            ->select('driver_wages.id as id', 'driver_wages.name as name')
            ->where('driver_wages_mapping.driver_wage_id', $driver_wage_id)
            ->pluck('driver_wages.id')
            ->toArray();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(DriverWage $driver_wage)
    {
        $this->authorize(Actions::Manage . '_' . Resources::DriverWage);
        $page_title = __('lang.edit') . __('driver_wages.page_title');
        $listStatus = $this->getListStatus();
        $wage_category_list = DriverWageCategory::select('name as name', 'id')->get();
        $service_type_list = ServiceType::statusActive()->get();
        $wage_list = DriverWage::select('name', 'id')->orderBy('name')->get();
        $listType = $this->getListType();
        $listWageType = $this->getListWageType();
        $listWageDay = $this->getListWageDay();
        $listWageTime = $this->getListWageTime();
        $listSpecialWageStatus = $this->getSpecialWageStatus();
        $wage = $this->getDriverWageArray($driver_wage->id);
        return view('admin.driver-wages.form', [
            'd' => $driver_wage,
            'page_title' => $page_title,
            'listStatus' => $listStatus,
            'listType' => $listType,
            'listWageType' => $listWageType,
            'listWageDay' => $listWageDay,
            'listWageTime' => $listWageTime,
            'listSpecialWageStatus' => $listSpecialWageStatus,
            'wage_category_list' => $wage_category_list,
            'service_type_list' => $service_type_list,
            'wage_list' => $wage_list,
            'wage' => $wage

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::DriverWage);
        $driver_wages = DriverWage::find($id);
        $driver_wages->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
