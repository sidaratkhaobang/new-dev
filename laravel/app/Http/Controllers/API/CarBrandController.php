<?php

namespace App\Http\Controllers\API;

use App\Models\CarBrand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CarBrandController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;
        $list = CarBrand::select(
            'car_brands.id',
            'car_brands.name',
            'car_brands.code',
        )
            ->when(!empty($s), function ($query) use ($s) {
                return $query->where(function ($q) use ($s) {
                    $q->where('car_brands.name', 'like', '%' . $s . '%');
                    $q->orWhere('car_brands.code', 'like', '%' . $s . '%');
                });
            })
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read(Request $request)
    {
        $data = CarBrand::select(
            'car_brands.id',
            'car_brands.name',
            'car_brands.code',
        )->where('car_brands.id', $request->id)
            ->first();
        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }
}
