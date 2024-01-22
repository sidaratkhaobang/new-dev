<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CarParkTransfer;
use App\Models\CarPark;
use App\Models\CarParkTransferLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Enums\TransferTypeEnum;
use Illuminate\Support\Facades\Validator;
use DateTime;
use App\Classes\CarParkManagement;
use Exception;

class CarParkLogAPIController extends Controller
{
    public function carParkLog(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'car_park_transfer_id' => ['required', 'exists:car_park_transfers,id'],
            // 'driver_id' => ['required'],
            'transfer_type' => ['required', 'in:' . TransferTypeEnum::IN . ',' . TransferTypeEnum::OUT],
        ], [], [
            'car_park_transfer_id' => __('car_park_transfers.id'),
            // 'driver_id' => __('car_park_transfers.driver_id'),
            'transfer_type' => __('car_park_transfers.transfer_type'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $transfer_type = $request->transfer_type;
        $carParkTransfer = CarParkTransfer::find($request->car_park_transfer_id);
        $carParkTransfer_new = CarParkTransfer::leftjoin('cars', 'cars.id', '=', 'car_park_transfers.car_id')
            ->leftjoin('driving_jobs', 'driving_jobs.id', '=', 'car_park_transfers.driving_job_id')
            ->leftJoin('car_parks', 'car_parks.id', '=', 'car_park_transfers.car_park_id')
            ->leftJoin('car_park_areas', 'car_park_areas.id', '=', 'car_parks.car_park_area_id')
            ->leftJoin('car_park_zones', 'car_park_zones.id', '=', 'car_park_areas.car_park_zone_id')
            ->where('car_park_transfers.id', $request->car_park_transfer_id)
            ->first();
        // dd($carParkTransfer_new);
        $start_date = $carParkTransfer->start_date;
        $end_date = $carParkTransfer->end_date;

        // check date
        $now_date = new DateTime();
        if (!empty($start_date)) {
            $start_date = new DateTime($start_date);
            if ($start_date > $now_date) {
                return response()->json([
                    'success' => false,
                    'message' => 'Start date'
                ], 422);
            }
        }
        if (!empty($end_date)) {
            $end_date = new DateTime($end_date);
            if ($end_date <= $now_date) {
                return response()->json([
                    'success' => false,
                    'message' => 'End date'
                ], 422);
            }
        }

        // init car_park management
        $car_id = $carParkTransfer->car_id;
        $car_park_id = $carParkTransfer->car_park_id;
        $branch_id = $carParkTransfer->branch_id;
        $is_difference_branch = boolval($carParkTransfer->is_difference_branch);
        $destination_branch_id = $carParkTransfer->destination_branch_id;
        $init_branch_id = $is_difference_branch ? $destination_branch_id : $branch_id;
        try {
            $cpm = new CarParkManagement($car_id, $init_branch_id);
            // check car out
            if (strcmp($transfer_type, TransferTypeEnum::OUT) == 0) {
                if (!$cpm->isActivated()) {
                    throw new Exception('Car not in park', 0);
                }
                $cpm->deActivate();
            } else if (strcmp($transfer_type, TransferTypeEnum::IN) == 0) {
                if ($cpm->isActivated()) {
                    throw new Exception('Car already in park', 0);
                }
                if (!empty($car_park_id)) {
                    $cpm->checkBooking();
                }
                $cpm->activate($car_park_id);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }


        $car_park_id_new = $cpm->carPark();
        $car_park_id_2 = CarPark::leftJoin('car_park_areas', 'car_park_areas.id', '=', 'car_parks.car_park_area_id')
            ->leftJoin('car_park_zones', 'car_park_zones.id', '=', 'car_park_areas.car_park_zone_id')
            ->where('car_parks.id', $car_park_id_new)->select('car_park_zones.code', 'car_parks.car_park_number')
            ->first();
        // $car = $car_park_id_new->carParkArea->carParkZone->code;
        // dd($car_park_id_new);
        $car_park_log = new CarParkTransferLog();
        $car_park_log->car_park_transfer_id = $carParkTransfer->id;
        $car_park_log->transfer_type = $request->transfer_type;
        $car_park_log->driver_id = $carParkTransfer_new->driver_id;
        $car_park_log->license_plate = $carParkTransfer_new->license_plate;
        $car_park_log->engine_no = $carParkTransfer_new->engine_no;
        $car_park_log->chassis_no = $carParkTransfer_new->chassis_no;
        $car_park_log->parking_slot = $carParkTransfer_new->car_park_id != null ? $carParkTransfer_new->code . $carParkTransfer_new->car_park_number : $car_park_id_2->code . $car_park_id_2->car_park_number;
        $car_park_log->car_park_id = $carParkTransfer_new->car_park_id != null ? $carParkTransfer_new->car_park_id : $car_park_id_new;
        $car_park_log->transfer_date = date('Y-m-d H:i:s');
        //$car_park_log->driver_id = $request->driver_id;
        $car_park_log->save();

        return response()->json([
            'success' => true,
            'message' => DATA_SUCCESS
        ]);

        /* if (((Carbon::now() >= $carParkTransfer->start_date) && (Carbon::now() <= $carParkTransfer->end_date)) && (($request->transfer_type == 1 || $request->transfer_type == 2))) {
            $car_park_log = new CarParkTransferLog;
            $car_park_log->car_park_transfer_id = $carParkTransfer->id;
            $car_park_log->transfer_type = $request->transfer_type;
            $car_park_log->transfer_date = Carbon::now()->timezone('Asia/Bangkok');
            $car_park_log->driver_id = $request->driver_id;
            $car_park_log->save();
            $message_status = $request->transfer_type == TransferTypeEnum::IN ? "Car In" : "Car Out";
            return response()->json($message_status, 200);
        } else {
            if (!isset($request->transfer_type) || !isset($request->driver_id)) {
                return response()->json('error', 200);
            } else {
                return response()->json('expire', 422);
            }
        } */
    }
}
