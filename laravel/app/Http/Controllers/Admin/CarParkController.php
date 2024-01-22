<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\CarParkStatusEnum;
use App\Enums\Resources;
use App\Enums\TransferTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarPark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CarParkController extends Controller
{
    public function addCarToParking(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ParkingZone);

        $validator = Validator::make($request->all(), [
            'car_id_outside' => 'required',
            'car_park_id_outside' => 'required',
        ], [], [
            'car_id_outside' => __('parking_lots.car_id'),
            'car_park_id_outside' => __('parking_lots.car_park_id'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $car_id_outside = $request->car_id_outside;
        $car_park_id_outside = $request->car_park_id_outside;

        $exists = CarPark::where('car_id', $car_id_outside)->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'รถอยู่ในช่องจอดแล้ว'
            ], 422);
        }

        $carpark = CarPark::where('id', $car_park_id_outside)->first();
        if ($carpark) {
            $carpark->car_id = $car_id_outside;
            $carpark->status = CarParkStatusEnum::USED;
            $carpark->save();
        }

        return response()->json([
            'success' => true,
            'data' => $request->all()
        ]);
    }

    public function removeCarFromParking(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ParkingZone);

        $validator = Validator::make($request->all(), [
            'car_id_inside' => 'required',
        ], [], [
            'car_id_inside' => __('parking_lots.car_id'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $car_id_inside = $request->car_id_inside;

        $exists = CarPark::where('car_id', $car_id_inside)->exists();
        if (!$exists) {
            return response()->json([
                'success' => false,
                'message' => 'รถไม่อยู่ในช่องจอด'
            ], 422);
        }

        $carpark = CarPark::where('car_id', $car_id_inside)->first();
        if ($carpark) {
            $carpark->car_id = null;
            $carpark->status = CarParkStatusEnum::FREE;
            $carpark->save();
        }

        return response()->json([
            'success' => true,
            'data' => $request->all()
        ]);
    }
}
