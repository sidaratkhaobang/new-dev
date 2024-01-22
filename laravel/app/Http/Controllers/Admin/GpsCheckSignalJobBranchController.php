<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Enums\RentalTypeEnum;
use App\Enums\RentalStatusEnum;
use App\Enums\GPSStatusEnum;
use App\Enums\LongTermRentalStatusEnum;
use App\Models\Car;
use App\Models\GpsCheckSignal;
use App\Models\Rental;
use App\Models\LongTermRental;
use App\Models\RentalLine;
use App\Models\Branch;
use DateTime;
use Illuminate\Support\Facades\Auth;
use App\Traits\GpsTrait;

class GpsCheckSignalJobBranchController extends Controller
{
    use GpsTrait;

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSCheckSignalShortTermBranch);
        $user = Auth::user();
        $license_plate = $request->license_plate;
        $chassis_no = $request->chassis_no;
        $vid = $request->vid;
        $check_date = $request->check_date;
        $branch = $request->branch;
        $today = date('Y-m-d');

        $list = GpsCheckSignal::leftJoin('cars', 'cars.id', '=', 'gps_check_signals.car_id')
            ->leftJoin('branches', 'branches.id', '=', 'gps_check_signals.branch_id')
            ->where('gps_check_signals.job_type', Rental::class)
            ->where('gps_check_signals.check_main_branch', BOOL_TRUE)
            ->whereIN('gps_check_signals.status', [GPSStatusEnum::NORMAL_SIGNAL, GPSStatusEnum::NO_SIGNAL])
            // ->whereDate('gps_check_signals.must_check_date', '=', $today)
            ->select(
                'gps_check_signals.id',
                'gps_check_signals.status',
                'gps_check_signals.check_date',
                'gps_check_signals.remark',
                'gps_check_signals.job_type',
                'gps_check_signals.job_id',
                'gps_check_signals.main_branch_date',
                'gps_check_signals.remark_main_branch',
                'gps_check_signals.status_main_branch',
                'cars.license_plate',
                'cars.chassis_no',
                'cars.vid',
                'branches.name as branch_name'
            )
            ->orderBy('gps_check_signals.created_at', 'desc')
            ->search($request)
            ->when($vid, function ($query) use ($vid) {
                return $query->where('cars.vid', 'like', '%' . $vid . '%');
            })
            ->when($chassis_no, function ($query) use ($chassis_no) {
                return $query->where('cars.chassis_no', 'like', '%' . $chassis_no . '%');
            })
            ->when($license_plate, function ($query) use ($license_plate) {
                return $query->where('cars.license_plate', 'like', '%' . $license_plate . '%');
            })
            ->when($branch, function ($query) use ($branch) {
                return $query->where('gps_check_signals.branch_id', $branch);
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
        $branch_list = GpsTrait::queryBranchList();

        if (strcmp($user->branch->is_main, BOOL_TRUE)  == 0) {
            // $route_group = [
            //     'route_short_term' => route('admin.gps-check-signal-jobs.index'),
            //     'route_long_term' => route('admin.gps-check-signal-jobs.long-term.index'),
            //     'route_short_term_branch' => route('admin.gps-check-signal-jobs.short-term-branch.index'),
            // ];
            $allow_user = true;
        } else {
            // $route_group = [
            //     'route_short_term' => route('admin.gps-check-signal-jobs.index'),
            // ];
            $allow_user = false;
        }
        return view('admin.gps-check-signal-job-branch.index', [
            'list' => $list,
            'license_plate_list' => $license_plate_list,
            'chassis_no_list' => $chassis_no_list,
            'vid_list' => $vid_list,
            'license_plate' => $license_plate,
            'chassis_no' => $chassis_no,
            'vid' => $vid,
            'check_date' => $check_date,
            'status_list' => $status_list,
            // 'route_group' => $route_group,
            'branch' => $branch,
            'branch_list' => $branch_list,
            'allow_user' => $allow_user,
            'short_term_count' => $short_term_count,
            'short_branch_count' => $short_branch_count,
            'long_term_count' => $long_term_count,
            'replacement_car_count' => $replacement_car_count,
        ]);
    }

    public function edit(GpsCheckSignal $gps_check_signal_job_branch)
    {
        $this->authorize(Actions::Manage . '_' . Resources::GPSCheckSignalShortTermBranch);
        $user = Auth::user();
        if (strcmp($gps_check_signal_job_branch->job_type, Rental::class) === 0) {
            $rental = Rental::find($gps_check_signal_job_branch->job_id);
            if ($rental) {
                $gps_check_signal_job_branch->worksheet_no = ($rental->worksheet_no) ? $rental->worksheet_no : '';
                $gps_check_signal_job_branch->service_type = ($rental && $rental->serviceType) ? __('gps.service_type_' . $rental->serviceType->service_type) : null;
                $gps_check_signal_job_branch->customer = ($rental) ? $rental->customer_name : null;
                $gps_check_signal_job_branch->pickup_date = ($rental) ? get_thai_date_format($rental->pickup_date, 'd/m/Y H:i') : null;
                $gps_check_signal_job_branch->return_date = ($rental) ? get_thai_date_format($rental->return_date, 'd/m/Y H:i') : null;
            }
        }
        $car = Car::find($gps_check_signal_job_branch->car_id);
        if ($car) {
            $gps_check_signal_job_branch->license_plate = ($car) ? $car->license_plate : null;
            $gps_check_signal_job_branch->engine_no = ($car) ? $car->engine_no : null;
            $gps_check_signal_job_branch->chassis_no = ($car) ? $car->chassis_no : null;;
            $gps_check_signal_job_branch->car_class = ($car && $car->carClass) ? $car->carClass->full_name : null;
            $gps_check_signal_job_branch->car_color = ($car && $car->carColor) ? $car->carColor->name : null;
            $gps_check_signal_job_branch->fleet = ($car) ? $car->fleet : null;
            $gps_check_signal_job_branch->vid = ($car) ? $car->vid : null;
            $gps_check_signal_job_branch->sim = ($car) ? $car->sim : null;
        }
        $branch_name = ($gps_check_signal_job_branch->branch) ? $gps_check_signal_job_branch->branch->name : null;

        $short_class = Rental::class;
        $long_class = LongTermRental::class;
        $replacement_car_class = ReplacementCar::class;
        $status_approve = GpsTrait::getStatusApprove();

        $page_title = __('lang.edit') . __('gps.gps_signal');
        return view('admin.gps-check-signal-job-branch.form',  [
            'page_title' => $page_title,
            'd' => $gps_check_signal_job_branch,
            'short_class' => $short_class,
            'long_class' => $long_class,
            'status_approve' => $status_approve,
            'branch_name' => $branch_name,
            'replacement_car_class' => $replacement_car_class,
        ]);
    }

    public function show(GpsCheckSignal $gps_check_signal_job_branch)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSCheckSignalShortTermBranch);
        $user = Auth::user();
        if (strcmp($gps_check_signal_job_branch->job_type, Rental::class) === 0) {
            $rental = Rental::find($gps_check_signal_job_branch->job_id);
            if ($rental) {
                $gps_check_signal_job_branch->worksheet_no = ($rental->worksheet_no) ? $rental->worksheet_no : '';
                $gps_check_signal_job_branch->service_type = ($rental && $rental->serviceType) ? __('gps.service_type_' . $rental->serviceType->service_type) : null;
                $gps_check_signal_job_branch->customer = ($rental) ? $rental->customer_name : null;
                $gps_check_signal_job_branch->pickup_date = ($rental) ? get_thai_date_format($rental->pickup_date, 'd/m/Y H:i') : null;
                $gps_check_signal_job_branch->return_date = ($rental) ? get_thai_date_format($rental->return_date, 'd/m/Y H:i') : null;
            }
        }

        $car = Car::find($gps_check_signal_job_branch->car_id);
        if ($car) {
            $gps_check_signal_job_branch->license_plate = ($car) ? $car->license_plate : null;
            $gps_check_signal_job_branch->engine_no = ($car) ? $car->engine_no : null;
            $gps_check_signal_job_branch->chassis_no = ($car) ? $car->chassis_no : null;;
            $gps_check_signal_job_branch->car_class = ($car && $car->carClass) ? $car->carClass->full_name : null;
            $gps_check_signal_job_branch->car_color = ($car && $car->carColor) ? $car->carColor->name : null;
            $gps_check_signal_job_branch->fleet = ($car) ? $car->fleet : null;
            $gps_check_signal_job_branch->vid = ($car) ? $car->vid : null;
            $gps_check_signal_job_branch->sim = ($car) ? $car->sim : null;
        }
        $branch_name = ($gps_check_signal_job_branch->branch) ? $gps_check_signal_job_branch->branch->name : null;
        $short_class = Rental::class;
        $long_class = LongTermRental::class;
        $replacement_car_class = ReplacementCar::class;
        $status_approve = GpsTrait::getStatusApprove();

        $page_title = __('lang.view') . __('gps.gps_signal');
        return view('admin.gps-check-signal-job-branch.form',  [
            'page_title' => $page_title,
            'd' => $gps_check_signal_job_branch,
            'short_class' => $short_class,
            'long_class' => $long_class,
            'status_approve' => $status_approve,
            'branch_name' => $branch_name,
            'replacement_car_class' => $replacement_car_class,
            'view' => true,
        ]);
    }

    public function store(Request $request)
    {
        $check_signal = GpsCheckSignal::find($request->id);
        $check_signal->main_branch_date = $request->main_branch_date;
        $check_signal->status_main_branch = $request->status_main_branch;
        $check_signal->remark_main_branch = $request->remark_main_branch;
        $check_signal->save();

        $redirect_route = route('admin.gps-check-signal-job-branch.index');
        return $this->responseValidateSuccess($redirect_route);
    }
}
