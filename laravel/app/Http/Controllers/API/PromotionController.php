<?php

namespace App\Http\Controllers\API;

use App\Enums\DiscountTypeEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotion;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $list = Promotion::select('id', 'name', 'code', 'promotion_type', 'discount_type', 'discount_mode', 'discount_amount', 'priority')
            ->addSelect('start_date', 'end_date', 'start_travel_date', 'end_travel_date', 'start_sale_date', 'end_sale_date')
            ->search($request->s, $request)
            ->when(!empty($request->promotion_type), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('promotions.promotion_type', 'like', '%' . $request->promotion_type . '%');
                });
            })
            ->where('status', STATUS_ACTIVE)
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read(Request $request)
    {
        $data = Promotion::select('id', 'name', 'code', 'promotion_type', 'discount_type', 'discount_mode', 'discount_amount', 'priority')
            ->addSelect('start_date', 'end_date', 'start_travel_date', 'end_travel_date', 'start_sale_date', 'end_sale_date')
            ->addSelect('is_check_min_total', 'min_total', 'is_check_min_hours', 'min_hours', 'is_check_min_days', 'min_days', 'is_check_min_distance', 'min_distance')
            ->where('promotions.id', $request->id)
            ->where('status', STATUS_ACTIVE)
            ->first();
        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        $data->free = [];
        if (strcmp($data->discount_type, DiscountTypeEnum::FREE_CAR_CLASS) == 0) {
            $free = $data->freeCarClasses->map(function ($item) {
                $car_class = $item->carClass;
                return [
                    'id' => $car_class->id,
                    'name' => $car_class->full_name,
                    'description' => $car_class->description
                ];
            })->toArray();
            $data->free = $free;
        } else if (strcmp($data->discount_type, DiscountTypeEnum::FREE_ADDITIONAL_PRODUCT) == 0) {
            $free = $data->freeProductAdditionals->map(function ($item) {
                $product_additional = $item->product_additional;
                return [
                    'id' => $product_additional->id,
                    'name' => $product_additional->name,
                    'description' => null
                ];
            })->toArray();
            $data->free = $free;
        }

        $incompatibles = $data->incompatibles->map(function ($item) {
            $promotion = $item->promotion;
            return [
                'id' => $promotion->id,
                'name' => $promotion->name,
                'description' => $promotion->code
            ];
        })->toArray();
        $data->incompatibles_list = $incompatibles;

        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }
}
