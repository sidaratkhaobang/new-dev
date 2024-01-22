<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\Actions;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\Resources;
use App\Enums\SellingPriceStatusEnum;
use App\Models\Car;
use App\Models\CarClass;
use App\Models\CarColor;
use App\Models\SellingPrice;
use App\Models\SellingPriceLine;
use App\Traits\CarAuctionTrait;
use Illuminate\Support\Facades\Validator;

class SellingPriceController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::SellingPrice);
        $car_id = $request->car_id;
        $car_class_id = $request->car_class_id;
        $year_id = $request->year_id;
        $worksheet_id = $request->worksheet_id;
        $car_color_id = $request->car_color_id;
        $status_id = $request->status_id;
        $from_registered = $request->from_registered;
        $to_registered = $request->to_registered;
        $list = SellingPriceLine::leftJoin('cars', 'cars.id', '=', 'selling_price_lines.car_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftjoin('car_colors', 'car_colors.id', '=', 'cars.car_color_id')
            ->leftjoin('selling_prices', 'selling_prices.id', '=', 'selling_price_lines.selling_price_id')
            ->select(
                'selling_price_lines.*',
                'cars.license_plate',
                'cars.chassis_no',
                'cars.engine_no',
                'cars.registered_date',
                'car_colors.name as car_color_name',
                'car_classes.full_name as car_class_name',
                'car_classes.manufacturing_year',
                'selling_prices.worksheet_no',
            )
            ->when($car_id, function ($query) use ($car_id) {
                $query->where('selling_price_lines.car_id', $car_id);
            })
            ->when($car_class_id, function ($query) use ($car_class_id) {
                $query->where('cars.car_class_id', $car_class_id);
            })
            ->when($status_id, function ($query) use ($status_id) {
                $query->where('selling_price_lines.status', $status_id);
            })
            ->when($worksheet_id, function ($query) use ($worksheet_id) {
                $query->where('selling_prices.id', $worksheet_id);
            })
            ->when($year_id, function ($query) use ($year_id) {
                $query->where('car_classes.id', $year_id);
            })
            ->when($car_color_id, function ($query) use ($car_color_id) {
                $query->where('cars.car_color_id', $car_color_id);
            })
            ->when($from_registered, function ($query) use ($from_registered) {
                $query->whereDate('cars.registered_date', '>=', $from_registered);
            })
            ->when($to_registered, function ($query) use ($to_registered) {
                $query->whereDate('cars.registered_date', '<=', $to_registered);
            })
            ->sortable('worksheet_no')
            ->paginate(PER_PAGE);

        $status_list =  CarAuctionTrait::getStatus();

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

        $worksheet_no = null;
        if ($worksheet_id) {
            $selling_price = SellingPrice::find($worksheet_id);
            $worksheet_no = $selling_price?->worksheet_no ?? null;
        }

        $year = null;
        if ($year_id) {
            $car_class = CarClass::find($year_id);
            $year = $car_class?->manufacturing_year ?? null;
        }

        $car_color_name = null;
        if ($car_color_id) {
            $car_color = CarColor::find($car_color_id);
            $car_color_name = $car_color?->name ?? null;
        }

        $page_title =  __('selling_prices.page_title');
        return view('admin.selling-prices.index', [
            'list' => $list,
            'page_title' => $page_title,
            'status_list' => $status_list,
            'car_id' => $car_id,
            'car_class_id' => $car_class_id,
            'status_id' => $status_id,
            'worksheet_id' => $worksheet_id,
            'year_id' => $year_id,
            'car_color_id' => $car_color_id,
            'from_registered' => $from_registered,
            'to_registered' => $to_registered,
            'car_name' => $car_name,
            'car_class_name' => $car_class_name,
            'worksheet_no' => $worksheet_no,
            'year' => $year,
            'car_color_name' => $car_color_name,
        ]);
    }

    public function edit(SellingPriceLine $selling_price)
    {
        $this->authorize(Actions::Manage . '_' . Resources::SellingPrice);
        $car = CarAuctionTrait::getCarInfo($selling_price->car_id);
        $car_accessory = CarAuctionTrait::getCarAccessorie($selling_price->car_id);
        $page_title =  __('lang.edit') . __('selling_prices.page_title');
        return view('admin.selling-prices.form', [
            'd' => $selling_price,
            'page_title' => $page_title,
            'car' => $car,
            'car_accessory' => $car_accessory,
        ]);
    }

    public function show(SellingPriceLine $selling_price)
    {
        $this->authorize(Actions::View . '_' . Resources::SellingPrice);
        $car = CarAuctionTrait::getCarInfo($selling_price->car_id);
        $car_accessory = CarAuctionTrait::getCarAccessorie($selling_price->car_id);
        $page_title =  __('lang.view') . __('selling_prices.page_title');
        return view('admin.selling-prices.form', [
            'd' => $selling_price,
            'page_title' => $page_title,
            'car' => $car,
            'car_accessory' => $car_accessory,
            'view' => true,
        ]);
    }

    public function store(Request $request)
    {
        if ($request->price <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณากรอกราคาอนุมัติขาย',
            ], 422);
        }

        $selling_price_line = SellingPriceLine::firstOrNew(['id' => $request->id]);
        $price = str_replace(',', '', $request->price);
        $selling_price_line->price = $price;
        $vat = str_replace(',', '', $request->vat);
        $selling_price_line->vat = $vat;
        $total = str_replace(',', '', $request->total);
        $selling_price_line->total = $total;
        $selling_price_line->status = SellingPriceStatusEnum::REQUEST_APPROVE;
        $selling_price_line->save();

        $redirect_route = route('admin.selling-prices.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function saveSalePrice(Request $request)
    {
        if (empty($request->price)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณากรอกราคาอนุมัติขาย',
            ], 422);
        }
        if (empty($request->arr_sale_price)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกรถที่ต้องการทำราคาขายล่วงหน้า',
            ], 422);
        }
        if ($request->arr_sale_price > 0) {
            foreach ($request->arr_sale_price as $item) {
                $selling_price_line = SellingPriceLine::find($item['id']);
                if ($selling_price_line) {
                    $price = str_replace(',', '', $request->price);
                    $selling_price_line->price = $price;
                    $vat = str_replace(',', '', $request->vat);
                    $selling_price_line->vat = $vat;
                    $total = str_replace(',', '', $request->total);
                    $selling_price_line->total = $total;
                    $selling_price_line->status = SellingPriceStatusEnum::REQUEST_APPROVE;
                    $selling_price_line->save();
                }
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'ok',
            'redirect' => route('admin.selling-prices.index'),
        ]);
    }

    public function saveSalePriceNew(Request $request)
    {
        if (empty($request->price)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณากรอกราคาอนุมัติขาย',
            ], 422);
        }
        $selling_price_line = SellingPriceLine::find($request->id);
        if ($selling_price_line) {
            $price = str_replace(',', '', $request->price);
            $selling_price_line->price = $price;
            $vat = str_replace(',', '', $request->vat);
            $selling_price_line->vat = $vat;
            $total = str_replace(',', '', $request->total);
            $selling_price_line->total = $total;
            $selling_price_line->status = SellingPriceStatusEnum::PENDING_REVIEW;
            $selling_price_line->save();

            $selling_price = SellingPrice::find($selling_price_line->selling_price_id);
            $selling_price->status = SellingPriceStatusEnum::PENDING_REVIEW;
            $selling_price->save();

            $approve_clear_status = new StepApproveManagement();
            $approve_return = $approve_clear_status->clearStatus(SellingPrice::class, $selling_price->id);
        }


        return response()->json([
            'success' => true,
            'message' => 'ok',
            'redirect' => route('admin.selling-prices.index'),
        ]);
    }

    public function saveRequestApprove(Request $request)
    {
        if (empty($request->arr_request_approve)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกรถที่ต้องการส่งขออนุมัติ',
            ], 422);
        }
        if ($request->arr_request_approve > 0) {
            $selling_price_count = SellingPrice::all()->count() + 1;
            $prefix = 'SP';
            $selling_price = new SellingPrice();
            $selling_price->worksheet_no = generateRecordNumber($prefix, $selling_price_count);
            $selling_price->status = SellingPriceStatusEnum::PENDING_REVIEW;
            $selling_price->save();
            foreach ($request->arr_request_approve as $item) {
                $selling_price_line = SellingPriceLine::find($item['id']);
                if ($selling_price_line) {
                    $selling_price_line->selling_price_id = $selling_price->id;
                    $selling_price_line->status = SellingPriceStatusEnum::PENDING_REVIEW;
                    $selling_price_line->save();
                }
            }

            $step_approve_management = new StepApproveManagement();
            $step_approve_management->createModelApproval(ConfigApproveTypeEnum::SELLING_PRICE, SellingPrice::class, $selling_price->id);
        }
        return response()->json([
            'success' => true,
            'message' => 'ok',
            'redirect' => route('admin.selling-prices.index'),
        ]);
    }
}
