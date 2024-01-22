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
use DateTime;
use Illuminate\Support\Facades\Auth;
use App\Traits\GpsTrait;

class GpsRemoveSignalJobController extends Controller
{
    use GpsTrait;

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSRemoveSignalJob);
        $worksheet_no = $request->worksheet_no;
        $chassis_no = $request->chassis_no;
        $vid = $request->vid;
        $remove_date = $request->remove_date;
        $remove_status_id = $request->remove_status_id;
        $job_type = $request->job_type;
        $today = date('Y-m-d');

        $list = GpsRemoveStopSignal::leftJoin('cars', 'cars.id', '=', 'gps_remove_stop_signals.car_id')
            // ->whereDate('gps_remove_stop_signals.inform_date', '=', $today)
            ->whereIn('remove_status', [GPSStopStatusEnum::ALERT_REMOVE_GPS, GPSStopStatusEnum::REMOVE_GPS, GPSStopStatusEnum::NOT_INSTALL])
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
            ->when($remove_status_id, function ($query) use ($remove_status_id) {
                return $query->where('gps_remove_stop_signals.remove_status', $remove_status_id);
            })
            ->when($job_type, function ($query) use ($job_type) {
                return $query->where('gps_remove_stop_signals.job_type', $job_type);
            })
            ->when($remove_date, function ($query) use ($remove_date) {
                return $query->where('gps_remove_stop_signals.remove_date', $remove_date);
            });
            // if (empty($remove_date)) {
            //     $list = $list->whereDate('gps_remove_stop_signals.inform_date', '=', $today);
            // }
            $list = $list->paginate(PER_PAGE);

        $remove_status_list = GpsTrait::getRemoveStatusList();
        $chassis_no_list = GpsRemoveStopSignal::select('cars.chassis_no as name', 'cars.chassis_no as id')->leftJoin('cars', 'cars.id', '=', 'gps_remove_stop_signals.car_id')->distinct()->get();
        $vid_list = GpsRemoveStopSignal::select('cars.vid as name', 'cars.vid as id')->whereNotNull('cars.vid')->leftJoin('cars', 'cars.id', '=', 'gps_remove_stop_signals.car_id')->distinct()->get();
        $worksheet_list = GpsRemoveStopSignal::select('worksheet_no as name', 'id')->get();
        $remove_list = GpsTrait::getRemoveStatus();
        $job_type_list = GpsTrait::getJobTypeList();

        return view('admin.gps-remove-signal-jobs.index', [
            'list' => $list,
            'worksheet_no' => $worksheet_no,
            'chassis_no' => $chassis_no,
            'vid' => $vid,
            'remove_date' => $remove_date,
            'remove_status_id' => $remove_status_id,
            'chassis_no_list' => $chassis_no_list,
            'vid_list' => $vid_list,
            'worksheet_list' => $worksheet_list,
            'remove_list' => $remove_list,
            'job_type_list' => $job_type_list,
            'job_type' => $job_type,
            'remove_status_list' => $remove_status_list,
        ]);
    }

    public function edit(GpsRemoveStopSignal $gps_remove_signal_job)
    {
        $this->authorize(Actions::Manage . '_' . Resources::GPSRemoveSignalJob);
        $car = Car::find($gps_remove_signal_job->car_id);
        if ($car) {
            $gps_remove_signal_job->license_plate = ($car) ? $car->license_plate : null;
            $gps_remove_signal_job->engine_no = ($car) ? $car->engine_no : null;
            $gps_remove_signal_job->chassis_no = ($car) ? $car->chassis_no : null;;
            $gps_remove_signal_job->car_class = ($car && $car->carClass) ? $car->carClass->full_name : null;
            $gps_remove_signal_job->car_color = ($car && $car->carColor) ? $car->carColor->name : null;
            $gps_remove_signal_job->fleet = ($car) ? $car->fleet : null;
            $gps_remove_signal_job->vid = ($car) ? $car->vid : null;
            $gps_remove_signal_job->sim = ($car) ? $car->sim : null;
        }
        $remove_status_list = GpsTrait::getRemoveStatusList();

        $page_title = __('lang.edit') . __('gps.job_remove_tab');
        return view('admin.gps-remove-signal-jobs.form',  [
            'page_title' => $page_title,
            'd' => $gps_remove_signal_job,
            'remove_status_list' => $remove_status_list,
        ]);
    }

    public function show(GpsRemoveStopSignal $gps_remove_signal_job)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSRemoveSignalJob);
        $car = Car::find($gps_remove_signal_job->car_id);
        if ($car) {
            $gps_remove_signal_job->license_plate = ($car) ? $car->license_plate : null;
            $gps_remove_signal_job->engine_no = ($car) ? $car->engine_no : null;
            $gps_remove_signal_job->chassis_no = ($car) ? $car->chassis_no : null;;
            $gps_remove_signal_job->car_class = ($car && $car->carClass) ? $car->carClass->full_name : null;
            $gps_remove_signal_job->car_color = ($car && $car->carColor) ? $car->carColor->name : null;
            $gps_remove_signal_job->fleet = ($car) ? $car->fleet : null;
            $gps_remove_signal_job->vid = ($car) ? $car->vid : null;
            $gps_remove_signal_job->sim = ($car) ? $car->sim : null;
        }
        $remove_status_list = GpsTrait::getRemoveStatusList();

        $page_title = __('lang.view') . __('gps.job_remove_tab');
        return view('admin.gps-remove-signal-jobs.form',  [
            'page_title' => $page_title,
            'd' => $gps_remove_signal_job,
            'remove_status_list' => $remove_status_list,
            'view' => true,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'remove_status' => ['required'],
        ], [], [
            'remove_status' => __('gps.remove_status'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        if (strcmp($request->remove_status, GPSStopStatusEnum::REMOVE_GPS) == 0 && empty($request->remove_date)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกวันที่ถอด GPS',
            ], 422);
        }
        $remove_job = GpsRemoveStopSignal::find($request->id);
        if ($remove_job) {
            $remove_job->remove_status = $request->remove_status;
            $remove_job->remove_date = $request->remove_date;
            $remove_job->remove_remark = $request->remove_remark;
            if (strcmp($remove_job->is_check_gps, STATUS_ACTIVE) == 0 && strcmp($request->remove_status, GPSStopStatusEnum::REMOVE_GPS) == 0) {
                $remove_job->stop_status = GPSStopStatusEnum::ALERT_STOP_SIGNAL;
            }
            $remove_job->save();
        }
        $redirect_route = route('admin.gps-remove-signal-jobs.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function updateRemoveJob(Request $request)
    {
        if (empty($request->remove_status)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกสถานะการถอด GPS',
            ], 422);
        }
        if (strcmp($request->remove_status, GPSStopStatusEnum::REMOVE_GPS) == 0 && empty($request->remove_date)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกวันที่ถอด GPS',
            ], 422);
        }
        if (empty($request->arr_update_remove)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกงานแจ้งถอด GPS',
            ], 422);
        }
        if ($request->arr_update_remove > 0) {
            foreach ($request->arr_update_remove as $key => $item) {
                $update_remove = GpsRemoveStopSignal::find($item['id']);
                if ($update_remove) {
                    $update_remove->remove_status = $request->remove_status;
                    $update_remove->remove_date = $request->remove_date;
                    $update_remove->remove_remark = $request->remove_remark;
                    if (strcmp($update_remove->is_check_gps, STATUS_ACTIVE) == 0 && strcmp($request->remove_status, GPSStopStatusEnum::REMOVE_GPS) == 0) {
                        $update_remove->stop_status = GPSStopStatusEnum::ALERT_STOP_SIGNAL;
                    }
                    $update_remove->save();
                }
            }
        }
        $redirect_route = route('admin.gps-remove-signal-jobs.index');
        return $this->responseValidateSuccess($redirect_route);
    }
}
