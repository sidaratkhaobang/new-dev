<?php

namespace App\Http\Controllers\Admin\Util;

use App\Enums\ComparisonPriceStatusEnum;
use App\Enums\ImportCarStatusEnum;
use App\Enums\LongTermRentalStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\POStatusEnum;
use App\Enums\RentalStatusEnum;
use App\Enums\RentalTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\InspectionFlow;
use App\Models\LongTermRental;
use App\Models\LongTermRentalLine;
use App\Models\LongTermRentalMonth;
use App\Models\PurchaseOrder;
use App\Models\Rental;
use App\Traits\InspectionTrait;
use App\Traits\LongTermRentalTrait;
use App\Traits\RentalTrait;
use Illuminate\Http\Request;
use App\Models\Bom;
use App\Enums\LongTermRentalTypeEnum;
use App\Enums\PaymentGatewayEnum;
use App\Models\CarClass;
use App\Traits\SelectOptionTrait;

class Select2RentalController extends Controller
{
    use InspectionTrait, RentalTrait, LongTermRentalTrait, SelectOptionTrait;

    public function getLongTermRentals(Request $request)
    {
        $lt_rentals = LongTermRental::select('id', 'worksheet_no')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('worksheet_no', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->comparison_price_status_list)) {
                    $query->whereIn('comparison_price_status', $request->comparison_price_status_list);
                }
                if (!empty($request->status_list)) {
                    $query->whereIn('status', $request->status_list);
                }
            })
            ->orderBy('worksheet_no')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->worksheet_no
                ];
            });
        return $lt_rentals;
    }

    public function getWorkSheetByInspectionFlow(Request $request)
    {
        $result = [];
        $inspection_flow_id = $request->parent_id;
        $inspection = InspectionFlow::find($inspection_flow_id);
        if ($inspection) {
            $inspection_enum = ($inspection && $inspection->inspection_type) ? $inspection->inspection_type : null;
            $model_class = InspectionTrait::getModelClassByInspectionType($inspection_enum);

            if ($model_class == 'PURCHASE_ORDER') {
                $result = PurchaseOrder::leftJoin('import_cars', 'import_cars.po_id', '=', 'purchase_orders.id')
                    ->where('purchase_orders.status', POStatusEnum::CONFIRM)
                    ->where('import_cars.status', ImportCarStatusEnum::SENT_REVIEW)
                    ->select('purchase_orders.id', 'purchase_orders.po_no as text')
                    ->orderBy('text')
                    ->get();
            }

            if ($model_class === 'SHORT_TERM_RENTAL') {
                $result = Rental::leftjoin('service_types', 'service_types.id', '=', 'rentals.service_type_id')
                    ->where('service_types.service_type', $inspection_enum)
                    ->select('rentals.id', 'rentals.worksheet_no as text')
                    ->orderBy('text')
                    ->get();
            }
            if ($model_class == 'LONG_TERM_RENTAL') {
                $result = LongTermRental::select('id', 'worksheet_no as text')
                    ->orderBy('text')
                    ->get();
            }
        }

        return response()->json($result);
    }

    public function getLongTermRentalsAllComparePriceStatus(Request $request)
    {
        $request->status_list = [
            LongTermRentalStatusEnum::COMPARISON_PRICE,
            LongTermRentalStatusEnum::QUOTATION,
        ];
        $list  = $this->getLongTermRentals($request);
        return response()->json($list);
    }

    public function getLongTermRentalsForComparePriceApprove(Request $request)
    {
        $request->status_list = [
            LongTermRentalStatusEnum::COMPARISON_PRICE,
            LongTermRentalStatusEnum::QUOTATION,
        ];

        $request->comparison_price_status_list = [
            ComparisonPriceStatusEnum::PENDING_REVIEW,
            ComparisonPriceStatusEnum::CONFIRM,
            ComparisonPriceStatusEnum::REJECT,
        ];

        $list  = $this->getLongTermRentals($request);
        return response()->json($list);
    }

    public function getLongTermRentalByBom(Request $request)
    {
        $boms = Bom::select('id', 'worksheet_no')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->orWhere('worksheet_no', 'like', '%' . $request->s . '%');
                    $query->orWhere('name', 'like', '%' . $request->s . '%');
                }
            })
            ->where('type', LongTermRentalTypeEnum::CAR)
            ->orderBy('worksheet_no')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->worksheet_no
                ];
            });
        return $boms;
    }

    public function getCarClassLongTermRentalLines(Request $request)
    {
        $rental_lines = LongTermRentalLine::leftjoin('car_classes', 'car_classes.id', '=', 'lt_rental_lines.car_class_id')
            ->leftjoin('car_colors', 'car_colors.id', '=', 'lt_rental_lines.car_color_id')
            ->select(
                'lt_rental_lines.id',
                'car_classes.full_name as full_name',
                'car_classes.name as name',
                'car_colors.name as color_name',
            )
            ->where('lt_rental_lines.lt_rental_id', $request->long_term_rental_id)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('car_classes.full_name', 'like', '%' . $request->s . '%');
                    $query->orWhere('car_classes.name', 'like', '%' . $request->s . '%');
                    $query->orWhere('car_colors.name', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->full_name . ' - ' . $item->color_name
                ];
            });
        return response()->json($rental_lines);
    }

    public function getLongTermRentalMonths(Request $request)
    {
        $months = LongTermRentalMonth::where('lt_rental_id', $request->long_term_rental_id)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('month', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->month
                ];
            });
        return response()->json($months);
    }

    public function getLongTermRentalLineCarAmount(Request $request)
    {
        $lt_rental_line = LongTermRentalLine::find($request->id);
        return response()->json($lt_rental_line->amount);
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
            // ->limit(30)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->full_name . ' - ' . $item->name
                ];
            });
        return response()->json($car_class);
    }
}
