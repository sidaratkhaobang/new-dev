<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\RentalCarManagement;
use Illuminate\Support\Facades\Validator;

class RentalServiceController extends Controller
{
    public function availablePickupDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type_id' => ['required', 'exists:service_types,id'],
        ], [], [
            'service_type_id' => __('service_types.name'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $service_type_id = $request->service_type_id;
        $rental = new RentalCarManagement($service_type_id);
        $dates = $rental->getAvailablePickupDates();
        return response()->json([
            'success' => true,
            'results' => $dates
        ], 200);
    }

    public function availablePickupTime(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type_id' => ['required', 'exists:service_types,id'],
            'pickup_date' => ['required', 'date'],
        ], [], [
            'service_type_id' => __('service_types.name'),
            'pickup_date' => __('short_term_rentals.pickup_date'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $service_type_id = $request->service_type_id;
        $pickup_date = $request->pickup_date;
        $rental = new RentalCarManagement($service_type_id);
        $dates = $rental->getAvailablePickupTimes($pickup_date);
        return response()->json([
            'success' => true,
            'results' => $dates
        ], 200);
    }

    public function availableReturnDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type_id' => ['required', 'exists:service_types,id'],
            'pickup_date' => ['required', 'date'],
        ], [], [
            'service_type_id' => __('service_types.name'),
            'pickup_date' => __('short_term_rentals.pickup_date'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $service_type_id = $request->service_type_id;
        $pickup_date = $request->pickup_date;
        $pickup_time = $request->pickup_time;
        $rental = new RentalCarManagement($service_type_id);
        $dates = $rental->getAvailableReturnDates($pickup_date, $pickup_time);
        return response()->json([
            'success' => true,
            'results' => $dates
        ], 200);
    }

    public function availableReturnTime(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type_id' => ['required', 'exists:service_types,id'],
            'return_date' => ['required', 'date'],
        ], [], [
            'service_type_id' => __('service_types.name'),
            'return_date' => __('short_term_rentals.return_date'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $service_type_id = $request->service_type_id;
        $return_date = $request->return_date;
        $rental = new RentalCarManagement($service_type_id);
        $dates = $rental->getAvailableReturnTimes($return_date);
        return response()->json([
            'success' => true,
            'results' => $dates
        ], 200);
    }

    function availableCars(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_type_id' => ['required', 'exists:service_types,id'],
            'pickup_date' => ['required', 'date'],
            'return_date' => ['required', 'date'],
        ], [], [
            'service_type_id' => __('service_types.name'),
            'pickup_date' => __('short_term_rentals.pickup_date'),
            'return_date' => __('short_term_rentals.return_date'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $service_type_id = $request->service_type_id;
        $pickup_date = $request->pickup_date;
        $pickup_time = $request->pickup_time;
        $return_date = $request->return_date;
        $return_time = $request->return_time;
        $rental = new RentalCarManagement($service_type_id);
        $car_ids = $rental->getAvailableCars($pickup_date, $pickup_time, $return_date, $return_time);
        return response()->json([
            'success' => true,
            'results' => $car_ids
        ], 200);
    }
}
