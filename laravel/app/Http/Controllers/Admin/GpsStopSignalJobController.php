<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Enums\GPSStopStatusEnum;
use App\Models\GpsRemoveStopSignal;
use App\Models\Rental;
use App\Models\Car;
use DateTime;
use Illuminate\Support\Facades\Auth;
use App\Traits\GpsTrait;

class GpsStopSignalJobController extends Controller
{
    use GpsTrait;

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSStopSignalJob);
        $worksheet_no = $request->worksheet_no;
        $chassis_no = $request->chassis_no;
        $vid = $request->vid;
        $stop_date = $request->stop_date;
        $stop_status_id = $request->stop_status_id;
        $job_type = $request->job_type;
        $today = date('Y-m-d');

        $list = GpsRemoveStopSignal::leftJoin('cars', 'cars.id', '=', 'gps_remove_stop_signals.car_id')
            // ->whereDate('gps_remove_stop_signals.inform_date', '=', $today)
            ->whereIn('stop_status', [GPSStopStatusEnum::ALERT_STOP_SIGNAL, GPSStopStatusEnum::STOP_SIGNAL])
            ->select(
                'gps_remove_stop_signals.id',
                'gps_remove_stop_signals.worksheet_no',
                'gps_remove_stop_signals.inform_date',
                'gps_remove_stop_signals.job_type',
                'gps_remove_stop_signals.remove_date',
                'gps_remove_stop_signals.remove_status',
                'gps_remove_stop_signals.stop_date',
                'gps_remove_stop_signals.stop_status',
                'cars.license_plate',
                'cars.chassis_no',
                'cars.vid',
            )
            ->orderBy('gps_remove_stop_signals.created_at', 'desc')
            // ->search($request)
            ->when($vid, function ($query) use ($vid) {
                return $query->where('cars.vid', 'like', '%' . $vid . '%');
            })
            ->when($chassis_no, function ($query) use ($chassis_no) {
                return $query->where('cars.chassis_no', 'like', '%' . $chassis_no . '%');
            })
            ->when($worksheet_no, function ($query) use ($worksheet_no) {
                return $query->where('gps_remove_stop_signals.id', $worksheet_no);
            })
            ->when($stop_status_id, function ($query) use ($stop_status_id) {
                return $query->where('gps_remove_stop_signals.stop_status', $stop_status_id);
            })
            ->when($job_type, function ($query) use ($job_type) {
                return $query->where('gps_remove_stop_signals.job_type', $job_type);
            })
            ->when($stop_date, function ($query) use ($stop_date) {
                return $query->where('gps_remove_stop_signals.stop_date', $stop_date);
            });
            // if (empty($stop_date)) {
            //     $list = $list->whereDate('gps_remove_stop_signals.inform_date', '=', $today);
            // }
            $list = $list->paginate(PER_PAGE);

        $stop_status_list = GpsTrait::getStopStatusList();
        $chassis_no_list = GpsRemoveStopSignal::select('cars.chassis_no as name', 'cars.chassis_no as id')->leftJoin('cars', 'cars.id', '=', 'gps_remove_stop_signals.car_id')->distinct()->get();
        $vid_list = GpsRemoveStopSignal::select('cars.vid as name', 'cars.vid as id')->whereNotNull('cars.vid')->leftJoin('cars', 'cars.id', '=', 'gps_remove_stop_signals.car_id')->distinct()->get();
        $worksheet_list = GpsRemoveStopSignal::select('worksheet_no as name', 'id')->get();
        $stop_list = GpsTrait::getStopStatus();
        $job_type_list = GpsTrait::getJobTypeList();

        return view('admin.gps-stop-signal-jobs.index', [
            'list' => $list,
            'worksheet_no' => $worksheet_no,
            'chassis_no' => $chassis_no,
            'vid' => $vid,
            'stop_date' => $stop_date,
            'stop_status_id' => $stop_status_id,
            'chassis_no_list' => $chassis_no_list,
            'vid_list' => $vid_list,
            'worksheet_list' => $worksheet_list,
            'stop_list' => $stop_list,
            'job_type_list' => $job_type_list,
            'job_type' => $job_type,
            'stop_status_list' => $stop_status_list,
        ]);
    }

    public function edit(GpsRemoveStopSignal $gps_stop_signal_job)
    {
        $this->authorize(Actions::Manage . '_' . Resources::GPSStopSignalJob);
        $car = Car::find($gps_stop_signal_job->car_id);
        if ($car) {
            $gps_stop_signal_job->license_plate = ($car) ? $car->license_plate : null;
            $gps_stop_signal_job->engine_no = ($car) ? $car->engine_no : null;
            $gps_stop_signal_job->chassis_no = ($car) ? $car->chassis_no : null;;
            $gps_stop_signal_job->car_class = ($car && $car->carClass) ? $car->carClass->full_name : null;
            $gps_stop_signal_job->car_color = ($car && $car->carColor) ? $car->carColor->name : null;
            $gps_stop_signal_job->fleet = ($car) ? $car->fleet : null;
            $gps_stop_signal_job->vid = ($car) ? $car->vid : null;
            $gps_stop_signal_job->sim = ($car) ? $car->sim : null;
        }
        $stop_status_list = GpsTrait::getStopStatusList();

        $page_title = __('lang.edit') . __('gps.job_stop_tab');
        return view('admin.gps-stop-signal-jobs.form',  [
            'page_title' => $page_title,
            'd' => $gps_stop_signal_job,
            'stop_status_list' => $stop_status_list,
        ]);
    }

    public function show(GpsRemoveStopSignal $gps_stop_signal_job)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSStopSignalJob);
        $car = Car::find($gps_stop_signal_job->car_id);
        if ($car) {
            $gps_stop_signal_job->license_plate = ($car) ? $car->license_plate : null;
            $gps_stop_signal_job->engine_no = ($car) ? $car->engine_no : null;
            $gps_stop_signal_job->chassis_no = ($car) ? $car->chassis_no : null;;
            $gps_stop_signal_job->car_class = ($car && $car->carClass) ? $car->carClass->full_name : null;
            $gps_stop_signal_job->car_color = ($car && $car->carColor) ? $car->carColor->name : null;
            $gps_stop_signal_job->fleet = ($car) ? $car->fleet : null;
            $gps_stop_signal_job->vid = ($car) ? $car->vid : null;
            $gps_stop_signal_job->sim = ($car) ? $car->sim : null;
        }
        $stop_status_list = GpsTrait::getStopStatusList();

        $page_title = __('lang.view') . __('gps.job_stop_tab');
        return view('admin.gps-stop-signal-jobs.form',  [
            'page_title' => $page_title,
            'd' => $gps_stop_signal_job,
            'stop_status_list' => $stop_status_list,
            'view' => true,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stop_status' => ['required'],
            'stop_date' => ['required'],
        ], [], [
            'stop_status' => __('gps.stop_status'),
            'stop_date' => __('gps.stop_date'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $stop_job = GpsRemoveStopSignal::find($request->id);
        if ($stop_job) {
            $stop_job->stop_status = $request->stop_status;
            $stop_job->stop_date = $request->stop_date;
            $stop_job->stop_remark = $request->stop_remark;
            $stop_job->save();
        }
        $redirect_route = route('admin.gps-stop-signal-jobs.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function updateStopJob(Request $request)
    {
        if (empty($request->stop_status)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกสถานะหยุดสัญญาณ GPS',
            ], 422);
        }
        if (empty($request->arr_update_stop)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกงานแจ้งหยุดสัญญาณ GPS',
            ], 422);
        }
        if ($request->arr_update_stop > 0) {
            foreach ($request->arr_update_stop as $key => $item) {
                $update_stop = GpsRemoveStopSignal::find($item['id']);
                if ($update_stop) {
                    $update_stop->stop_status = $request->stop_status;
                    $update_stop->stop_date = $request->stop_date;
                    $update_stop->stop_remark = $request->stop_remark;
                    $update_stop->save();
                }
            }
        }
        $redirect_route = route('admin.gps-stop-signal-jobs.index');
        return $this->responseValidateSuccess($redirect_route);
    }
}
