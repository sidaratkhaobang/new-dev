<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\LocationGroup;
use App\Models\Province;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Enums\TransportationTypeEnum;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;
        $list = Location::select('locations.id', 'locations.name', 'location_groups.id as location_group_id', 'location_groups.name as location_group_name', 'provinces.id as province_id', 'provinces.name_th as province_name')
            ->leftjoin('location_groups', 'location_groups.id', '=', 'locations.location_group_id')
            ->leftJoin('provinces', 'provinces.id', '=', 'locations.province_id')
            ->when(!empty($s), function ($query) use ($s) {
                return $query->where(function ($q) use ($s) {
                    $q->where('locations.name', 'like', '%' . $s . '%');
                    $q->orWhere('location_groups.name', 'like', '%' . $s . '%');
                    $q->orWhere('provinces.name_th', 'like', '%' . $s . '%');
                });
            })
            ->when(!empty($request->province_id), function ($query) use ($request) {
                return $query->where('provinces.id', $request->province_id);
            })
            ->when(!empty($request->location_group_id), function ($query) use ($request) {
                return $query->where('location_groups.id', $request->location_group_id);
            })
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read(Request $request)
    {
        $data = Location::select('locations.id', 'locations.name', 'location_groups.id as location_group_id', 'location_groups.name as location_group_name', 'provinces.id as province_id', 'provinces.name_th as province_name', 'locations.lat', 'locations.lng')
            ->leftjoin('location_groups', 'location_groups.id', '=', 'locations.location_group_id')
            ->leftJoin('provinces', 'provinces.id', '=', 'locations.province_id')
            ->where('locations.id', $request->id)
            ->first();
        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }
}
