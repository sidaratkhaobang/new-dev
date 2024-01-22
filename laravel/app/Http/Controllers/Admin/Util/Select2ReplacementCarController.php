<?php

namespace App\Http\Controllers\Admin\Util;

use App\Enums\CarEnum;
use App\Enums\ReplacementJobTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarBrand;
use App\Models\CarClass;
use App\Models\CarColor;
use App\Models\ReplacementCar;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Enums\RepairStatusEnum;
use App\Models\Accident;
use App\Models\Contracts;
use App\Models\RepairOrder;
use App\Enums\RentalTypeEnum;
use App\Enums\ReplacementTypeEnum;

class Select2ReplacementCarController extends Controller
{

    public function getReplacementCars(Request $request)
    {
        $replacement_cars = ReplacementCar::select('id', 'worksheet_no')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('worksheet_no', 'like', '%' . $request->s . '%');
                }
            })
            ->orderBy('worksheet_no')
            ->limit(50)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->worksheet_no
                ];
            });
        return response()->json($replacement_cars);
    }

    public function getReplacementMainCars(Request $request)
    {
        $replacement_main_cars = Car::join('replacement_cars', 'replacement_cars.main_car_id', '=', 'cars.id')
            ->select('cars.id', 'cars.license_plate as text')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('cars.license_plate', 'like', '%' . $request->s . '%');
                }
            })
            ->orderBy('cars.license_plate')
            ->limit(50)
            ->distinct('cars.id')
            ->get();
        return response()->json($replacement_main_cars);
    }

    public function getReplacementReplaceCars(Request $request)
    {
        $replacement_replace_cars = Car::join('replacement_cars', 'replacement_cars.replacement_car_id', '=', 'cars.id')
            ->select('cars.id', 'cars.license_plate as text')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('cars.license_plate', 'like', '%' . $request->s . '%');
                }
            })
            ->orderBy('cars.license_plate')
            ->limit(50)
            ->distinct('cars.id')
            ->get();
        return response()->json($replacement_replace_cars);
    }

    public function getReplacementJobs(Request $request)
    {
        // PENDING
        // $replacement_cars = ReplacementCar::select('id', 'worksheet_no')
        //     ->where(function ($query) use ($request) {
        //         if (!empty($request->s)) {
        //             $query->where('worksheet_no', 'like', '%' . $request->s . '%');
        //         }
        //     })
        //     ->orderBy('worksheet_no')
        //     ->limit(50)
        //     ->get()->map(function ($item) {
        //         return [
        //             'id' => $item->id,
        //             'text' => $item->worksheet_no
        //         ];
        //     });
        // return response()->json($replacement_cars);
    }

    public function getReplacementJob(Request $request)
    {
        $jobs = [];
        if (strcmp($request->parent_id, ReplacementJobTypeEnum::REPAIR) == 0) {
            $replacement_id = ReplacementCar::where('job_type', $request->parent_id)->where('replacement_type', $request->parent_id_2)->pluck('job_id')->toArray();
            $jobs = RepairOrder::select('repair_orders.id', 'repair_orders.worksheet_no')
                ->whereNotIn('repair_orders.status', [RepairStatusEnum::CANCEL])
                ->where(function ($query) use ($request, $replacement_id) {
                    if (!empty($request->s)) {
                        $query->where('repair_orders.worksheet_no', 'like', '%' . $request->s . '%');
                    }
                    if (!empty($request->parent_id_2)) {
                        $query->whereNotIn('repair_orders.id', $replacement_id);
                    }
                })
                ->orderBy('repair_orders.worksheet_no')
                ->limit(50)
                ->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'text' => $item->worksheet_no
                    ];
                });
        }
        if (strcmp($request->parent_id, ReplacementJobTypeEnum::ACCIDENT) == 0) {
            // $replacement_id = ReplacementCar::where('job_type', $request->parent_id)->where('replacement_type', $request->parent_id_2)->pluck('job_id')->toArray();
            $jobs = Accident::select('accidents.id', 'accidents.worksheet_no')
                ->where(function ($query) use ($request) {
                    if (!empty($request->s)) {
                        $query->where('accidents.worksheet_no', 'like', '%' . $request->s . '%');
                    }
                    // if (!empty($request->parent_id_2)) {
                    //     $query->whereNotIn('repair_orders.id', $replacement_id);
                    // }
                })
                ->orderBy('accidents.worksheet_no')
                ->limit(50)
                ->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'text' => $item->worksheet_no
                    ];
                });
        }
        return response()->json($jobs);
    }

    public function getReplacementJobDetail(Request $request)
    {
        $id = $request->id;
        $job_type = $request->job_type;
        $accident_job_detail = [];
        $accident_job_detail['job_type'] = $job_type;
        $accident_job_detail['id'] = $id;
        if (strcmp($job_type, ReplacementJobTypeEnum::REPAIR) == 0) {
            $repair_order = RepairOrder::select('repair_orders.id', 'repairs.car_id', 'repair_orders.worksheet_no')
                ->leftJoin('repairs', 'repairs.id', '=', 'repair_orders.repair_id')
                ->where('repair_orders.id', $id)->first();
            if ($repair_order) {
                $accident_job_detail['worksheet_no'] = $repair_order->worksheet_no;
                $car = Car::find($repair_order->car_id);
                if ($car) {
                    $accident_job_detail['car_id'] = $car->id;
                    $accident_job_detail['main_car'] = ($car) ? $car->license_plate : null;
                    $contract = Contracts::leftJoin('contract_lines', 'contract_lines.contract_id', '=', 'contracts.id')
                        ->where('contract_lines.car_id', $car->id)
                        ->select('contracts.id', 'contracts.worksheet_no')
                        ->first();
                    $accident_job_detail['contract_no'] = ($contract) ? $contract->worksheet_no : null;
                }
                $replacement_car = ReplacementCar::where('job_id', $repair_order->id)->where('job_type', ReplacementJobTypeEnum::REPAIR)
                    ->where('replacement_type', ReplacementTypeEnum::SEND_REPLACE_RECEIVE_MAIN)->first();
                if ($replacement_car) {
                    $car = Car::find($repair_order->car_id);
                    $accident_job_detail['replacement_car'] = $car ? $car->license_plate : null;
                    $accident_job_detail['replacement_car_class'] = ($car && $car->carClass) ? $car->carClass->full_name : null;
                    $accident_job_detail['replacement_car_color'] = ($car && $car->carColor) ? $car->carColor->name : null;
                }
            }
        }
        if (strcmp($job_type, ReplacementJobTypeEnum::ACCIDENT) == 0) {
            $accident = Accident::find($id);
            if ($accident) {
                $accident_job_detail['worksheet_no'] = $accident->worksheet_no;
                $car = Car::find($accident->car_id);
                if ($car) {
                    $accident_job_detail['car_id'] = $car->id;
                    $accident_job_detail['main_car'] = ($car) ? $car->license_plate : null;
                    $contract = Contracts::leftJoin('contract_lines', 'contract_lines.contract_id', '=', 'contracts.id')
                        ->where('contract_lines.car_id', $car->id)
                        ->select('contracts.id', 'contracts.worksheet_no')
                        ->first();
                    $accident_job_detail['contract_no'] = ($contract) ? $contract->worksheet_no : null;
                }
            }
        }
        return response()->json($accident_job_detail);
    }

    public function getAvailableReplacementCars(Request $request)
    {
        // $replacement_car = Car::where('rental_type', RentalTypeEnum::REPLACEMENT)->get();
        // $replacement_car->map(function ($item) {
        //     $item->id = $item->id;
        //     $item->text = $item->license_plate;
        // });
        // return response()->json($replacement_car);
        $is_used_replacement_cars = ReplacementCar::whereNotNull('replacement_car_id')
            ->select('replacement_car_id')->pluck('replacement_car_id')->toArray();
        $replacement_car = Car::where('status', CarEnum::READY_TO_USE)
            ->where('rental_type', RentalTypeEnum::REPLACEMENT)
            ->whereNotIn('id', $is_used_replacement_cars)
            ->first();

        if (!$replacement_car) {
            $rand_num  = rand(0, 9999);
            $replacement_car = new Car();
            $replacement_car->code = 'RAND1' . $rand_num;
            $replacement_car->license_plate = 'ทด '  . $rand_num;
            $replacement_car->engine_no = 'RC-E' . $rand_num;
            $replacement_car->chassis_no = 'RC-C' . $rand_num;
            $replacement_car->status = CarEnum::READY_TO_USE;
            $replacement_car->rental_type = RentalTypeEnum::REPLACEMENT;
            $replacement_car->car_class_id = CarClass::inRandomOrder()->first()->id;
            $replacement_car->car_color_id = CarColor::inRandomOrder()->first()->id;
            $replacement_car->save();
        }
        $replacement_car = Car::where('status', CarEnum::READY_TO_USE)
            ->where('rental_type', RentalTypeEnum::REPLACEMENT)
            ->whereNotIn('id', $is_used_replacement_cars)
            ->get();

        $replacement_car->map(function ($item) {
            $item->name = $item->license_plate;
        });
        return response()->json($replacement_car);
    }
}
