<?php

namespace App\Http\Controllers\Admin;

use App\Classes\NotificationManagement;
use App\Enums\Actions;
use App\Enums\CarEnum;
use App\Enums\CarPartTypeEnum;
use App\Enums\CarStateEnum;
use App\Enums\CreditorTypeEnum;
use App\Enums\DepartmentEnum;
use App\Enums\GPSJobTypeEnum;
use App\Enums\InspectionStatusEnum;
use App\Enums\InspectionTypeEnum;
use App\Enums\InstallEquipmentStatusEnum;
use App\Enums\NotificationScopeEnum;
use App\Enums\RentalBillTypeEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\Resources;
use App\Enums\SellingPriceStatusEnum;
use App\Enums\StorageEnum;
use App\Enums\StorageLocationEnum;
use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\AccidentRepairOrder;
use App\Models\BorrowCar;
use App\Models\Branch;
use App\Models\Car;
use App\Models\CarAccessory;
use App\Models\CarAuction;
use App\Models\CarBattery;
use App\Models\CarCategory;
use App\Models\CarGroup;
use App\Models\CarPark;
use App\Models\CarParkZone;
use App\Models\CarPart;
use App\Models\CarTire;
use App\Models\CarWiper;
use App\Models\CMI;
use App\Models\Creditor;
use App\Models\ImportCar;
use App\Models\ImportCarLine;
use App\Models\InspectionFlow;
use App\Models\InspectionJob;
use App\Models\InspectionStep;
use App\Models\InstallEquipmentLine;
use App\Models\Leasing;
use App\Models\LongTermRental;
use App\Models\LongTermRentalPRCar;
use App\Models\LongTermRentalPRLine;
use App\Models\Rental;
use App\Models\RentalBill;
use App\Models\RentalLine;
use App\Models\Repair;
use App\Models\ReplacementCar;
use App\Models\SellingPriceLine;
use App\Models\User;
use App\Models\VMI;
use App\Traits\GpsTrait;
use App\Traits\InspectionTrait;
use App\Traits\NotificationTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DateTime;
use App\Factories\InspectionJobFactory;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;
        $this->authorize(Actions::View . '_' . Resources::Car);
        $list = Car::sortable(['created_at' => 'desc'])->select('cars.*', 'car_colors.name as car_color_name', 'car_classes.name as car_class_name', 'car_classes.full_name as class_name')
            ->addSelect('car_park_zones.zone_code', 'car_park_zones.car_park_number', 'car_park_zones.slot')
            ->leftjoin('car_colors', 'car_colors.id', '=', 'cars.car_color_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftJoinSub(get_sub_query_car_park_zones(), 'car_park_zones', function ($join) {
                $join->on('cars.id', '=', 'car_park_zones.car_id');
            })
            ->when($s, function ($query) use ($s) {
                $query->where('car_park_zones.slot', 'like', '%' . $s . '%');
                $query->orWhere('car_classes.full_name', 'like', '%' . $s . '%');
                $query->orWhere('car_classes.name', 'like', '%' . $s . '%');
            })
            //->where('car_park_zones.branch_id', get_branch_id())
            ->where('cars.branch_id', get_branch_id())
            ->search($request->s, $request)
            ->paginate(PER_PAGE);
        $list->map(function ($item) {
            $car_age_start = Carbon::now()->diff($item->start_date);
            $item->car_age_start = $car_age_start->y . " ปี " . $car_age_start->m . " เดือน " . $car_age_start->d . " วัน";

            $item->sale_car_total = 0;
            $today = date('Y-m-d');
            /* if (!in_array($item->status, [CarEnum::NEWCAR, CarEnum::PENDING_SALE, CarEnum::SOLD_OUT])) {
                if (strcmp($item->rental_type, RentalTypeEnum::REPLACEMENT) == 0) {
                    $replacement_count = ReplacementCar::where('replacement_car_id', $item->id)->where('status', '=', 'COMPLETE')->count();
                    if ($replacement_count > 0) {
                        $item->sale_car_total += $replacement_count;
                    }
                }
                if (strcmp($item->rental_type, RentalTypeEnum::BORROW) == 0) {
                    $borrow_count = BorrowCar::where('car_id', $item->id)->where('status', '=', 'SUCCESS')->count();
                    if ($borrow_count > 0) {
                        $item->sale_car_total += $borrow_count;
                    }
                }
                if (strcmp($item->rental_type, RentalTypeEnum::SHORT) == 0) {
                    $short_line = RentalLine::where('car_id', $item->id)->count();
                    if ($short_line > 0) {
                        $short_count = RentalLine::leftJoin('rentals', 'rentals.id', '=', 'rental_lines.rental_id')
                            ->where('rental_lines.car_id', $short_line)
                            ->whereDate('rentals.return_date', '<=', $today)
                            ->count();
                        if ($short_count) {
                            $item->sale_car_total += $short_count;
                        }
                    } else {
                        $item->sale_car_total = 1;
                    }
                }
                if (strcmp($item->rental_type, RentalTypeEnum::LONG) == 0) {
                    $long_term = LongTermRentalPRCar::leftJoin('lt_rental_pr_lines', 'lt_rental_pr_lines_cars.lt_rental_pr_line_id', 'lt_rental_pr_lines.id')
                        ->leftJoin('lt_rentals', 'lt_rentals.id', 'lt_rental_pr_lines.lt_rental_id')
                        ->where('lt_rental_pr_lines_cars.car_id', $item->id)
                        ->where('lt_rentals.contract_end_date', '<=', Carbon::now())
                        ->count();
                    if ($long_term) {
                        $item->sale_car_total += $long_term;
                    }
                }

                if (in_array($item->rental_type, [RentalTypeEnum::OTHER, RentalTypeEnum::SPARE, RentalTypeEnum::TRANSPORT])) {
                    $item->sale_car_total = 1;
                }
            } */

            //            Status Check Can Change Type
            //$item->status_change_type = $this->checkCarTypeChangeStatus($item);
            return $item;
        });

        $license_plate_list = [];
        $engine_no_list = [];
        $chassis_no_list = [];
        $car_category_list = CarCategory::select('name', 'id')->get();
        $rental_type_list = $this->getRentalType();
        $status_list = $this->getStatus();

        $storage_location_list = $this->getStorageLocationList();
        return view('admin.cars.index', [
            'list' => $list,
            's' => $request->s,
            'storage_location_list' => $storage_location_list,
            'license_plate_list' => $license_plate_list,
            'engine_no_list' => $engine_no_list,
            'chassis_no_list' => $chassis_no_list,
            'car_category_list' => $car_category_list,
            'rental_type_list' => $rental_type_list,
            'status_list' => $status_list,
            'engine_no' => $request->engine_no,
            'license_plate' => $request->license_plate,
            'chassis_no' => $request->chassis_no,
            'car_category' => $request->car_category,
            'rental_type' => $request->rental_type,
            'storage_location' => $request->storage_location,
            'status' => $request->status,
        ]);
    }

    public function checkCarTypeChangeStatus($dataCar)
    {
        $checkStatus = [];
        $stautsPendingSale = strcmp($dataCar->status, CarEnum::PENDING_SALE);
        $statusSoldOut = strcmp($dataCar->status, CarEnum::SOLD_OUT);
        if ($stautsPendingSale === 0 || $statusSoldOut === 0) {
            $checkStatus[] = false;
        }
        //        Check Rental
        $checkStatus[] = $this->checkRentalCar($dataCar);
        $checkStatus[] = $this->checkLongTermRentalCar($dataCar);
        if (!in_array(false, $checkStatus)) {
            return true;
        } else {
            return false;
        }
    }

    public function checkRentalCar($dataCar)
    {
        $statusRental = false;
        $dataRental = $dataCar?->rentalLines;
        if (!empty($dataRental->count())) {
            $dataRentalLatest = $dataRental
                ->where('pickup_date', '<=', Carbon::now())
                ->where('return_date', '>=', Carbon::now())
                ->count();
            if (empty($dataRentalLatest)) {
                $statusRental = true;
            }
        } else {
            $statusRental = true;
        }
        return $statusRental;
    }

    public function checkLongTermRentalCar($dataCar)
    {
        $statusRental = false;
        $dataLongTermRental = $dataCar->leftjoin('lt_rental_pr_lines_cars', 'lt_rental_pr_lines_cars.car_id', 'cars.id')
            ->leftJoin('lt_rental_pr_lines', 'lt_rental_pr_lines_cars.lt_rental_pr_line_id', 'lt_rental_pr_lines.id')
            ->leftJoin('lt_rentals', 'lt_rentals.id', 'lt_rental_pr_lines.lt_rental_id')
            ->where('contract_start_date', '<=', Carbon::now())
            ->where('contract_end_date', '>=', Carbon::now())
            ->count();
        if (empty($dataLongTermRental)) {
            $statusRental = true;
        }
        return $statusRental;
    }

    public static function getRentalType()
    {
        $rental_type = collect([
            (object) [
                'id' => RentalTypeEnum::SHORT,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::SHORT),
                'value' => RentalTypeEnum::SHORT,
            ],
            (object) [
                'id' => RentalTypeEnum::LONG,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::LONG),
                'value' => RentalTypeEnum::LONG,
            ],
            (object) [
                'id' => RentalTypeEnum::REPLACEMENT,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::REPLACEMENT),
                'value' => RentalTypeEnum::REPLACEMENT,
            ],
            (object) [
                'id' => RentalTypeEnum::TRANSPORT,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::TRANSPORT),
                'value' => RentalTypeEnum::TRANSPORT,
            ],
            (object) [
                'id' => RentalTypeEnum::SPARE,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::SPARE),
                'value' => RentalTypeEnum::SPARE,
            ],
            (object) [
                'id' => RentalTypeEnum::BORROW,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::BORROW),
                'value' => RentalTypeEnum::BORROW,
            ],
            (object) [
                'id' => RentalTypeEnum::OTHER,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::OTHER),
                'value' => RentalTypeEnum::OTHER,
            ],

        ]);
        return $rental_type;
    }

    public static function getStatus()
    {
        $rental_type = collect([
            (object) [
                'id' => CarEnum::NEWCAR,
                'name' => __('cars.status_' . CarEnum::NEWCAR),
                'value' => CarEnum::NEWCAR,
            ],
            (object) [
                'id' => CarEnum::NEWCAR_PENDING,
                'name' => __('cars.status_' . CarEnum::NEWCAR_PENDING),
                'value' => CarEnum::NEWCAR_PENDING,
            ],
            (object) [
                'id' => CarEnum::EQUIPMENT,
                'name' => __('cars.status_' . CarEnum::EQUIPMENT),
                'value' => CarEnum::EQUIPMENT,
            ],
            (object) [
                'id' => CarEnum::LEASE,
                'name' => __('cars.status_' . CarEnum::LEASE),
                'value' => CarEnum::LEASE,
            ],
            (object) [
                'id' => CarEnum::PENDING_RETURN,
                'name' => __('cars.status_' . CarEnum::PENDING_RETURN),
                'value' => CarEnum::PENDING_RETURN,
            ],
            (object) [
                'id' => CarEnum::ACCIDENT,
                'name' => __('cars.status_' . CarEnum::ACCIDENT),
                'value' => CarEnum::ACCIDENT,
            ],
            (object) [
                'id' => CarEnum::REPAIR,
                'name' => __('cars.status_' . CarEnum::REPAIR),
                'value' => CarEnum::REPAIR,
            ],
            (object) [
                'id' => CarEnum::PENDING_SALE,
                'name' => __('cars.status_' . CarEnum::PENDING_SALE),
                'value' => CarEnum::PENDING_SALE,
            ],
            (object) [
                'id' => CarEnum::READY_TO_USE,
                'name' => __('cars.status_' . CarEnum::READY_TO_USE),
                'value' => CarEnum::READY_TO_USE,
            ],
            (object) [
                'id' => CarEnum::CONTRACT_EXPIRED,
                'name' => __('cars.status_' . CarEnum::CONTRACT_EXPIRED),
                'value' => CarEnum::CONTRACT_EXPIRED,
            ],
            (object) [
                'id' => CarEnum::SOLD_OUT,
                'name' => __('cars.status_' . CarEnum::SOLD_OUT),
                'value' => CarEnum::SOLD_OUT,
            ],
            (object) [
                'id' => CarEnum::PENDING_REVIEW,
                'name' => __('cars.status_' . CarEnum::PENDING_REVIEW),
                'value' => CarEnum::PENDING_REVIEW,
            ],
            (object) [
                'id' => CarEnum::PENDING_DELIVER,
                'name' => __('cars.status_' . CarEnum::PENDING_DELIVER),
                'value' => CarEnum::PENDING_DELIVER,
            ],
        ]);
        return $rental_type;
    }

    public function getStorageLocationList()
    {
        $storage_locations = collect([
            (object) [
                'id' => StorageLocationEnum::TRUE_LEASING,
                'name' => __('cars.car_storage_' . StorageLocationEnum::TRUE_LEASING),
                'value' => StorageLocationEnum::TRUE_LEASING,
            ],
        ]);
        return $storage_locations;
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::Car);
        $d = new Car();
        $d->code = 'TY-' . strtotime("now");
        $car_wiper = CarWiper::all();
        $car_battery = CarBattery::all();
        $car_tire = CarTire::all();
        $car_park_zone_list = CarParkZone::branch()->get();
        $car_group_list = CarGroup::all();
        $car_category_list = CarCategory::all();
        $zone = null;

        $car_part = CarPart::leftJoin('car_part_types', 'car_part_types.id', '=', 'car_parts.car_part_type_id')
            ->select('car_parts.*', 'car_part_types.type as car_part_type')
            ->get();
        $gear = [];
        $drive_system = [];
        $car_seat = [];
        $side_mirror = [];
        $air_bag = [];
        $central_lock = [];
        $front_brake = [];
        $rear_brake = [];
        $abs = [];
        $anti_thift_system = [];
        foreach ($car_part as $item) {
            if ($item->car_part_type == CarPartTypeEnum::GEAR) {
                array_push($gear, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::DRIVE_SYSTEM) {
                array_push($drive_system, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::CAR_SEAT) {
                array_push($car_seat, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::SIDE_MIRROR) {
                array_push($side_mirror, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::AIR_BAG) {
                array_push($air_bag, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::CENTRAL_LOCK) {
                array_push($central_lock, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::FRONT_BRAKE) {
                array_push($front_brake, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::REAR_BRAKE) {
                array_push($rear_brake, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::ABS) {
                array_push($abs, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::ANTI_THIFT_SYSTEM) {
                array_push($anti_thift_system, $item);
            }
        }
        $storage_list = $this->getStorageList();
        $storage_location_list = $this->getStorageLocationList();

        $rental_type_list = $this->getRentalType();
        $status_list = $this->getStatus();
        $branch_lists = Branch::select('id', 'name')->get();
        $leasings = $this->getLeasingList();

        $page_title = __('cars.add_car');
        $mode = MODE_CREATE;

        $car_group_name = null;
        $car_category_name = null;
        $car_class_name = null;
        $car_color_name = null;
        $car_brand_name = null;
        $car_age = null;
        $car_age_start = null;
        return view('admin.cars.form', [
            'd' => $d,
            'page_title' => $page_title,
            'mode' => $mode,
            'gear' => $gear,
            'drive_system' => $drive_system,
            'car_seat' => $car_seat,
            'side_mirror' => $side_mirror,
            'air_bag' => $air_bag,
            'central_lock' => $central_lock,
            'front_brake' => $front_brake,
            'rear_brake' => $rear_brake,
            'abs' => $abs,
            'anti_thift_system' => $anti_thift_system,
            'car_wiper' => $car_wiper,
            'car_battery' => $car_battery,
            'car_tire' => $car_tire,
            'storage_list' => $storage_list,
            'storage_location_list' => $storage_location_list,
            'rental_type_list' => $rental_type_list,
            'car_group_name' => $car_group_name,
            'car_category_name' => $car_category_name,
            'car_class_name' => $car_class_name,
            'car_color_name' => $car_color_name,
            'car_brand_name' => $car_brand_name,
            'status_list' => $status_list,
            'car_park_zone_list' => $car_park_zone_list,
            'car_group_list' => $car_group_list,
            'car_category_list' => $car_category_list,
            'car_age' => $car_age,
            'car_age_start' => $car_age_start,
            'zone' => $zone,
            'branch_lists' => $branch_lists,
            'leasings' => $leasings,
            'state' => CarStateEnum::CREATE_DETAIL
        ]);
    }

    public function getStorageList()
    {
        $storages = collect([
            (object) [
                'id' => StorageEnum::IN_GARAGE,
                'name' => __('cars.car_park_' . StorageEnum::IN_GARAGE),
                'value' => StorageEnum::IN_GARAGE,
            ],
            (object) [
                'id' => StorageEnum::OUT_GARAGE,
                'name' => __('cars.car_park_' . StorageEnum::OUT_GARAGE),
                'value' => StorageEnum::OUT_GARAGE,
            ],
        ]);
        return $storages;
    }

    public function edit(Car $car)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Car);
        $storage_list = $this->getStorageList();
        $storage_location_list = $this->getStorageLocationList();

        $rental_type_list = $this->getRentalType();
        $status_list = $this->getStatus();

        $car_park_zone_list = CarParkZone::branch()->get();

        $page_title = __('lang.edit') . __('cars.title');
        $mode = MODE_VIEW;
        $view = false;

        $car_age = Carbon::now()->diff($car->registered_date);
        $car_age = $car_age->y . " ปี " . $car_age->m . " เดือน " . $car_age->d . " วัน";

        $car_age_start = Carbon::now()->diff($car->start_date);
        $car_age_start = $car_age_start->y . " ปี " . $car_age_start->m . " เดือน " . $car_age_start->d . " วัน";

        $car_group_name = $car->carGroup ? $car->carGroup->name : null;
        $car_category_name = $car->carCategory ? $car->carCategory->name : null;
        $car_class_name = $car->carClass ? $car->carClass->full_name : null;
        $car_color_name = $car->carColor ? $car->carColor->name : null;
        $car_brand_name = ($car->carClass && $car->carClass->carType && $car->carClass->carType->car_brand) ? $car->carClass->carType->car_brand->name : null;
        $car->car_brand_id = ($car->carClass && $car->carClass->carType && $car->carClass->carType->car_brand) ? $car->carClass->carType->car_brand->id : null;

        $car_wiper = CarWiper::all();
        $car_battery = CarBattery::all();
        $car_tire = CarTire::all();
        $car_part = CarPart::leftJoin('car_part_types', 'car_part_types.id', '=', 'car_parts.car_part_type_id')
            ->select('car_parts.*', 'car_part_types.type as car_part_type')
            ->get();
        $car_group_list = CarGroup::all();
        $car_category_list = CarCategory::all();
        $gear = [];
        $drive_system = [];
        $car_seat = [];
        $side_mirror = [];
        $air_bag = [];
        $central_lock = [];
        $front_brake = [];
        $rear_brake = [];
        $abs = [];
        $anti_thift_system = [];
        foreach ($car_part as $item) {
            if ($item->car_part_type == CarPartTypeEnum::GEAR) {
                array_push($gear, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::DRIVE_SYSTEM) {
                array_push($drive_system, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::CAR_SEAT) {
                array_push($car_seat, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::SIDE_MIRROR) {
                array_push($side_mirror, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::AIR_BAG) {
                array_push($air_bag, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::CENTRAL_LOCK) {
                array_push($central_lock, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::FRONT_BRAKE) {
                array_push($front_brake, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::REAR_BRAKE) {
                array_push($rear_brake, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::ABS) {
                array_push($abs, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::ANTI_THIFT_SYSTEM) {
                array_push($anti_thift_system, $item);
            }
        }
        $storage_list = $this->getStorageList();
        $storage_location_list = $this->getStorageLocationList();

        $car_accessory_list = CarAccessory::where('car_id', $car->id)->get();
        $car_accessory_list->map(function ($item) {
            $item->accessory_text = ($item->carAccessory) ? $item->carAccessory->name : '';
            $item->accessory_id = $item->accessory_id;
            $item->amount_accessory = $item->amount;
            $item->remark = $item->remark;
            return $item;
        });
        $zone = CarPark::leftjoin('car_park_areas', 'car_park_areas.id', 'car_parks.car_park_area_id')
            ->leftjoin('car_park_zones', 'car_park_zones.id', 'car_park_areas.car_park_zone_id')
            ->where('car_parks.car_id', $car->id)
            //->where('car_park_zones.branch_id', get_branch_id())
            ->select('car_park_zones.code', 'car_parks.car_park_number')
            ->first();


        $rental_type_list = $this->getRentalType();
        $status_list = $this->getStatus();
        $branch_lists = Branch::select('id', 'name')->get();
        $leasings = $this->getLeasingList();
        return view('admin.cars.form', [
            'd' => $car,
            'mode' => $mode,
            'page_title' => $page_title,
            'storage_list' => $storage_list,
            'storage_location_list' => $storage_location_list,
            'rental_type_list' => $rental_type_list,
            'car_group_name' => $car_group_name,
            'car_category_name' => $car_category_name,
            'car_class_name' => $car_class_name,
            'car_color_name' => $car_color_name,
            'status_list' => $status_list,
            'gear' => $gear,
            'drive_system' => $drive_system,
            'car_seat' => $car_seat,
            'side_mirror' => $side_mirror,
            'air_bag' => $air_bag,
            'central_lock' => $central_lock,
            'front_brake' => $front_brake,
            'rear_brake' => $rear_brake,
            'abs' => $abs,
            'anti_thift_system' => $anti_thift_system,
            'car_wiper' => $car_wiper,
            'car_battery' => $car_battery,
            'car_tire' => $car_tire,
            'storage_list' => $storage_list,
            'storage_location_list' => $storage_location_list,
            'rental_type_list' => $rental_type_list,
            'car_park_zone_list' => $car_park_zone_list,
            'car_group_list' => $car_group_list,
            'car_category_list' => $car_category_list,
            'car_age' => $car_age,
            'car_age_start' => $car_age_start,
            'car_brand_name' => $car_brand_name,
            'car_accessory_list' => $car_accessory_list,
            'zone' => $zone,
            'branch_lists' => $branch_lists,
            'leasings' => $leasings,
            'state' => CarStateEnum::EDIT_DETAIL
        ]);
    }

    public function show(Car $car)
    {
        $this->authorize(Actions::View . '_' . Resources::Car);
        $storage_list = $this->getStorageList();
        $storage_location_list = $this->getStorageLocationList();

        $rental_type_list = $this->getRentalType();
        $status_list = $this->getStatus();

        $page_title = __('lang.view') . __('cars.title');
        $mode = MODE_VIEW;
        $view = true;
        $car_park_zone_list = CarParkZone::branch()->get();
        $car_group_list = CarGroup::all();
        $car_category_list = CarCategory::all();

        $car_age = Carbon::now()->diff($car->registered_date);
        $car_age = $car_age->y . " ปี " . $car_age->m . " เดือน " . $car_age->d . " วัน";

        $car_age_start = Carbon::now()->diff($car->start_date);
        $car_age_start = $car_age_start->y . " ปี " . $car_age_start->m . " เดือน " . $car_age_start->d . " วัน";

        $car_class_name = $car->carClass ? $car->carClass->full_name : null;
        $car_group_name = $car->carGroup ? $car->carGroup->name : null;
        $car_category_name = $car->carCategory ? $car->carCategory->name : null;
        $car_brand_name = $car->carBrand ? $car->carBrand->name : null;
        $car_color_name = $car->carColor ? $car->carColor->name : null;

        $car_wiper = CarWiper::all();
        $car_battery = CarBattery::all();
        $car_tire = CarTire::all();
        $car_part = CarPart::leftJoin('car_part_types', 'car_part_types.id', '=', 'car_parts.car_part_type_id')
            ->select('car_parts.*', 'car_part_types.type as car_part_type')
            ->get();
        $gear = [];
        $drive_system = [];
        $car_seat = [];
        $side_mirror = [];
        $air_bag = [];
        $central_lock = [];
        $front_brake = [];
        $rear_brake = [];
        $abs = [];
        $anti_thift_system = [];
        foreach ($car_part as $item) {
            if ($item->car_part_type == CarPartTypeEnum::GEAR) {
                array_push($gear, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::DRIVE_SYSTEM) {
                array_push($drive_system, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::CAR_SEAT) {
                array_push($car_seat, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::SIDE_MIRROR) {
                array_push($side_mirror, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::AIR_BAG) {
                array_push($air_bag, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::CENTRAL_LOCK) {
                array_push($central_lock, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::FRONT_BRAKE) {
                array_push($front_brake, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::REAR_BRAKE) {
                array_push($rear_brake, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::ABS) {
                array_push($abs, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::ANTI_THIFT_SYSTEM) {
                array_push($anti_thift_system, $item);
            }
        }
        $storage_list = $this->getStorageList();
        $storage_location_list = $this->getStorageLocationList();

        $rental_type_list = $this->getRentalType();
        $status_list = $this->getStatus();
        $branch_lists = Branch::select('id', 'name')->get();
        $leasings = $this->getLeasingList();

        $car_accessory_list = CarAccessory::where('car_id', $car->id)->get();
        $car_accessory_list->map(function ($item) {
            $item->accessory_text = ($item->carAccessory) ? $item->carAccessory->name : '';
            $item->accessory_id = $item->accessory_id;
            $item->amount_accessory = $item->amount;
            $item->remark = $item->remark;
            return $item;
        });
        $zone = CarPark::leftjoin('car_park_areas', 'car_park_areas.id', 'car_parks.car_park_area_id')
            ->leftjoin('car_park_zones', 'car_park_zones.id', 'car_park_areas.car_park_zone_id')
            ->where('car_parks.car_id', $car->id)
            //->where('car_park_zones.branch_id', get_branch_id())
            ->select('car_park_zones.code', 'car_parks.car_park_number')
            ->first();

        return view('admin.cars.form', [
            'd' => $car,
            'mode' => $mode,
            'page_title' => $page_title,
            'storage_list' => $storage_list,
            'storage_location_list' => $storage_location_list,
            'rental_type_list' => $rental_type_list,
            'view' => $view,
            'car_group_name' => $car_group_name,
            'car_category_name' => $car_category_name,
            'car_class_name' => $car_class_name,
            'car_color_name' => $car_color_name,
            'status_list' => $status_list,
            'gear' => $gear,
            'drive_system' => $drive_system,
            'car_seat' => $car_seat,
            'side_mirror' => $side_mirror,
            'air_bag' => $air_bag,
            'central_lock' => $central_lock,
            'front_brake' => $front_brake,
            'rear_brake' => $rear_brake,
            'abs' => $abs,
            'anti_thift_system' => $anti_thift_system,
            'car_wiper' => $car_wiper,
            'car_battery' => $car_battery,
            'car_tire' => $car_tire,
            'storage_list' => $storage_list,
            'storage_location_list' => $storage_location_list,
            'rental_type_list' => $rental_type_list,
            'car_park_zone_list' => $car_park_zone_list,
            'car_group_list' => $car_group_list,
            'car_category_list' => $car_category_list,
            'car_age' => $car_age,
            'car_age_start' => $car_age_start,
            'car_brand_name' => $car_brand_name,
            'car_accessory_list' => $car_accessory_list,
            'zone' => $zone,
            'branch_lists' => $branch_lists,
            'leasings' => $leasings,
            'state' => CarStateEnum::SHOW_DETAIL
        ]);
    }

    public function store(Request $request)
    {
        // TODO
        $validator = Validator::make($request->all(), [
            'car_code' => [
                'required', 'string',
                Rule::unique('cars', 'code')->whereNull('deleted_at')->ignore($request->id),
            ],
            'license_plate' => [
                'required', 'string', 'max:100',
                Rule::unique('cars', 'license_plate')->whereNull('deleted_at')->ignore($request->id),
            ],
            'engine_no' => ['required', 'string'],
            'chassis_no' => ['required', 'string'],
            'car_brand_id' => ['required'],
            'car_class_id' => ['required'],
            'car_color_id' => ['required'],
            //            'car_group_id' => ['required'],
            //            'car_categorie_id' => ['required'],
            'rental_type' => ['required'],
            'branch_id' => ['required'],
            'status' => ['required'],
        ], [], [
            'car_code' => __('cars.code'),
            'license_plate' => __('cars.license_plate'),
            'engine_no' => __('cars.engine_no'),
            'chassis_no' => __('cars.chassis_no'),
            'car_brand_id' => __('car_classes.car_brand'),
            'car_class_id' => __('car_classes.class'),
            'car_color_id' => __('purchase_requisitions.car_color'),
            //            'car_group_id' => __('cars.car_group'),
            //            'car_categorie_id' => __('cars.car_category'),
            'rental_type' => __('cars.rental_type'),
            'branch_id' => __('lang.branch_name'),
            'status' => __('lang.status'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $car = Car::firstOrNew(['id' => $request->id]);
        $car->code = $request->car_code;
        $car->license_plate = $request->license_plate;
        $car->engine_no = $request->engine_no;
        $car->chassis_no = $request->chassis_no;
        $car->rental_type = $request->rental_type;
        $car->car_class_id = $request->car_class_id;
        $car->car_color_id = $request->car_color_id;
        $car->status = $request->status;
        $car->branch_id = $request->branch_id;
        $car->leasing_id = $request->leasing_id;
        //save car part
        $car->engine_size = $request->engine_size;
        $car->oil_type = $request->oil_type;
        $car->oil_tank_capacity = $request->oil_tank_capacity;
        $car->gear_id = $request->gear_id;
        $car->drive_system_id = $request->drive_system_id;
        $car->central_lock_id = $request->central_lock_id;
        $car->car_seat_id = $request->car_seat_id;
        $car->air_bag_id = $request->air_bag_id;
        $car->side_mirror_id = $request->side_mirror_id;
        $car->anti_thift_system_id = $request->anti_thift_system_id;
        $car->abs_id = $request->abs_id;
        $car->front_brake_id = $request->front_brake_id;
        $car->rear_brake_id = $request->rear_brake_id;
        $car->car_tire_id = $request->car_tire_id;
        $car->car_battery_id = $request->car_battery_id;
        $car->car_wiper_id = $request->car_wiper_id;

        $car->car_brand_id = $request->car_brand_id;
        //        $car->car_group_id = $request->car_group_id;
        //        $car->car_categorie_id = $request->car_categorie_id;
        $car->registered_date = $request->registered_date;
        $car->start_date = $request->start_date;
        $car->car_storage = $request->car_storage;
        $car->car_park = $request->car_park;
        $car->save();

        if (isset($request->car_accessory)) {
            CarAccessory::where('car_id', $car->id)->delete();
            foreach ($request->car_accessory as $data) {
                $car_accessory = new CarAccessory();
                $car_accessory->car_id = $car->id;
                $car_accessory->accessory_id = $data['accessory_id'];
                $car_accessory->amount = $data['amount'];
                $car_accessory->remark = $data['remark'];
                $car_accessory->save();
            }
        }


        $redirect_route = route('admin.cars.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    function getSaleCar(Request $request)
    {
        $car_id_arr = $request->car_id_arr;
        $car_arr = [];
        $today = date('Y-m-d');
        if ($car_id_arr) {
            $cars = Car::whereIn('id', $car_id_arr)->whereNotIn('status', [CarEnum::NEWCAR, CarEnum::PENDING_SALE, CarEnum::SOLD_OUT])->get();
            if (sizeof($cars) > 0) {
                foreach ($cars as $index => $item_car) {
                    if (strcmp($item_car->rental_type, RentalTypeEnum::REPLACEMENT) == 0) {
                        $replacement_arr = ReplacementCar::where('replacement_car_id', $item_car->id)->where('status', '=', 'COMPLETE')->pluck('replacement_car_id')->toArray();
                        if ($replacement_arr) {
                            array_push($car_arr, $replacement_arr);
                        }
                    }
                    if (strcmp($item_car->rental_type, RentalTypeEnum::BORROW) == 0) {
                        $borrow_arr = BorrowCar::where('car_id', $item_car->id)->where('status', '=', 'SUCCESS')->pluck('car_id')->toArray();
                        if ($borrow_arr) {
                            array_push($car_arr, $borrow_arr);
                        }
                    }
                    if (strcmp($item_car->rental_type, RentalTypeEnum::SHORT) == 0) {
                        $short_line = RentalLine::where('car_id', $item_car->id)->pluck('car_id')->toArray();
                        if ($short_line) {
                            $short_arr = RentalLine::leftJoin('rentals', 'rentals.id', '=', 'rental_lines.rental_id')
                                ->where('rental_lines.car_id', $short_line)
                                ->whereDate('rentals.return_date', '<=', $today)
                                ->pluck('car_id')->toArray();
                            if ($short_arr) {
                                array_push($car_arr, $short_arr);
                            }
                        } else {
                            array_push($car_arr, $item_car->id);
                        }
                    }
                    if (strcmp($item_car->rental_type, RentalTypeEnum::LONG) == 0) {
                        $long_arr = LongTermRentalPRCar::leftJoin('lt_rental_pr_lines', 'lt_rental_pr_lines_cars.lt_rental_pr_line_id', 'lt_rental_pr_lines.id')
                            ->leftJoin('lt_rentals', 'lt_rentals.id', 'lt_rental_pr_lines.lt_rental_id')
                            ->where('lt_rental_pr_lines_cars.car_id', $item_car->id)
                            // ->where('lt_rentals.contract_start_date', '<=', Carbon::now())
                            ->where('lt_rentals.contract_end_date', '<=', Carbon::now())
                            ->pluck('car_id')->toArray();
                        if ($long_arr) {
                            array_push($car_arr, $long_arr);
                        }
                    }

                    if (in_array($item_car->rental_type, [RentalTypeEnum::OTHER, RentalTypeEnum::SPARE, RentalTypeEnum::TRANSPORT])) {
                        array_push($car_arr, $item_car->id);
                    }
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'ไม่สามารถส่งขายรถยนต์ได้',
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'กรุณาเลือกรถที่ส่งขาย',
            ];
        }

        if ($car_arr) {
            return [
                'success' => true,
                'data' => $car_arr,
            ];
        } else {
            return [
                'success' => false,
                'message' => 'รถคันที่เลือก มีใบงานที่ดำเนินการอยู่',
            ];
        }
        //เลขทะเบียน ยังติดงานเช่า ->first()
    }

    function updateStatusSale(Request $request)
    {
        $car_ids = $request->car_ids;
        $license_plate = null;
        $car_id = null;
        if ($car_ids > 0) {
            foreach ($car_ids as $item) {
                $car = Car::find($item);
                if ($car) {
                    $selling_count = SellingPriceLine::where('car_id', $car->id)->where('status', SellingPriceStatusEnum::CONFIRM)->count();
                    //มีcar statusCONFIRM
                    if ($selling_count > 0) {
                        //สร้าง car auction status รอส่งขาย
                        $car_auction = new CarAuction();
                        $car_auction->car_id = $car->id;
                        $car_auction->status = SellingPriceStatusEnum::PENDING_SALE;
                        $car_auction->save();
                    }

                    $selling_car_count = SellingPriceLine::where('car_id', $car->id)->count();
                    //ไม่มีอะไรเลย
                    //สร้าง selling price
                    if ($selling_car_count <= 0) {
                        $selling_price = new SellingPriceLine();
                        $selling_price->car_id = $car->id;
                        $selling_price->status = SellingPriceStatusEnum::PRE_SALE_PRICE;
                        $selling_price->save();
                    }

                    if (!is_null($car->have_gps)) {
                        $create_gps = GpsTrait::createGPSRemoveStopSignal(GPSJobTypeEnum::AUCTION_SALE, $car->id, null, STATUS_ACTIVE);
                    }

                    //selling_price_line มี car_id และstatus อื่นๆ
                    //update car status  PENDING_SALE
                    $car->status = CarEnum::PENDING_SALE;
                    $car->save();
                    $license_plate = $car->license_plate;
                    $car_id = $car->id;
                }
            }
            if (count($car_ids) > 1) {
                $description = "รถจำนวน" . count($car_ids) . "คัน ได้พิจารณาส่งขายประมูล";
                $url = route('admin.cars.index', ['status' => CarEnum::PENDING_SALE]);
            } else {
                $description = "รถทะเบียน" . $license_plate . "ได้พิจารณาส่งขายประมูล";
                $url = route('admin.cars.index', ['license_plate' => $car_id]);
            }

            $dataDepartment = [
                DepartmentEnum::PCD_RESALE,
            ];
            $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
            $notiTypeChange = new NotificationManagement('ส่งขายรถยนต์', $description, $url, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, []);
            $notiTypeChange->send();
        }

        return response()->json([
            'success' => true,
            'message' => 'ok',
            'redirect' => route('admin.cars.index'),
        ]);
    }

    public function updateCarType(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Car);
        $validator = Validator::make($request->all(), [
            'car_type' => [
                'required'
            ],
        ], [], [
            'car_type' => __('cars.required_car_type'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $carId = $request?->car_id;
        $carType = $request?->car_type;
        $checkInspectionJob = InspectionJob::where('car_id', $carId)
            ->where('inspection_status', InspectionStatusEnum::DRAFT)
            ->count();
        if ($checkInspectionJob > 0) {
            return $this->responseWithCode(false, 'ไม่สามารเปลี่ยนประเภทรถได้เนื่องจากมีใบตรวจค้างในระบบ', null, 404);
        }
        $car = Car::findOrFail($carId);
        $carTypeOld = $car->rental_type;
        $car->rental_type = $carType;
        $car->save();
        $textCarType = __('cars.rental_type_' . $carType);
        $textCarTypeOld = __('cars.rental_type_' . $carTypeOld);
        $url = route('admin.cars.show', ['car' => $car]);
        $this->autoModelDrivingJob($carId);
        $dataDepartment = [
            DepartmentEnum::RMD_INSURANCE,
            DepartmentEnum::PCD_REGISTRATION,
            DepartmentEnum::QMD_QUALITY_ASSURANCE,
        ];
        $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
        $notiTypeChange = new NotificationManagement('เปลี่ยนประเภทรถ', 'รถทะเบียน ' . $car?->license_plate . '  ได้เปลี่ยนประเภทรถจาก ' . $textCarTypeOld . ' เป็น ' . $textCarType, $url, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, []);
        $notiTypeChange->send();
        $redirect_route = route('admin.cars.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function autoModelDrivingJob($import_car_id)
    {
        $ijf = new InspectionJobFactory(InspectionTypeEnum::CHANGE_TYPE, null, $import_car_id, $import_car_id);
        $ijf->create();
    }

    public function getCarCMIList(Request $request)
    {
        $car_id = $request->car_id;
        if (!$car_id) {
            return [];
        }
        $list = CMI::with('insurer', 'car', 'job')->select('*')
            ->where('car_id', $car_id)
            ->sortable(['worksheet_no' => 'desc'])
            ->get();
        foreach ($list as $item) {
            $item->link = route('admin.cmi-cars.show', ['cmi_car' => $item->id]);
            $item->type = __('cmi_cars.type_' . $item->type);
            $item->class_status = __('cmi_cars.class_' . $item->status);
            $item->status_text = __('cmi_cars.status_' . $item->status);
        }
        return response()->json($list);
    }

    public function getCarVMIList(Request $request)
    {
        $car_id = $request->car_id;
        if (!$car_id) {
            return [];
        }
        $list = VMI::with('insurer', 'car', 'job')->select('*')
            ->where('car_id', $car_id)
            ->sortable(['worksheet_no' => 'desc'])
            ->get();
        foreach ($list as $item) {
            $item->link = route('admin.vmi-cars.show', ['vmi_car' => $item->id]);
            $item->type = __('cmi_cars.type_' . $item->type);
            $item->class_status = __('cmi_cars.class_' . $item->status);
            $item->status_text = __('cmi_cars.status_' . $item->status);
        }
        return response()->json($list);
    }

    public function getShortTermRentalList(Request $request)
    {
        $car_id = $request->car_id;
        if (!$car_id) {
            return [];
        }
        $list = Rental::leftJoin('rental_lines', 'rental_lines.rental_id', '=', 'rentals.id')
            ->leftJoin('branches', 'branches.id', '=', 'rentals.branch_id')
            ->leftJoin('service_types', 'service_types.id', '=', 'rentals.service_type_id')
            ->leftJoin('quotations', 'quotations.id', '=', 'rentals.quotation_id')
            ->where('rental_lines.car_id', $car_id)
            ->select('rentals.*', 'branches.name as branch_name', 'service_types.name as service_type_name', 'quotations.qt_no')
            ->get();

        foreach ($list as $item) {
            $item->link = route('admin.short-term-rentals.show', ['short_term_rental' => $item->id]);
            $item->created_date = get_thai_date_format($item->created_at, 'd/m/Y');
            $item->class_status = __('short_term_rentals.class_' . $item->status);
            $item->status_text = __('short_term_rentals.status_' . $item->status);
        }
        return response()->json($list);
    }

    public function getInstallEquipmentList(Request $request)
    {
        $car_id = $request->car_id;
        if (!$car_id) {
            return [];
        }

        $list = InstallEquipmentLine::leftjoin('install_equipments', 'install_equipments.id', '=', 'install_equipment_lines.install_equipment_id')
            ->leftjoin('install_equipment_purchase_orders', 'install_equipment_purchase_orders.install_equipment_id', '=', 'install_equipments.id')
            ->leftjoin('accessories', 'accessories.id', '=', 'install_equipment_lines.accessory_id')
            ->leftjoin('creditors', 'creditors.id', '=', 'install_equipments.supplier_id')
            ->where('install_equipments.car_id', $car_id)
            ->select(
                'install_equipments.*',
                'install_equipment_purchase_orders.id as ie_po_id',
                'install_equipment_purchase_orders.worksheet_no as po_worksheet_no',
                'accessories.name',
                'creditors.name as supplier_name',
            )
            ->get();

        foreach ($list as $item) {
            $item->ie_link = route('admin.install-equipments.show', ['install_equipment' => $item->id]);
            $item->po_link = route('admin.install-equipment-purchase-orders.show', ['install_equipment_purchase_order' => $item->ie_po_id]);
            $item->created_date = get_thai_date_format($item->created_at, 'd/m/Y');
            if ($item->start_date) {
                $datetime_start = new DateTime($item->start_date);
                $datetime_today = new DateTime();
                $interval = $datetime_start->diff($datetime_today);
                $days = $interval->format('%a');
                $item->day_amount = $days;
                if ($item->install_day_amount) {
                    if (
                        in_array($item->status, [
                            InstallEquipmentStatusEnum::WAITING,
                            InstallEquipmentStatusEnum::PENDING_REVIEW,
                            InstallEquipmentStatusEnum::INSTALL_IN_PROCESS,
                            InstallEquipmentStatusEnum::INSTALL_IN_PROCESS,
                        ])
                    ) {
                        if ($days == 0) {
                            $item->status = InstallEquipmentStatusEnum::DUE;
                        }
                        if ($days > $item->install_day_amount) {
                            $item->status = InstallEquipmentStatusEnum::OVERDUE;
                            $item->day_amount = $days - $item->install_day_amount;
                        }
                    }
                }
            }
            $item->class_status = __('install_equipments.class_' . $item->status);
            $item->status_text = __('install_equipments.status_' . $item->status);
        }
        return response()->json($list);
    }

    public function getAccidentList(Request $request)
    {
        $car_id = $request->car_id;
        if (!$car_id) {
            return [];
        }

        $list = Accident::where('car_id', $car_id)
            ->search($request)
            ->select('accidents.*')
            ->get();

        $list->map(function ($item) {
            $accident_order = AccidentRepairOrder::where('accident_id', $item->id)->first();
            $item->accident_order_worksheet_no = $accident_order ? $accident_order->worksheet_no : null;
            $item->accident_type_text = __('accident_informs.accident_type_index_' . $item->accident_type);
            $item->accident_inform_link = route('admin.accident-inform-sheets.show', ['accident_inform_sheet' => $item->id]);
            $item->accident_order_link = $accident_order ? route('admin.accident-orders.show', ['accident_order' => $accident_order->id]) : null;
            $item->created_date = get_thai_date_format($item->created_at, 'd/m/Y');
            $item->accident_date = get_thai_date_format($item->accident_date, 'd/m/Y H:i');
            $item->class_status = __('accident_informs.class_job_' . $item->status);
            $item->status_text = __('accident_informs.status_job_' . $item->status);
            $item->case_text = __('accident_informs.case_' . $item->case);
            $car = Car::find($item->car_id);
            $item->license_plate = null;
            if ($car) {
                $text = null;
                if ($car->license_plate) {
                    $text = $car->license_plate;
                } else if ($car->engine_no) {
                    $text = __('inspection_cars.engine_no') . ' ' . $car->engine_no;
                } else if ($car->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
                }
                $item->license_plate = $text;
            }

            // rental
            $worksheet = RentalLine::where('car_id', $car->id)
                ->whereDate('pickup_date', '<=', Carbon::now())
                ->whereDate('return_date', '>=', Carbon::now())
                ->first();

            // lt rental
            if (is_null($worksheet)) {
                $worksheet = null;
                $lt_rental_car = LongTermRentalPRCar::where('car_id', $car->id)
                    ->first();
                if ($lt_rental_car) {
                    $lt_pr_line = LongTermRentalPRLine::where('id', $lt_rental_car->lt_rental_pr_line_id)->first();
                    if ($lt_pr_line) {
                        $worksheet = LongTermRental::find($lt_pr_line->lt_rental_id);
                    }
                }
            } else {
                $worksheet = Rental::find($worksheet->rental_id);
            }

            if ($worksheet) {
                $item->customer_name = $worksheet->customer_name;
            }

            return $item;
        });
        return response()->json($list);
    }

    public function getRepairList(Request $request)
    {
        $car_id = $request->car_id;
        if (!$car_id) {
            return [];
        }
        $list = Repair::leftJoin('cars', 'cars.id', '=', 'repairs.car_id')
            ->leftJoin('repair_orders', 'repair_orders.repair_id', '=', 'repairs.id')
            ->select(
                'repairs.id',
                'repairs.status',
                'repairs.worksheet_no',
                'repairs.repair_type',
                'repairs.repair_date',
                'repairs.contact',
                'repairs.in_center_date',
                'cars.license_plate',
                'repair_orders.id as repair_order_id',
                'repair_orders.worksheet_no as order_worksheet_no',
                'repair_orders.expected_repair_date',
                'repair_orders.repair_date as completed_date',
            )
            ->where('repairs.car_id', $car_id)
            ->orderBy('repairs.created_at', 'desc')
            ->get();
        $list->map(function ($item) {
            $item->repair_type_text = __('repairs.repair_type_' . $item->repair_type);
            $item->repair_link = route('admin.repairs.show', ['repair' => $item->id]);
            $item->repair_order_link = $item->repair_order_id ? route('admin.repair-orders.show', ['repair_order' => $item->repair_order_id]) : null;
            $item->created_date = get_thai_date_format($item->created_at, 'd/m/Y');
            $item->repair_date = $item->repair_date ? get_thai_date_format($item->repair_date, 'd/m/Y') : null;
            $item->in_center_date = $item->in_center_date ? get_thai_date_format($item->in_center_date, 'd/m/Y') : null;
            $item->expected_repair_date = $item->expected_repair_date ? get_thai_date_format($item->expected_repair_date, 'd/m/Y') : null;
            $item->completed_date = $item->completed_date ? get_thai_date_format($item->completed_date, 'd/m/Y') : null;

            $item->class_status = __('repairs.repair_class_' . $item->status);
            $item->status_text = __('repairs.repair_text_' . $item->status);
            return $item;
        });
        return response()->json($list);
    }

    function getLeasingList()
    {
        $creditors_types_relation = DB::table('creditors_types_relation')->select('creditor_id')
            ->join('creditor_types', 'creditor_types.id', '=', 'creditors_types_relation.creditor_type_id')
            ->where('creditor_types.type', CreditorTypeEnum::LEASING);
        $leasings = Creditor::select('creditors.id', 'creditors.name')->whereIn('creditors.id', $creditors_types_relation)
            ->orderBy('creditors.name')
            ->get();
        return $leasings;
    }
}
