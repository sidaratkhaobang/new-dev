<?php

namespace App\Http\Controllers\API\CarPark\V1;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;

use function response;

use const PER_PAGE;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;
        $list = Car::select([
            'cars.id',
            'cars.license_plate',
            'car_classes.id as car_class_id',
            'car_classes.name as car_class_name',
            'car_colors.id as car_colors_id',
            'car_colors.name as car_colors_name',
            'cars.status',
        ])
            ->leftjoin('car_colors', 'car_colors.id', '=', 'cars.car_color_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->when(!empty($s), function ($query) use ($s) {
                $query->where('cars.license_plate', 'like', '%' . $s . '%');
            })
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read($id)
    {
        $data = Car::select([
            'cars.id',
            'cars.license_plate',
            'car_classes.id as car_class_id',
            'car_classes.name as car_class_name',
            'car_colors.id as car_colors_id',
            'car_colors.name as car_colors_name',
            'cars.status',
        ])
            ->leftjoin('car_colors', 'car_colors.id', '=', 'cars.car_color_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->when(!empty($id), function ($query) use ($id) {
                return $query->where(function ($q) use ($id) {
                    $q->orWhere('cars.id', $id);
                    $q->orWhere('cars.license_plate', $id);
                });
            })
            ->first();
        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }
}
