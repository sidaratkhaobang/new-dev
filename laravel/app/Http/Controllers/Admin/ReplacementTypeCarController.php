<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\CarEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarClass;
use App\Models\CarPark;
use App\Models\ReplacementCar;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use ReplacementCarStatusEnum;

class ReplacementTypeCarController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ReplacementTypeCar);
        $s = $request->s;
        $license_plate = $request->license_plate;
        $car_class_id = $request->car_class_id;
        $status = $request->status;
        $list = Car::sortable(['created_at' => 'desc'])
            ->select(
                'cars.*',
                'car_classes.name as car_class_name',
                'car_classes.full_name as class_name'
            )
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->when($car_class_id, function ($query) use ($car_class_id) {
                $query->where('car_classes.id', $car_class_id);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('cars.status', $status);
            })
            ->where('rental_type', RentalTypeEnum::REPLACEMENT)
            ->search($request->s, $request)
            ->paginate(PER_PAGE);
        $list->map(function ($item) {
            $car_age_start = Carbon::now()->diff($item->start_date);
            $item->car_age_start = $car_age_start->y . " ปี " . $car_age_start->m . " เดือน " . $car_age_start->d . " วัน";
            $zone = CarPark::leftjoin('car_park_areas', 'car_park_areas.id', 'car_parks.car_park_area_id')
                ->leftjoin('car_park_zones', 'car_park_zones.id', 'car_park_areas.car_park_zone_id')
                ->where('car_parks.car_id', $item->id)
                ->where('car_park_zones.branch_id', get_branch_id())
                ->select('car_park_zones.code', 'car_parks.car_park_number')
                ->first();
            $item->slot = $zone ? $zone->code . $zone->car_park_number : null;
            return $item;
        });

        $license_plate_list = Car::where('rental_type', RentalTypeEnum::REPLACEMENT)->select('license_plate as name', 'id')->get();
        $car_class_list = CarClass::leftjoin('cars', 'cars.car_class_id', '=', 'car_classes.id')
            ->where('cars.rental_type', RentalTypeEnum::REPLACEMENT)
            ->select('car_classes.id', 'car_classes.name')->get();
        $status_list = $this->getStatus();
        $page_title = __('replacement_cars.car_main_page_title');
        return view('admin.replacement-type-cars.index', [
            's' => $s,
            'license_plate' => $license_plate,
            'car_class_id' => $car_class_id,
            'status' => $status,
            'list' => $list,
            'page_title' => $page_title,
            'license_plate_list' => $license_plate_list,
            'car_class_list' => $car_class_list,
            'status_list' => $status_list,
        ]);
    }

    public function show(Car $replacement_type_car)
    {
        $this->authorize(Actions::View . '_' . Resources::ReplacementTypeCar);
        $car_age_in_storage = Carbon::now()->diff($replacement_type_car->start_date);
        $replacement_type_car->car_age_in_storage = $car_age_in_storage->y . " ปี " . $car_age_in_storage->m . " เดือน " . $car_age_in_storage->d . " วัน";
        $list = ReplacementCar::where('replacement_car_id', $replacement_type_car->id)
            ->whereIn('status', [ReplacementCarStatusEnum::PENDING, ReplacementCarStatusEnum::IN_PROCESS, ReplacementCarStatusEnum::COMPLETE])
            ->get();

        $page_title = __('replacement_cars.license_plate') . ' ' . $replacement_type_car->license_plate;
        return view('admin.replacement-type-cars.view', [
            'd' => $replacement_type_car,
            'list' => $list,
            'page_title' => $page_title,
        ]);
    }
    public function edit()
    {
        abort(404);
    }

    public static function getStatus()
    {
        $car_statues = collect([
            (object) [
                'id' => CarEnum::READY_TO_USE,
                'name' => __('cars.status_' . CarEnum::READY_TO_USE),
                'value' => CarEnum::READY_TO_USE,
            ],
        ]);
        return $car_statues;
    }
}
