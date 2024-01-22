<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\AmountTypeEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\Driver;
use App\Models\Position;
use App\Models\DriverDrivingSkills;
use App\Models\DriverWageRelation;
use App\Enums\EmployeeStatusEnum;
use App\Enums\Resources;
use App\Enums\WageCalType;
use App\Models\Branch;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Driver);
        $query_driver_driving_skill = DB::table('drivers_driving_skills')
            ->leftJoin('driving_skills', 'driving_skills.id', '=', 'drivers_driving_skills.driving_skill_id')
            ->select('drivers_driving_skills.driver_id', DB::raw("group_concat(driving_skills.name SEPARATOR ', ') as driving_skill_name"))
            ->groupBy('drivers_driving_skills.driver_id');

        $list = Driver::leftJoin('provinces', 'provinces.id', '=', 'drivers.province_id')
            ->leftJoin('positions', 'positions.id', '=', 'drivers.position_id')
            ->leftJoin('drivers_driving_skills as drivers_driving_skill_join', 'drivers_driving_skill_join.driver_id', '=', 'drivers.id')
            ->sortable(['created_at' => 'desc'])
            ->leftjoinSub($query_driver_driving_skill, 'drivers_driving_skill', function ($join) {
                $join->on('drivers_driving_skill.driver_id', '=', 'drivers.id');
            })
            ->select(
                'drivers.id',
                'drivers.name',
                'drivers.code',
                'drivers.emp_status',
                'provinces.name_th as province',
                'drivers_driving_skill.driving_skill_name as driving_skill_name'
            )
            ->groupBy(
                'drivers.id',
                'drivers.name',
                'drivers.code',
                'drivers.emp_status',
                'province',
                'drivers_driving_skill.driving_skill_name'
            )
            ->search($request->s)
            ->paginate(PER_PAGE);

        return view('admin.drivers.index', [
            's' => $request->s,
            'list' => $list,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::Driver);
        $d = new Driver();
        $d->status = STATUS_ACTIVE;
        $status_list = $this->getStatusList();
        $emp_status_list = $this->getEmpStatusList();
        $position_list = $this->getPositionList();
        $days = getDayCollection();
        $branch_list = Branch::select('name', 'id')->get();
        $province_name = null;
        $working_day_arr = [];
        $citizen_files = [];
        $driver_wage_list = DB::table('driver_wages')
            ->leftJoin('driver_wage_categories', 'driver_wage_categories.id', '=', 'driver_wages.driver_wage_category_id')
            ->leftJoin('service_types', 'service_types.id', '=', 'driver_wages.service_type_id')
            ->select(
                'driver_wages.id as driver_wage_id',
                'driver_wages.name as driver_wage_name',
                'driver_wage_categories.name as driver_wage_category_name',
                'driver_wage_categories.id as driver_wage_category_id',
                'driver_wages.wage_cal_type',
                'service_types.name as service_type_text',
                'service_types.service_type as service_type_id'
            )
            ->where('driver_wages.is_standard', STATUS_ACTIVE)->get();
        $driver_wage_list->map(function ($item) {
            $item->driver_wage_text = ($item->driver_wage_name) ? $item->driver_wage_name : '';
            $item->driver_wage_category_text = ($item->driver_wage_category_name) ? $item->driver_wage_category_name : '';
            if ($item->wage_cal_type == WageCalType::PER_MONTH) {
                $item->wage_cal_type_text  = 'ต่อเดือน';
            } elseif ($item->wage_cal_type == WageCalType::PER_DAY) {
                $item->wage_cal_type_text  = 'ต่อวัน';
            } elseif ($item->wage_cal_type == WageCalType::PER_HOUR) {
                $item->wage_cal_type_text  = 'ต่อชั่วโมง';
            } elseif ($item->wage_cal_type == WageCalType::PER_TRIP) {
                $item->wage_cal_type_text  = 'ต่อเที่ยว';
            }
            $item->amount  = 0;
            $item->amount_type  = AmountTypeEnum::BAHT;

            return $item;
        });


        $page_title = __('lang.create') . __('drivers.page_title');
        return view('admin.drivers.form', [
            'd' => $d,
            'page_title' => $page_title,
            'status_list' => $status_list,
            'emp_status_list' => $emp_status_list,
            'position_list' => $position_list,
            'days' => $days,
            'province_name' => $province_name,
            'working_day_arr' => $working_day_arr,
            'citizen_files' => $citizen_files,
            'driver_wage_list' => $driver_wage_list,
            'branch_list' => $branch_list,
            'profile_image' => [],
        ]);
    }

    public function edit(Driver $driver)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Driver);
        $status_list = $this->getStatusList();
        $emp_status_list = $this->getEmpStatusList();
        $position_list = $this->getPositionList();
        $days = getDayCollection();
        $province_name = ($driver->province) ? $driver->province->name_th : null;
        $working_day_arr = [];
        foreach ($days as $day) {
            $working_day =  'working_day_' . $day['value'];
            if ($driver->$working_day == STATUS_ACTIVE) {
                array_push($working_day_arr, $day['value']);
            }
        }
        $branch_list = Branch::select('name', 'id')->get();
        $citizen_file = $driver->getMedia('citizen_file');
        $citizen_files = get_medias_detail($citizen_file);
        $profile_image = $driver->getMedia('profile_image');
        $profile_image = get_medias_detail($profile_image);
        $driver_skill_list = $this->getDriverDrivingSkillList($driver);
        $driver_wage_list = $this->getDriverWageList($driver->id);
        $page_title = __('lang.edit') . __('drivers.page_title');
        return view('admin.drivers.form', [
            'd' => $driver,
            'page_title' => $page_title,
            'status_list' => $status_list,
            'emp_status_list' => $emp_status_list,
            'position_list' => $position_list,
            'days' => $days,
            'province_name' => $province_name,
            'working_day_arr' => $working_day_arr,
            'citizen_files' => $citizen_files,
            'profile_image' => $profile_image,
            'driver_skill_list' => $driver_skill_list,
            'driver_wage_list' => $driver_wage_list,
            'branch_list' => $branch_list,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Driver);
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('drivers', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'code' => [
                'required', 'string', 'max:10'
            ],
            'branch' => ['required'],
            'emp_status' => ['required'],
            'citizen_id' => ['required'],
            'working_day_arr' => ['required'],
            'start_working_time' => ['required'],
            'end_working_time' => ['required'],
            'phone' => ['nullable', 'string', 'max:20'],
            'tel' => ['nullable', 'string', 'max:20'],
        ], [], [
            'name' => __('drivers.name'),
            'code' => __('drivers.code'),
            'branch' => __('drivers.branch'),
            'emp_status' => __('drivers.emp_status'),
            'citizen_id' => __('drivers.citizen_id'),
            'working_day_arr' => __('drivers.working_day'),
            'start_working_time' => __('drivers.start_working_time'),
            'end_working_time' => __('drivers.end_working_time'),
            'phone' => __('drivers.phone'),
            'tel' => __('drivers.tel'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $driver = Driver::firstOrNew(['id' => $request->id]);
        $driver->name = $request->name;
        $driver->code = $request->code;
        $driver->branch_id = $request->branch;
        $driver->emp_status = $request->emp_status;
        $driver->position_id = $request->position_id;
        $driver->province_id = $request->province_id;
        $driver->citizen_id = $request->citizen_id;
        $driver->tel = $request->tel;
        $driver->phone = $request->phone;

        if ($request->working_day_arr) {
            $driver->working_day_mon = STATUS_DEFAULT;
            $driver->working_day_tue = STATUS_DEFAULT;
            $driver->working_day_wed = STATUS_DEFAULT;
            $driver->working_day_thu = STATUS_DEFAULT;
            $driver->working_day_fri = STATUS_DEFAULT;
            $driver->working_day_sat = STATUS_DEFAULT;
            $driver->working_day_sun = STATUS_DEFAULT;
            foreach ($request->working_day_arr as $key => $value) {
                $working_day =  'working_day_' . $value;
                $driver->$working_day = STATUS_ACTIVE;
            }
        }

        $driver->start_working_time = $request->start_working_time;
        $driver->end_working_time = $request->end_working_time;
        $driver->status = $request->status;
        $driver->save();

        if ($request->citizen_file__pending_delete_ids) {
            $pending_delete_ids = $request->citizen_file__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $driver->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('citizen_file')) {
            foreach ($request->file('citizen_file') as $image) {
                if ($image->isValid()) {
                    $driver->addMedia($image)->toMediaCollection('citizen_file');
                }
            }
        }

        if ($request->profile_image__pending_delete_ids) {
            $pending_delete_ids = $request->profile_image__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $driver->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('profile_image')) {
            foreach ($request->file('profile_image') as $image) {
                if ($image->isValid()) {
                    $driver->addMedia($image)->toMediaCollection('profile_image');
                }
            }
        }

        if ($driver->id) {
            $driver_driving_skills = $this->saveDriverDrivingSkill($request, $driver);
            $driver_wage_relation = $this->saveDriverWageRelation($request, $driver);
        }

        $redirect_route = route('admin.drivers.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    private function saveDriverDrivingSkill($request, $driver_model)
    {
        $delete_driver_skill_ids = $request->delete_driver_skill_ids;
        if ((!empty($delete_driver_skill_ids)) && (is_array($delete_driver_skill_ids))) {
            foreach ($delete_driver_skill_ids as $delete_id) {
                $driver_skill_delete = DriverDrivingSkills::find($delete_id);
                $driving_license_medias = $driver_skill_delete->getMedia('driver_skill');
                foreach ($driving_license_medias as $driving_license_media) {
                    $driving_license_media->delete();
                }
                $driver_skill_delete->delete();
            }
        }

        $pending_delete_skill_files = $request->skill_file__pending_delete_ids;
        if (!empty($request->driver_skill)) {
            foreach ($request->driver_skill as $key => $request_driver_skill) {
                $driver_skill = DriverDrivingSkills::firstOrNew(['id' => $request_driver_skill['id']]);
                if (!$driver_skill->exists) {
                    //
                }
                $driver_skill->driver_id = $driver_model->id;
                $driver_skill->driving_skill_id = $request_driver_skill['driving_skill_id'];
                $driver_skill->save();

                // delete file driver skill
                if ((!empty($pending_delete_skill_files)) && (sizeof($pending_delete_skill_files) > 0)) {
                    foreach ($pending_delete_skill_files as $skill_media_id) {
                        $skill_media = Media::find($skill_media_id);
                        if ($skill_media && $skill_media->model_id) {
                            $skill_model = DriverDrivingSkills::find($skill_media->model_id);
                            $skill_model->deleteMedia($skill_media->id);
                        }
                    }
                }

                // insert + update driver skill
                if ((!empty($request->driver_skill_file)) && (sizeof($request->driver_skill_file) > 0)) {
                    foreach ($request->driver_skill_file as $table_row_index => $driver_skill_files) {
                        foreach ($driver_skill_files as $driver_skill_file) {
                            if ($driver_skill_file->isValid() && (strcmp($table_row_index, 'table_row_' . $key) == 0)) {
                                $driver_skill->addMedia($driver_skill_file)->toMediaCollection('driver_skill');
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    private function saveDriverWageRelation($request, $driver_model)
    {
        DriverWageRelation::where('driver_id', $driver_model->id)->delete();
        if (!empty($request->driver_wage)) {
            foreach ($request->driver_wage as $driver_wage) {
                $driver_wage_relation = new DriverWageRelation();
                $driver_wage_relation->driver_id = $driver_model->id;
                $driver_wage_relation->driver_wage_id = $driver_wage['driver_wage_id'];
                $driver_wage_relation->amount = $driver_wage['amount'];
                $driver_wage_relation->amount_type = $driver_wage['amount_type'];
                $driver_wage_relation->save();
            }
        }
        return true;
    }

    public function getDriverDrivingSkillList($driver_model)
    {
        $driver_skill_list = DriverDrivingSkills::where('driver_id', $driver_model->id)->get();
        $driver_skill_list->map(function ($item) {
            $item->driving_skill_id = ($item->driving_skill_id) ? $item->driving_skill_id : '';
            $item->driving_skill_text = ($item->driving_skill) ? $item->driving_skill->name : '';
            //// get driver license files
            $driver_skill_medias = $item->getMedia('driver_skill');
            $skill_files = get_medias_detail($driver_skill_medias);
            $skill_files = collect($skill_files)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $item->skill_files = $skill_files;
            $item->pending_delete_skill_files = [];

            return $item;
        });

        return $driver_skill_list;
    }

    public function getDriverWageList($driver_id)
    {
        $driver_wage_list = DriverWageRelation::where('driver_id', $driver_id)->get();
        $driver_wage_list->map(function ($item) {
            $item->service_type_id = $item->driver_wage?->service_type?->service_type;
            $item->service_type_text = $item->driver_wage?->service_type?->name;
            $item->driver_wage_text = ($item->driver_wage) ? $item->driver_wage->name : '';
            $item->driver_wage_category_text = $item->driver_wage && $item->driver_wage->driver_wage_category ? $item->driver_wage->driver_wage_category->name : '';
            if ($item->driver_wage->wage_cal_type == WageCalType::PER_MONTH) {
                $item->wage_cal_type_text  = 'ต่อเดือน';
            } elseif ($item->driver_wage->wage_cal_type == WageCalType::PER_DAY) {
                $item->wage_cal_type_text  = 'ต่อวัน';
            } elseif ($item->driver_wage->wage_cal_type == WageCalType::PER_HOUR) {
                $item->wage_cal_type_text  = 'ต่อชั่วโมง';
            } elseif ($item->driver_wage->wage_cal_type == WageCalType::PER_TRIP) {
                $item->wage_cal_type_text  = 'ต่อเที่ยว';
            }
            $item->amount  = ($item->amount) ? $item->amount : '';
            return $item;
        });

        return $driver_wage_list;
    }

    public function show(Driver $driver)
    {
        $this->authorize(Actions::View . '_' . Resources::Driver);
        $status_list = $this->getStatusList();
        $emp_status_list = $this->getEmpStatusList();
        $position_list = $this->getPositionList();
        $days = getDayCollection();
        $province_name = ($driver->province) ? $driver->province->name_th : null;
        $working_day_arr = [];
        foreach ($days as $day) {
            $working_day =  'working_day_' . $day['value'];
            if ($driver->$working_day == STATUS_ACTIVE) {
                array_push($working_day_arr, $day['value']);
            }
        }
        $branch_list = Branch::select('name', 'id')->get();
        $citizen_file = $driver->getMedia('citizen_file');
        $citizen_files = get_medias_detail($citizen_file);
        $profile_image = $driver->getMedia('profile_image');
        $profile_image = get_medias_detail($profile_image);
        $driver_skill_list = $this->getDriverDrivingSkillList($driver);
        $driver_wage_list = $this->getDriverWageList($driver->id);

        $page_title = __('lang.view') . __('drivers.page_title');
        return view('admin.drivers.form', [
            'd' => $driver,
            'page_title' => $page_title,
            'status_list' => $status_list,
            'emp_status_list' => $emp_status_list,
            'position_list' => $position_list,
            'days' => $days,
            'province_name' => $province_name,
            'working_day_arr' => $working_day_arr,
            'citizen_files' => $citizen_files,
            'profile_image' => $profile_image,
            'view' => true,
            'driver_skill_list' => $driver_skill_list,
            'driver_wage_list' => $driver_wage_list,
            'branch_list' => $branch_list,
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Driver);
        $driver = Driver::find($id);
        $driver->delete();

        return $this->responseComplete();
    }

    private function getStatusList()
    {
        return collect([
            [
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => __('lang.status_' . STATUS_ACTIVE),
            ],
            [
                'id' => STATUS_INACTIVE,
                'value' => STATUS_INACTIVE,
                'name' => __('lang.status_' . STATUS_INACTIVE),
            ],
        ]);
    }

    private function getEmpStatusList()
    {
        return collect([
            (object)[
                'id' => EmployeeStatusEnum::FULL_TIME,
                'value' => EmployeeStatusEnum::FULL_TIME,
                'name' => __('drivers.emp_status_' . EmployeeStatusEnum::FULL_TIME),
            ],
            (object)[
                'id' => EmployeeStatusEnum::PART_TIME,
                'value' => EmployeeStatusEnum::PART_TIME,
                'name' => __('drivers.emp_status_' . EmployeeStatusEnum::PART_TIME),
            ],
            (object)[
                'id' => EmployeeStatusEnum::CONTRACT,
                'value' => EmployeeStatusEnum::CONTRACT,
                'name' => __('drivers.emp_status_' . EmployeeStatusEnum::CONTRACT),
            ],
            (object)[
                'id' => EmployeeStatusEnum::NOT_SPECIFIED,
                'value' => EmployeeStatusEnum::NOT_SPECIFIED,
                'name' => __('drivers.emp_status_' . EmployeeStatusEnum::NOT_SPECIFIED),
            ],
        ]);
    }

    public function getPositionList()
    {
        $list = Position::select('positions.id', 'positions.name')->get();

        return $list;
    }

    function getDefaultDriverWage(Request $request)
    {
        $driver_wage_id = $request->driver_wage_id;
        $data = DB::table('driver_wages')
            ->leftJoin('driver_wage_categories', 'driver_wage_categories.id', '=', 'driver_wages.driver_wage_category_id')
            ->leftJoin('service_types', 'service_types.id', '=', 'driver_wages.service_type_id')
            ->select(
                'driver_wages.id as driver_wage_id',
                'driver_wages.name as driver_wage_name',
                'driver_wage_categories.name as driver_wage_category_name',
                'driver_wage_categories.id as driver_wage_category_id',
                'driver_wages.wage_cal_type',
                'service_types.name as service_type_name',
                'service_types.service_type as service_type_id'
            )
            ->where('driver_wages.id', $driver_wage_id)
            ->get()->map(function ($item) {
                $item->driver_wage_text = ($item->driver_wage_name) ? $item->driver_wage_name : '';
                $item->driver_wage_category_text = ($item->driver_wage_category_name) ? $item->driver_wage_category_name : '';
                if ($item->wage_cal_type == WageCalType::PER_MONTH) {
                    $item->wage_cal_type_text  = 'ต่อเดือน';
                } elseif ($item->wage_cal_type == WageCalType::PER_DAY) {
                    $item->wage_cal_type_text  = 'ต่อวัน';
                } elseif ($item->wage_cal_type == WageCalType::PER_HOUR) {
                    $item->wage_cal_type_text  = 'ต่อชั่วโมง';
                } elseif ($item->wage_cal_type == WageCalType::PER_TRIP) {
                    $item->wage_cal_type_text  = 'ต่อเที่ยว';
                }

                return $item;
            });
        return [
            'success' => true,
            'driver_wage_id' => $request->driver_wage_id,
            'data' => $data
        ];
    }
}
