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

class GpsCheckSignalJobLongTermController extends Controller
{
    use GpsTrait;
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSCheckSignalLongTerm);
        $user = Auth::user();
        $license_plate = $request->license_plate;
        $chassis_no = $request->chassis_no;
        $vid = $request->vid;
        $status = $request->status;
        $must_check_date = $request->must_check_date;
        $check_date = $request->check_date;
        $today = date('Y-m-d');

        $list = GpsCheckSignal::leftJoin('cars', 'cars.id', '=', 'gps_check_signals.car_id')
            ->where('gps_check_signals.branch_id', $user->branch_id)
            ->where('gps_check_signals.job_type', LongTermRental::class)
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
            if (strcmp($item->job_type, LongTermRental::class) == 0) {
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
            // $route_group = [
            //     'route_short_term' => route('admin.gps-check-signal-jobs.index'),
            //     'route_long_term' => route('admin.gps-check-signal-jobs.long-term.index'),
            //     'route_short_term_branch' => route('admin.gps-check-signal-jobs.short-term-branch.index')
            // ];
            $allow_user = true;
        } else {
            // $route_group = [
            //     'route_short_term' => route('admin.gps-check-signal-jobs.index'),
            // ];
            $allow_user = false;
        }

        return view('admin.gps-check-signal-job-long-term.index', [
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
            // 'route_group' => $route_group,
            'allow_user' => $allow_user,
            'short_term_count' => $short_term_count,
            'short_branch_count' => $short_branch_count,
            'long_term_count' => $long_term_count,
            'replacement_car_count' => $replacement_car_count,
        ]);
    }

    public function edit(GpsCheckSignal $gps_check_signal_job_long_term)
    {
        $this->authorize(Actions::Manage . '_' . Resources::GPSCheckSignalLongTerm);
        $user = Auth::user();
        if (strcmp($gps_check_signal_job_long_term->job_type, LongTermRental::class) === 0) {
            $long_rental = LongTermRental::find($gps_check_signal_job_long_term->job_id);
            if ($long_rental) {
                $gps_check_signal_job_long_term->worksheet_no = ($long_rental->worksheet_no) ? $long_rental->worksheet_no : '';
                $gps_check_signal_job_long_term->long_type = ($long_rental) ?  __('long_term_rentals.type_' . $long_rental->approval_type) : null;
                $gps_check_signal_job_long_term->rental_duration = ($long_rental) ?  $long_rental->rental_duration . 'เดือน' : null;
                $gps_check_signal_job_long_term->delivery_place =  null;
                $gps_check_signal_job_long_term->delivery_date = ($long_rental) ? get_thai_date_format($long_rental->actual_delivery_date, 'd/m/Y') : null;
            }
        }
        $car = Car::find($gps_check_signal_job_long_term->car_id);
        if ($car) {
            $gps_check_signal_job_long_term->license_plate = ($car) ? $car->license_plate : null;
            $gps_check_signal_job_long_term->engine_no = ($car) ? $car->engine_no : null;
            $gps_check_signal_job_long_term->chassis_no = ($car) ? $car->chassis_no : null;;
            $gps_check_signal_job_long_term->car_class = ($car && $car->carClass) ? $car->carClass->full_name : null;
            $gps_check_signal_job_long_term->car_color = ($car && $car->carColor) ? $car->carColor->name : null;
            $gps_check_signal_job_long_term->fleet = ($car) ? $car->fleet : null;
            $gps_check_signal_job_long_term->vid = ($car) ? $car->vid : null;
            $gps_check_signal_job_long_term->sim = ($car) ? $car->sim : null;
        }

        $short_class = Rental::class;
        $long_class = LongTermRental::class;
        $replacement_car_class = ReplacementCar::class;
        $status_approve = GpsTrait::getStatusApprove();
        $repair_list = GpsTrait::getRepairList();

        $page_title = __('lang.edit') . __('gps.gps_signal');
        return view('admin.gps-check-signal-job-long-term.form',  [
            'page_title' => $page_title,
            'd' => $gps_check_signal_job_long_term,
            'short_class' => $short_class,
            'long_class' => $long_class,
            'status_approve' => $status_approve,
            'repair_list' => $repair_list,
            'replacement_car_class' => $replacement_car_class,
        ]);
    }

    public function show(GpsCheckSignal $gps_check_signal_job_long_term)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSCheckSignalLongTerm);
        $user = Auth::user();
        if (strcmp($gps_check_signal_job_long_term->job_type, LongTermRental::class) === 0) {
            $long_rental = LongTermRental::find($gps_check_signal_job_long_term->job_id);
            if ($long_rental) {
                $gps_check_signal_job_long_term->worksheet_no = ($long_rental->worksheet_no) ? $long_rental->worksheet_no : '';
                $gps_check_signal_job_long_term->long_type = ($long_rental) ?  __('long_term_rentals.type_' . $long_rental->approval_type) : null;
                $gps_check_signal_job_long_term->rental_duration = ($long_rental) ?  $long_rental->rental_duration . 'เดือน' : null;
                $gps_check_signal_job_long_term->delivery_place =  null;
                $gps_check_signal_job_long_term->delivery_date = ($long_rental) ? get_thai_date_format($long_rental->actual_delivery_date, 'd/m/Y') : null;
            }
        }

        $car = Car::find($gps_check_signal_job_long_term->car_id);
        if ($car) {
            $gps_check_signal_job_long_term->license_plate = ($car) ? $car->license_plate : null;
            $gps_check_signal_job_long_term->engine_no = ($car) ? $car->engine_no : null;
            $gps_check_signal_job_long_term->chassis_no = ($car) ? $car->chassis_no : null;;
            $gps_check_signal_job_long_term->car_class = ($car && $car->carClass) ? $car->carClass->full_name : null;
            $gps_check_signal_job_long_term->car_color = ($car && $car->carColor) ? $car->carColor->name : null;
            $gps_check_signal_job_long_term->fleet = ($car) ? $car->fleet : null;
            $gps_check_signal_job_long_term->vid = ($car) ? $car->vid : null;
            $gps_check_signal_job_long_term->sim = ($car) ? $car->sim : null;
        }

        $short_class = Rental::class;
        $long_class = LongTermRental::class;
        $replacement_car_class = ReplacementCar::class;
        $status_approve = GpsTrait::getStatusApprove();
        $repair_list = GpsTrait::getRepairList();

        $page_title = __('lang.view') . __('gps.gps_signal');
        return view('admin.gps-check-signal-job-long-term.form',  [
            'page_title' => $page_title,
            'd' => $gps_check_signal_job_long_term,
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

        $redirect_route = route('admin.gps-check-signal-job-long-term.index');
        return $this->responseValidateSuccess($redirect_route);
    }
}
