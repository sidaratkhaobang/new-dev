<?php

namespace App\Http\Controllers\Admin;

use App\Classes\ProductManagement;
use App\Enums\Actions;
use App\Enums\CalculateTypeEnum;
use App\Enums\RentalStateEnum;
use App\Enums\RentalStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Amphure;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\CustomerBillingAddress;
use App\Models\District;
use App\Models\Location;
use App\Models\Product;
use App\Models\Province;
use App\Models\Rental;
use App\Traits\CustomerTrait;
use App\Traits\RentalTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ShortTermRentalInfoController extends Controller
{
    use CustomerTrait, RentalTrait;

    public function edit(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $rental_id = $request->rental_id;
        $d = Rental::find($rental_id);
        if (empty($d)) {
            return redirect()->route('admin.short-term-rentals.index');
        }

        $order_channel_list = $this->getOrderChannelList();

        // branch
        $branch_name = null;
        if (empty($d->branch_id)) {
            $d->branch_id = Auth::user()?->branch_id;
            $branch_name = find_name_by_id($d->branch_id, Branch::class);
        }
        $branch_list = RentalTrait::getBranchOfServiceType($d->service_type_id);

        // product
        $product_name = find_name_by_id($d->product_id, Product::class);

        // customer
        $customer_name = null;
        if ($d->customer_id) {
            $customer = Customer::find($d->customer_id);
            $customer_name = ($customer) ? $customer->customer_code . ' - ' . $customer->name : null;
            $d->customer_type = ($customer) ? $customer->customer_type : null;
        }
        $customer_province_name = find_name_by_id($d->customer_province_id, Province::class, 'name_th');
        $customer_district_name = find_name_by_id($d->customer_district_id, Amphure::class, 'name_th');
        $customer_subdistrict_name = find_name_by_id($d->customer_subdistrict_id, District::class, 'name_th');
        $customer_zipcode = find_name_by_id($d->customer_subdistrict_id, District::class, 'zip_code');

        // check_customer_address
        $check_customer_address = (boolval($d->check_customer_address));
        $is_required_tax_invoice = boolval($d->is_required_tax_invoice);

        $customer_billing_province_name = find_name_by_id($d->customer_billing_province_id, Province::class, 'name_th');
        $customer_billing_district_name = find_name_by_id($d->customer_billing_district_id, Amphure::class, 'name_th');
        $customer_billing_subdistrict_name = find_name_by_id($d->customer_billing_subdistrict_id, District::class, 'name_th');
        $customer_billing_zipcode = find_name_by_id($d->customer_billing_subdistrict_id, District::class, 'zip_code');

        $customer_type_list = CustomerTrait::getCustomerType();
        $province_list = Province::select('id', 'name_th as name')->get();
        $branch_office_list = RentalTrait::getBranchOfficeList();

        // location
        $origin_name = find_name_by_id($d->origin_id, Location::class);
        $destination_name = find_name_by_id($d->destination_id, Location::class);
        if ($d->origin_name) {
            $d->origin_id = ADDITIONAL;
            $origin_name = $d->origin_name;
        }
        if ($d->destination_name) {
            $d->destination_id = ADDITIONAL;
            $destination_name = $d->destination_name;
        }

        $page_title = __('lang.edit') . __('short_term_rentals.sheet');
        return view('admin.short-term-rental-info.form', [
            'd' => $d,
            'branch_name' => $branch_name,
            'product_name' => $product_name,
            'origin_name' => $origin_name,
            'destination_name' => $destination_name,
            'customer_name' => $customer_name,
            'rental_id' => $d->id,
            'service_type_id' => $d->service_type_id,
            'type_package' => $d->type_package,
            'customer_type_list' => $customer_type_list,
            'province_list' => $province_list,
            'customer_district_name' => $customer_district_name,
            'customer_subdistrict_name' => $customer_subdistrict_name,
            'customer_zipcode' => $customer_zipcode,
            'customer_billing_province_name' => $customer_billing_province_name,
            'customer_billing_district_name' => $customer_billing_district_name,
            'customer_billing_subdistrict_name' => $customer_billing_subdistrict_name,
            'customer_billing_zipcode' => $customer_billing_zipcode,
            'branch_office_list' => $branch_office_list,
            'branch_list' => $branch_list,
            'check_customer_address' => $check_customer_address,
            'is_required_tax_invoice' => $is_required_tax_invoice,
            'order_channel_list' => $order_channel_list,
            'page_title' => $page_title,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $today = date("Y-m-d H:i");
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required',
            'product_id' => 'required',
            'pickup_date' => 'required|date_format:Y-m-d H:i|after:' . $today,
            'return_date' => 'required|date_format:Y-m-d H:i|after_or_equal:pickup_date',
            'origin_id' => 'required',
            'destination_id' => 'required',
            'customer_id' => 'required',
            'customer_tel' => 'nullable|max:20',
            'customer_name' => 'required',
            'customer_billing_address_id' => ['required_if:is_customer_address,=,0'],
        ], [
            'required_if' => 'กรุณาเลือกข้อมูลลูกค้า เมื่อไม่ได้เลือก:other'
        ], [
            'branch_id' => __('short_term_rentals.branch'),
            'product_id' => __('short_term_rentals.package'),
            'origin_id' => __('short_term_rentals.origin'),
            'destination_id' => __('short_term_rentals.destination'),
            'pickup_date' => __('short_term_rentals.pickup_date'),
            'return_date' => __('short_term_rentals.return_date'),
            'customer_id' => __('short_term_rentals.customer_id'),
            'customer_tel' => __('short_term_rentals.tel'),
            'customer_name' => __('short_term_rentals.customer_name'),
            'is_customer_address' => __('short_term_rentals.is_customer_address'),
            'customer_billing_address_id' => __('short_term_rentals.tax_invoice_detail'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        //dd($request->all());

        $pm = new ProductManagement($request->service_type_id);
        $pm->setDates($request->pickup_date, $request->return_date);
        $validated = $pm->validate($request->product_id);
        if (!$validated) {
            return response()->json([
                'success' => false,
                'message' => $pm->error_message
            ], 422);
        }
        $rental = Rental::findorFail($request->rental_id);
        $rental->branch_id = $request->branch_id;
        $rental->product_id = $request->product_id;
        $rental->pickup_date = $request->pickup_date;
        $rental->return_date = $request->return_date;
        if (strcmp($request->origin_id, ADDITIONAL) === 0) {
            $rental->origin_id = null;
            $rental->origin_lat = $request->origin_lat;
            $rental->origin_lng = $request->origin_lng;
            $rental->origin_name = $request->origin_name;
            $rental->origin_address = $request->origin_address;
        } else {
            $rental->origin_id = $request->origin_id;
            $original = Location::find($request->origin_id);
            $rental->origin_lat = $original?->lat;
            $rental->origin_lng = $original?->lng;
        }

        if (strcmp($request->destination_id, ADDITIONAL) === 0) {
            $rental->destination_id = null;
            $rental->destination_lat = $request->destination_lat;
            $rental->destination_lng = $request->destination_lng;
            $rental->destination_name = $request->destination_name;
            $rental->destination_address = $request->destination_address;
        } else {
            $rental->destination_id = $request->destination_id;
            $destination = Location::find($request->origin_id);
            $rental->destination_lat = $destination?->lat;
            $rental->destination_lng = $destination?->lng;
        }

        $rental->avg_distance = $request->avg_distance;
        $rental->origin_remark = $request->origin_remark;
        $rental->destination_remark = $request->destination_remark;

        // save customer
        $rental->customer_id = $request->customer_id;
        $rental->customer_name = $request->customer_name;
        $rental->customer_address = $request->customer_address;
        $rental->customer_tel = $request->customer_tel;
        $rental->customer_email = $request->customer_email;
        $rental->customer_tax_no = $request->customer_tax_no;
        $rental->customer_province_id = $request->customer_province_id;
        $rental->customer_district_id = $request->customer_district_id;
        $rental->customer_subdistrict_id = $request->customer_subdistrict_id;

        $check_customer_address = boolval($request->check_customer_address);
        $rental->check_customer_address = $check_customer_address;

        // customer billing address
        $is_required_tax_invoice = boolval($request->is_required_tax_invoice);
        $rental->is_required_tax_invoice = $is_required_tax_invoice;

        if ($is_required_tax_invoice) {
            if ($check_customer_address) {
                $rental->customer_billing_name = $rental->customer_name;
                $rental->customer_billing_address = $rental->customer_address;
                $rental->customer_billing_tel = $rental->customer_tel;
                $rental->customer_billing_email = $rental->customer_email;
                $rental->customer_billing_tax_no = $rental->customer_tax_no;
                $rental->customer_billing_province_id = $rental->customer_province_id;
                $rental->customer_billing_district_id = $rental->customer_district_id;
                $rental->customer_billing_subdistrict_id = $rental->customer_subdistrict_id;
            } else {
                $customer_billing_address = CustomerBillingAddress::find($request->customer_billing_address_id);
                if ($customer_billing_address) {
                    $rental->customer_billing_address_id = $request->customer_billing_address_id;
                    $rental->customer_billing_name = $customer_billing_address->name;
                    $rental->customer_billing_address = $request->customer_billing_address;
                    $rental->customer_billing_tel = $request->customer_billing_tel;
                    $rental->customer_billing_email = $request->customer_billing_email;
                    $rental->customer_billing_tax_no = $request->customer_billing_tax_no;
                    $rental->customer_billing_province_id = $request->customer_billing_province_id;
                    $rental->customer_billing_district_id = $request->customer_billing_district_id;
                    $rental->customer_billing_subdistrict_id = $request->customer_billing_subdistrict_id;
                }
            }
        } else {
            $rental->customer_billing_address_id = null;
            $rental->customer_billing_name = null;
            $rental->customer_billing_address = null;
            $rental->customer_billing_tel = null;
            $rental->customer_billing_email = null;
            $rental->customer_billing_tax_no = null;
            $rental->customer_billing_province_id = null;
            $rental->customer_billing_district_id = null;
            $rental->customer_billing_subdistrict_id = null;
        }

        $rental->rental_state = RentalStateEnum::ASSET;
        $rental->status = RentalStatusEnum::DRAFT;
        $rental->save();

        $redirect_route = route('admin.short-term-rental.asset.edit', [
            'rental_id' => $rental->id,
        ]);
        return $this->responseValidateSuccess($redirect_route);
    }

    function getDataCustomerBillingAddress(Request $request)
    {
        $customer_id = $request->customer_id;
        $data = CustomerBillingAddress::where('customer_id', $customer_id)
            ->get()
            ->map(function ($item) {
                $item->billing_customer_type_text = __('customers.type_' . $item->billing_customer_type);
                $province = Province::find($item->province_id);
                $item->province_text = ($province) ? $province->name_th : null;
                return $item;
            });
        $html = view('admin.short-term-rental-info.components.customer-billing-address-items', [
            'data' => $data
        ])->render();
        return [
            'success' => true,
            'customer_id' => $request->customer_id,
            'data' => $data,
            'html' => $html
        ];
    }

    public function storeCustomerBilling(Request $request)
    {
        $customer_id = $request->customer_id;
        $tax_invoice = $request->tax_invoice;
        if ($tax_invoice) {
            $customer_billing_address = new CustomerBillingAddress();
            $customer_billing_address->customer_id = $customer_id;
            $customer_billing_address->billing_customer_type = $tax_invoice['tax_customer_type_id'];
            $customer_billing_address->name = $tax_invoice['tax_customer_name'];
            $customer_billing_address->tax_no = $tax_invoice['tax_tax_no'];
            $customer_billing_address->province_id = $tax_invoice['tax_customer_province_id'];
            $customer_billing_address->address = $tax_invoice['tax_customer_address'];
            $customer_billing_address->tel = $tax_invoice['tax_customer_tel'];
            $customer_billing_address->email = $tax_invoice['tax_customer_email'];
            $customer_billing_address->zipcode = $tax_invoice['tax_customer_zipcode'];
            $customer_billing_address->save();
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function getDataProduct(Request $request)
    {
        $product_id_filter = $request->product_id_filter;
        $branch_id = $request->branch_id;
        $service_type_id = $request->service_type_id;
        $type_package = $request->type_package;
        $dataProduct = Product::select('products.*')
            ->when(!empty($product_id_filter), function ($querySearch) use ($product_id_filter) {
                $querySearch->where('id', $product_id_filter);
            })
            ->when(!empty($type_package), function ($querySearch) use ($type_package) {
                if (strcmp($type_package, CalculateTypeEnum::DAILY) == 0) {
                    $querySearch->wherein('calculate_type', [CalculateTypeEnum::DAILY, CalculateTypeEnum::HOURLY, CalculateTypeEnum::FIXED]);
                }
                if (strcmp($type_package, CalculateTypeEnum::MONTHLY) == 0) {
                    $querySearch->wherein('calculate_type', [CalculateTypeEnum::MONTHLY]);
                }
            })
            ->where('branch_id', $branch_id)
            ->where('service_type_id', $service_type_id)
            ->where('status', STATUS_ACTIVE)
            ->whereDate('end_date', '>=', Carbon::now())
            ->whereNotNull('end_date')
            ->orderBy('name')
            ->get()->chunk(6);
        $html = view('admin.short-term-rental-info.components.products-carousel-item', [
            'products' => $dataProduct
        ])->render();
        return [
            'success' => true,
            'data' => $dataProduct,
            'html' => $html
        ];
    }
}
