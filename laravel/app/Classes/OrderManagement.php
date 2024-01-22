<?php

namespace App\Classes;

use App\Enums\CalculateTypeEnum;
use App\Enums\DiscountModeEnum;
use App\Enums\DiscountTypeEnum;
use App\Enums\OrderLineTypeEnum;
use App\Models\Car;
use App\Models\Product;
use App\Models\ProductAdditional;
use App\Models\Promotion;
use App\Models\PromotionCode;
use App\Models\PromotionEffectiveCarClass;
use App\Models\PromotionFreeCarClass;
use App\Models\ProductAdditionalRelation;
use App\Models\PromotionFreeProductAdditional;
use App\Models\RentalBill;
use App\Models\Rental;
use App\Models\RentalLine;
use App\Traits\RentalTrait;
use DateTime;
use App\Traits\DayTrait;

class OrderManagement
{
    use DayTrait;

    public $rental;
    public $subtotal;
    public $discount;
    public $vat;
    public $total;
    public $is_withholding_tax;
    public $withholding_tax;
    public $withholding_tax_value;
    public $coupon_discount;
    public $subtotal_with_discount;
    public $subtotal_with_vat;
    public $promotion_name_list;
    public $coupon_name_list;

    use RentalTrait;
    public function __construct(Rental $rental, RentalBill $rental_bill = null)
    {
        $this->rental = $rental;
        $this->subtotal = 0;
        $this->discount = 0;
        $this->vat = 0;
        $this->total = 0;
        $this->is_withholding_tax = boolval($rental->is_withholding_tax);
        $this->withholding_tax_value = intval($rental->withholding_tax_value);
        $this->withholding_tax = 0;
        $this->subtotal_with_discount = 0;
        $this->subtotal_with_vat = 0;
        $this->promotion_name_list = [];
        $this->coupon_name_list = [];
    }

    function calculate($autosave = true)
    {
        // clear old value
        $this->subtotal = 0;
        $this->discount = 0;
        $this->vat = 0;
        $this->total = 0;
        $this->subtotal_with_discount = 0;

        RentalLine::where('rental_id', $this->rental->id)->update(['discount' => 0]);

        $rental_lines = $this->rental->rentalLines;
        $rental_line_products = $rental_lines->filter(function ($line) {
            return (strcmp($line->item_type, Product::class) == 0);
        });

        $rental_line_promotions = $rental_lines->filter(function ($line) {
            return (strcmp($line->item_type, Promotion::class) == 0);
        });

        $rental_line_promotion_codes = $rental_lines->filter(function ($line) {
            return (strcmp($line->item_type, PromotionCode::class) == 0);
        });

        $rental_line_extras = $rental_lines->filter(function ($line) {
            return (strcmp($line->item_type, OrderLineTypeEnum::EXTRA) == 0);
        });

        foreach ($rental_line_products as $key => $line) {
            $line->subtotal = price_format(abs(intval($line->amount)) * abs(floatval($line->unit_price)));
            if ($autosave) {
                $line->save();
            }
        }



        // save promotion discount in product lines
        foreach ($rental_line_promotions as $key => $rental_line_promotion) {
            $rental_line_promotion_discount_total = 0;
            foreach ($rental_line_products as $key => $rental_line_product) {
                $line_promotion_discount = $this->findPromotionDiscount($rental_line_promotion->item_id, $rental_line_product->subtotal, $rental_line_product);
                $rental_line_product->discount += $line_promotion_discount;
                $rental_line_product->save();
                $rental_line_promotion_discount_total += $line_promotion_discount;
            }
            $rental_line_promotion->unit_price = 0;
            $rental_line_promotion->discount = $rental_line_promotion_discount_total;
            $rental_line_promotion->save();

            // save name for display
            if (($rental_line_promotion->item) && ($rental_line_promotion->item->name)) {
                $this->promotion_name_list[] = $rental_line_promotion->item->name;
            }
        }

        // save promotion code discount in product lines
        foreach ($rental_line_promotion_codes as $key => $rental_line_promotion_code) {
            $promotion_code = PromotionCode::find($rental_line_promotion_code->item_id);
            if (!$promotion_code) {
                continue;
            }
            $rental_line_promotion_code_discount_total = 0;
            foreach ($rental_line_products as $key => $rental_line_product) {
                $line_promotion_code_discount = $this->findPromotionDiscount($promotion_code->promotion_id, $rental_line_product->subtotal, $rental_line_product);
                $rental_line_product->discount += $line_promotion_code_discount;
                $rental_line_product->save();
                $rental_line_promotion_code_discount_total += $line_promotion_code_discount;
            }
            $rental_line_promotion_code->unit_price = 0;
            $rental_line_promotion_code->discount = $rental_line_promotion_code_discount_total;
            $rental_line_promotion_code->save();

            // save name for display
            if (($rental_line_promotion_code->item) && ($rental_line_promotion_code->item->promotion)) {
                $coupon = $rental_line_promotion_code->item;
                $promotion = $rental_line_promotion_code->item->promotion;
                $name = $coupon->code . ' (' . $promotion->name . ')';
                $this->coupon_name_list[] = $name;
            }
        }

        // load new product_additionals
        $rental_line_product_additionals = RentalLine::where('rental_id', $this->rental->id)->where('item_type', ProductAdditional::class)->get();
        foreach ($rental_line_product_additionals as $key => $line_product_additional) {
            $line_product_additional->subtotal = price_format(abs(intval($line_product_additional->amount)) * abs(floatval($line_product_additional->unit_price)));
            if (boolval($line_product_additional->is_free)) {
                $line_product_additional->subtotal = 0;
            }
            $line_product_additional->save();
        }

        foreach ($rental_line_extras as $key => $rental_line_extra) {
            $rental_line_extra->subtotal = price_format(abs(intval($rental_line_extra->amount)) * abs(floatval($rental_line_extra->unit_price)));
            $rental_line_extra->save();
        }

        $rental_lines = RentalLine::where('rental_id', $this->rental->id)->get();
        foreach ($rental_lines as $key => $line) {
            $subtotal_with_discount = price_format($line->subtotal - $line->discount);
            $subtotal_with_discount = ($subtotal_with_discount < 0 ? 0 : $subtotal_with_discount);
            $line->vat = price_format(cal_vat($subtotal_with_discount));
            $line->total = price_format($subtotal_with_discount + $line->vat);
            $line->save();

            if (!in_array($line->item_type, [Promotion::class, PromotionCode::class])) {
                $this->subtotal += $line->subtotal;
                $this->discount += $line->discount;
                $this->vat += $line->vat;
                $this->total += $line->total;
            }
        }


        $this->subtotal_with_discount = $this->subtotal - $this->discount;
        $this->subtotal_with_vat = $this->subtotal_with_discount + $this->vat;

        // cal withholding tax
        $this->withholding_tax = 0;
        if ($this->is_withholding_tax) {
            $this->withholding_tax = ($this->withholding_tax_value / 100) * $this->subtotal;
        }
        $this->total = $this->total - $this->withholding_tax;

        $this->subtotal = (($this->subtotal >= 0) ? $this->subtotal : 0);
        $this->total = (($this->total >= 0) ? $this->total : 0);

        $this->rental->subtotal = $this->subtotal;
        $this->rental->discount = $this->discount;
        $this->rental->vat = $this->vat;
        $this->rental->withholding_tax = $this->withholding_tax;
        $this->rental->total = $this->total;
        if ($autosave) {
            $this->rental->save();
        }
    }

    function findPromotionDiscount($promotion_id, $subtotal, $line = null)
    {
        if (empty($promotion_id)) {
            return 0;
        }
        $promotion = Promotion::find($promotion_id);
        if (empty($promotion)) {
            return 0;
        }

        /* if (strcmp($promotion->discount_mode, DiscountModeEnum::TRANSACTION) === 0) {
            $effective_car_class_arr = PromotionEffectiveCarClass::where('promotion_id', $promotion_id)->pluck('car_class_id')->toArray();
            if (sizeof($effective_car_class_arr) > 0 && $line && $line->car_id) {
                if (!in_array($line->car->car_class_id, $effective_car_class_arr)) {
                    return 0;
                }
            }
        } */

        // find start - end date promotion
        $promotion_start_date = $promotion->start_date;
        $promotion_end_date = $promotion->end_date;

        $pickup_date = $line->pickup_date;
        $return_date = $line->return_date;

        // get calculate_type
        $calculate_type = CalculateTypeEnum::FIXED;
        $product = $line->item;
        if (is_a($product, Product::class)) {
            $calculate_type = $product->calculate_type;
        }

        // get object date for calculate
        $effective_amount_promotion = 0;
        $start_time = strtotime($pickup_date);
        $end_time = strtotime($return_date);
        if ((!empty($promotion_start_date)) && (!empty($promotion_end_date))) {
            $start_time = strtotime($pickup_date) > strtotime($promotion_start_date) ? strtotime($pickup_date) : strtotime($promotion_start_date);
            $end_time = strtotime($promotion_end_date) > strtotime($return_date) ? strtotime($return_date) : strtotime($promotion_end_date);
        } else if (!empty($promotion_start_date)) {
            $start_time = strtotime($pickup_date) > strtotime($promotion_start_date) ? strtotime($pickup_date) : strtotime($promotion_start_date);
        } else if (!empty($promotion_end_date)) {
            $end_time = strtotime($promotion_end_date) > strtotime($return_date) ? strtotime($return_date) : strtotime($promotion_end_date);
        }
        // get effective_amount_promotion
        if (strcmp($calculate_type, CalculateTypeEnum::DAILY) == 0) {
            $effective_amount_promotion = $this->getDaysDiff(date('Y-m-d', $start_time), date('Y-m-d', $end_time));
        } else if (strcmp($calculate_type, CalculateTypeEnum::HOURLY) == 0) {
            $effective_amount_promotion = $this->getHoursDiff(date('Y-m-d', $start_time), date('Y-m-d', $end_time));
        }

        $discount = 0;
        $discount_type = $promotion->discount_type;
        $discount_amount = floatval($promotion->discount_amount);
        if (strcmp($discount_type, DiscountTypeEnum::PERCENT) == 0) {
            $effective_subtotal = price_format(abs(intval($effective_amount_promotion)) * abs(floatval($line->unit_price)));
            $discount = (($effective_subtotal * $discount_amount) / 100);
        } else if (strcmp($discount_type, DiscountTypeEnum::AMOUNT) == 0) {
            $discount = $discount_amount;
        } else if (strcmp($discount_type, DiscountTypeEnum::FIXED_PRICE) == 0) {
            $target_price = $discount_amount;
            $discount = $subtotal - $target_price;
        } else if (strcmp($discount_type, DiscountTypeEnum::FREE_CAR_CLASS) == 0) {
            $free_car_class_arr = PromotionFreeCarClass::where('promotion_id', $promotion_id)->pluck('car_class_id')->toArray();
            if (sizeof($free_car_class_arr) > 0) {
                if ((strcmp($line->item_type, Product::class) == 0) && (!empty($line->car))) { // product = $line->item
                    $car_class_id = $line->car->car_class_id;
                    if (in_array($car_class_id, $free_car_class_arr)) {
                        if ($discount_amount <= 0) {
                            $discount = $subtotal;
                        } else {
                            $discount = $discount_amount * floatval($line->unit_price);
                        }
                    }
                }
            }
        }

        if ($discount < 0) {
            $discount = 0;
        }
        if ($discount > $subtotal) {
            $discount = $subtotal;
        }
        return $discount;
    }

    function getSummary()
    {
        return [
            'subtotal' => price_format($this->subtotal),
            'discount' => price_format($this->discount),
            'coupon_discount' => price_format($this->coupon_discount),
            'subtotal_with_discount' => price_format($this->subtotal_with_discount),
            'subtotal_with_vat' => price_format($this->subtotal_with_vat),
            'vat' => price_format($this->vat),
            'withholding_tax' => price_format($this->withholding_tax),
            'total' => price_format($this->total),

            'subtotal_text' => price_format($this->subtotal, true),
            'discount_text' => price_format($this->discount, true),
            'coupon_discount_text' => price_format($this->coupon_discount, true),
            'subtotal_with_discount_text' => price_format($this->subtotal_with_discount, true),
            'subtotal_with_vat_text' => price_format($this->subtotal_with_vat, true),
            'vat_text' => price_format($this->vat, true),
            'withholding_tax_text' => price_format($this->withholding_tax, true),
            'total_text' => price_format($this->total, true),

            'promotion_name_list' => $this->promotion_name_list,
            'coupon_name_list' => $this->coupon_name_list,
        ];
    }
}
