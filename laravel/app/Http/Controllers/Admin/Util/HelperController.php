<?php

namespace App\Http\Controllers\Admin\Util;

use App\Models\Car;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HelperController extends Controller
{
    function carDetail(Request $request)
    {
        $car_id = $request->car_id;
        $car = get_car_detail($car_id);
        return response()->json([
            'success' => $car ? true : false,
            'data' => $car
        ]);
    }
}
