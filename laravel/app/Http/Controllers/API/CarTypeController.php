<?php

namespace App\Http\Controllers\API;

use App\Models\CarType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CarTypeController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;
        $car_brand_id = $request->car_brand_id;
        $list = CarType::leftJoin('car_brands', 'car_brands.id', '=', 'car_types.car_brand_id')
            ->select(
                'car_types.id',
                'car_types.name',
                'car_types.code',
                'car_brands.id as car_brand_id',
                'car_brands.name as car_brand_name',
            )
            ->when(!empty($s), function ($query) use ($s) {
                return $query->where(function ($q) use ($s) {
                    $q->where('car_types.name', 'like', '%' . $s . '%');
                    $q->orWhere('car_types.code', 'like', '%' . $s . '%');
                });
            })
            ->when(!empty($car_brand_id), function ($query) use ($car_brand_id) {
                return $query->where('car_types.car_brand_id', $car_brand_id);
            })
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read(Request $request)
    {
        $data = CarType::leftJoin('car_brands', 'car_brands.id', '=', 'car_types.car_brand_id')
            ->select(
                'car_types.id',
                'car_types.name',
                'car_types.code',
                'car_brands.id as car_brand_id',
                'car_brands.name as car_brand_name',
            )
            ->where('car_types.id', $request->id)
            ->first();
        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }
}
