<?php

namespace App\Http\Controllers\Admin;

use App\Classes\ProductManagement;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\RentalLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Enums\RentalStateEnum;
use App\Models\Product;
use App\Models\ProductAdditionalRelation;
use App\Traits\RentalTrait;
use App\Classes\RentalCarManagement;
use App\Classes\OrderManagement;
use App\Models\ProductAdditional;
use App\Models\Promotion;
use App\Models\PromotionCode;

class ShortTermRentalAssetController extends Controller
{
    use RentalTrait;
    public function edit(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $rental_id =  $request->rental_id;
        $rental = Rental::find($rental_id);

        if (empty($rental)) {
            return redirect()->route('admin.short-term-rentals.index');
        }

        $service_type_id = $rental->service_type_id;
        $product_id = $rental->product_id;

        $rm = new RentalCarManagement($service_type_id);
        $brand_list = $rm->getRentalCarBrands($service_type_id, $product_id);
        $brand_list = $this->formatCarBrandList($brand_list);
        $selected_car_array = RentalTrait::getCarRentalLineArray($rental_id);
        $car_list = $rm->getRentalCars($service_type_id, $product_id);
        $car_list = RentalTrait::formatCarList($car_list);
        $car_list = $this->checkCarSelected($car_list, $selected_car_array);
        $available_car_ids = $rm->getAvailableCars($rental->pickup_date, null, $rental->return_date, null);
        $available_car_ids = array_values($available_car_ids);
        $status_list = RentalTrait::getRentalStatusList();
        $select_multiple = RentalTrait::canAddMutipleCar($rental->serviceType->service_type);
        $page_title = __('lang.edit') . __('short_term_rentals.sheet');

        return view('admin.short-term-rental-asset.form', [
            'd' => $rental,
            'rental_id' => $rental_id,
            'brand_list' => $brand_list,
            'select_multiple' => $select_multiple,
            'car_list' => $car_list,
            'status_list' => $status_list,
            'available_car_ids' => $available_car_ids,
            'page_title' => $page_title,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $rental_id = $request->rental_id;
        $cars = $request->items;
        $validator = Validator::make($request->all(), [
            'rental_id' => 'required',
            'items' => 'required|array|min:1',
        ], [], [
            'items' => __('short_term_rentals.car'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $rental = Rental::find($rental_id);
        if (!$rental) {
            __log('RENTAL NOT FOUND');
            return $this->responseWithCode(false, __('lang.not_found'), null, 422);
        }
        if (!$rental->product_id) {
            __log('RENTAL PRODUCT EMPTY');
            return $this->responseWithCode(false, __('lang.not_found'), null, 422);
        }
        $rental->rental_state = RentalStateEnum::DRIVER;
        $rental->save();

        RentalLine::where('rental_id', $rental_id)->forceDelete();

        if (count($cars) > 0) {
            foreach ($cars as $car_id) {
                // calculate price
                $pm = new ProductManagement($rental->service_type_id, $rental->branch_id);
                $unit_price = $pm->findPrice($rental->product_id, $rental->pickup_date, $rental->return_date, ['unit_price' => true]);
                $order_amount = $pm->getOrderAmount($rental->product_id, $rental->pickup_date, $rental->return_date);

                $rental_line = new RentalLine();
                $rental_line->rental_id = $rental_id;
                $rental_line->item_type = Product::class;
                $rental_line->item_id = $rental->product_id;
                $rental_line->car_id = $car_id;

                $rental_line->amount = $order_amount;
                $rental_line->unit_price = abs(floatval($unit_price));
                $rental_line->pickup_date = $rental->pickup_date;
                $rental_line->return_date = $rental->return_date;
                $rental_line->save();

                // *** add product-additional
                $product_additional_relations = ProductAdditionalRelation::select('*')->where('product_id', $rental->product_id)->get();
                foreach ($product_additional_relations as $relation) {
                    $product_additional = $relation->product_additional;
                    if ($product_additional) {
                        $is_free = boolval($relation->is_free);
                        $optionals = [
                            'car_id' => $car_id,
                            'is_free' => $is_free,
                            'is_from_product' => true,
                            'name' => $product_additional->name
                        ];
                        $unit_price = abs(floatval($product_additional->price));
                        if ($is_free) {
                            $unit_price = 0;
                        }
                        RentalTrait::saveRentalLine(
                            $rental,
                            ProductAdditional::class,
                            $product_additional->id,
                            abs(intval($relation->amount)),
                            $unit_price,
                            $optionals
                        );
                    }
                }
            }
            $om = new OrderManagement($rental);
            $om->calculate();
        }

        $redirect_route = route('admin.short-term-rental.driver.edit', [
            'rental_id' => $rental_id,
        ]);
        return $this->responseValidateSuccess($redirect_route);
    }

    function back(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $rental_id =  $request->rental_id;
        $rental = Rental::find($rental_id);

        if (empty($rental)) {
            return redirect()->route('admin.short-term-rentals.index');
        }

        // clear all rental_lines (cars + product additionals + promotion)
        /* RentalTrait::clearRentalLines($rental_id, Product::class);
        RentalTrait::clearRentalLines($rental_id, ProductAdditional::class);
        RentalTrait::clearRentalLines($rental_id, Promotion::class);
        RentalTrait::clearRentalLines($rental_id, PromotionCode::class);

        $om = new OrderManagement($rental);
        $om->calculate(); */
        return redirect()->route('admin.short-term-rental.asset.edit', ['rental_id' => $rental_id]);
    }

    function formatCarBrandList($brand_list)
    {
        $count_all = 0;
        $brand_list->map(function ($item) use (&$count_all) {
            $count_all += intval($item->car_sum);
            $image = $item->getMedia('car_brand_images');
            $item->image = get_medias_detail($image);
        });
        $object = new \stdClass();
        $object->id = 'all';
        $object->name = 'ดูทุกยี่ห้อ';
        $object->car_sum = $count_all;
        $object->image = [];
        $brand_list->prepend($object);
        return $brand_list;
    }

    public function checkCarSelected($list, $selected_car_array)
    {
        foreach ($list as $item) {
            if (in_array($item->id, $selected_car_array)) {
                __log('d');
                $item->checked = true;
            }
        }
        return $list;
    }
}
