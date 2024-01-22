<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\Actions;
use App\Enums\CarEnum;
use App\Enums\Resources;
use App\Enums\SellingPriceStatusEnum;
use App\Models\AuctionPlace;
use App\Models\Car;
use App\Models\CarAuction;
use App\Models\CarClass;
use App\Models\SellingPrice;
use App\Traits\CarAuctionTrait;
use Illuminate\Support\Facades\Validator;

class SellingCarController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::SellingCar);
        $car_id = $request->car_id;
        $car_class_id = $request->car_class_id;
        $list = CarAuction::leftjoin('cars', 'cars.id', '=', 'car_auctions.car_id')
            ->leftjoin('selling_price_lines', 'selling_price_lines.car_id', '=', 'cars.id')
            ->leftjoin('leasings', 'leasings.id', '=', 'cars.leasing_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->where('selling_price_lines.status', SellingPriceStatusEnum::CONFIRM)
            ->where('leasings.is_true_leasing', STATUS_DEFAULT)
            ->select(
                'car_auctions.*',
                'car_classes.full_name as car_class_name',
                'cars.license_plate',
                'cars.chassis_no',
                'cars.engine_no',
                'cars.current_mileage',
            )
            ->when($car_id, function ($query) use ($car_id) {
                $query->where('car_auctions.car_id', $car_id);
            })
            ->when($car_class_id, function ($query) use ($car_class_id) {
                $query->where('cars.car_class_id', $car_class_id);
            })
            ->sortable('license_plate')
            ->paginate(PER_PAGE);

        $car_name = null;
        if ($car_id) {
            $car = Car::find($car_id);
            if ($car && $car->license_plate) {
                $car_name = $car?->license_plate ?? null;
            } else if ($car && $car->engine_no) {
                $car_name = __('inspection_cars.engine_no') . ' ' . $car?->engine_no ?? null;
            } else if ($car && $car->chassis_no) {
                $car_name = __('inspection_cars.chassis_no') . ' ' . $car?->chassis_no ?? null;
            }
        }

        $car_class_name = null;
        if ($car_class_id) {
            $car_class = CarClass::find($car_class_id);
            $car_class_name = $car_class?->full_name ?? null;
        }
        $page_title =  __('selling_prices.page_title');
        return view('admin.selling-cars.index', [
            'list' => $list,
            'page_title' => $page_title,
            'car_id' => $car_id,
            'car_class_id' => $car_class_id,
            'car_name' => $car_name,
            'car_class_name' => $car_class_name,
        ]);
    }

    public function edit(CarAuction $selling_car)
    {
        $this->authorize(Actions::Manage . '_' . Resources::SellingCar);
        $car = CarAuctionTrait::getCarInfo($selling_car->car_id);
        $car_accessory = CarAuctionTrait::getCarAccessorie($selling_car->car_id);
        $transfer_status_name = __('selling_prices.status_' . SellingPriceStatusEnum::PENDING_TRANSFER);
        $page_title =  __('lang.edit') . __('selling_prices.page_title');
        return view('admin.selling-cars.form', [
            'd' => $selling_car,
            'page_title' => $page_title,
            'car' => $car,
            'car_accessory' => $car_accessory,
            'transfer_status_name' => $transfer_status_name,
        ]);
    }

    public function show(CarAuction $selling_car)
    {
        $this->authorize(Actions::View . '_' . Resources::SellingCar);
        $car = CarAuctionTrait::getCarInfo($selling_car->car_id);
        $car_accessory = CarAuctionTrait::getCarAccessorie($selling_car->car_id);
        $transfer_status_name = __('selling_prices.status_' . SellingPriceStatusEnum::PENDING_TRANSFER);
        $page_title =  __('lang.view') . __('selling_prices.page_title');
        return view('admin.selling-cars.form', [
            'd' => $selling_car,
            'page_title' => $page_title,
            'car' => $car,
            'car_accessory' => $car_accessory,
            'transfer_status_name' => $transfer_status_name,
            'view' => true,
        ]);
    }

    public function store(Request $request)
    {
        $car_auction = CarAuction::firstOrNew(['id' => $request->id]);
        if (strcmp($request->status_sale, SellingPriceStatusEnum::PENDING_SALE) == 0) {
            $car_auction->status = SellingPriceStatusEnum::PENDING_FINANCE;
            $car_auction->request_finance_date = date('Y-m-d');
            $car_auction->expected_finance_date = date('Y-m-d');
        }
        if (strcmp($request->status, SellingPriceStatusEnum::PENDING_FINANCE) == 0) {
            $car_auction->status = SellingPriceStatusEnum::PENDING_TRANSFER;
            $car_auction->expected_transfer_ownership_date = date('Y-m-d');
            $car_auction->transfer_ownership_date = date('Y-m-d');
        }
        $car_auction->save();

        if (strcmp($request->status, SellingPriceStatusEnum::PENDING_TRANSFER) == 0) {
            $car = Car::find($car_auction->car_id);
            if ($car) {
                $car->leasing_id = $request->ownership;
                $car->save();
            }
            $car_auction->status = SellingPriceStatusEnum::PENDING_SALE;
            $car_auction->save();
        }


        $redirect_route = route('admin.selling-cars.index');
        return $this->responseValidateSuccess($redirect_route);
    }
}
