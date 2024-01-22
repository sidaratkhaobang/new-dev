<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\CarClass;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\ProductPriceCarClass;
use App\Models\ProductPriceCustomerGroup;
use App\Models\ProductPriceDestination;
use App\Models\ProductPriceOrigin;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ProductPriceController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Product);
        $product_id = $request->product_id;
        $product = Product::find($product_id);
        if (empty($product)) {
            return redirect()->route('admin.products.index');
        }
        $list = ProductPrice::leftjoin('products', 'products.id', '=', 'product_prices.product_id')
            ->where('product_prices.product_id', $product_id)
            ->select('product_prices.*')
            ->sortable('name')
            ->paginate(PER_PAGE);

        return view('admin.product-prices.index', [
            'list' => $list,
            'product_id' => $product_id,
            'product' => $product
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Product);
        $product_id = $request->product_id;
        $d = new ProductPrice();
        $status_list = getStatusList();
        $yes_no_list = getYesNoList();
        $car_class_list = CarClass::select('id', 'name')
            ->orderBy('name')
            ->get();
        $location_list = Location::select('id', 'name')
            ->orderBy('name')
            ->get();
        $customer_group_list = CustomerGroup::select('id', 'name')
            ->orderBy('name')
            ->get();
        $days = getDayCollection();
        $page_title = __('lang.create') . __('product_prices.page_title');
        return view('admin.product-prices.form', [
            'd' => $d,
            'product_id' => $product_id,
            'page_title' => $page_title,
            'yes_no_list' => $yes_no_list,
            'status_list' => $status_list,
            'car_class_list' => $car_class_list,
            'location_list' => $location_list,
            'customer_group_list' => $customer_group_list,
            'days' => $days,
            'booking_day_arr' => [],
            'car_class_ids' => [],
            'origin_ids' => [],
            'destination_ids' => [],
            'customer_group_ids' => [],
        ]);
    }

    public function edit(ProductPrice $product_price, Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Product);
        $product_id = $request->product_id;
        $status_list = getStatusList();
        $yes_no_list = getYesNoList();
        $car_class_list = CarClass::select('id', 'name')
            ->orderBy('name')
            ->get();
        $location_list = Location::select('id', 'name')
            ->orderBy('name')
            ->get();
        $customer_group_list = CustomerGroup::select('id', 'name')
            ->orderBy('name')
            ->get();

        $days = getDayCollection();
        $booking_day_arr = [];
        foreach ($days as $day) {
            $booking_day =  'booking_day_' . $day['value'];
            if ($product_price->$booking_day == STATUS_ACTIVE) {
                array_push($booking_day_arr, $day['value']);
            }
        }
        $car_class_ids = ProductPriceCarClass::where('product_price_id', $product_price->id)
            ->pluck('car_class_id')->toArray();
        $origin_ids = ProductPriceOrigin::where('product_price_id', $product_price->id)
            ->pluck('origin_id')->toArray();
        $destination_ids = ProductPriceDestination::where('product_price_id', $product_price->id)
            ->pluck('destination_id')->toArray();
        $customer_group_ids = ProductPriceCustomerGroup::where('product_price_id', $product_price->id)
            ->pluck('customer_group_id')->toArray();
        $days = getDayCollection();
        $page_title = __('lang.edit') . __('product_prices.page_title');
        return view('admin.product-prices.form', [
            'd' => $product_price,
            'product_id' => $product_id,
            'page_title' => $page_title,
            'yes_no_list' => $yes_no_list,
            'status_list' => $status_list,
            'car_class_list' => $car_class_list,
            'location_list' => $location_list,
            'customer_group_list' => $customer_group_list,
            'days' => $days,
            'booking_day_arr' => $booking_day_arr,
            'car_class_ids' => $car_class_ids,
            'origin_ids' => $origin_ids,
            'destination_ids' => $destination_ids,
            'customer_group_ids' => $customer_group_ids,
        ]);
    }

    public function show(ProductPrice $product_price)
    {
        $this->authorize(Actions::View . '_' . Resources::Product);
        $product_id = $product_price->product_id;
        $status_list = getStatusList();
        $yes_no_list = getYesNoList();
        $car_class_list = CarClass::select('id', 'name')
            ->orderBy('name')
            ->get();
        $location_list = Location::select('id', 'name')
            ->orderBy('name')
            ->get();
        $customer_group_list = CustomerGroup::select('id', 'name')
            ->orderBy('name')
            ->get();

        $days = getDayCollection();
        $booking_day_arr = [];
        foreach ($days as $day) {
            $booking_day =  'booking_day_' . $day['value'];
            if ($product_price->$booking_day == STATUS_ACTIVE) {
                array_push($booking_day_arr, $day['value']);
            }
        }
        $car_class_ids = ProductPriceCarClass::where('product_price_id', $product_price->id)
            ->pluck('car_class_id')->toArray();
        $origin_ids = ProductPriceOrigin::where('product_price_id', $product_price->id)
            ->pluck('origin_id')->toArray();
        $destination_ids = ProductPriceDestination::where('product_price_id', $product_price->id)
            ->pluck('destination_id')->toArray();
        $customer_group_ids = ProductPriceCustomerGroup::where('product_price_id', $product_price->id)
            ->pluck('customer_group_id')->toArray();
        $days = getDayCollection();
        $page_title = __('lang.view') . __('product_prices.page_title');
        return view('admin.product-prices.form', [
            'd' => $product_price,
            'product_id' => $product_id,
            'page_title' => $page_title,
            'yes_no_list' => $yes_no_list,
            'status_list' => $status_list,
            'car_class_list' => $car_class_list,
            'location_list' => $location_list,
            'customer_group_list' => $customer_group_list,
            'days' => $days,
            'booking_day_arr' => $booking_day_arr,
            'car_class_ids' => $car_class_ids,
            'origin_ids' => $origin_ids,
            'destination_ids' => $destination_ids,
            'customer_group_ids' => $customer_group_ids,
            'view' => true,

        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Product);
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('product_prices', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'price' => ['required', 'numeric'],
            'priority' => ['required'],
            'is_product_additional_free' => ['required'],
            'status' => ['required'],
        ], [], [
            'name' => __('product_prices.name'),
            'price' => __('product_prices.price'),
            'priority' => __('product_prices.priority'),
            'is_product_additional_free' => __('product_prices.is_product_additional_free'),
            'status' => __('lang.status'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $product_price = ProductPrice::firstOrNew(['id' => $request->id]);
        $product_price->product_id = $request->product_id;
        $product_price->name = $request->name;
        $product_price->price = $request->price;
        $product_price->priority = $request->priority;
        $product_price->is_product_additional_free = $request->is_product_additional_free;
        if ($request->reserve_date) {
            foreach ($request->reserve_date as $key => $value) {
                $booking_day =  'booking_day_' . $value;
                $product_price->$booking_day = STATUS_ACTIVE;
            }
        }
        $product_price->start_date = $request->start_date;
        $product_price->end_date = $request->end_date;
        $product_price->status = $request->status;
        $product_price->save();


        if ($product_price->id) {
            $this->saveProductPriceCarclass($request, $product_price->id);
            $this->saveProductPriceCustomerGroup($request, $product_price->id);
            $this->saveProductPriceOrigin($request, $product_price->id);
            $this->saveProductPriceDestination($request, $product_price->id);
        }

        $redirect_route = route('admin.product-prices.index', ['product_id' => $request->product_id]);
        return $this->responseValidateSuccess($redirect_route);
    }

    private function saveProductPriceCarclass($request, $product_price_id)
    {
        ProductPriceCarClass::where('product_price_id', $product_price_id)->delete();
        if (!empty($request->car_class_ids)) {
            foreach ($request->car_class_ids as $key => $item) {
                $product_price_car_class = new ProductPriceCarClass();
                $product_price_car_class->product_price_id = $product_price_id;
                $product_price_car_class->car_class_id = $item;
                $product_price_car_class->save();
            }
        }
        return true;
    }

    private function saveProductPriceCustomerGroup($request, $product_price_id)
    {
        ProductPriceCustomerGroup::where('product_price_id', $product_price_id)->delete();
        if (!empty($request->customer_group_ids)) {
            foreach ($request->customer_group_ids as $key => $item) {
                $product_price_customer_group = new ProductPriceCustomerGroup();
                $product_price_customer_group->product_price_id = $product_price_id;
                $product_price_customer_group->customer_group_id = $item;
                $product_price_customer_group->save();
            }
        }
        return true;
    }

    private function saveProductPriceOrigin($request, $product_price_id)
    {
        ProductPriceOrigin::where('product_price_id', $product_price_id)->delete();
        if (!empty($request->origin_ids)) {
            foreach ($request->origin_ids as $key => $item) {
                $product_price_origin = new ProductPriceOrigin();
                $product_price_origin->product_price_id = $product_price_id;
                $product_price_origin->origin_id = $item;
                $product_price_origin->save();
            }
        }
        return true;
    }

    private function saveProductPriceDestination($request, $product_price_id)
    {
        ProductPriceDestination::where('product_price_id', $product_price_id)->delete();
        if (!empty($request->destination_ids)) {
            foreach ($request->destination_ids as $key => $item) {
                $product_price_destination = new ProductPriceDestination();
                $product_price_destination->product_price_id = $product_price_id;
                $product_price_destination->destination_id = $item;
                $product_price_destination->save();
            }
        }
        return true;
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Product);
        $product_price = ProductPrice::find($id);
        $product_price->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
