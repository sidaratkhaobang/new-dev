<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchLocation;
use DateTime;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;
        $list = Branch::select('branches.id', 'branches.name', 'branches.lat', 'branches.lng')
            ->when(!empty($s), function ($query) use ($s) {
                return $query->where(function ($q) use ($s) {
                    $q->where('branches.name', 'like', '%' . $s . '%');
                });
            })
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read(Request $request)
    {
        $data_location = Branch::leftjoin('branches_locations', 'branches_locations.branch_id', '=', 'branches.id')
            ->leftjoin('locations', 'locations.id', '=', 'branches_locations.location_id')
            ->leftjoin('location_groups', 'location_groups.id', '=', 'branches_locations.location_group_id')
            ->where('branches.id', $request->id)
            ->select(
                'locations.name as location_name',
                'location_groups.name as location_group_name',
                'branches_locations.can_origin',
                'branches_locations.can_stopover',
                'branches_locations.can_destination'
            )->get();
        // dd($data_location);

        $data = Branch::where('branches.id', $request->id)
            ->select(
                'branches.id',
                'branches.name',
                'branches.is_main',
                'branches.open_time',
                'branches.close_time',
                'branches.tax_no',
                'branches.tel',
                'branches.email',
                'branches.address',
                'branches.lat',
                'branches.lng'
            )->get();
        $data->map(function ($item) use ($data_location) {
            $arr = [];
            foreach ($data_location as $data_lo) {
                $arr[] =
                    collect(
                        [
                            'location_name' => $data_lo->location_name,
                            'location_group_name' => $data_lo->location_group_name,
                            'can_origin' => $data_lo->can_origin,
                            'can_stopover' => $data_lo->can_stopover,
                            'can_destination' => $data_lo->can_destination,
                        ]
                    );
            }
            $item->location = $arr;
            return $item;
        });
        // dd($data);

        if (empty($data) || sizeof($data) <= 0) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }
}
