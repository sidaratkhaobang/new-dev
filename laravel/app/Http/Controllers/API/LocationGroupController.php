<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LocationGroup;
use App\Models\Province;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Enums\TransportationTypeEnum;

class LocationGroupController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;
        $list = LocationGroup::select('id', 'name')
            ->when(!empty($s), function ($query) use ($s) {
                return $query->where(function ($q) use ($s) {
                    $q->where('name', 'like', '%' . $s . '%');
                });
            })
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read(Request $request)
    {
        $data = LocationGroup::select('id', 'name')
            ->where('id', $request->id)
            ->first();
        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }
}
