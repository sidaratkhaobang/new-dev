<?php

namespace App\Http\Controllers\API\CarPark\V1;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;
        $list = Driver::select([
            'drivers.id',
            'drivers.name',
            'drivers.code',
            'drivers.emp_status',
            'positions.id as position_id',
            'positions.name as position_name',
        ])
            ->leftjoin('positions', 'positions.id', '=', 'drivers.position_id')
            ->when(!empty($s), function ($query) use ($s) {
                $query->where('drivers.name', 'like', '%' . $s . '%');
                $query->orWhere('drivers.name', 'like', '%' . $s . '%');
            })
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read($id)
    {
        $data = Driver::select([
            'drivers.id',
            'drivers.name',
            'drivers.code',
            'drivers.emp_status',
            'positions.id as position_id',
            'positions.name as position_name',
            'provinces.id as province_id',
            'provinces.name_th as province_name',
            'drivers.tel',
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
            'branches.id as branch_id',
            'branches.name as branch_name',
        ])
            ->leftjoin('positions', 'positions.id', '=', 'drivers.position_id')
            ->leftjoin('provinces', 'provinces.id', '=', 'drivers.province_id')
            ->leftjoin('branches', 'branches.id', '=', 'drivers.branch_id')
            ->when(!empty($id), function ($query) use ($id) {
                return $query->where(function ($q) use ($id) {
                    $q->orWhere('drivers.id', $id);
                    $q->orWhere('drivers.code', $id);
                });
            })
            ->first();
        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }
}
