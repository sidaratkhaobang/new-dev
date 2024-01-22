<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Enums\GPSStatusEnum;
use App\Models\Car;
use App\Models\GpsCheckSignal;
use App\Models\Rental;
use App\Models\LongTermRental;
use DateTime;
use Illuminate\Support\Facades\Auth;
use App\Traits\GpsTrait;

class GpsCheckSignalJobKratosController extends Controller
{
    use GpsTrait;

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSCheckSignalKratos);
        $user = Auth::user();
        $license_plate = $request->license_plate;
        $chassis_no = $request->chassis_no;
        $vid = $request->vid;
        $status = $request->status;
        $must_check_date = $request->must_check_date;
        $check_date = $request->check_date;
        $today = date('Y-m-d');

        $list = GpsCheckSignal::leftJoin('cars', 'cars.id', '=', 'gps_check_signals.car_id')
            // ->where('gps_check_signals.branch_id', $user->branch_id)
            ->where('gps_check_signals.status', GPSStatusEnum::CHECK_SIGNAL)
            // ->whereDate('gps_check_signals.must_check_date', '=', $today)
            ->select(
                'gps_check_signals.id',
                'gps_check_signals.status',
                'gps_check_signals.must_check_date',
                'gps_check_signals.check_date',
                'gps_check_signals.repair_date',
                'gps_check_signals.remark',
                'gps_check_signals.job_type',
                'gps_check_signals.job_id',
                'cars.license_plate',
                'cars.chassis_no',
                'cars.vid',
            )
            ->orderBy('gps_check_signals.created_at', 'desc')
            // ->search($request)
            ->when($vid, function ($query) use ($vid) {
                return $query->where('cars.vid', 'like', '%' . $vid . '%');
            })
            ->when($chassis_no, function ($query) use ($chassis_no) {
                return $query->where('cars.chassis_no', 'like', '%' . $chassis_no . '%');
            })
            ->when($license_plate, function ($query) use ($license_plate) {
                return $query->where('cars.license_plate', 'like', '%' . $license_plate . '%');
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('gps_check_signals.status', $status);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('gps_check_signals.status', $status);
            })
            ->when($must_check_date, function ($query) use ($must_check_date) {
                return $query->where('gps_check_signals.must_check_date', $must_check_date);
            })
            ->when($check_date, function ($query) use ($check_date) {
                return $query->where('gps_check_signals.check_date', $check_date);
            })
            ->paginate(PER_PAGE);
        $list->map(function ($item) {
            $item->rental_date = '';
            if (strcmp($item->job_type, Rental::class) == 0) {
                $rental = Rental::find($item->job_id);
                $item->rental_date = ($rental) ? get_thai_date_format($rental->pickup_date, 'd/m/Y') : null;
            } elseif (strcmp($item->job_type, LongTermRental::class) == 0) {
                $long_rental = LongTermRental::find($item->job_id);
                $item->rental_date = ($long_rental) ? get_thai_date_format($long_rental->delivery_date, 'd/m/Y') : null;
            }
            return $item;
        });

        $license_plate_list = GpsTrait::queryLicensePlateList($user->branch_id);
        $chassis_no_list = GpsTrait::queryChassisNoList($user->branch_id);
        $vid_list = GpsTrait::queryVidList($user->branch_id);
        $status_list = GpsTrait::getStatus();
        $short_term_count = GpsTrait::queryShortTermCount($user->branch_id);
        $short_branch_count = GpsTrait::queryShortBranchCount($user->branch_id);
        $long_term_count = GpsTrait::queryLongTermCount($user->branch_id);
        $replacement_car_count = GpsTrait::queryReplacementCarCount($user->branch_id);

        if (strcmp($user->branch->is_main, BOOL_TRUE)  == 0) {
            $allow_user = true;
        } else {
            $allow_user = false;
        }

        return view('admin.gps-check-signal-job-kratos.index', [
            'list' => $list,
            'license_plate_list' => $license_plate_list,
            'chassis_no_list' => $chassis_no_list,
            'vid_list' => $vid_list,
            'license_plate' => $license_plate,
            'chassis_no' => $chassis_no,
            'vid' => $vid,
            'status' => $status,
            'must_check_date' => $must_check_date,
            'check_date' => $check_date,
            'status_list' => $status_list,
            'allow_user' => $allow_user,
            'short_term_count' => $short_term_count,
            'short_branch_count' => $short_branch_count,
            'long_term_count' => $long_term_count,
            'replacement_car_count' => $replacement_car_count,
        ]);
    }

    public function edit(GpsCheckSignal $gps_check_signal_job_krato)
    {
        $this->authorize(Actions::Manage . '_' . Resources::GPSCheckSignalKratos);
        $user = Auth::user();
        if (strcmp($gps_check_signal_job_krato->job_type, Rental::class) === 0) {
            $rental = Rental::find($gps_check_signal_job_krato->job_id);
            if ($rental) {
                $gps_check_signal_job_krato->worksheet_no = ($rental->worksheet_no) ? $rental->worksheet_no : '';
                $gps_check_signal_job_krato->service_type = ($rental && $rental->serviceType) ? __('gps.service_type_' . $rental->serviceType->service_type) : null;
                $gps_check_signal_job_krato->customer = ($rental) ? $rental->customer_name : null;
                $gps_check_signal_job_krato->pickup_date = ($rental) ? get_thai_date_format($rental->pickup_date, 'd/m/Y H:i') : null;
                $gps_check_signal_job_krato->return_date = ($rental) ? get_thai_date_format($rental->return_date, 'd/m/Y H:i') : null;
            }
        }

        $car = Car::find($gps_check_signal_job_krato->car_id);
        if ($car) {
            $gps_check_signal_job_krato->license_plate = ($car) ? $car->license_plate : null;
            $gps_check_signal_job_krato->engine_no = ($car) ? $car->engine_no : null;
            $gps_check_signal_job_krato->chassis_no = ($car) ? $car->chassis_no : null;;
            $gps_check_signal_job_krato->car_class = ($car && $car->carClass) ? $car->carClass->full_name : null;
            $gps_check_signal_job_krato->car_color = ($car && $car->carColor) ? $car->carColor->name : null;
            $gps_check_signal_job_krato->fleet = ($car) ? $car->fleet : null;
            $gps_check_signal_job_krato->vid = ($car) ? $car->vid : null;
            $gps_check_signal_job_krato->sim = ($car) ? $car->sim : null;
        }

        $short_class = Rental::class;
        $long_class = LongTermRental::class;
        $replacement_car_class = ReplacementCar::class;
        $status_approve = GpsTrait::getStatusApprove();
        $repair_list = GpsTrait::getRepairList();

        $page_title = __('lang.edit') . __('gps.gps_signal');
        return view('admin.gps-check-signal-job-kratos.form',  [
            'page_title' => $page_title,
            'd' => $gps_check_signal_job_krato,
            'short_class' => $short_class,
            'long_class' => $long_class,
            'status_approve' => $status_approve,
            'repair_list' => $repair_list,
            'replacement_car_class' => $replacement_car_class,
        ]);
    }

    public function show(GpsCheckSignal $gps_check_signal_job_krato)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSCheckSignalKratos);
        $user = Auth::user();
        if (strcmp($gps_check_signal_job_krato->job_type, Rental::class) === 0) {
            $rental = Rental::find($gps_check_signal_job_krato->job_id);
            if ($rental) {
                $gps_check_signal_job_krato->worksheet_no = ($rental->worksheet_no) ? $rental->worksheet_no : '';
                $gps_check_signal_job_krato->service_type = ($rental && $rental->serviceType) ? __('gps.service_type_' . $rental->serviceType->service_type) : null;
                $gps_check_signal_job_krato->customer = ($rental) ? $rental->customer_name : null;
                $gps_check_signal_job_krato->pickup_date = ($rental) ? get_thai_date_format($rental->pickup_date, 'd/m/Y H:i') : null;
                $gps_check_signal_job_krato->return_date = ($rental) ? get_thai_date_format($rental->return_date, 'd/m/Y H:i') : null;
            }
        }

        $car = Car::find($gps_check_signal_job_krato->car_id);
        if ($car) {
            $gps_check_signal_job_krato->license_plate = ($car) ? $car->license_plate : null;
            $gps_check_signal_job_krato->engine_no = ($car) ? $car->engine_no : null;
            $gps_check_signal_job_krato->chassis_no = ($car) ? $car->chassis_no : null;;
            $gps_check_signal_job_krato->car_class = ($car && $car->carClass) ? $car->carClass->full_name : null;
            $gps_check_signal_job_krato->car_color = ($car && $car->carColor) ? $car->carColor->name : null;
            $gps_check_signal_job_krato->fleet = ($car) ? $car->fleet : null;
            $gps_check_signal_job_krato->vid = ($car) ? $car->vid : null;
            $gps_check_signal_job_krato->sim = ($car) ? $car->sim : null;
        }

        $short_class = Rental::class;
        $long_class = LongTermRental::class;
        $replacement_car_class = ReplacementCar::class;
        $status_approve = GpsTrait::getStatusApprove();
        $repair_list = GpsTrait::getRepairList();

        $page_title = __('lang.view') . __('gps.gps_signal');
        return view('admin.gps-check-signal-job-kratos.form',  [
            'page_title' => $page_title,
            'd' => $gps_check_signal_job_krato,
            'short_class' => $short_class,
            'long_class' => $long_class,
            'status_approve' => $status_approve,
            'repair_list' => $repair_list,
            'replacement_car_class' => $replacement_car_class,
            'view' => true,
        ]);
    }

    public function store(Request $request)
    {
        $check_signal = GpsCheckSignal::find($request->id);
        $check_signal->check_date = $request->check_date;
        $check_signal->status = (in_array($request->status, [GPSStatusEnum::NO_SIGNAL, GPSStatusEnum::NORMAL_SIGNAL])) ? $request->status : $check_signal->status;
        $check_signal->remark = $request->remark;
        $check_signal->save();

        if (strcmp($check_signal->status, GPSStatusEnum::NO_SIGNAL) == 0) {
            $check_signal->repair_date = $request->repair_date;
            $check_signal->repair_immediately = $request->repair_immediately;
            $check_signal->remark_repair = $request->remark_repair;
            $check_signal->save();
        }

        $redirect_route = route('admin.gps-check-signal-job-kratos.index');
        return $this->responseValidateSuccess($redirect_route);
    }
}
