<?php

namespace App\Http\Controllers\Admin\Util;

use App\Enums\RentalStatusEnum;
use App\Enums\SellingPriceStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\AuctionPlace;
use App\Models\Car;
use App\Models\CarClass;
use App\Models\CarColor;
use App\Models\SellingPrice;
use App\Models\SellingPriceLine;
use App\Models\Customer;
use App\Models\Leasing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Select2CarAuctionController extends Controller
{
    function getSalePriceLicensePlates(Request $request)
    {
        $data = Car::select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
            ->join('selling_price_lines', 'selling_price_lines.car_id', '=', 'cars.id')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('cars.license_plate', 'like', '%' . $request->s . '%');
                    $query->orWhere('cars.engine_no', 'like', '%' . $request->s . '%');
                    $query->orWhere('cars.chassis_no', 'like', '%' . $request->s . '%');
                }
            })
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

        return response()->json($data);
    }

    function getSalePriceCarClass(Request $request)
    {
        $data = CarClass::select('car_classes.id', 'car_classes.full_name')
            ->join('cars', 'cars.car_class_id', '=', 'car_classes.id')
            ->join('selling_price_lines', 'selling_price_lines.car_id', '=', 'cars.id')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('car_classes.full_name', 'like', '%' . $request->s . '%');
                }
            })
            ->distinct('car_classes.full_name')
            ->get()->map(function ($item) {
                $item->id = $item->id;
                $item->text = $item->full_name;
                return $item;
            });

        return response()->json($data);
    }

    function getSalePriceWorksheets(Request $request)
    {
        $data = SellingPrice::select('id', 'worksheet_no')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('worksheet_no', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                $item->id = $item->id;
                $item->text = $item->worksheet_no;
                return $item;
            });

        return response()->json($data);
    }

    function getSalePriceCarClassYear(Request $request)
    {
        $data = CarClass::select('car_classes.id', 'car_classes.manufacturing_year')
            ->join('cars', 'cars.car_class_id', '=', 'car_classes.id')
            ->join('selling_price_lines', 'selling_price_lines.car_id', '=', 'cars.id')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('car_classes.manufacturing_year', 'like', '%' . $request->s . '%');
                }
            })
            ->distinct('car_classes.manufacturing_year')
            ->get()->map(function ($item) {
                $item->id = $item->id;
                $item->text = $item->manufacturing_year;
                return $item;
            });

        return response()->json($data);
    }

    function getSalePriceCarColor(Request $request)
    {
        $data = CarColor::select('car_colors.id', 'car_colors.name')
            ->join('cars', 'cars.car_color_id', '=', 'car_colors.id')
            ->join('selling_price_lines', 'selling_price_lines.car_id', '=', 'cars.id')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->orWhere('car_colors.name', 'like', '%' . $request->s . '%');
                }
            })
            ->distinct('car_colors.name')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($data);
    }

    public function getStatusList()
    {
        return collect([
            [
                'id' => SellingPriceStatusEnum::PENDING_TRANSFER,
                'value' => SellingPriceStatusEnum::PENDING_TRANSFER,
                'text' => __('selling_prices.status_' . SellingPriceStatusEnum::PENDING_TRANSFER),
            ],
        ]);
    }

    function getOwnerShipList(Request $request)
    {
        $data = Leasing::select('id', 'name')
            ->where('is_true_leasing', STATUS_ACTIVE)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($data);
    }

    function getSaleCarLicensePlates(Request $request)
    {
        $data = Car::select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
            ->join('car_auctions', 'car_auctions.car_id', '=', 'cars.id')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('cars.license_plate', 'like', '%' . $request->s . '%');
                    $query->orWhere('cars.engine_no', 'like', '%' . $request->s . '%');
                    $query->orWhere('cars.chassis_no', 'like', '%' . $request->s . '%');
                }
            })
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

        return response()->json($data);
    }

    function getSaleCarCarClass(Request $request)
    {
        $data = CarClass::select('car_classes.id', 'car_classes.full_name')
            ->join('cars', 'cars.car_class_id', '=', 'car_classes.id')
            ->join('car_auctions', 'car_auctions.car_id', '=', 'cars.id')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('car_classes.full_name', 'like', '%' . $request->s . '%');
                }
            })
            ->distinct('car_classes.full_name')
            ->get()->map(function ($item) {
                $item->id = $item->id;
                $item->text = $item->full_name;
                return $item;
            });

        return response()->json($data);
    }

    function getAuctionPlace(Request $request)
    {
        $data = AuctionPlace::select('id', 'name')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->whereNotIn('id', [$request->parent_id]);
                }
            })
            ->where('status', STATUS_ACTIVE)
            ->get()->map(function ($item) {
                $item->id = $item->id;
                $item->text = $item->name;
                return $item;
            });

        return response()->json($data);
    }
}
