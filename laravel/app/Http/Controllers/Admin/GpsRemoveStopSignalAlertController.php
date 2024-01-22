<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Enums\GPSJobTypeEnum;
use App\Enums\CarEnum;
use App\Enums\GPSStopStatusEnum;
use App\Models\Car;
use App\Models\GpsRemoveStopSignal;
use DateTime;
use Illuminate\Support\Facades\Auth;
use App\Traits\GpsTrait;


class GpsRemoveStopSignalAlertController extends Controller
{
    use GpsTrait;

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSRemoveStopSignalAlert);
        $worksheet_no = $request->worksheet_no;
        $chassis_no = $request->chassis_no;
        $vid = $request->vid;
        $remove_date = $request->remove_date;
        $remove_status = $request->remove_status;
        $stop_date = $request->stop_date;
        $stop_status = $request->stop_status;
        $job_type = $request->job_type;

        $list = GpsRemoveStopSignal::leftJoin('cars', 'cars.id', '=', 'gps_remove_stop_signals.car_id')
            ->select(
                'gps_remove_stop_signals.id',
                'gps_remove_stop_signals.worksheet_no',
                'gps_remove_stop_signals.job_type',
                'gps_remove_stop_signals.is_check_gps',
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
            })
            ->paginate(PER_PAGE);

        $chassis_no_list = GpsRemoveStopSignal::select('cars.chassis_no as name', 'cars.chassis_no as id')->leftJoin('cars', 'cars.id', '=', 'gps_remove_stop_signals.car_id')->distinct()->get();
        $vid_list = GpsRemoveStopSignal::select('cars.vid as name', 'cars.vid as id')->whereNotNull('cars.vid')->leftJoin('cars', 'cars.id', '=', 'gps_remove_stop_signals.car_id')->distinct()->get();
        $worksheet_list = GpsRemoveStopSignal::select('worksheet_no as name', 'id')->get();
        $remove_list = GpsTrait::getRemoveStatus();
        $stop_list = GpsTrait::getStopStatus();
        $job_type_list = GpsTrait::getJobTypeList();

        return view('admin.gps-remove-stop-signal-alerts.index', [
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

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::GPSCheckSignalAlert);
        $d = new GpsRemoveStopSignal();
        $job_list = GpsTrait::getJobTypeList();

        $page_title = __('lang.create') . __('gps.page_title_stop');
        return view('admin.gps-remove-stop-signal-alerts.form', [
            'd' =>  $d,
            'page_title' => $page_title,
            'job_list' => $job_list,
            'create' => true,
            'doc_additional_files' => [],
        ]);
    }

    public function edit(GpsRemoveStopSignal $gps_remove_stop_signal_alert)
    {
        $this->authorize(Actions::Manage . '_' . Resources::GPSRemoveStopSignalAlert);
        $car_list = Car::where('id', $gps_remove_stop_signal_alert->car_id)
            ->select('id', 'license_plate', 'chassis_no', 'engine_no', 'vid', 'car_class_id', 'car_color_id')->get();
        $car_list->map(function ($item) use ($gps_remove_stop_signal_alert) {
            $item->license_plate_text = $item->license_plate;
            $item->license_plate_id = $item->id;
            $item->chassis_no_text = $item->chassis_no;
            $item->chassis_no_id = $item->id;
            $item->engine_no_text = $item->engine_no;
            $item->engine_no_id = $item->id;
            $item->vid_text = $item->vid;
            $item->vid_id = $item->id;
            $item->gps_remark = $gps_remove_stop_signal_alert->remark;
            $item->is_check_gps = $gps_remove_stop_signal_alert->is_check_gps;
            $item->car_class_name = ($item->carClass) ? $item->carClass->full_name : null;
            $item->car_color_name = ($item->carColor) ? $item->carColor->name : null;
            $item->gps_id = $gps_remove_stop_signal_alert->id;
            return $item;
        });

        $job_list = GpsTrait::getJobTypeList();
        $doc_additional_files = $gps_remove_stop_signal_alert->getMedia('doc_additional_files');
        $doc_additional_files = get_medias_detail($doc_additional_files);

        $page_title = __('lang.edit') . __('gps.gps_signal');
        return view('admin.gps-remove-stop-signal-alerts.form',  [
            'page_title' => $page_title,
            'd' => $gps_remove_stop_signal_alert,
            'job_list' => $job_list,
            'car_list' => $car_list,
            'edit' => true,
            'doc_additional_files' => $doc_additional_files,
        ]);
    }

    public function show(GpsRemoveStopSignal $gps_remove_stop_signal_alert)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSRemoveStopSignalAlert);
        $car_list = Car::where('id', $gps_remove_stop_signal_alert->car_id)
            ->select('id', 'license_plate', 'chassis_no', 'engine_no', 'vid', 'car_class_id', 'car_color_id')->get();
        $car_list->map(function ($item) use ($gps_remove_stop_signal_alert) {
            $item->license_plate_text = $item->license_plate;
            $item->license_plate_id = $item->id;
            $item->chassis_no_text = $item->chassis_no;
            $item->chassis_no_id = $item->id;
            $item->engine_no_text = $item->engine_no;
            $item->engine_no_id = $item->id;
            $item->vid_text = $item->vid;
            $item->vid_id = $item->id;
            $item->gps_remark = $gps_remove_stop_signal_alert->remark;
            $item->is_check_gps = $gps_remove_stop_signal_alert->is_check_gps;
            $item->car_class_name = ($item->carClass) ? $item->carClass->full_name : null;
            $item->car_color_name = ($item->carColor) ? $item->carColor->name : null;
            $item->gps_id = $gps_remove_stop_signal_alert->id;
            return $item;
        });

        $job_list = GpsTrait::getJobTypeList();
        $doc_additional_files = $gps_remove_stop_signal_alert->getMedia('doc_additional_files');
        $doc_additional_files = get_medias_detail($doc_additional_files);

        $page_title = __('lang.view') . __('gps.gps_signal');
        return view('admin.gps-remove-stop-signal-alerts.form',  [
            'page_title' => $page_title,
            'd' => $gps_remove_stop_signal_alert,
            'job_list' => $job_list,
            'car_list' => $car_list,
            'view' => true,
            'doc_additional_files' => $doc_additional_files,
        ]);
    }


    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'job_type' => ['required'],
            'cars' => ['required', 'array', 'min:1'],
        ], [], [
            'job_type' => __('gps.job_type'),
            'cars' => __('gps.car_data'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        if (strcmp($request->job_type, GPSJobTypeEnum::CONTRACT_EXPIRED) == 0) {
            $prefix = 'QA';
        } else if (strcmp($request->job_type, GPSJobTypeEnum::AUCTION_SALE) == 0) {
            $prefix = 'AUC';
        } else if (strcmp($request->job_type, GPSJobTypeEnum::TOTAL_LOSS) == 0) {
            $prefix = 'RMD';
        } else if (strcmp($request->job_type, GPSJobTypeEnum::CONTRACT_BRANCH) == 0) {
            $prefix = 'AUC';
        } else if (strcmp($request->job_type, GPSJobTypeEnum::AFTER_CONTRACT_EXPIRED) == 0) {
            $prefix = 'AUU';
        }

        if ($request->cars) {
            foreach ($request->cars as $key => $item) {
                $remove_stop_alert = GpsRemoveStopSignal::firstOrNew(['id' => $item['gps_id']]);
                $remove_stop_count = GpsRemoveStopSignal::where('worksheet_no', 'like', '%' . $prefix . '%')
                    ->where('job_type', $request->job_type)->count();
                if (!($remove_stop_alert->exists)) {
                    $remove_stop_alert->worksheet_no = generateRecordNumber($prefix, $remove_stop_count + 1);
                }
                $remove_stop_alert->job_type = $request->job_type;
                $remove_stop_alert->car_id = $item['license_plate_id'];
                $remove_stop_alert->inform_date = $request->inform_date;
                $remove_stop_alert->remark = $item['gps_remark'];
                $remove_stop_alert->is_check_gps = intval($item['is_check_gps']);

                if (strcmp($item['is_check_gps'], STATUS_ACTIVE) == 0) {
                    $remove_stop_alert->remove_status = GPSStopStatusEnum::WAIT_REMOVE_GPS;
                    $remove_stop_alert->stop_status = null;
                } else if (strcmp($item['is_check_gps'], STATUS_INACTIVE) == 0) {
                    $remove_stop_alert->remove_status = null;
                    $remove_stop_alert->stop_status = GPSStopStatusEnum::WAIT_STOP_SIGNAL;
                }

                $remove_stop_alert->save();
            }
        }

        if ($request->doc_additional__pending_delete_ids) {
            $pending_delete_ids = $request->doc_additional__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $remove_stop_alert->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('doc_additional')) {
            foreach ($request->file('doc_additional') as $file) {
                if ($file->isValid()) {
                    $remove_stop_alert->addMedia($file)->toMediaCollection('doc_additional_files');
                }
            }
        }

        $redirect_route = route('admin.gps-remove-stop-signal-alerts.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function getCarLicensePlate(Request $request)
    {
        $list = Car::select('id', 'license_plate')
            ->whereIn('status', [CarEnum::ACCIDENT, CarEnum::CONTRACT_EXPIRED, CarEnum::PENDING_SALE, CarEnum::PENDING_RETURN, CarEnum::REPAIR, CarEnum::READY_TO_USE])
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('license_plate', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('id', $request->parent_id);
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->license_plate
                ];
            });
        return response()->json($list);
    }

    public function getCarVid(Request $request)
    {
        $list = Car::select('id', 'vid')
            ->whereIn('status', [CarEnum::ACCIDENT, CarEnum::CONTRACT_EXPIRED, CarEnum::PENDING_SALE, CarEnum::PENDING_RETURN, CarEnum::REPAIR, CarEnum::READY_TO_USE])
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('vid', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('id', $request->parent_id);
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->vid
                ];
            });
        return response()->json($list);
    }

    public function getCarEngineNo(Request $request)
    {
        $list = Car::select('id', 'engine_no')
            ->whereIn('status', [CarEnum::ACCIDENT, CarEnum::CONTRACT_EXPIRED, CarEnum::PENDING_SALE, CarEnum::PENDING_RETURN, CarEnum::REPAIR, CarEnum::READY_TO_USE])
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('engine_no', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('id', $request->parent_id);
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->engine_no
                ];
            });
        return response()->json($list);
    }

    public function getCarChassisNo(Request $request)
    {
        $list = Car::select('id', 'chassis_no')
            ->whereIn('status', [CarEnum::ACCIDENT, CarEnum::CONTRACT_EXPIRED, CarEnum::PENDING_SALE, CarEnum::PENDING_RETURN, CarEnum::REPAIR, CarEnum::READY_TO_USE])
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('chassis_no', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('id', $request->parent_id);
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->chassis_no
                ];
            });
        return response()->json($list);
    }

    public function getDefaultCar(Request $request)
    {
        $license_plate_id = $request->license_plate_id;
        $data = [];
        $car = Car::find($license_plate_id);
        $data['car_class'] = ($car && $car->carClass) ? $car->carClass->full_name : null;
        $data['car_color'] = ($car && $car->carColor) ? $car->carColor->name : null;
        return [
            'success' => true,
            'data' => $data,
        ];
    }
}
