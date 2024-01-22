<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\BorrowCarEnum;
use App\Enums\CarEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\Resources;
use App\Enums\StorageLocationEnum;
use App\Http\Controllers\Controller;
use App\Models\BorrowCar;
use App\Models\Car;
use App\Models\CarBrand;
use App\Models\CarCategory;
use App\Models\CarClass;
use App\Models\CarPark;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowCarListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::BorrowCarList);
        $s = $request->s;
        $car_id = $request->car_id;
        $car_brand = $request->car_brand;
        $car_class = $request->car_class;
        $status_borrow = $request->status_borrow;


        $list = Car::sortable(['created_at' => 'desc'])
            ->select('cars.*', 'car_colors.name as car_color_name', 'car_classes.name as car_class_name', 'car_classes.full_name as class_name', 'car_park_zones.slot')
            ->leftJoin('borrow_cars', 'borrow_cars.car_id', 'cars.id')
            ->leftJoin('car_colors', 'car_colors.id', '=', 'cars.car_color_id')
            ->leftJoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftJoinSub(get_sub_query_car_park_zones(), 'car_park_zones', function ($join) {
                $join->on('cars.id', '=', 'car_park_zones.car_id');
            })
            ->where('cars.status', CarEnum::READY_TO_USE)
            ->where('cars.rental_type', RentalTypeEnum::BORROW)
            ->when(!empty($status_borrow), function ($query) use ($status_borrow) {
                if ($status_borrow == CarEnum::READY_TO_USE) {
                    $query->whereNotExists(function ($subQuery) {
                        $subQuery->select(DB::raw(1))
                            ->from('borrow_cars')
                            ->whereRaw('borrow_cars.car_id = cars.id')
                            ->whereIn('borrow_cars.status', [BorrowCarEnum::PENDING_DELIVERY]);
                    });
                } else {
                    $query->where('borrow_cars.status', 'like', '%' . $status_borrow . '%');
                }
            })
            ->when(!empty($car_id), function ($query) use ($car_id) {
                $query->where('cars.id', 'like', '%' . $car_id . '%');
            })
            ->when(!empty($car_brand), function ($query) use ($car_brand) {
                $query->where('cars.car_brand_id', 'like', '%' . $car_brand . '%');
            })
            ->when(!empty($car_class), function ($query) use ($car_class) {
                $query->where('cars.car_class_id', 'like', '%' . $car_class . '%');
            })
            ->distinct('cars.id')
            ->paginate(PER_PAGE);

        $list->map(function ($item) {
            $borrow_car = BorrowCar::where('car_id', $item->id)->latest()->first();
            $item->borrow_type = $borrow_car ? $borrow_car->status : $item->status;
            $car_age_start = Carbon::now()->diff($item->start_date);
            $item->car_age_start = $car_age_start->y . " ปี " . $car_age_start->m . " เดือน " . $car_age_start->d . " วัน";
            return $item;
        });


        $license_plate_list = Car::where('status', CarEnum::READY_TO_USE)->where('cars.rental_type', RentalTypeEnum::BORROW)->select('license_plate as name', 'id')->groupBy('license_plate', 'id')->get();
        $engine_no_list = Car::where('status', CarEnum::READY_TO_USE)->where('cars.rental_type', RentalTypeEnum::BORROW)->select('engine_no as name', 'id')->groupBy('engine_no', 'id')->get();
        $chassis_no_list = Car::where('status', CarEnum::READY_TO_USE)->where('cars.rental_type', RentalTypeEnum::BORROW)->select('chassis_no as name', 'id')->groupBy('chassis_no', 'id')->get();
        $car_category_list = CarCategory::where('status', CarEnum::READY_TO_USE)->select('name', 'id')->groupBy('name', 'id')->get();
        // $rental_type_list = $this->getRentalType();
        $status_list = $this->getStatus();

        $storage_location_list = $this->getStorageLocationList();

        $license_plate_engine_chassis_list = Car::where('status', CarEnum::READY_TO_USE)->where('cars.rental_type', RentalTypeEnum::BORROW)->select('id', 'license_plate', 'engine_no', 'chassis_no')->get();
        $license_plate_engine_chassis_list->map(function ($item) {
            if ($item->license_plate) {
                $text = $item->license_plate;
            } else if ($item->engine_no) {
                $text = __('inspection_cars.engine_no') . ' ' . $item->engine_no;
            } else if ($item->chassis_no) {
                $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
            }
            $item->id = $item->id;
            $item->name = $text;
            return $item;
        });

        $brand_list = CarBrand::leftjoin('cars', 'cars.car_brand_id', 'car_brands.id')->where('cars.status', CarEnum::READY_TO_USE)->select('car_brands.id', 'car_brands.name')
            ->groupBy('car_brands.id', 'car_brands.name')->get();
        $class_list = CarClass::leftjoin('cars', 'cars.car_class_id', 'car_classes.id')->where('cars.status', CarEnum::READY_TO_USE)->select('car_classes.id', 'car_classes.full_name as name')
            ->groupBy('car_classes.id', 'car_classes.full_name')->get();
        return view('admin.borrow-car-lists.index', [
            'list' => $list,
            's' => $request->s,
            'storage_location_list' => $storage_location_list,
            'license_plate_list' => $license_plate_list,
            'engine_no_list' => $engine_no_list,
            'chassis_no_list' => $chassis_no_list,
            'car_category_list' => $car_category_list,
            // 'rental_type_list' => $rental_type_list,
            'status_list' => $status_list,
            'engine_no' => $request->engine_no,
            'license_plate' => $request->license_plate,
            'chassis_no' => $request->chassis_no,
            'car_category' => $request->car_category,
            'rental_type' => $request->rental_type,
            'storage_location' => $request->storage_location,
            'status' => $status_borrow,
            'car_id' => $car_id,
            'license_plate_engine_chassis_list' => $license_plate_engine_chassis_list,
            'brand_list' => $brand_list,
            'car_brand' => $car_brand,
            'class_list' => $class_list,
            'car_class' => $car_class,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Car $borrow_car_list)
    {
        $this->authorize(Actions::View . '_' . Resources::BorrowCarList);
        $borrow_car = BorrowCar::where('car_id', $borrow_car_list->id)->latest()->first();
        $list = BorrowCar::sortable(['worksheet_no' => 'desc'])->where('car_id', $borrow_car_list->id)->paginate(PER_PAGE);
        if (!is_null($borrow_car)) {
            $borrow_type = $borrow_car->status;
        } else {
            $borrow_type = $borrow_car_list->status;
        }

        $car_age = Carbon::now()->diff($borrow_car_list->registered_date);
        $car_age = $car_age->y . " ปี " . $car_age->m . " เดือน " . $car_age->d . " วัน";

        $car_age_start = Carbon::now()->diff($borrow_car_list->start_date);
        $car_age_start = $car_age_start->y . " ปี " . $car_age_start->m . " เดือน " . $car_age_start->d . " วัน";

        $car_class_name = $borrow_car_list->carClass ? $borrow_car_list->carClass->full_name : null;
        // $car_group_name = $borrow_car_list->carGroup ? $borrow_car_list->carGroup->name : null;
        // $car_category_name = $borrow_car_list->carCategory ? $borrow_car_list->carCategory->name : null;
        $car_brand_name = $borrow_car_list->carBrand ? $borrow_car_list->carBrand->name : null;
        $car_color_name = $borrow_car_list->carColor ? $borrow_car_list->carColor->name : null;
        // dd($borrow_car);
        $page_title = __('borrow_car_lists.license_plate');
        $url = 'admin.borrow-car-lists.index';
        return view('admin.borrow-car-lists.form', [
            'd' => $borrow_car_list,
            'borrow_car' => $borrow_car,
            'page_title' => $page_title,
            'url' => $url,
            'car_age' => $car_age,
            'car_age_start' => $car_age_start,
            'car_brand_name' => $car_brand_name,
            'car_class_name' => $car_class_name,
            'car_color_name' => $car_color_name,
            'borrow_type' => $borrow_type,
            'list' => $list,

        ]);
        // dd($borrow_car);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public static function getStatus()
    {
        $rental_type = collect([
            (object) [
                'id' => BorrowCarEnum::PENDING_DELIVERY,
                'name' => __('borrow_car_lists.status_' . BorrowCarEnum::PENDING_DELIVERY),
                'value' => BorrowCarEnum::PENDING_DELIVERY,
            ],
            (object) [
                'id' => BorrowCarEnum::IN_PROCESS,
                'name' => __('borrow_car_lists.status_' . BorrowCarEnum::IN_PROCESS),
                'value' => BorrowCarEnum::IN_PROCESS,
            ],
            (object) [
                'id' => CarEnum::READY_TO_USE,
                'name' => __('borrow_car_lists.status_' . CarEnum::READY_TO_USE),
                'value' => CarEnum::READY_TO_USE,
            ],

        ]);
        return $rental_type;
    }

    public function getStorageLocationList()
    {
        $storage_locations = collect([
            (object) [
                'id' => StorageLocationEnum::TRUE_LEASING,
                'name' =>  __('cars.car_storage_' . StorageLocationEnum::TRUE_LEASING),
                'value' => StorageLocationEnum::TRUE_LEASING,
            ],
        ]);
        return $storage_locations;
    }
}
