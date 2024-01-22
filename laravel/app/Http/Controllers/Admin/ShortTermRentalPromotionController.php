<?php

namespace App\Http\Controllers\Admin;

use App\Classes\OrderManagement;
use App\Classes\PromotionManagement;
use App\Enums\Actions;
use App\Enums\DiscountTypeEnum;
use App\Enums\PromotionTypeEnum;
use App\Enums\RentalStateEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductAdditional;
use App\Models\Promotion;
use App\Models\PromotionCode;
use App\Models\PromotionFreeProductAdditional;
use App\Models\Rental;
use App\Models\RentalBill;
use App\Models\RentalLine;
use App\Traits\RentalTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\PromotionTrait;

class ShortTermRentalPromotionController extends Controller
{
    use RentalTrait, PromotionTrait;

    public function edit(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);

        $rental = Rental::find($request->rental_id);
        if (empty($rental)) {
            return redirect()->route('admin.short-term-rentals.index');
        }

        $d = null;
        $voucher_list = null;
        $summary = null;
        $checked_vouchers = null;
        $customer_type = null;
        $checked_promotion = null;
        $d = new RentalLine();
        $customer = Customer::find($rental->customer_id);
        $customer_type = $customer->customer_type;

        $voucher_list = $this->getAvaliablePromotionCodesByUser($rental->customer_id);
        $checked_vouchers = []; //RentalTrait::getSelectedVoucher($rental_bill->id);
        $checked_promotion = []; //RentalTrait::getSelectedPromotion($rental_bill->id)->first();

        $promotion_id_selected = $rental->promotion_id;

        $page_title = __('lang.edit') . __('short_term_rentals.sheet');

        return view('admin.short-term-rental-promotion.form', [
            'd' => $d,
            'rental_id' => $rental->id,
            'voucher_list' => $voucher_list,
            'rental' => $rental,
            'summary' => $summary,
            'checked_vouchers' => $checked_vouchers,
            'customer_type' => $customer_type,
            'checked_promotions' => $checked_promotion,
            'page_title' => $page_title,
        ]);
    }

    function getAvaliablePromotionCodesByUser($customer_id)
    {
        $promotion_code_list = PromotionCode::leftjoin('promotions', 'promotions.id', '=', 'promotion_codes.promotion_id')
            ->leftJoin('rental_lines', 'rental_lines.item_id', '=', 'promotion_codes.id')
            ->where('promotion_codes.customer_id', $customer_id)
            ->where('promotion_codes.is_used', STATUS_DEFAULT)
            ->where('promotions.promotion_type', PromotionTypeEnum::VOUCHER)
            ->whereNull('promotion_codes.use_date')
            ->select('promotion_codes.id', 'promotion_codes.code as voucher_code', 'promotions.name as promotion_name', 'rental_lines.item_id as selected_id')
            ->orderBy('promotions.name')
            ->orderBy('promotion_codes.code')
            ->get();
        return $promotion_code_list;
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $validator = Validator::make($request->all(), [
            'rental_id' => 'required',
            'promotion_id' => 'nullable',
            'voucher_id' => 'nullable',
            'withholding_tax' => 'required_if:active_tax,=,1',
        ], [], [
            'rental_id' => __('short_term_rentals.rental_id'),
            'active_tax' => __('short_term_rentals.active_tax'),
            'withholding_tax' => __('short_term_rentals.withholding_tax_value'),

        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $rental_id = $request->rental_id;
        $promotion_id = $request->promotion_id;
        $vouchers = $request->vouchers;
        $promotion_code_ids = $request->promotion_code_ids;
        $car_data = RentalTrait::getCarDataFromRentalLine($rental_id);
        $rental = Rental::find($rental_id);
        if (!$rental) {
            return $this->responseWithCode(false, __('lang.not_found'), null, 422);
        }

        // TODO CLEAR PREVIOUS PROMOTOIN, PROMOTION CODE HERE
        RentalTrait::clearRentalLines($rental_id, Promotion::class);
        RentalTrait::clearRentalLines($rental_id, PromotionCode::class);
        RentalTrait::clearRentalLines($rental_id, ProductAdditional::class, ['is_from_promotion' => true]);
        RentalTrait::clearRentalLines($rental_id, ProductAdditional::class, ['is_from_coupon' => true]);
        $rental->promotion_id = null;
        if (!empty($promotion_id)) {
            $promotion = PromotionTrait::findAvailablePromotionsByRental($promotion_id, $rental);
            if (!$promotion) {
                __log('PROMOTION NOT FOUND');
                return $this->responseWithCode(false, __('lang.not_found') . __('promotions.promotion'), null, 422);
            }
            RentalTrait::saveRentalLine($rental, Promotion::class, $promotion->id, 1, 0, []);

            $free_products = RentalTrait::getProductAdditionalsFromPromotion($promotion);
            foreach ($car_data as $key_car => $car_id) {
                foreach ($free_products as $key => $free_product) {
                    $optionals['is_free'] = true;
                    $optionals['is_from_promotion'] = true;
                    $optionals['car_id'] = $car_id;
                    RentalTrait::saveRentalLine($rental, ProductAdditional::class, $free_product->id, 1, 0, $optionals);
                }
            }


            $rental->promotion_id = $promotion_id;
        }

        if (!empty($promotion_code_ids) && is_array($promotion_code_ids)) {
            $customer_id = $rental->customer_id;
            foreach ($promotion_code_ids as $promotion_code_id) {
                // TODO VALIDATE PROMOTION CODE FIRST
                $promotion_code = PromotionCode::find($promotion_code_id);
                if ($promotion_code) {
                    $valid = PromotionTrait::validatePromotionVoucherCode($promotion_code, $customer_id);
                    if (!$valid) {
                        continue;
                    }
                    RentalTrait::saveRentalLine($rental, PromotionCode::class, $promotion_code_id, 1, 0, []);

                    $promotion = $promotion_code->promotion;
                    if ($promotion) {
                        $free_products = RentalTrait::getProductAdditionalsFromPromotion($promotion);
                        foreach ($car_data as $key_car => $car_id) {
                            foreach ($free_products as $key => $free_product) {
                                $optionals['is_free'] = true;
                                $optionals['is_from_coupon'] = true;
                                $optionals['car_id'] = $car_id;
                                RentalTrait::saveRentalLine($rental, ProductAdditional::class, $free_product->id, 1, 0, $optionals);
                            }
                        }
                    }
                }
            }
        }
        $rental->rental_state = RentalStateEnum::SUMMARY;
        $rental->save();

        $om = new OrderManagement($rental);
        $om->calculate();

        $redirect_route = route('admin.short-term-rental.summary.edit', [
            'rental_id' => $rental->id,
        ]);
        return $this->responseValidateSuccess($redirect_route);
    }

    function getPromotionData(Request $request)
    {
        $rental_id = $request->rental_id;
        $s = $request->s;
        $rental = Rental::find($rental_id);
        if (empty($rental)) {
            return [
                'success' => false,
                'html' => null
            ];
        }
        $promotions = PromotionTrait::getAvailablePromotionsByRental($rental, $s);
        $promotions_html = view('admin.short-term-rental-promotion.components.promotion-carousel-items', [
            'promotions' => $promotions
        ])->render();
        return [
            'success' => true,
            'html' => $promotions_html
        ];
    }

    function getPromotionCoupon(Request $request)
    {
        $rental_id = $request->rental_id;
        $s = $request->s;
        $coupon_code = $request->coupon_code;
        $rental = Rental::find($rental_id);
        if (empty($rental)) {
            return [
                'success' => false,
                'html' => null,
                'message' => 'rental_not_found'
            ];
        }

        $customer_id = $rental->customer_id;
        $valid = PromotionTrait::validatePromotionCouponCode($coupon_code, $customer_id);
        if (!$valid) {
            return [
                'success' => false,
                'html' => null,
                'message' => 'ไม่สามารถใช้งานโค้ดโปรโมชัน / คูปองนี้ได้'
            ];
        }

        $promotions = PromotionTrait::getAvailablePromotionsWithCouponsByRental($coupon_code, $rental, $s);
        $promotions_html = view('admin.short-term-rental-promotion.components.promotion-carousel-items', [
            'promotions' => $promotions
        ])->render();
        return [
            'success' => true,
            'html' => $promotions_html
        ];
    }

    function getPromotionVoucher(Request $request)
    {
        $rental_id = $request->rental_id;
        $s = $request->s;
        $voucher_code = $request->voucher_code;
        $rental = Rental::find($rental_id);
        if (empty($rental)) {
            return [
                'success' => false,
                'html' => null,
                'message' => 'rental_not_found'
            ];
        }

        $customer_id = $rental->customer_id;
        $valid = PromotionTrait::validatePromotionVoucherCode($voucher_code, $customer_id);
        if (!$valid) {
            return [
                'success' => false,
                'html' => null,
                'message' => 'ไม่สามารถใช้งานเลข Voucher นี้ได้'
            ];
        }

        $voucher = Promotion::select('promotions.id as promotion_id', 'promotions.name as promotion_name', 'promotion_codes.id', 'promotion_codes.code as voucher_code')
            ->join('promotion_codes', 'promotion_codes.promotion_id', '=', 'promotions.id')
            ->where('promotion_codes.code', $voucher_code)
            ->first();
        if (!$voucher) {
            return [
                'success' => false,
                'html' => null,
                'message' => 'ไม่สามารถใช้งานเลข Voucher นี้ได้ (2)'
            ];
        }
        $voucher_html = view('admin.short-term-rental-promotion.components.voucher-item', [
            'voucher_code' => $voucher->voucher_code,
            'promotion_name' => $voucher->promotion_name,
            'id' => $voucher->id,
            'selected_id' => $voucher->selected_id
        ])->render();
        return [
            'success' => true,
            'html' => $voucher_html
        ];
    }

    function saveFreeCarClassPromotion($rental_bill, $vouchers)
    {
        $om = new OrderManagement($rental_bill);
        $om->setPromotion($rental_bill->promotion_code, $vouchers);
        $om->findFreeCarClasses();
        $free_car_classes_arr = $om->getFreeCarClasses();
        $rental_cars = RentalTrait::getRentalLineCars($rental_bill->rental_id);
        foreach ($rental_cars as $key => $rental_car_line) {
            $car = Car::find($rental_car_line->car_id);
            if ($car && $car->car_class_id) {
                if (in_array($car->car_class_id, $free_car_classes_arr)) {
                    $rental_car_line->is_free = STATUS_ACTIVE;
                    $rental_car_line->save();
                }
            }
        }
    }
}
