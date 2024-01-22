<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Enums\GPSHistoricalDataStatusEnum;
use App\Enums\GPSHistoricalDataTypeEnum;
use App\Models\GpsHistoricalData;
use App\Models\GpsHistoricalDataLine;
use App\Models\Car;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Traits\GpsTrait;
use DateTime;

class GpsHistoricalDataAlertController extends Controller
{
    use GpsTrait;

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSHistoricalDataAlert);

        $list = GpsHistoricalData::select(
            'id',
            'status',
            'worksheet_no',
            'created_at',
            'created_by',
        )
            ->search($request)
            ->orderBy('created_at', 'desc')
            ->paginate(PER_PAGE);

        $worksheet_list = GpsHistoricalData::select('worksheet_no as name', 'id')->get();
        $status_list = GpsTrait::getHistoricalStatus();
        $request_user_list = User::select('name', 'id')->get();

        return view('admin.gps-historical-data-alerts.index', [
            'list' => $list,
            'worksheet_no' => $request->worksheet_no,
            'request_user' => $request->request_user,
            'request_date' => $request->request_date,
            'status' => $request->status,
            'worksheet_list' => $worksheet_list,
            'status_list' => $status_list,
            'request_user_list' => $request_user_list,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::GPSHistoricalDataAlert);
        $d = new GpsHistoricalData();
        $user = Auth::user();
        $d->created_at = now();
        $d->created_by = $user->id;
        $type_file_list = GpsTrait::getTypeFileList();
        $license_plate_list = Car::select('license_plate as name', 'id')->whereNotNull('license_plate')->orderBy('license_plate')->get();

        $page_title = __('lang.create') . __('gps.page_title_data');
        return view('admin.gps-historical-data-alerts.form', [
            'd' =>  $d,
            'page_title' => $page_title,
            'type_file_list' => $type_file_list,
            'license_plate_list' => $license_plate_list,
            'type_file_arr' => [],
        ]);
    }

    public function edit(GpsHistoricalData $gps_historical_data_alert)
    {
        $this->authorize(Actions::Manage . '_' . Resources::GPSHistoricalDataAlert);
        $car_list = GpsHistoricalDataLine::leftJoin('cars', 'cars.id', '=', 'gps_historical_data_lines.car_id')
            ->where('gps_historical_data_lines.gps_historical_data_id', $gps_historical_data_alert->id)
            ->select(
                'cars.id as car_id',
                'cars.license_plate',
                'gps_historical_data_lines.id as id',
                'gps_historical_data_lines.start_date',
                'gps_historical_data_lines.end_date',
            )->get();

        $car_list->map(function ($item) {
            $start_timestamp = (strtotime($item->start_date));
            $start_date = ($start_timestamp) ? date('Y-m-d', $start_timestamp) : null;
            $start_time = ($start_timestamp) ? date('H:i', $start_timestamp) : null;
            $end_timestamp = (strtotime($item->end_date));
            $end_date = ($end_timestamp) ? date('Y-m-d', $end_timestamp) : null;
            $end_time = ($end_timestamp) ? date('H:i', $end_timestamp) : null;
            $item->license_plate_text = $item->license_plate;
            $item->license_plate_id = $item->car_id;
            $item->id = $item->id;
            $item->start_date = $start_date;
            $item->start_time = ($start_time > '00:00') ? $start_time : null;
            $item->end_date = $end_date;
            $item->end_time = ($end_time > '00:00') ? $end_time : null;
            return $item;
        });

        $type_file_list = GpsTrait::getTypeFileList();
        $license_plate_list = Car::select('license_plate as name', 'id')->whereNotNull('license_plate')->orderBy('license_plate')->get();
        $type_file_arr = explode(",", $gps_historical_data_alert->type_file);

        $page_title = __('lang.edit') . __('gps.page_title_data')  . ' ' . 'เลขที่' . $gps_historical_data_alert->worksheet_no;
        return view('admin.gps-historical-data-alerts.form',  [
            'page_title' => $page_title,
            'd' => $gps_historical_data_alert,
            'car_list' => $car_list,
            'edit' => true,
            'type_file_list' => $type_file_list,
            'license_plate_list' => $license_plate_list,
            'type_file_arr' => $type_file_arr,
        ]);
    }

    public function show(GpsHistoricalData $gps_historical_data_alert)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSHistoricalDataAlert);
        $car_list = GpsHistoricalDataLine::leftJoin('cars', 'cars.id', '=', 'gps_historical_data_lines.car_id')
            ->where('gps_historical_data_lines.gps_historical_data_id', $gps_historical_data_alert->id)
            ->select(
                'cars.id as car_id',
                'cars.license_plate',
                'gps_historical_data_lines.id as id',
                'gps_historical_data_lines.start_date',
                'gps_historical_data_lines.end_date',
            )->get();

        $car_list->map(function ($item) {
            $start_timestamp = (strtotime($item->start_date));
            $start_date = ($start_timestamp) ? date('Y-m-d', $start_timestamp) : null;
            $start_time = ($start_timestamp) ? date('H:i', $start_timestamp) : null;
            $end_timestamp = (strtotime($item->end_date));
            $end_date = ($end_timestamp) ? date('Y-m-d', $end_timestamp) : null;
            $end_time = ($end_timestamp) ? date('H:i', $end_timestamp) : null;
            $item->license_plate_text = $item->license_plate;
            $item->license_plate_id = $item->car_id;
            $item->id = $item->id;
            $item->start_date = $start_date;
            $item->start_time = ($start_time > '00:00') ? $start_time : null;
            $item->end_date = $end_date;
            $item->end_time = ($end_time > '00:00') ? $end_time : null;
            return $item;
        });

        $type_file_list = GpsTrait::getTypeFileList();
        $license_plate_list = Car::select('license_plate as name', 'id')->whereNotNull('license_plate')->orderBy('license_plate')->get();
        $type_file_arr = explode(",", $gps_historical_data_alert->type_file);

        $page_title = __('lang.view') . __('gps.page_title_data') . ' ' . 'เลขที่' . $gps_historical_data_alert->worksheet_no;
        return view('admin.gps-historical-data-alerts.form',  [
            'page_title' => $page_title,
            'd' => $gps_historical_data_alert,
            'car_list' => $car_list,
            'type_file_list' => $type_file_list,
            'license_plate_list' => $license_plate_list,
            'type_file_arr' => $type_file_arr,
            'view' => true,
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'purpose' => ['required'],
            'cars' => [Rule::when($request->update_status == true, ['required']), 'array', 'min:1'],
        ], [], [
            'purpose' => __('gps.purpose'),
            'cars' => __('gps.request_table'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $type_files = null;
        if ($request->type_file != null) {
            $type_files = implode(',', $request->type_file);
        }
        $prefix = '';
        $historical_count = GpsHistoricalData::count() + 1;

        $historical_alert = GpsHistoricalData::firstOrNew(['id' => $request->id]);

        if (!($historical_alert->exists)) {
            $historical_alert->worksheet_no = generateRecordNumber($prefix, $historical_count);
        }
        $historical_alert->purpose = $request->purpose;
        $historical_alert->type_file = $type_files;
        if ($request->update_status) {
            $historical_alert->status = GPSHistoricalDataStatusEnum::REQUEST;
        } else {
            $historical_alert->status = GPSHistoricalDataStatusEnum::DRAFT;
        }
        $historical_alert->save();
        if (isset($request->pending_delete_car_ids)) {
            GpsHistoricalDataLine::whereIn('id', $request->pending_delete_car_ids)->delete();
        }
        if ($request->cars) {
            foreach ($request->cars as $key => $item) {
                if (isset($item['id'])) {
                    $historical_alert_line = GpsHistoricalDataLine::find($item['id']);
                } else {
                    $historical_alert_line = new GpsHistoricalDataLine();
                }
                $historical_alert_line->gps_historical_data_id = $historical_alert->id;
                $historical_alert_line->car_id = $item['license_plate_id'];
                $historical_alert_line->start_date = $item['start_date'] . " " . $item['start_time'];
                $historical_alert_line->end_date = $item['end_date'] . " " . $item['end_time'];
                $historical_alert_line->save();
            }
        }

        $redirect_route = route('admin.gps-historical-data-alerts.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function uploadExcel(Request $request)
    {
        if ($request->json_object) {
            foreach ($request->json_object as $key => $item_excel) {
                $date = explode("-", $item_excel['วันที่']);
                $date_0 = str_replace('/', '-', $date[0]);
                $start_date = date('Y-m-d', strtotime($date_0));
                $date_1 = str_replace('/', '-', $date[0]);
                $end_date = date('Y-m-d', strtotime($date_1));
                $start_timestamp = (strtotime($item_excel['เวลาเริ่มต้น (น.)']));
                $start_time = ($start_timestamp) ? date('H:i', $start_timestamp) : null;
                $end_timestamp = (strtotime($item_excel['เวลาสิ้นสุด (น.)']));
                $end_time = ($end_timestamp) ? date('H:i', $end_timestamp) : null;
                $license_plate_id = Car::where('license_plate', 'like', '%' . $item_excel['ทะเบียน'] . '%')->pluck('id')->first();
                $car_list[] = (object) array(
                    "license_plate_text" => !empty($item_excel['ทะเบียน']) ? $item_excel['ทะเบียน'] : null,
                    "license_plate_id" => $license_plate_id ? $license_plate_id : null,
                    "start_date" => $start_date,
                    "end_date" => $end_date,
                    "start_time" => $start_time,
                    "end_time" => $end_time,
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'ok',
                'data' => $car_list,
                'redirect' => view('admin.gps-historical-data-alerts.form')
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }
}
