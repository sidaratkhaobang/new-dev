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
use App\Models\Car;
use App\Traits\GpsTrait;

class GpsRemoveStopSignalJobController extends Controller
{
    use GpsTrait;

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSRemoveStopSignalJob);
        $worksheet_no = $request->worksheet_no;
        $chassis_no = $request->chassis_no;
        $vid = $request->vid;
        $remove_date = $request->remove_date;
        $remove_status = $request->remove_status;
        $stop_date = $request->stop_date;
        $stop_status = $request->stop_status;
        $job_type = $request->job_type;
        $today = date('Y-m-d');

        $list = GpsRemoveStopSignal::leftJoin('cars', 'cars.id', '=', 'gps_remove_stop_signals.car_id')
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
            ->when($remove_status, function ($query) use ($remove_status) {
                return $query->where('gps_remove_stop_signals.remove_status', $remove_status);
            })
            ->when($stop_status, function ($query) use ($stop_status) {
                return $query->where('gps_remove_stop_signals.stop_status', $stop_status);
            })
            ->when($job_type, function ($query) use ($job_type) {
                return $query->where('gps_remove_stop_signals.job_type', $job_type);
            })
            ->when($remove_date, function ($query) use ($remove_date) {
                return $query->where('gps_remove_stop_signals.remove_date', $remove_date);
            })
            ->when($stop_date, function ($query) use ($stop_date) {
                return $query->where('gps_remove_stop_signals.stop_date', $stop_date);
            });
        // if (empty($remove_date) && empty($stop_date)) {
        //     $list = $list->whereDate('gps_remove_stop_signals.inform_date', '=', $today);
        // }
        $list = $list->paginate(PER_PAGE);

        $chassis_no_list = GpsRemoveStopSignal::select('cars.chassis_no as name', 'cars.chassis_no as id')->leftJoin('cars', 'cars.id', '=', 'gps_remove_stop_signals.car_id')->distinct()->get();
        $vid_list = GpsRemoveStopSignal::select('cars.vid as name', 'cars.vid as id')->whereNotNull('cars.vid')->leftJoin('cars', 'cars.id', '=', 'gps_remove_stop_signals.car_id')->distinct()->get();
        $worksheet_list = GpsRemoveStopSignal::select('worksheet_no as name', 'id')->get();
        $remove_list = GpsTrait::getRemoveStatus();
        $stop_list = GpsTrait::getStopStatus();
        $job_type_list = GpsTrait::getJobTypeList();

        return view('admin.gps-remove-stop-signal-jobs.index', [
            'list' => $list,
            'worksheet_no' => $worksheet_no,
            'chassis_no' => $chassis_no,
            'vid' => $vid,
            'remove_date' => $remove_date,
            'remove_status' => $remove_status,
            'stop_date' => $stop_date,
            'stop_status' => $stop_status,
            'chassis_no_list' => $chassis_no_list,
            'vid_list' => $vid_list,
            'worksheet_list' => $worksheet_list,
            'remove_list' => $remove_list,
            'stop_list' => $stop_list,
            'job_type_list' => $job_type_list,
            'job_type' => $job_type,
        ]);
    }

    public function edit(GpsRemoveStopSignal $gps_remove_stop_signal_job)
    {
        $this->authorize(Actions::Manage . '_' . Resources::GPSRemoveStopSignalJob);
        $car = Car::find($gps_remove_stop_signal_job->car_id);
        if ($car) {
            $gps_remove_stop_signal_job->license_plate = ($car) ? $car->license_plate : null;
            $gps_remove_stop_signal_job->engine_no = ($car) ? $car->engine_no : null;
            $gps_remove_stop_signal_job->chassis_no = ($car) ? $car->chassis_no : null;;
            $gps_remove_stop_signal_job->car_class = ($car && $car->carClass) ? $car->carClass->full_name : null;
            $gps_remove_stop_signal_job->car_color = ($car && $car->carColor) ? $car->carColor->name : null;
            $gps_remove_stop_signal_job->fleet = ($car) ? $car->fleet : null;
            $gps_remove_stop_signal_job->vid = ($car) ? $car->vid : null;
            $gps_remove_stop_signal_job->sim = ($car) ? $car->sim : null;
        }
        $page_title = __('lang.edit') . __('gps.gps_signal');
        return view('admin.gps-remove-stop-signal-jobs.form',  [
            'page_title' => $page_title,
            'd' => $gps_remove_stop_signal_job,
        ]);
    }

    public function show(GpsRemoveStopSignal $gps_remove_stop_signal_job)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSRemoveStopSignalJob);
        $car = Car::find($gps_remove_stop_signal_job->car_id);
        if ($car) {
            $gps_remove_stop_signal_job->license_plate = ($car) ? $car->license_plate : null;
            $gps_remove_stop_signal_job->engine_no = ($car) ? $car->engine_no : null;
            $gps_remove_stop_signal_job->chassis_no = ($car) ? $car->chassis_no : null;;
            $gps_remove_stop_signal_job->car_class = ($car && $car->carClass) ? $car->carClass->full_name : null;
            $gps_remove_stop_signal_job->car_color = ($car && $car->carColor) ? $car->carColor->name : null;
            $gps_remove_stop_signal_job->fleet = ($car) ? $car->fleet : null;
            $gps_remove_stop_signal_job->vid = ($car) ? $car->vid : null;
            $gps_remove_stop_signal_job->sim = ($car) ? $car->sim : null;
        }

        $page_title = __('lang.view') . __('gps.gps_signal');
        return view('admin.gps-remove-stop-signal-jobs.form',  [
            'page_title' => $page_title,
            'd' => $gps_remove_stop_signal_job,
            'view' => true,
        ]);
    }

    public function store(Request $request)
    {
        $remove_stop_job = GpsRemoveStopSignal::find($request->id);
        if ($remove_stop_job) {
            $remove_stop_job->inform_date = $request->inform_date;
            $remove_stop_job->save();
        }

        $redirect_route = route('admin.gps-remove-stop-signal-jobs.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function sendRemoveJob(Request $request)
    {
        if ($request->arr_send_remove > 0) {
            foreach ($request->arr_send_remove as $key => $item) {
                $send_remove = GpsRemoveStopSignal::find($item['id']);
                if ($send_remove) {
                    $send_remove->remove_status = GPSStopStatusEnum::ALERT_REMOVE_GPS;
                    $send_remove->save();
                }
            }
        }
        $redirect_route = route('admin.gps-remove-stop-signal-jobs.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function sendStopJob(Request $request)
    {
        if ($request->arr_send_stop > 0) {
            foreach ($request->arr_send_stop as $key => $item) {
                $send_stop = GpsRemoveStopSignal::find($item['id']);
                if ($send_stop) {
                    $send_stop->stop_status = GPSStopStatusEnum::ALERT_STOP_SIGNAL;
                    $send_stop->save();
                }
            }
        }
        $redirect_route = route('admin.gps-remove-stop-signal-jobs.index');
        return $this->responseValidateSuccess($redirect_route);
    }
}
