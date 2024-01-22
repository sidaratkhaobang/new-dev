<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Models\CarParkTransferLog;
use App\Models\Car;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\TransferTypeEnum;
use App\Models\User;

class CarParkTransferLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CarParkTransferLog);
        $transfer_type = $request->transfer_type;
        $est_transfer_date = $request->est_transfer_date;
        $from_delivery_date = $request->from_delivery_date;
        $to_delivery_date = $request->to_delivery_date;
        $license_plate = null;

        if (!empty($request->car_id)) {
            $car = Car::find($request->car_id);
            $license_plate = $car->license_plate;
        }
        $engine_no = null;
        if (!empty($request->engine_no)) {
            $car = Car::find($request->engine_no);
            $engine_no = $car->engine_no;
        }
        $chassis_no = null;
        if (!empty($request->chassis_no)) {
            $car = Car::find($request->chassis_no);
            $chassis_no = $car->chassis_no;
        }
        $driver_id = $request->driver_id;
        $car_list = Car::select('id', 'license_plate as name')->get();
        $lists = CarParkTransferLog::sortable()->leftjoin('car_park_transfers', 'car_park_transfers.id', '=', 'car_park_transfer_logs.car_park_transfer_id')
            ->leftjoin('cars', 'cars.id', '=', 'car_park_transfers.car_id')
            ->leftjoin('drivers', 'drivers.id', '=', 'car_park_transfer_logs.driver_id')
            ->leftjoin('car_statuses', 'car_statuses.id', '=', 'car_park_transfers.car_status_id')
            ->leftJoin('car_parks', 'car_parks.id', '=', 'car_park_transfer_logs.car_park_id')
            ->leftJoin('car_park_areas', 'car_park_areas.id', '=', 'car_parks.car_park_area_id')
            ->leftJoin('car_park_zones', 'car_park_zones.id', '=', 'car_park_areas.car_park_zone_id')
            ->select(
                'car_park_transfer_logs.*',
                'car_park_transfers.est_transfer_date',
                'car_park_transfers.start_date as date_start',
                'car_park_transfers.end_date as date_end',
                'car_statuses.name as car_type_name',
                'cars.license_plate',
                'cars.engine_no',
                'cars.chassis_no',
                'car_park_transfers.worksheet_no',
                'drivers.name as fullname',
                'car_parks.car_park_number as car_park_number',
                'car_park_zones.code as zone_code'
            )
            ->where(function ($q) use ($transfer_type, $est_transfer_date, $from_delivery_date, $to_delivery_date, $driver_id, $request) {
                if (!is_null($transfer_type)) {
                    $q->where('car_park_transfer_logs.transfer_type', $transfer_type);
                }
                if (!is_null($est_transfer_date)) {
                    $q->where('car_park_transfers.est_transfer_date', $est_transfer_date);
                }
                if (!is_null($from_delivery_date) && !is_null($to_delivery_date)) {
                    $q->whereDate('car_park_transfers.start_date', '<=', $from_delivery_date)->whereDate('car_park_transfers.end_date', '>=', $to_delivery_date);
                }
                if (!is_null($request->car_id) || !is_null($request->engine_no) || !is_null($request->chassis_no)) {
                    $q->whereIn('car_park_transfers.car_id', [$request->car_id, $request->engine_no, $request->chassis_no]);
                }
                if (!is_null($driver_id)) {
                    $q->where('car_park_transfer_logs.driver_id', $driver_id);
                }
            })
            ->where('car_park_transfers.branch_id', get_branch_id())
            ->search($request->s, $request)
            ->orderBy('car_park_transfer_logs.created_at', 'DESC')
            ->paginate(PER_PAGE);
        $transfer_type_list = $this->getTransferType();
        $license_plate_list = Car::select('license_plate as name', 'id')->orderBy('license_plate')->get();
        $engine_no_list = Car::select('engine_no as name', 'id')->orderBy('engine_no')->get();
        $chassis_no_list = Car::select('chassis_no as name', 'id')->orderBy('chassis_no')->get();
        $driver_list = User::select('name', 'id')->orderBy('name')->get();

        return view('admin.car-park-transfer-logs.index', [
            's' => $request->s,
            'lists' => $lists,
            'car_id' => $request->car_id,
            'engine_no_id' => $request->engine_no,
            'chassis_no_id' => $request->chassis_no,
            'car_list' => $car_list,
            'transfer_type_list' => $transfer_type_list,
            'transfer_type' => $transfer_type,
            'est_transfer_date' => $est_transfer_date,
            'from_delivery_date' => $from_delivery_date,
            'to_delivery_date' => $to_delivery_date,
            'license_plate_list' => $license_plate_list,
            'engine_no_list' => $engine_no_list,
            'chassis_no_list' => $chassis_no_list,
            'license_plate' => $license_plate,
            'engine_no' => $engine_no,
            'chassis_no' => $chassis_no,
            'driver_list' => $driver_list,
            'driver_id' => $driver_id,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $page_title =  __('car_park_transfer_logs.detail_sheet');
        // $view = true;
        // $car_type = '';
        // $license_category = '';
        // return view('admin.car-in-out-logs.form',[
        //     'page_title' => $page_title,
        //     'd' => '',
        //     'license_category' => '',
        //     'car_type' => ''
        // ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getTransferType()
    {
        return collect([
            (object)[
                'id' => TransferTypeEnum::IN,
                'value' => TransferTypeEnum::IN,
                'name' => __('car_park_transfers.transfer_type_' . TransferTypeEnum::IN),
            ],
            (object)[
                'id' => TransferTypeEnum::OUT,
                'value' => TransferTypeEnum::OUT,
                'name' => __('car_park_transfers.transfer_type_' . TransferTypeEnum::OUT),
            ],

        ]);
    }

    public function getEngineNo($id)
    {
        if ($id == '0') {
            $engine = Car::pluck("engine_no", "id");
        } else {
            $engine = Car::Where("id", $id)->pluck("engine_no", "id");
        }

        return json_encode($engine);
    }
    public function getChassisNo($id)
    {
        if ($id == '0') {
            $chassis = Car::pluck("chassis_no", "id");
        } else {
            $chassis = Car::Where("id", $id)->pluck("chassis_no", "id");
        }
        return json_encode($chassis);
    }
}
