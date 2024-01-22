<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\AmountTypeEnum;
use App\Enums\DrivingJobStatusEnum;
use App\Enums\DrivingJobTypeStatusEnum;
use App\Enums\RentalBillTypeEnum;
use App\Enums\Resources;
use App\Enums\SelfDriveTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\DriverWageJob;
use App\Models\DrivingJob;
use App\Models\ImportCar;
use App\Models\ImportCarLine;
use App\Models\LongTermRental;
use App\Models\Rental;
use App\Models\RentalBill;
use App\Models\ServiceType;
use App\Models\TransferCar;
use Carbon;
use Illuminate\Http\Request;

use function Clue\StreamFilter\fun;

class DriverReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::DriverReport);

        $list = DrivingJob::select(['driving_jobs.driver_id', 'drivers.id', 'drivers.name'])
            ->leftjoin('drivers', 'drivers.id', '=', 'driving_jobs.driver_id')
            ->search($request->driver_id, $request)
            ->whereNotNull('driving_jobs.driver_id')
            ->groupBy(['driving_jobs.driver_id', 'drivers.id', 'drivers.name'])
            ->sortable()
            ->paginate(PER_PAGE);

        $list->map(function ($item) {
            $total_summary = 0;
            $drivingJob = DrivingJob::where('driver_id', $item->driver_id)->orderBy('id', 'DESC')->get();

            foreach ($drivingJob as $row) {
                $driver_wages_jobs = DriverWageJob::where('driving_job_id', $row->id)->get();
                $total = $this->calculateWageJob($row, $driver_wages_jobs);
                $total_summary += $total;
            }

            $item->summary_wage_job = number_format((float)$total_summary, 2);
            return $item;
        });

        $driver_list = DrivingJob::select(['drivers.id', 'drivers.name'])
            ->leftjoin('drivers', 'drivers.id', '=', 'driving_jobs.driver_id')
            ->whereNotNull('driving_jobs.driver_id')
            ->groupBy(['drivers.id', 'drivers.name'])
            ->get();

        return view('admin.driver-report.index', [
            'list' => $list,
            'driver_id' => $request->driver_id,
            'driver_list' => $driver_list,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::DriverReport);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::DriverReport);
        $driving_job = DrivingJob::where('id', $request->id)->first();
        $driver = Driver::find($driving_job->driver_id);

        if ($driving_job->id && !empty($request->wage_job)) {
            DrivingJobController::saveDriverWageJob($request->wage_job, $driving_job);
        }

        $redirect_route = route('admin.driver-report.show', $driver);
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(Driver $driver_report, Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::DriverReport);
        $report_month = $request->report_month;
        $report_year = $request->report_year;

        $month_list = collect();
        $year_list = collect();

        DrivingJob::where('driving_jobs.status', DrivingJobStatusEnum::COMPLETE)
            ->where('driving_jobs.driver_id', $driver_report->id)
            ->get()->map(function ($item) use ($month_list, $year_list) {
                $date = Carbon::parse($item->created_at);
                $tempMonth = [
                    'id' => $date->month,
                    'name' => get_thai_month($date->month),
                ];
                if (!$month_list->where('id', $date->month)->first()) {
                    $month_list->push((object)$tempMonth);
                }

                $tempYear = [
                    'id' => $date->year,
                    'name' => $date->year,
                ];
                if (!$year_list->where('id', $date->year)->first()) {
                    $year_list->push((object)$tempYear);
                }
            });

        $driving_job_list = DrivingJob::leftJoin('drivers', 'drivers.id', '=', 'driving_jobs.driver_id')
            ->sortable(['created_at' => 'desc'])
            ->where('driving_jobs.status', DrivingJobStatusEnum::COMPLETE)
            ->select(
                'driving_jobs.id',
                'driving_jobs.worksheet_no',
                'driving_jobs.driver_name',
                'driving_jobs.job_id',
                'driving_jobs.job_type',
                'driving_jobs.is_confirm_wage',
                'driving_jobs.status',
                'driving_jobs.self_drive_type',
                'driving_jobs.car_id',
                'driving_jobs.driver_id',
            )
            ->groupBy(
                'driving_jobs.id',
                'driving_jobs.worksheet_no',
                'driving_jobs.driver_name',
                'driving_jobs.job_id',
                'driving_jobs.job_type',
                'driving_jobs.is_confirm_wage',
                'driving_jobs.status',
                'driving_jobs.self_drive_type',
                'driving_jobs.car_id',
                'driving_jobs.driver_id',
            )
            ->where('driving_jobs.driver_id', $driver_report->id)
            ->where(function ($query) use ($report_month, $report_year) {
                if (!empty($report_month)) {
                    $query->whereMonth('driving_jobs.created_at', $report_month);
                }

                if (!empty($report_year)) {
                    $query->whereYear('driving_jobs.created_at', $report_year);
                }
            })
            ->paginate(PER_PAGE);

        $driving_job_list->map(function ($item) {
            $item->work_day = '';
            $item->worksheet_no_ref = '';
            if (strcmp($item->job_type, Rental::class) === 0) {
                $rental = Rental::find($item->job_id);
                if (strcmp($item->self_drive_type, SelfDriveTypeEnum::OTHER) === 0) {
                    $item->work_day = get_thai_date_format($rental->pickup_date, 'd/m/Y H:i') . ' - ' . get_thai_date_format($rental->return_date, 'd/m/Y H:i');
                } else {
                    if (strcmp($item->self_drive_type, SelfDriveTypeEnum::PICKUP) === 0) {
                        $item->work_day = get_thai_date_format($rental->return_date, 'd/m/Y H:i');
                    } else {
                        if (strcmp($item->self_drive_type, SelfDriveTypeEnum::SEND) === 0) {
                            $item->work_day = get_thai_date_format($rental->pickup_date, 'd/m/Y H:i');
                        }
                    }
                }
                $service_type = ServiceType::find($rental->service_type_id);
                $item->service_type_name = $service_type->name;
            } else {
                if (strcmp($item->job_type, LongTermRental::class) === 0) {
                    $lt_rental = LongTermRental::find($item->job_id);
                    if (strcmp($item->self_drive_type, SelfDriveTypeEnum::SEND) === 0) {
                        $item->work_day = get_thai_date_format($lt_rental->contract_start_date, 'd/m/Y H:i');
                    }
                } else {
                    if (strcmp($item->job_type, ImportCarLine::class) === 0) {
                        if (strcmp($item->self_drive_type, SelfDriveTypeEnum::SEND) === 0) {
                            $import_car_line = ImportCarLine::select('id', 'import_car_id', 'delivery_date')->where('id', $item->job_id)->first();
                            if ($import_car_line) {
                                $item->work_day = ($import_car_line->delivery_date) ? get_thai_date_format($import_car_line->delivery_date, 'd/m/Y H:i') : null;
                                $import_car = ImportCar::find($import_car_line->import_car_id);
                                $item->worksheet_no_ref = ($import_car && $import_car->purchaseOrder) ? $import_car->purchaseOrder->po_no : '';
                            }
                        }
                    } else {
                        if (strcmp($item->job_type, TransferCar::class) === 0) {
                            $lt_rental = TransferCar::find($item->job_id);
                            if (strcmp($item->self_drive_type, SelfDriveTypeEnum::SEND) === 0) {
                                $item->work_day = get_thai_date_format($lt_rental->delivery_date, 'd/m/Y H:i');
                            }
                        } else {
                            if (strcmp($item->job_type, DrivingJobTypeStatusEnum::OTHER) == 0) {
                                $item->work_day = '';
                            }
                        }
                    }
                }
            }

            $driver_wages_jobs = DriverWageJob::where('driving_job_id', $item->id)->get();
            $item->summary_wage_job = number_format((float)$this->calculateWageJob($item, $driver_wages_jobs), 2);

            return $item;
        });

//        return $driving_job_list;

        return view('admin.driver-report.form', [
            'd' => $driver_report,
            'driving_job_list' => $driving_job_list,
            'report_month' => $report_month,
            'month_list' => $month_list,
            'report_year' => $report_year,
            'year_list' => $year_list,
        ]);
    }

    public function calculateWageJob($driving_job, $driver_wages_jobs)
    {
        $total = 0;
        foreach ($driver_wages_jobs as $item) {
            if ($driving_job->job_type == Rental::class and $item->amount_type == AmountTypeEnum::PERCENT) {
                $rental_bill = RentalBill::where('rental_id', $driving_job->job_id)->where('bill_type', RentalBillTypeEnum::PRIMARY)->first();
                $amount = ($rental_bill->total / 100) * $item->amount;
                $total += $amount;
            } else {
                $total += $item->amount;
            }
        }
        return $total;
    }

    public function edit($driver_id, $driving_id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::DriverReport);
        $driving_job = DrivingJob::find($driving_id);
        $driver = Driver::find($driver_id);
        $driver_wage_job_list = DrivingJobController::getDriverWageJobList($driving_id);
        return view('admin.driver-report.edit', [
            'driving_job' => $driving_job,
            'driver' => $driver,
            'driver_wage_job_list' => $driver_wage_job_list
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::DriverReport);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::DriverReport);
    }
}
