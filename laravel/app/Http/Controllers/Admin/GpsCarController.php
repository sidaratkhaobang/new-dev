<?php

namespace App\Http\Controllers\Admin;

use App\Classes\GPSService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Models\Car;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Enums\CarEnum;
use App\Enums\AccessoryTypeEnum;
use App\Enums\GPSStatusEnum;
use App\Enums\LongTermRentalTypeAccessoryEnum;
use App\Models\CarAccessory;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;

class GpsCarController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSCar);
        $engine_no = $request->engine_no;
        $license_plate = $request->license_plate;
        $chassis_no = $request->chassis_no;
        $vid = $request->vid;
        $from_install_date = $request->from_install_date;
        $to_install_date = $request->to_install_date;
        $from_revoke_date = $request->from_revoke_date;
        $to_revoke_date = $request->to_revoke_date;
        $s = null;

        $list = Car::leftJoin('car_accessories', 'car_accessories.car_id', '=', 'cars.id')
            ->select(
                'cars.id',
                'cars.status',
                'cars.license_plate',
                'cars.engine_no',
                'cars.chassis_no',
                'cars.status_gps',
                'cars.vid',
                'cars.current_location',
                DB::raw('MAX(car_accessories.install_date) as install_date'),
                DB::raw('MAX(car_accessories.revoke_date) as revoke_date'),
            )
            ->whereNotIn('cars.status', [CarEnum::NEWCAR, CarEnum::EQUIPMENT])
            ->search($s, $request)
            ->when($vid, function ($query) use ($vid) {
                return $query->where('cars.vid', 'like', '%' . $vid . '%');
            })
            ->where(function ($q) use ($from_install_date, $to_install_date) {
                if (!is_null($from_install_date) || !is_null($to_install_date)) {
                    return  $q->whereDate('car_accessories.install_date', '>=', $from_install_date)->whereDate('car_accessories.install_date', '<=', $to_install_date);
                }
            })
            ->where(function ($q) use ($from_revoke_date, $to_revoke_date) {
                if (!is_null($from_revoke_date) || !is_null($to_revoke_date)) {
                    return $q->whereDate('car_accessories.revoke_date', '>=', $from_revoke_date)->whereDate('car_accessories.revoke_date', '<=', $to_revoke_date);
                }
            })
            ->groupBy(
                'cars.id',
                'cars.status',
                'cars.license_plate',
                'cars.engine_no',
                'cars.chassis_no',
                'cars.status_gps',
                'cars.vid',
                'cars.current_location',
            )
            ->paginate(PER_PAGE);

        $license_plate_list = Car::select('license_plate as name', 'id')->whereNotIn('status', [CarEnum::NEWCAR, CarEnum::EQUIPMENT])->orderBy('created_at', 'desc')->get();
        $engine_no_list = Car::select('engine_no as name', 'id')->whereNotIn('status', [CarEnum::NEWCAR, CarEnum::EQUIPMENT])->orderBy('created_at', 'desc')->get();
        $chassis_no_list = Car::select('chassis_no as name', 'id')->whereNotIn('status', [CarEnum::NEWCAR, CarEnum::EQUIPMENT])->orderBy('created_at', 'desc')->get();
        $vid_list = Car::select('vid as name', 'vid as id')->whereNotIn('status', [CarEnum::NEWCAR, CarEnum::EQUIPMENT])->whereNotNull('vid')->orderBy('created_at', 'desc')->get();
        $excel_type_list = $this->getExcelType();

        return view('admin.gps-cars.index', [
            'list' => $list,
            'license_plate_list' => $license_plate_list,
            'engine_no_list' => $engine_no_list,
            'chassis_no_list' => $chassis_no_list,
            'vid_list' => $vid_list,
            'engine_no' => $engine_no,
            'license_plate' => $license_plate,
            'chassis_no' => $chassis_no,
            'vid' => $vid,
            'excel_type_list' => $excel_type_list,
            'from_install_date' => $from_install_date,
            'to_install_date' => $to_install_date,
            'from_revoke_date' => $from_revoke_date,
            'to_revoke_date' => $to_revoke_date,
        ]);
    }

    public function edit(Car $gps_car)
    {
        $this->authorize(Actions::Manage . '_' . Resources::GPSCar);
        $car_accessory = CarAccessory::leftJoin('cars', 'cars.id', '=', 'car_accessories.car_id')
            ->leftJoin('accessories', 'accessories.id', '=', 'car_accessories.accessory_id')
            ->select('car_accessories.install_date', 'car_accessories.revoke_date', 'cars.id', 'car_accessories.id as car_accessory_id')
            ->where('accessories.accessory_type', AccessoryTypeEnum::GPS)
            ->where('car_accessories.type_accessories', LongTermRentalTypeAccessoryEnum::ADDITIONAL)
            ->where('car_accessories.car_id', $gps_car->id)->first();
        if ($car_accessory) {
            $gps_car->install_date = $car_accessory->install_date;
            $gps_car->revoke_date = $car_accessory->revoke_date;
        }

        $sim_list = $this->getSimTrue();
        $page_title = __('lang.edit') . __('gps.car_data');
        return view('admin.gps-cars.form',  [
            'page_title' => $page_title,
            'd' => $gps_car,
            'sim_list' => $sim_list,
        ]);
    }

    public function show(Car $gps_car)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSCar);
        $sim_list = $this->getSimTrue();
        $page_title = __('lang.view') . __('gps.car_data');
        return view('admin.gps-cars.form',  [
            'page_title' => $page_title,
            'd' => $gps_car,
            'sim_list' => $sim_list,
            'view' => true,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sim' => [
                Rule::when($request->have_gps == BOOL_TRUE, ['required']),
            ],
        ], [], [
            'sim' => __('gps.sim'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $car = Car::find($request->id);
        $car->fleet = $request->fleet;
        $car->sim = $request->sim;
        $car->dvr = $request->dvr;
        $car->censor_oil = $request->censor_oil;
        $car->speed = $request->speed;
        $car->save();

        $redirect_route = route('admin.gps-cars.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public static function getSimTrue()
    {
        $sim_true = collect([
            (object) [
                'id' => BOOL_TRUE,
                'name' => __('gps.sim_' . BOOL_TRUE),
                'value' => BOOL_TRUE,
            ],
            (object) [
                'id' => BOOL_FALSE,
                'name' => __('gps.sim_' . BOOL_FALSE),
                'value' => BOOL_FALSE,
            ],
        ]);
        return $sim_true;
    }

    public static function getExcelType()
    {
        $excel_type = collect([
            (object) [
                'id' => 1,
                'name' => 'รถทั้งหมด',
                'value' => 1,
            ],
            (object) [
                'id' => 2,
                'name' => 'รถที่ติดตั้ง GPS',
                'value' => 2,
            ],
            (object) [
                'id' => 3,
                'name' => 'รถที่หยุดสัญญาณ GPS',
                'value' => 3,
            ],
            // (object) [
            //     'id' => 4,
            //     'name' => 'ค่าบริการ GPS และ DVR',
            //     'value' => 4,
            // ],
        ]);
        return $excel_type;
    }

    public function exportExcel(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSCar);
        $excel_type_id = $request->excel_type_id;
        $from_install_date = $request->from_install_date;
        $to_install_date = $request->to_install_date;
        $from_revoke_date = $request->from_revoke_date;
        $to_revoke_date = $request->to_revoke_date;
        $excel_cars = [];

        if (strcmp($excel_type_id, "1") == 0) {
            $excel_cars = Car::select('id', 'license_plate', 'engine_no', 'chassis_no', 'fleet', 'car_class_id')->get();
        }
        if (strcmp($excel_type_id, "2") == 0) {
            $excel_cars = Car::select(
                'cars.id',
                'cars.license_plate',
                'cars.engine_no',
                'cars.chassis_no',
                'cars.fleet',
                'cars.car_class_id',
                'car_accessories.install_date'
            )
                ->leftJoin('car_accessories', 'car_accessories.car_id', '=', 'cars.id')
                ->leftJoin('accessories', 'accessories.id', '=', 'car_accessories.accessory_id')
                ->where('accessories.accessory_type', AccessoryTypeEnum::GPS)
                ->where('car_accessories.type_accessories', LongTermRentalTypeAccessoryEnum::ADDITIONAL)
                ->where(function ($q) use ($from_install_date, $to_install_date) {
                    if (!is_null($from_install_date) || !is_null($to_install_date)) {
                        $q->whereDate('car_accessories.install_date', '>=', $from_install_date)->whereDate('car_accessories.install_date', '<=', $to_install_date);
                    }
                })
                ->get();
        }
        if (strcmp($excel_type_id, "3") == 0) {
            $excel_cars = Car::select(
                'cars.id',
                'cars.license_plate',
                'cars.engine_no',
                'cars.chassis_no',
                'cars.fleet',
                'cars.car_class_id',
                'car_accessories.revoke_date'
            )
                ->leftJoin('car_accessories', 'car_accessories.car_id', '=', 'cars.id')
                ->leftJoin('accessories', 'accessories.id', '=', 'car_accessories.accessory_id')
                ->where('accessories.accessory_type', AccessoryTypeEnum::GPS)
                ->where('car_accessories.type_accessories', LongTermRentalTypeAccessoryEnum::ADDITIONAL)
                ->where(function ($q) use ($from_revoke_date, $to_revoke_date) {
                    if (!is_null($from_revoke_date) || !is_null($to_revoke_date)) {
                        $q->whereDate('car_accessories.revoke_date', '>=', $from_revoke_date)->whereDate('car_accessories.revoke_date', '<=', $to_revoke_date);
                    }
                })
                ->get();
        }

        foreach ($excel_cars as $key => $excel_car) {
            $excel_car->index = $key + 1;
            $excel_car->license_plate = $excel_car->license_plate ? $excel_car->license_plate : '';
            $excel_car->fleet = $excel_car->fleet  ? $excel_car->fleet : '';
            $excel_car->engine_no = $excel_car->engine_no  ? $excel_car->engine_no : '';
            $excel_car->chassis_no = $excel_car->chassis_no  ? $excel_car->chassis_no : '';
            $excel_car->car_class = $excel_car->carClass ? $excel_car->carClass->name : '';
            if (strcmp($excel_type_id, "2") == 0) {
                $excel_car->install_date = $excel_car->install_date ? $excel_car->install_date : '';
            }
            if (strcmp($excel_type_id, "3") == 0) {
                $excel_car->revoke_date = $excel_car->revoke_date ? $excel_car->revoke_date : '';
            }
        }
        if (count($excel_cars) > 0) {
            if (strcmp($excel_type_id, "1") == 0) {
                return (new FastExcel($excel_cars))->download('file.xlsx', function ($line) {
                    return [
                        'ลำดับ' => $line->index,
                        'ทะเบียน' => $line->license_plate,
                        'รุ่นรถ' => $line->car_class,
                        'เลขเครื่อง' => $line->engine_no,
                        'เลขตัวถัง' => $line->chassis_no,
                        'FleetName' => $line->fleet,
                    ];
                });
            }
            if (strcmp($excel_type_id, "2") == 0) {
                return (new FastExcel($excel_cars))->download('file.xlsx', function ($line) {
                    return [
                        'ลำดับ' => $line->index,
                        'ทะเบียน' => $line->license_plate,
                        'รุ่นรถ' => $line->car_class,
                        'เลขเครื่อง' => $line->engine_no,
                        'เลขตัวถัง' => $line->chassis_no,
                        'FleetName' => $line->fleet,
                        'วันที่ติดตั้ง GPS' => $line->install_date,
                    ];
                });
            }
            if (strcmp($excel_type_id, "3") == 0) {
                return (new FastExcel($excel_cars))->download('file.xlsx', function ($line) {
                    return [
                        'ลำดับ' => $line->index,
                        'ทะเบียน' => $line->license_plate,
                        'รุ่นรถ' => $line->car_class,
                        'เลขเครื่อง' => $line->engine_no,
                        'เลขตัวถัง' => $line->chassis_no,
                        'FleetName' => $line->fleet,
                        'วันที่หยุดสัญญาณ GPS' => $line->revoke_date,
                    ];
                });
            }
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function updateVehLastLocation(Request $request)
    {
        $vehicle_list = $request->vehicle;
        $vehicle_id = [
            "VehicleList" => $vehicle_list,
        ];
        $gpsService = new GPSService();
        $res = $gpsService->getVehLastLocations($vehicle_id);
        if ($res['successful'] && (strcmp($res['status'], '200') == 0)) {
            $data = $res['data'];
            if (is_array($data)) {
                foreach ($data as $d) {
                    $vehicle_id = $d['Vehicle_ID'];
                    $io_status = $d['IO_Status'];
                    $location_th = $d['Location_TH'];

                    if ((!empty($vehicle_id))) {
                        if (strcmp($vehicle_id, "222150159") == 0) {
                            Car::where('vid', $vehicle_id)->update([
                                'current_location' => $location_th,
                                'status_gps' => GPSStatusEnum::NORMAL_SIGNAL,
                            ]);
                        } else {
                            if (!$io_status) {
                                Car::where('vid', $vehicle_id)->update([
                                    'current_location' => $location_th,
                                    'status_gps' => GPSStatusEnum::NO_SIGNAL,
                                ]);
                            } else {
                                Car::where('vid', $vehicle_id)->update([
                                    'current_location' => $location_th,
                                    'status_gps' => GPSStatusEnum::NORMAL_SIGNAL,
                                ]);
                            }
                        }
                    }
                }
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลสัญญาณGPS',
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'ok',
            'redirect' => route('admin.gps-cars.index'),
        ]);
    }
}
