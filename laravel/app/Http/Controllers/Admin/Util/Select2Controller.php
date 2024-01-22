<?php

namespace App\Http\Controllers\Admin\Util;

use App\Enums\CreditorTypeEnum;
use App\Models\CarType;
use App\Models\CarBrand;
use App\Models\CarColor;
use App\Models\Accessories;
use App\Models\CarCategory;
use App\Models\Contracts;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Dealers;
use App\Models\CarClass;
use App\Models\CarGroup;
use App\Models\Creditor;
use App\Models\Location;
use App\Models\LocationGroup;
use App\Models\ProductAdditional;
use App\Models\Province;
use App\Models\Amphure;
use App\Models\District;
use App\Models\PurchaseRequisition;
use App\Models\ServiceType;
use App\Models\Car;
use App\Models\CarPark;
use App\Models\CarParkArea;
use App\Models\CarParkZone;
use App\Models\Product;
use App\Models\PromotionCode;
use App\Models\DrivingSkill;
use App\Models\Driver;
use App\Models\DriverWage;
use App\Classes\ProductManagement;
use App\Classes\PromotionManagement;
use App\Enums\LongTermRentalTypeEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\ZoneTypeEnum;
use App\Enums\CarParkStatusEnum;
use App\Enums\PromotionTypeEnum;
use App\Models\Rental;
use App\Models\RentalBill;
use App\Models\Bom;
use App\Models\Promotion;
use App\Models\ImportCar;
use App\Models\ImportCarLine;
use App\Models\Section;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;

class Select2Controller extends Controller
{
    function getCarBrand(Request $request)
    {
        $list = CarBrand::select('id', 'name')
            ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getCarType(Request $request)
    {
        $list = CarType::select('id', 'name')
            ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('car_brand_id', $request->parent_id);
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getCarCategory(Request $request)
    {
        $list = CarCategory::select('car_categories.id', 'car_categories.name')
            ->leftJoin('car_types', 'car_types.car_category_id', '=', 'car_categories.id')
            ->where('car_categories.status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('car_categories.name', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('car_types.id', $request->parent_id);
                }
            })
            ->groupBy('car_categories.id')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getCarCategoryByCarClasses(Request $request)
    {
        $list = CarCategory::select('car_categories.id', 'car_categories.name as text')
            ->leftJoin('car_types', 'car_types.car_category_id', '=', 'car_categories.id')
            ->leftJoin('car_classes', 'car_classes.car_type_id', '=', 'car_types.id')
            ->where('car_categories.status', STATUS_ACTIVE)
            ->distinct('car_categories.id')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('car_categories.name', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('car_classes.id', $request->parent_id);
                }
            })
            ->get();
        return response()->json($list);
    }

    function getCarColors(Request $request)
    {
        $car_colors = CarColor::select('id', 'name')
            // ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('car_colors.code', 'like', '%' . $request->s . '%');
                    $query->orWhere('car_colors.name', 'like', '%' . $request->s . '%');
                }
            })
            // ->orderBy('seq')->orderBy('name')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($car_colors);
    }

    function getAccessories(Request $request)
    {
        $accessories = Accessories::select('id', 'name')
            // ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('accessories.code', 'like', '%' . $request->s . '%');
                    $query->orWhere('accessories.name', 'like', '%' . $request->s . '%');
                }
            })
            // ->orderBy('seq')
            ->orderBy('name')
            ->limit(50)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($accessories);
    }

    function getAccessoriesTypeAccessory(Request $request)
    {
        $accessories = Accessories::select('accessories.id', 'accessories.name')
            ->join('creditors', 'creditors.id', '=', 'accessories.creditor_id')
            ->join('creditors_types_relation', 'creditors_types_relation.creditor_id', '=', 'creditors.id')
            ->join('creditor_types', 'creditor_types.id', '=', 'creditors_types_relation.creditor_type_id')
            ->where('accessories.status', STATUS_ACTIVE)
            ->where('creditor_types.type', CreditorTypeEnum::ACCESSORIES)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('accessories.code', 'like', '%' . $request->s . '%');
                    $query->orWhere('accessories.name', 'like', '%' . $request->s . '%');
                }
            })
            // ->orderBy('seq')
            ->orderBy('accessories.name')
            ->limit(30)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($accessories);
    }

    function getAccessoriesBom(Request $request)
    {
        $accessories = Bom::select('id', 'worksheet_no as name')
            ->where('type', LongTermRentalTypeEnum::ACCESSORY)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name', 'like', '%' . $request->s . '%');
                    $query->orWhere('worksheet_no', 'like', '%' . $request->s . '%');
                }
            })
            // ->orderBy('seq')
            ->orderBy('name')
            ->limit(30)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($accessories);
    }

    function getAccessoryVersions(Request $request)
    {
        $accessory_versions = Accessories::select('id', 'version')
            // ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('accessories.version', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('accessories.id', $request->parent_id);
                }
            })
            // ->orderBy('seq')
            // ->orderBy('name')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->version,
                ];
            });
        return response()->json($accessory_versions);
    }

    function getDealers(Request $request)
    {
        $dealers = Creditor::leftjoin('creditors_types_relation', 'creditors_types_relation.creditor_id', '=', 'creditors.id')
            ->leftjoin('creditor_types', 'creditor_types.id', '=', 'creditors_types_relation.creditor_type_id')
            ->select('creditors.id', 'creditors.name')
            ->where('creditor_types.type', CreditorTypeEnum::DEALER)
            ->where('creditors.status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('creditors.name', 'like', '%' . $request->s . '%');
                }
            })
            ->orderBy('creditors.name')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($dealers);
    }

    function getCarClasses(Request $request)
    {
        $car_class = CarClass::select('id', 'name', 'full_name')
            // ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('car_classes.name', 'like', '%' . $request->s . '%');
                    $query->orWhere('car_classes.full_name', 'like', '%' . $request->s . '%');
                }
            })
            // ->orderBy('seq')
            ->orderBy('name')
            ->limit(30)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->full_name . ' - ' . $item->name
                ];
            });
        return response()->json($car_class);
    }

    function getCarClassesByCarBrand(Request $request)
    {
        $car_class = CarClass::leftjoin('car_types', 'car_types.id', '=', 'car_classes.car_type_id')
            ->leftjoin('car_brands', 'car_brands.id', '=', 'car_types.car_brand_id')
            ->select('car_classes.id', 'car_classes.name', 'car_classes.full_name')
            // ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('car_classes.full_name', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('car_brands.id', $request->parent_id);
                }
            })
            // ->orderBy('seq')
            ->orderBy('name')
            ->limit(30)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->full_name . ' - ' . $item->name
                ];
            });
        return response()->json($car_class);
    }

    function getPRParent(Request $request)
    {
        $list = PurchaseRequisition::select('id', 'pr_no')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('pr_no', 'like', '%' . $request->s . '%');
                }
            })
            ->orderBy('pr_no')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->pr_no
                ];
            });
        return response()->json($list);
    }

    function getCarGroups(Request $request)
    {
        $car_groups = CarGroup::select('car_groups.id', 'car_groups.name as text')
            ->leftJoin('car_categories', 'car_categories.car_group_id', '=', 'car_groups.id')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('car_groups.name', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('car_categories.id', $request->parent_id);
                }
            })
            ->groupBy('car_groups.id', 'car_groups.name')
            ->get();
        return response()->json($car_groups);
    }

    function getZoneType(Request $request)
    {
        $status = collect([
            (object)[
                'id' => ZoneTypeEnum::NEWCAR,
                'text' => __('parking_lots.zone_type_' . ZoneTypeEnum::NEWCAR),
                // 'value' => ZoneTypeEnum::NEWCAR,
            ],
            (object)[
                'id' => ZoneTypeEnum::SHORT,
                'text' => __('parking_lots.zone_type_' . ZoneTypeEnum::SHORT),
                // 'value' => ZoneTypeEnum::SHORT,
            ],
            (object)[
                'id' => ZoneTypeEnum::LONG,
                'text' => __('parking_lots.zone_type_' . ZoneTypeEnum::LONG),
                // 'value' => ZoneTypeEnum::LONG,
            ],
            (object)[
                'id' => ZoneTypeEnum::POOL,
                'text' => __('parking_lots.zone_type_' . ZoneTypeEnum::POOL),
                // 'value' => ZoneTypeEnum::POOL,
            ],
        ]);
        return response()->json($status);
    }

    function getCarClassColor(Request $request)
    {
        $list = CarColor::select('car_colors.id', 'car_colors.name')
            ->leftJoin('car_class_colors', 'car_class_colors.car_color_id', '=', 'car_colors.id')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('car_colors.name', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('car_class_colors.car_class_id', $request->parent_id);
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getProvinces(Request $request)
    {
        $list = Province::select('id', 'name_th')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name_th', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('geography_id', $request->parent_id);
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name_th
                ];
            });
        return response()->json($list);
    }

    function getDistricts(Request $request)
    {
        $list = Amphure::select('id', 'name_th')
            ->where(function ($query) use ($request) {
                $query->where('province_id', $request->parent_id);
                if (!empty($request->s)) {
                    $query->where('name_th', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name_th
                ];
            });
        return response()->json($list);
    }

    function getSubdistricts(Request $request)
    {
        $list = District::select('id', 'name_th', 'zip_code')
            ->where(function ($query) use ($request) {
                $query->where('amphure_id', $request->parent_id);
                if (!empty($request->s)) {
                    $query->where('name_th', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name_th,
                    'zip_code' => $item->zip_code
                ];
            });
        return response()->json($list);
    }

    function getLocationGroups(Request $request)
    {
        $list = LocationGroup::select('id', 'name')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getLocations(Request $request)
    {
        $list = Location::select('locations.id', 'locations.name')
            ->leftJoin('location_groups', 'locations.location_group_id', '=', 'location_groups.id')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('locations.name', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('locations.location_group_id', $request->parent_id);
                }
            })
            ->where('locations.status', STATUS_ACTIVE)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getServiceTypes(Request $request)
    {
        $list = ServiceType::select('id', 'name')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getProducts(Request $request)
    {
        $list = Product::select('id', 'name')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getProductSkus(Request $request)
    {
        $list = Product::select('id', 'name')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getProductAdditionals(Request $request)
    {
        $search = !empty($request->search) ? $request->search : $request->s;
        $list = ProductAdditional::select('id', 'name')
            ->where(function ($query) use ($search) {
                if (!empty($search)) {
                    $query->where('name', 'Like', '%' . $search . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    public function getCarLicensePlateByContract(Request $request)
    {
        $car_list = Contracts::join('contract_lines', 'contract_lines.contract_id', '=', 'contracts.id')
            ->join('cars', 'cars.id', '=', 'contract_lines.car_id')
            ->where('contracts.id', $request->parent_id)
            ->select(['cars.id', 'cars.license_plate as text'])->get();

        return response()->json($car_list);
    }

    function getCarLicensePlate(Request $request)
    {
        $rental_type = null;
        if (strcmp($request->parent_id, "0") == 0) {
            $rental_type = 0;
        } else {
            $rental_type = $request->parent_id;
        }

        if (strcmp($request->parent_type, ImportCarLine::class) == 0) {
            $list = Car::select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
                ->leftJoin('import_car_lines', 'import_car_lines.id', '=', 'cars.id')
                ->where('import_car_lines.import_car_id', $request->parent_id_2)
                ->where(function ($query) use ($request, $rental_type) {
                    if (!empty($request->s)) {
                        $query->where('cars.license_plate', 'like', '%' . $request->s . '%');
                        $query->orWhere('cars.engine_no', 'like', '%' . $request->s . '%');
                        $query->orWhere('cars.chassis_no', 'like', '%' . $request->s . '%');
                    }
                    if (!is_null($rental_type)) {
                        $query->where('cars.rental_type', $rental_type);
                    }
                })
                ->where('cars.branch_id', get_branch_id())
                ->limit(30)
                ->get()->map(function ($item) {
                    if ($item->license_plate) {
                        $text = $item->license_plate;
                    } else if ($item->engine_no) {
                        $text = __('inspection_cars.engine_no') . ' ' . $item->engine_no;
                    } else if ($item->chassis_no) {
                        $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
                    }
                    $item->id = $item->id;
                    $item->text = $text;
                    return $item;
                });
        } else {
            $list = Car::select('id', 'license_plate', 'engine_no', 'chassis_no')
                ->where(function ($query) use ($request, $rental_type) {
                    if (!empty($request->s)) {
                        $query->where('license_plate', 'like', '%' . $request->s . '%');
                        $query->orWhere('engine_no', 'like', '%' . $request->s . '%');
                        $query->orWhere('chassis_no', 'like', '%' . $request->s . '%');
                    }
                    if (!is_null($rental_type)) {
                        $query->where('rental_type', $rental_type);
                    }
                })
                ->where('cars.branch_id', get_branch_id())
                ->limit(30)
                ->get()->map(function ($item) {
                    if ($item->license_plate) {
                        $text = $item->license_plate;
                    } else if ($item->engine_no) {
                        $text = __('inspection_cars.engine_no') . ' ' . $item->engine_no;
                    } else if ($item->chassis_no) {
                        $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
                    }
                    $item->id = $item->id;
                    $item->text = $text;
                    return $item;
                });
        }

        return response()->json($list);
    }

    function getCarLicensePlateShortRental(Request $request)
    {
        $rental_type = null;
        if (strcmp($request->parent_id, "0") == 0) {
            $rental_type = 0;
        } else {
            $rental_type = [RentalTypeEnum::SHORT, RentalTypeEnum::SPARE];
        }

        $list = Car::select('id', 'license_plate', 'engine_no', 'chassis_no')
            ->where(function ($query) use ($request, $rental_type) {
                if (!empty($request->s)) {
                    $query->where('license_plate', 'like', '%' . $request->s . '%');
                }
                if (!is_null($rental_type)) {
                    $query->whereIn('rental_type', $rental_type);
                }
            })
            ->whereNotNull('license_plate')
            ->get()->map(function ($item) {
                if ($item->license_plate) {
                    $text = $item->license_plate;
                }
                $item->id = $item->id;
                $item->text = $text;
                return $item;
            });

        return response()->json($list);
    }

    function getCarEngineNo(Request $request)
    {
        $list = Car::select('id', 'engine_no')
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

    function getCarEngineNoRentalType(Request $request)
    {
        $rental_type = null;
        if (strcmp($request->parent_id, "0") == 0) {
            $rental_type = 0;
        } else {
            $rental_type = $request->parent_id;
        }
        $list = Car::select('id', 'engine_no')
            ->where(function ($query) use ($request, $rental_type) {
                if (!empty($request->s)) {
                    $query->where('engine_no', 'like', '%' . $request->s . '%');
                }
                if (!is_null($rental_type)) {
                    $query->where('rental_type', $rental_type);
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

    function getCarChassisNo(Request $request)
    {
        $list = Car::select('id', 'chassis_no')
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

    function getCarChassisNoRentalType(Request $request)
    {
        $rental_type = null;
        if (strcmp($request->parent_id, "0") == 0) {
            $rental_type = 0;
        } else {
            $rental_type = $request->parent_id;
        }
        $list = Car::select('id', 'chassis_no')
            ->where(function ($query) use ($request, $rental_type) {
                if (!empty($request->s)) {
                    $query->where('chassis_no', 'like', '%' . $request->s . '%');
                }
                if (!is_null($rental_type)) {
                    $query->where('rental_type', $rental_type);
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

    function getCarParkZoneCodeName(Request $request)
    {
        $list = CarParkZone::select('id', 'name', 'code')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('code', 'like', '%' . $request->s . '%');
                    $query->orWhere('name', 'like', '%' . $request->s . '%');
                }
            })
            ->branch()
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->code . ' : ' . $item->name
                ];
            });
        return response()->json($list);
    }

    function getCarParkZone(Request $request)
    {
        $list = Car::select('car_park_zones.id as id', 'car_park_zones.name as name', 'car_park_zones.code as code')
            ->join('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->join('car_types', 'car_classes.car_type_id', '=', 'car_types.id')
            ->Join('car_groups', 'car_groups.id', '=', 'car_types.car_group_id')
            ->Join('car_park_areas_relation', 'car_park_areas_relation.car_group_id', '=', 'car_groups.id')
            ->Join('car_park_areas', 'car_park_areas.id', '=', 'car_park_areas_relation.car_park_area_id')
            ->Join('car_park_zones', 'car_park_zones.id', '=', 'car_park_areas.car_park_zone_id')
            ->where(function ($query) use ($request) {
                if (!empty($request->car_id)) {
                    $query->where('cars.id', $request->car_id);
                }
                if (!empty($request->s)) {
                    $query->where('car_park_zones.name', 'like', '%' . $request->s . '%');
                }
            })
            ->where('car_park_zones.branch_id', get_branch_id())
            ->whereNull('car_park_zones.deleted_at')
            ->get();
        $list = $list->unique('id')->values();
        $list = $list->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->code . ' : ' . $item->name
            ];
        });
        return response()->json($list);
    }

    function getCarParkAreaNumber(Request $request)
    {
        if (empty($request->parent_id)) {
            return response()->json([]);
        }
        $list = CarParkArea::select('id', 'start_number', 'end_number')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('start_number', 'like', '%' . $request->s . '%');
                    $query->orWhere('end_number', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('car_park_zone_id', $request->parent_id);
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->start_number . ' - ' . $item->end_number
                ];
            });
        return response()->json($list);
    }

    function getCarPark(Request $request)
    {
        $list = Car::select('car_parks.id as id', 'car_parks.car_park_number as car_park_number')
            ->join('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->join('car_types', 'car_classes.car_type_id', '=', 'car_types.id')
            ->Join('car_groups', 'car_groups.id', '=', 'car_types.car_group_id')
            ->Join('car_park_areas_relation', 'car_park_areas_relation.car_group_id', '=', 'car_groups.id')
            ->Join('car_park_areas', 'car_park_areas.id', '=', 'car_park_areas_relation.car_park_area_id')
            ->join('car_parks', 'car_park_areas.id', '=', 'car_parks.car_park_area_id')
            ->where(function ($query) use ($request) {
                if (!empty($request->parent_id)) {
                    $query->where('car_park_areas.car_park_zone_id', $request->parent_id);
                }
                if (!empty($request->car_id)) {
                    $query->where('cars.id', $request->car_id);
                }
                if (!empty($request->s)) {
                    $query->where('car_parks.car_park_number', 'like', '%' . $request->s . '%');
                }
            })
            ->whereNull('car_parks.car_id')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->car_park_number
                ];
            });
        return response()->json($list);
    }

    function getCarParkFree(Request $request)
    {
        $list = CarPark::select('car_parks.id as id', 'car_parks.car_park_number')
            ->addSelect('car_park_zones.name as car_park_zone_name', 'car_park_zones.code as car_park_zone_code')
            ->join('car_park_areas', 'car_park_areas.id', '=', 'car_parks.car_park_area_id')
            ->join('car_park_zones', 'car_park_zones.id', '=', 'car_park_areas.car_park_zone_id')
            ->where(function ($query) use ($request) {
                if (!empty($request->parent_id)) {
                    $query->where('car_park_zones.id', $request->parent_id);
                }
                if (!empty($request->s)) {
                    $query->where('car_parks.car_park_number', 'like', '%' . $request->s . '%');
                }
            })
            ->whereNull('car_parks.car_id')
            ->where('car_parks.status', CarParkStatusEnum::FREE)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->car_park_zone_code . ' : ' . $item->car_park_number
                ];
            });
        return response()->json($list);
    }

    function getCarParkCarInParking(Request $request)
    {
        $list = Car::select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
            ->join('car_parks', 'car_parks.car_id', '=', 'cars.id')
            ->whereNotNull('car_parks.car_id')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('cars.license_plate', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => (!empty($item->license_plate) ? $item->license_plate : $item->engine_no)
                ];
            });
        return response()->json($list);
    }

    function getCarParkCarOutsideParking(Request $request)
    {
        $list = Car::select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
            ->leftJoin('car_parks', 'car_parks.car_id', '=', 'cars.id')
            ->whereNull('car_parks.car_id')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('cars.license_plate', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => (!empty($item->license_plate) ? $item->license_plate : $item->engine_no)
                ];
            });
        return response()->json($list);
    }

    function getBranch(Request $request)
    {
        $list = Branch::select('id', 'name')
            ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }


    function getProductByBranch(Request $request)
    {
        if (empty($request->parent_id)) {
            return response()->json([]);
        }
        $pm = new ProductManagement($request->parent_id_2, $request->parent_id);
        $pm->setBranchId($request->parent_id);
        $pm->setTypePackage($request->parent_id_3);
        $pm->setDates($request->pickup_date, $request->return_date);
        $products = $pm->getAvailableProducts($request->s);
        $list = $products->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name

            ];
        });
        return response()->json($list);
    }

    function getOriginByBranch(Request $request)
    {
        if (empty($request->parent_id)) {
            return response()->json([]);
        }
        $list = Location::leftjoin('branches_locations', 'branches_locations.location_id', '=', 'locations.id')
            ->where('branches_locations.can_origin', STATUS_ACTIVE)
            ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('branches_locations.branch_id', $request->parent_id);
                }
            })
            ->select('locations.id', 'locations.name')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getDestinationByBranch(Request $request)
    {
        if (empty($request->parent_id)) {
            return response()->json([]);
        }
        $list = Location::leftjoin('branches_locations', 'branches_locations.location_id', '=', 'locations.id')
            ->where('branches_locations.can_destination', STATUS_ACTIVE)
            ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('branches_locations.branch_id', $request->parent_id);
                }
            })
            ->select('locations.id', 'locations.name')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getDrivingSkill(Request $request)
    {
        $list = DrivingSkill::select('id', 'name')
            ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getDriverWage(Request $request)
    {
        $list = DriverWage::select('id', 'name')
            ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getDriver(Request $request)
    {
        $list = Driver::select('id', 'name')
            ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getProductAdditionalDetail(Request $request)
    {
        $product_additional_id = $request->product_additional_id;
        $product_additional = ProductAdditional::find($product_additional_id);
        return [
            'success' => true,
            'data' => $product_additional
        ];
    }

    function getProductDetail(Request $request)
    {
        $product_id = $request->product_id;
        $product = Product::find($product_id);
        return [
            'success' => true,
            'data' => $product
        ];
    }

    function getCarParkNumber(Request $request)
    {
        $list = CarParkArea::leftjoin('car_parks', 'car_parks.car_park_area_id', '=', 'car_park_areas.id')
            ->select('car_parks.id', 'car_parks.car_park_number as name')
            ->where('car_park_areas.car_park_zone_id', $request->parent_id)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('car_parks.car_park_number', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getPromotion(Request $request)
    {
        $list = Promotion::select('id', 'name')
            ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getDepartments(Request $request)
    {
        $list = Department::select('id', 'name')
            ->where('status', STATUS_ACTIVE)
            ->search($request->s)
            ->orderBy('name')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getSections(Request $request)
    {
        $list = Section::select('id', 'name')
            ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->parent_id)) {
                    $query->where('department_id', $request->parent_id);
                }
            })
            ->search($request->s, $request)
            ->orderBy('name')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getRoles(Request $request)
    {
        $list = Role::select('id', 'name')
            ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                $query->where('department_id', $request->parent_id);
                if (!empty($request->parent_id_2)) {
                    $query->where('section_id', $request->parent_id_2);
                }
            })
            ->search($request->s)
            ->orderBy('name')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getUsers(Request $request)
    {
        $list = User::select('id', 'name')
            ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->parent_id)) {
                    $query->where('department_id', $request->parent_id);
                }
                if (!empty($request->parent_id_2)) {
                    $query->where('section_id', $request->parent_id_2);
                }
            })
            ->search($request->s, $request)
            ->orderBy('name')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getCarLicense(Request $request)
    {
        $list = Car::select('id', 'license_plate', 'engine_no', 'chassis_no')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('license_plate', 'like', '%' . $request->s . '%');
                    $query->orWhere('engine_no', 'like', '%' . $request->s . '%');
                    $query->orWhere('chassis_no', 'like', '%' . $request->s . '%');
                }
            })
            ->where('cars.branch_id', get_branch_id())
            ->get()->map(function ($item) {
                if ($item->license_plate) {
                    $text = $item->license_plate . ' / ' . $item->engine_no . ' / ' . $item->chassis_no;
                } else {
                    $text = $item->engine_no . ' / ' . $item->chassis_no;
                }
                $item->id = $item->id;
                $item->text = $text;
                return $item;
            });

        return response()->json($list);
    }
}
