<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Enums\RentalTypeEnum;
use App\Enums\CarEnum;
use App\Enums\RepairTypeEnum;
use App\Models\Car;
use App\Models\CarBrand;
use App\Models\CarClass;
use App\Models\CheckDistance;
use App\Models\ImportCarLine;
use App\Models\LongTermRentalPRCar;
use App\Models\LongTermRentalPRLine;
use App\Models\Rental;
use App\Models\RentalLine;
use App\Models\Repair;
use Carbon\Carbon;

class CheckDistanceNoticeController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CheckDistanceNotice);
        $car_brand_id = $request->car_brand_id;
        $car_class_id = $request->car_class_id;
        $license_plate = $request->license_plate;
        $rental_worksheet_no = $request->rental_worksheet_no;
        $from_check_next_date = $request->from_check_next_date;
        $to_check_next_date = $request->to_check_next_date;

        $list = Car::select(
            'cars.*',
            'car_classes.full_name as class_name',
            'rentals.id as rental_id',
            'rentals.worksheet_no as rental_no',
            'lt_rentals.id as lt_rental_id',
            'lt_rentals.worksheet_no as lt_rental_no',
        )
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftjoin('car_types', 'car_classes.car_type_id', '=', 'car_types.id')
            ->leftJoin('car_brands', 'car_brands.id', '=', 'car_types.car_brand_id')
            ->leftJoin('rental_lines', 'cars.id', '=', 'rental_lines.car_id')
            ->leftJoin('rentals', 'rentals.id', '=', 'rental_lines.rental_id')
            ->leftJoin('lt_rental_pr_lines_cars', 'cars.id', '=', 'lt_rental_pr_lines_cars.car_id')
            ->leftJoin('lt_rental_pr_lines', 'lt_rental_pr_lines.id', '=', 'lt_rental_pr_lines_cars.lt_rental_pr_line_id')
            ->leftJoin('lt_rentals', 'lt_rentals.id', '=', 'lt_rental_pr_lines.lt_rental_id')
            ->leftJoin('lt_rental_month', 'lt_rental_month.id', '=', 'lt_rental_pr_lines.lt_rental_month_id')
            ->whereNotIn('cars.status', [CarEnum::SOLD_OUT])
            ->where(function ($query) use ($rental_worksheet_no) {
                $query->where(function ($query2) use ($rental_worksheet_no) {
                    $query2->where('rentals.id', $rental_worksheet_no);
                    $query2->orWhere('lt_rentals.id', $rental_worksheet_no);
                });
            })
            ->when($license_plate, function ($query) use ($license_plate) {
                return $query->where('cars.id', $license_plate);
            })
            ->when($car_brand_id, function ($query) use ($car_brand_id) {
                return $query->where('car_types.car_brand_id', $car_brand_id);
            })
            ->when($car_class_id, function ($query) use ($car_class_id) {
                return $query->where('cars.car_class_id', $car_class_id);
            })
            ->paginate(PER_PAGE);

        $list->map(function ($item) {
            $car_age = Carbon::now()->diff($item->registered_date);
            $item->car_age = $car_age->y . " ปี " . $car_age->m . " เดือน " . $car_age->d . " วัน";
            $item->rental_job_type = null;
            $item->rental_duration = null;
            if (strcmp($item->rental_type, RentalTypeEnum::SHORT) == 0) {
                $rental = Rental::find($item->rental_id);
                if ($rental) {
                    $item->rental_no = $item->rental_no;
                    $item->rental_id = $item->rental_id;
                    $item->rental_job_type = __('lang.job_type_rental');
                }
            }
            if (strcmp($item->rental_type, RentalTypeEnum::LONG) == 0) {
                $lt_rental = LongTermRentalPRLine::leftJoin('lt_rental_month', 'lt_rental_month.id', '=', 'lt_rental_pr_lines.lt_rental_month_id')
                    ->where('lt_rental_pr_lines.lt_rental_id', $item->lt_rental_id)
                    ->select('lt_rental_month.month')
                    ->first();
                if ($lt_rental) {
                    $item->rental_no = $item->lt_rental_no;
                    $item->rental_id = $item->lt_rental_id;
                    $item->rental_job_type = __('lang.job_type_lt_rental');
                    $item->rental_duration = $lt_rental->month;
                }
            }

            $repair = Repair::leftJoin('repair_orders', 'repair_orders.repair_id', '=', 'repairs.id')
                ->where('repairs.car_id', $item->id)
                ->whereNotNull('repair_orders.repair_date')
                ->select(
                    'repair_orders.check_distance',
                    'repair_orders.repair_date'
                )
                ->orderBy('repair_orders.created_at', 'desc')->first();

            $item->check_latest_date = null;
            $item->check_latest = null;
            $item->check_next_date = null;
            $item->check_next = null;

            if ($repair) {
                $check_distance = CheckDistance::select('id', 'distance', 'month')
                    ->where('car_class_id', $item->car_class_id)
                    ->where('distance', '>', floatval($repair->check_distance))
                    ->first();
                $month = ($check_distance) ? $check_distance->month : 0;
                $repair_date = $repair->repair_date;
                $repair_date_format = Carbon::createFromFormat('Y-m-d', $repair_date);
                $check_next_date = $repair_date_format->addMonth($month);
                $check_next_date = $check_next_date->format('Y-m-d');
                $item->check_next_date = ($check_next_date > $repair_date) ? $check_next_date : null;
                $item->check_next = ($check_distance) ? $check_distance->distance : null;
                $item->check_latest_date = ($repair_date) ? $repair_date : null;
                $item->check_latest = ($repair) ? $repair->check_distance : null;
            } else {
                $check_distance = CheckDistance::select('id', 'distance', 'month')
                    ->where('car_class_id', $item->car_class_id)
                    ->where('distance', '>', $item->current_mileage)
                    ->first();

                $import_car = ImportCarLine::find($item->id);
                $month = ($check_distance) ? $check_distance->month : 0;
                if ($import_car) {
                    $delivery_date = $import_car->delivery_date;
                    $delivery_date_format = Carbon::createFromFormat('Y-m-d', $delivery_date);
                    $check_next_date = $delivery_date_format->addMonth($month);
                    $check_next_date = $check_next_date->format('Y-m-d');
                    $item->check_next_date = ($check_next_date > $delivery_date) ? $check_next_date : null;
                    $item->check_latest_date = null;
                }
                $item->check_next = ($check_distance) ? $check_distance->distance : null;
                $item->check_latest = ($item->current_mileage) ? $item->current_mileage : null;
            }
            return $item;
        });

        $rental = RentalLine::leftJoin('rentals', 'rentals.id', '=', 'rental_lines.rental_id')
            ->leftJoin('cars', 'cars.id', '=', 'rental_lines.car_id')
            ->select('rentals.id', 'rentals.worksheet_no as name')
            ->get();

        $lt_rental = LongTermRentalPRCar::leftJoin('lt_rental_pr_lines', 'lt_rental_pr_lines.id', '=', 'lt_rental_pr_lines_cars.lt_rental_pr_line_id')
            ->leftJoin('lt_rentals', 'lt_rentals.id', '=', 'lt_rental_pr_lines.lt_rental_id')
            ->leftJoin('lt_rental_month', 'lt_rental_month.id', '=', 'lt_rental_pr_lines.lt_rental_month_id')
            ->leftJoin('cars', 'cars.id', '=', 'lt_rental_pr_lines_cars.car_id')
            ->select('lt_rentals.id', 'lt_rentals.worksheet_no  as name')
            ->get();

        $rental_no_list = [];
        $rental_no_list = $rental->merge($lt_rental);

        $car_brand_name = null;
        if (!empty($car_brand_id)) {
            $car_brand = CarBrand::find($car_brand_id);
            if ($car_brand) {
                $car_brand_name = $car_brand->name;
            }
        }
        $license_plate_name = null;
        if (!empty($license_plate)) {
            $car = Car::find($license_plate);
            if ($car) {
                $license_plate_name = $car->license_plate;
            }
        }
        $car_class_name = null;
        if (!empty($car_class_id)) {
            $car_class = CarClass::find($car_class_id);
            if ($car_class) {
                $car_class_name = $car_class->full_name;
            }
        }

        $page_title = __('check_distance_notices.page_title');
        return view('admin.check-distance-notices.index', [
            'page_title' => $page_title,
            'list' => $list,
            'license_plate' => $license_plate,
            'car_brand_id' => $car_brand_id,
            'car_class_id' => $car_class_id,
            'car_brand_name' => $car_brand_name,
            'car_class_name' => $car_class_name,
            'license_plate_name' => $license_plate_name,
            'rental_no_list' => $rental_no_list,
            'rental_worksheet_no' => $rental_worksheet_no,
        ]);
    }

    public function show(Car $check_distance_notice)
    {
        $this->authorize(Actions::View . '_' . Resources::CheckDistanceNotice);
        $car_age = Carbon::now()->diff($check_distance_notice->registered_date);
        $car_age = $car_age->y . " ปี " . $car_age->m . " เดือน " . $car_age->d . " วัน";
        $car_age_start = Carbon::now()->diff($check_distance_notice->start_date);
        $car_age_start = $car_age_start->y . " ปี " . $car_age_start->m . " เดือน " . $car_age_start->d . " วัน";
        $car_class_name = $check_distance_notice->carClass ? $check_distance_notice->carClass->full_name : null;
        $car_color_name = $check_distance_notice->carColor ? $check_distance_notice->carColor->name : null;
        $car_brand_name = ($check_distance_notice->carClass && $check_distance_notice->carClass->carType && $check_distance_notice->carClass->carType->car_brand) ? $check_distance_notice->carClass->carType->car_brand->name : null;
        $rental_no = null;
        $rental_job_type = null;
        $rental_duration = null;
        $rental_customer_name = null;
        $rental_customer_tel = null;
        $check_latest_date = null;
        $contact_latest =  null;
        $contact_tel =  null;
        $check_next_date = null;
        $check_next = null;
        $check_latest = null;
        if (strcmp($check_distance_notice->rental_type, RentalTypeEnum::SHORT) == 0) {
            $rental = RentalLine::leftJoin('rentals', 'rentals.id', '=', 'rental_lines.rental_id')
                ->where('rental_lines.car_id', $check_distance_notice->id)
                ->select(
                    'rentals.id',
                    'rentals.worksheet_no',
                    'rentals.customer_name',
                    'rentals.customer_tel',
                )
                ->first();
            if ($rental) {
                $rental_no = $rental->worksheet_no;
                $rental_job_type = __('lang.job_type_rental');
                $rental_customer_name = $rental->customer_name;
                $rental_customer_tel = $rental->customer_tel;
            }
        }
        if (strcmp($check_distance_notice->rental_type, RentalTypeEnum::LONG) == 0) {
            $rental = LongTermRentalPRCar::leftJoin('lt_rental_pr_lines', 'lt_rental_pr_lines.id', '=', 'lt_rental_pr_lines_cars.lt_rental_pr_line_id')
                ->leftJoin('lt_rentals', 'lt_rentals.id', '=', 'lt_rental_pr_lines.lt_rental_id')
                ->leftJoin('lt_rental_month', 'lt_rental_month.id', '=', 'lt_rental_pr_lines.lt_rental_month_id')
                ->where('lt_rental_pr_lines_cars.car_id', $check_distance_notice->id)
                ->select(
                    'lt_rentals.id',
                    'lt_rentals.worksheet_no',
                    'lt_rental_month.month',
                    'lt_rentals.customer_name',
                    'lt_rentals.customer_tel',
                )
                ->first();
            if ($rental) {
                $rental_no = $rental->worksheet_no;
                $rental_job_type = __('lang.job_type_lt_rental');
                $rental_duration = $rental->month;
                $rental_customer_name = $rental->customer_name;
                $rental_customer_tel = $rental->customer_tel;
            }
        }
        $repair = Repair::leftJoin('repair_orders', 'repair_orders.repair_id', '=', 'repairs.id')
            ->where('repairs.car_id', $check_distance_notice->id)
            ->whereNotNull('repair_orders.repair_date')
            ->select(
                'repair_orders.check_distance',
                'repair_orders.repair_date',
                'repairs.contact',
                'repairs.tel'
            )
            ->orderBy('repair_orders.created_at', 'desc')->first();

        if ($repair) {
            $check_distance = CheckDistance::select('id', 'distance', 'month')
                ->where('car_class_id', $check_distance_notice->car_class_id)
                ->where('distance', '>', $repair->check_distance)
                ->first();
            $month = ($check_distance) ? $check_distance->month : 0;
            $repair_date = $repair->repair_date;
            $repair_date_format = Carbon::createFromFormat('Y-m-d', $repair_date);
            $check_next_date = $repair_date_format->addMonth($month);
            $check_next_date = $check_next_date->format('Y-m-d');
            $check_next_date = ($check_next_date > $repair_date) ? $check_next_date : null;
            $check_next = ($check_distance) ? $check_distance->distance : null;
            $check_latest_date = ($repair_date) ? $repair_date : null;
            $check_latest = ($repair) ? $repair->check_distance : null;
            $contact_latest = ($repair) ? $repair->contact : null;
            $contact_tel = ($repair) ? $repair->tel : null;
        } else {
            $check_distance = CheckDistance::select('id', 'distance', 'month')
                ->where('car_class_id', $check_distance_notice->car_class_id)
                ->where('distance', '>', $check_distance_notice->current_mileage)
                ->first();

            $import_car = ImportCarLine::find($check_distance_notice->id);
            $month = ($check_distance) ? $check_distance->month : 0;
            if ($import_car) {
                $delivery_date = $import_car->delivery_date;
                $delivery_date_format = Carbon::createFromFormat('Y-m-d', $delivery_date);
                $check_next_date = $delivery_date_format->addMonth($month);
                $check_next_date = $check_next_date->format('Y-m-d');
                $check_next_date = ($check_next_date > $delivery_date) ? $check_next_date : null;
            }
            $check_next = ($check_distance) ? $check_distance->distance : null;
            $check_latest = ($check_distance_notice) ? $check_distance_notice->current_mileage : null;
        }

        $check_history_list = Repair::where('car_id', $check_distance_notice->id)
            ->whereIn('repair_type', [RepairTypeEnum::CHECK_DISTANCE, RepairTypeEnum::CHECK_AND_REPAIR])
            ->get();

        $page_title =  __('lang.view') . __('check_distance_notices.page_title');
        return view('admin.check-distance-notices.form', [
            'd' => $check_distance_notice,
            'page_title' => $page_title,
            'car_age' => $car_age,
            'car_age_start' => $car_age_start,
            'car_class_name' => $car_class_name,
            'car_color_name' => $car_color_name,
            'car_brand_name' => $car_brand_name,
            'rental_no' => $rental_no,
            'rental_job_type' => $rental_job_type,
            'rental_duration' => $rental_duration,
            'rental_customer_name' => $rental_customer_name,
            'rental_customer_tel' => $rental_customer_tel,
            'check_latest' => $check_latest,
            'check_latest_date' => $check_latest_date,
            'check_next' => $check_next,
            'check_next_date' => $check_next_date,
            'contact_latest' => $contact_latest,
            'contact_tel' => $contact_tel,
            'check_history_list' => $check_history_list,
            'view' => true,
        ]);
    }
}
