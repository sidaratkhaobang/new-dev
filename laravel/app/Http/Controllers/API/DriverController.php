<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Driver;
use App\Enums\EmployeeStatusEnum;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;

        $list = Driver::select(
            'drivers.id',
            'drivers.name',
            'drivers.code',
            'drivers.emp_status',
        )
            ->when(!empty($s), function ($query) use ($s) {
                return $query->where(function ($q) use ($s) {
                    $q->where('drivers.name', 'like', '%' . $s . '%');
                    $q->orWhere('drivers.code', 'like', '%' . $s . '%');
                });
            })
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read(Request $request)
    {
        $data = Driver::leftjoin('branches', 'branches.id', '=', 'drivers.branch_id')
            ->select(
            'drivers.id',
            'drivers.name',
            'drivers.code',
            'drivers.branch_id',
            'branches.name as branch_name',
            'drivers.emp_status',
            'drivers.position_id',
            'drivers.tel',
            'drivers.phone',
            'drivers.citizen_id',
            'drivers.start_working_time',
            'drivers.end_working_time',
            'drivers.working_day_mon',
            'drivers.working_day_tue',
            'drivers.working_day_wed',
            'drivers.working_day_thu',
            'drivers.working_day_fri',
            'drivers.working_day_sat',
            'drivers.working_day_sun',
        )
            ->where('drivers.id', $request->id)
            ->first();
        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        $data->profile_image_url = $data->profile_url;
        $data = $data->makeHidden('media');
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'emp_status' => ['required', Rule::in([EmployeeStatusEnum::FULL_TIME, EmployeeStatusEnum::PART_TIME, EmployeeStatusEnum::CONTRACT, EmployeeStatusEnum::NOT_SPECIFIED])],
            'citizen_id' => ['required'],
        ], [], [
            'name' => __('drivers.name'),
            'emp_status' => __('drivers.emp_status'),
            'citizen_id' => __('drivers.citizen_id'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $driver = new Driver;
        $driver->name = $request->name;
        $driver->code = $request->code;
        $driver->emp_status = $request->emp_status;
        $driver->tel = $request->tel;
        $driver->phone = $request->phone;
        $driver->citizen_id = $request->citizen_id;
        $driver->start_working_time = $request->start_working_time;
        $driver->end_working_time = $request->end_working_time;
        $driver->working_day_mon = boolval($request->working_day_mon);
        $driver->working_day_tue = boolval($request->working_day_tue);
        $driver->working_day_wed = boolval($request->working_day_wed);
        $driver->working_day_thu = boolval($request->working_day_thu);
        $driver->working_day_fri = boolval($request->working_day_fri);
        $driver->working_day_sat = boolval($request->working_day_sat);
        $driver->working_day_sun = boolval($request->working_day_sun);
        $driver->status = STATUS_ACTIVE;
        $driver->save();
        return $this->responseWithCode(true, DATA_SUCCESS, $driver->id, 201);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required'],
            'emp_status' => ['nullable', Rule::in([EmployeeStatusEnum::FULL_TIME, EmployeeStatusEnum::PART_TIME, EmployeeStatusEnum::CONTRACT, EmployeeStatusEnum::NOT_SPECIFIED])],
            'position_id' => ['nullable', 'exists:positions,id']
        ], [], [
            'id' => __('drivers.id'),
            'emp_status' => __('drivers.emp_status'),
            'position_id' => __('drivers.position'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $driver = Driver::find($request->id);
        if (empty($driver)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        $driver->fill($request->all());
        $driver->save();

        return $this->responseWithCode(true, DATA_SUCCESS, $driver->id, 200);
    }

    public function destroy($id)
    {
        $customer = Driver::find($id);
        if (empty($customer)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        $customer->save();
        $customer->delete();
        return $this->responseWithCode(true, DATA_SUCCESS, $customer->id, 200);
    }
}
