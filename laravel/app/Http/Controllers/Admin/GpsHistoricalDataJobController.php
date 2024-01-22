<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Enums\GPSHistoricalDataStatusEnum;
use App\Models\GpsHistoricalData;
use App\Models\GpsHistoricalDataLine;
use App\Models\Car;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Traits\GpsTrait;
use Rap2hpoutre\FastExcel\FastExcel;

class GpsHistoricalDataJobController extends Controller
{
    use GpsTrait;

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSHistoricalDataJob);

        $list = GpsHistoricalData::select(
            'id',
            'status',
            'worksheet_no',
            'created_at',
            'created_by',
        )
            ->whereNotIn('status', [GPSHistoricalDataStatusEnum::DRAFT])
            ->search($request)
            ->orderBy('created_at', 'desc')
            ->paginate(PER_PAGE);

        $worksheet_list = GpsHistoricalData::select('worksheet_no as name', 'id')->whereNotIn('status', [GPSHistoricalDataStatusEnum::DRAFT])->get();
        $status_list = GpsTrait::getHistoricalStatus();
        $request_user_list = User::select('name', 'id')->get();

        return view('admin.gps-historical-data-jobs.index', [
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

    public function edit(GpsHistoricalData $gps_historical_data_job)
    {
        $this->authorize(Actions::Manage . '_' . Resources::GPSHistoricalDataJob);
        $car_query = GpsHistoricalDataLine::leftJoin('cars', 'cars.id', '=', 'gps_historical_data_lines.car_id')
            ->where('gps_historical_data_lines.gps_historical_data_id', $gps_historical_data_job->id)
            ->select(
                'cars.id as car_id',
                'cars.license_plate',
                'gps_historical_data_lines.id as id',
                'gps_historical_data_lines.start_date',
                'gps_historical_data_lines.end_date',
            )->get();

        $car_list = $car_query->map(function ($item) {
            $start_timestamp = (strtotime($item->start_date));
            $start_date = ($start_timestamp) ? date('d/m/Y', $start_timestamp) : null;
            $start_time = ($start_timestamp) ? date('H:i', $start_timestamp) : null;
            $end_timestamp = (strtotime($item->end_date));
            $end_date = ($end_timestamp) ? date('d/m/Y', $end_timestamp) : null;
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
        $status_approve_list = $this->getStatusApprove();
        $type_file_arr = explode(",", $gps_historical_data_job->type_file);

        $page_title = __('lang.edit') . __('gps.page_title_data') . ' ' . 'เลขที่' . $gps_historical_data_job->worksheet_no;
        return view('admin.gps-historical-data-jobs.form',  [
            'page_title' => $page_title,
            'd' => $gps_historical_data_job,
            'car_list' => $car_list,
            'type_file_list' => $type_file_list,
            'status_approve_list' => $status_approve_list,
            'doc_additional_files' => [],
            'type_file_arr' => $type_file_arr,
        ]);
    }

    public function show(GpsHistoricalData $gps_historical_data_job)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSHistoricalDataJob);
        $car_query = GpsHistoricalDataLine::leftJoin('cars', 'cars.id', '=', 'gps_historical_data_lines.car_id')
            ->where('gps_historical_data_lines.gps_historical_data_id', $gps_historical_data_job->id)
            ->select(
                'cars.id as car_id',
                'cars.license_plate',
                'gps_historical_data_lines.id as id',
                'gps_historical_data_lines.start_date',
                'gps_historical_data_lines.end_date',
            )->get();

        $car_list = $car_query->map(function ($item) {
            $start_timestamp = (strtotime($item->start_date));
            $start_date = ($start_timestamp) ? date('d/m/Y', $start_timestamp) : null;
            $start_time = ($start_timestamp) ? date('H:i', $start_timestamp) : null;
            $end_timestamp = (strtotime($item->end_date));
            $end_date = ($end_timestamp) ? date('d/m/Y', $end_timestamp) : null;
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
        $status_approve_list = $this->getStatusApprove();
        $doc_additional_files = $gps_historical_data_job->getMedia('doc_additional_files');
        $doc_additional_files = get_medias_detail($doc_additional_files);
        $type_file_arr = explode(",", $gps_historical_data_job->type_file);

        $page_title = __('lang.view') . __('gps.page_title_data')  . ' ' . 'เลขที่' . $gps_historical_data_job->worksheet_no;
        return view('admin.gps-historical-data-jobs.form',  [
            'page_title' => $page_title,
            'd' => $gps_historical_data_job,
            'car_list' => $car_list,
            'type_file_list' => $type_file_list,
            'status_approve_list' => $status_approve_list,
            'view' => true,
            'doc_additional_files' => $doc_additional_files,
            'type_file_arr' => $type_file_arr,
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'status' => ['required'],
            'reason' => [
                Rule::when($request->status == GPSHistoricalDataStatusEnum::REJECT, ['required']),
            ],
        ], [], [
            'status' => __('lang.status'),
            'reason' => __('gps.reason'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $historical_alert = GpsHistoricalData::firstOrNew(['id' => $request->id]);
        $historical_alert->status = $request->status;
        $historical_alert->reason = $request->reason;
        $historical_alert->link = $request->link;
        $historical_alert->save();

        if ($request->doc_additional__pending_delete_ids) {
            $pending_delete_ids = $request->doc_additional__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $historical_alert->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('doc_additional')) {
            foreach ($request->file('doc_additional') as $file) {
                if ($file->isValid()) {
                    $historical_alert->addMedia($file)->toMediaCollection('doc_additional_files');
                }
            }
        }


        $redirect_route = route('admin.gps-historical-data-jobs.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function getStatusApprove()
    {
        $status = collect([
            (object) [
                'id' => GPSHistoricalDataStatusEnum::CONFIRM,
                'name' => __('gps.data_text_' . GPSHistoricalDataStatusEnum::CONFIRM),
                'value' => GPSHistoricalDataStatusEnum::CONFIRM,
            ],
            (object) [
                'id' => GPSHistoricalDataStatusEnum::REJECT,
                'name' => __('gps.data_text_' . GPSHistoricalDataStatusEnum::REJECT),
                'value' => GPSHistoricalDataStatusEnum::REJECT,
            ],
        ]);
        return $status;
    }

    public function exportExcel(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSHistoricalDataJob);
        $gps_historical_data_job_id = $request->id;
        $excel_cars = [];

        $car_query = GpsHistoricalDataLine::leftJoin('cars', 'cars.id', '=', 'gps_historical_data_lines.car_id')
            ->where('gps_historical_data_lines.gps_historical_data_id', $gps_historical_data_job_id)
            ->select(
                'cars.id as car_id',
                'cars.license_plate',
                'gps_historical_data_lines.id as id',
                'gps_historical_data_lines.start_date',
                'gps_historical_data_lines.end_date',
            )->get();

        $excel_cars = $car_query->map(function ($item) {
            $start_timestamp = (strtotime($item->start_date));
            $start_date = ($start_timestamp) ? date('d/m/Y', $start_timestamp) : null;
            $start_time = ($start_timestamp) ? date('H:i', $start_timestamp) : null;
            $end_timestamp = (strtotime($item->end_date));
            $end_date = ($end_timestamp) ? date('d/m/Y', $end_timestamp) : null;
            $end_time = ($end_timestamp) ? date('H:i', $end_timestamp) : null;
            $end_date = ($end_date) ? '-' . $end_date : null;
            $item->license_plate = $item->license_plate;
            $item->date = $start_date . $end_date;
            $item->start_time = ($start_time > '00:00') ? $start_time : null;
            $item->end_time = ($end_time > '00:00') ? $end_time : null;
            return $item;
        });

        if (count($excel_cars) > 0) {
            return (new FastExcel($excel_cars))->download('file.xlsx', function ($line) {
                return [
                    'ทะเบียน' => $line->license_plate,
                    'วันที่' => $line->date,
                    'เวลาเริ่มต้น (น.)' => $line->start_time,
                    'เวลาสิ้นสุด (น.)' => $line->end_time,
                ];
            });
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }
}
