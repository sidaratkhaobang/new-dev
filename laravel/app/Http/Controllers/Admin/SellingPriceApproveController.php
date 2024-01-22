<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\Actions;
use App\Enums\CarAuctionStatusEnum;
use App\Enums\CarEnum;
use App\Enums\Resources;
use App\Enums\SellingPriceStatusEnum;
use App\Models\AuctionPlace;
use App\Models\Car;
use App\Models\CarAuction;
use App\Models\CarClass;
use App\Models\SellingPrice;
use App\Models\SellingPriceLine;
use App\Traits\CarAuctionTrait;
use App\Traits\HistoryTrait;
use Illuminate\Support\Facades\Validator;

class SellingPriceApproveController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::SellingPriceApprove);
        $car_id = $request->car_id;
        $car_class_id = $request->car_class_id;
        $worksheet_id = $request->worksheet_id;
        $list = SellingPrice::select(
            'selling_prices.*',
        )
            ->where('status', SellingPriceStatusEnum::PENDING_REVIEW)
            // ->sortable('name')
            ->when($worksheet_id, function ($query) use ($worksheet_id) {
                $query->where('selling_prices.id', $worksheet_id);
            })
            ->paginate(PER_PAGE);
        $list->map(function ($item) use ($car_id, $car_class_id) {
            $sub_query = SellingPriceLine::where('selling_price_lines.selling_price_id', $item->id)
                ->leftJoin('cars', 'cars.id', '=', 'selling_price_lines.car_id')
                ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
                ->where('selling_price_lines.status', SellingPriceStatusEnum::PENDING_REVIEW)
                ->select(
                    'selling_price_lines.id as selling_price_line_id',
                    'cars.id as car_id',
                    'cars.license_plate',
                    'cars.chassis_no',
                    'cars.engine_no',
                    'cars.current_mileage',
                    'car_classes.full_name as car_class_name',
                    'car_classes.manufacturing_year',
                    'selling_price_lines.price as selling_price_line_price',
                    'selling_price_lines.vat as selling_price_line_vat',
                    'selling_price_lines.total as selling_price_line_total',
                    'selling_price_lines.status as selling_price_line_status',
                )
                ->when($car_id, function ($query) use ($car_id) {
                    $query->where('selling_price_lines.car_id', $car_id);
                })
                ->when($car_class_id, function ($query) use ($car_class_id) {
                    $query->where('cars.car_class_id', $car_class_id);
                })
                ->get();
            $item->child_list = $sub_query;
            $item->amount_car = count($sub_query);
            return $item;
        });

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

        $page_title =  __('selling_prices.approve_title');
        return view('admin.selling-price-approves.index', [
            'list' => $list,
            'page_title' => $page_title,
            'car_id' => $car_id,
            'car_class_id' => $car_class_id,
            'worksheet_id' => $worksheet_id,
            'car_name' => $car_name,
            'car_class_name' => $car_class_name,
            'worksheet_no' => $worksheet_no,
        ]);
    }

    public function show(SellingPrice $selling_price_approve)
    {
        $this->authorize(Actions::View . '_' . Resources::SellingPriceApprove);
        $approve_line = HistoryTrait::getHistory(SellingPrice::class, $selling_price_approve->id);
        $approve_line_list = $approve_line['approve_line_list'];
        $approve = $approve_line['approve'];
        $approve_line_logs = $approve_line['approve_line_logs'];
        if (!is_null($approve_line_list)) {
            // can approve or super user
            $approve_line_owner = new StepApproveManagement();
            // $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
            $approve_line_owner = $approve_line_owner->checkCanApprove(SellingPrice::class, $selling_price_approve->id);
        } else {
            $approve_line_owner = null;
        }

        $selling_price_line = SellingPriceLine::where('selling_price_lines.selling_price_id', $selling_price_approve->id)
            ->leftJoin('cars', 'cars.id', '=', 'selling_price_lines.car_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftjoin('leasings', 'leasings.id', '=', 'cars.leasing_id')
            ->where('selling_price_lines.status', SellingPriceStatusEnum::PENDING_REVIEW)
            ->select(
                'selling_price_lines.id as selling_price_line_id',
                'cars.id as car_id',
                'cars.license_plate',
                'cars.chassis_no',
                'cars.engine_no',
                'cars.current_mileage',
                'car_classes.full_name as car_class_name',
                'car_classes.manufacturing_year',
                'leasings.name as ownership',
                'selling_price_lines.price as selling_price_line_price',
                'selling_price_lines.vat as selling_price_line_vat',
                'selling_price_lines.total as selling_price_line_total',
            )
            ->paginate(PER_PAGE);

        $page_title =  __('lang.view') . __('selling_prices.approve_title');
        return view('admin.selling-price-approves.form', [
            'd' => $selling_price_approve,
            'page_title' => $page_title,
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
            'approve_line_owner' => $approve_line_owner,
            'approve_line_logs' => $approve_line_logs,
            'selling_price_line' => $selling_price_line,
            'view' => true,
        ]);
    }

    public function store(Request $request)
    {
        $selling_price = SellingPrice::find($request->id);
        if (!$selling_price) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }

        $validator = Validator::make($request->all(), [
            'status_update' => 'required',
            'reject_reason' => 'required_if:status_update,REJECT'
        ], [
            'required_if' => 'กรุณากรอก :attribute'
        ], [
            'status_update' => __('lang.status'),
            'reject_reason' => 'เหตุผลการไม่อนุมัติ',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        // // update approve step
        $approve_update = new StepApproveManagement();
        // $approve_update = $approve_update->updateApprove($request, $selling_price, $request->status_update, SellingPrice::class);
        $approve_update = $approve_update->updateApprove(SellingPrice::class, $selling_price->id, $request->status_update,null,null);

        $selling_price->status = $approve_update;
        $selling_price->save();

        $selling_price_lines = SellingPriceLine::where('selling_price_id', $selling_price->id)->get();
        foreach ($selling_price_lines as $item) {
            $selling_price_line = SellingPriceLine::find($item->id);
            if ($selling_price_line) {
                $selling_price_line->status = $selling_price->status;
                $selling_price_line->save();

                if (strcmp($selling_price_line->status, SellingPriceStatusEnum::CONFIRM) == 0) {
                    $car_exist = Car::where('id', $selling_price_line->car_id)->where('status', CarEnum::PENDING_SALE)->exists();
                    if ($car_exist) {
                        $car_auction = CarAuction::where('car_id', $selling_price_line->car_id)->first();
                        if (is_null($car_auction)) {
                            $car_auction = new CarAuction();
                        }
                        $car_auction->car_id = $selling_price_line->car_id;
                        $car_auction->status = SellingPriceStatusEnum::PENDING_SALE;
                        $car_auction->save();
                    }
                }
            }
        }

        $redirect_route = route('admin.selling-price-approves.index');
        return $this->responseValidateSuccess($redirect_route);
    }
}
