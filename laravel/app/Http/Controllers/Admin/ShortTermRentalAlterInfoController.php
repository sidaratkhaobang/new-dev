<?php

namespace App\Http\Controllers\Admin;

use App\Classes\OrderManagement;
use App\Classes\ProductManagement;
use App\Classes\Sap\SapProcess;
use App\Enums\Actions;
use App\Enums\OrderLineTypeEnum;
use App\Enums\RentalBillTypeEnum;
use App\Enums\RentalStateEnum;
use App\Enums\RentalStatusEnum;
use App\Enums\Resources;
use App\Factories\QuotationFactory;
use App\Http\Controllers\Controller;
use App\Models\Amphure;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\District;
use App\Models\Location;
use App\Models\Product;
use App\Models\Province;
use App\Models\Rental;
use App\Models\RentalBill;
use App\Models\RentalLine;
use App\Traits\CustomerTrait;
use App\Traits\RentalTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\CustomerBillingAddress;

class ShortTermRentalAlterInfoController extends Controller
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
        $product = Product::find($d->product_id);

        // customer
        $customer_name = null;
        if ($d->customer_id) {
            $customer = Customer::find($d->customer_id);
            $customer_name = ($customer) ? $customer->customer_code . ' - ' . $customer->name : null;
            $d->customer_type = ($customer) ? $customer->customer_type : null;
        }
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

        // check_customer_address
        $use_customer_billing_address = ($d->customer_billing_address_id ? true : false);
        $is_required_tax_invoice = boolval($d->is_required_tax_invoice);
        $check_customer_address = (boolval($d->check_customer_address));
        $customer_billing_province_name = find_name_by_id($d->customer_billing_province_id, Province::class, 'name_th');
        $customer_billing_district_name = find_name_by_id($d->customer_billing_district_id, Amphure::class, 'name_th');
        $customer_billing_subdistrict_name = find_name_by_id($d->customer_billing_subdistrict_id, District::class, 'name_th');
        $customer_billing_zipcode = find_name_by_id($d->customer_billing_subdistrict_id, District::class, 'zip_code');
        $customer_district_name = find_name_by_id($d->customer_district_id, Amphure::class, 'name_th');
        $customer_subdistrict_name = find_name_by_id($d->customer_subdistrict_id, District::class, 'name_th');
        $customer_zipcode = find_name_by_id($d->customer_subdistrict_id, District::class, 'zip_code');
        $page_title = __('lang.edit') . __('short_term_rentals.sheet');

        return view('admin.short-term-rental-alter.form', [
            'd' => $d,
            'order_channel_list' => $order_channel_list,
            'branch_name' => $branch_name,
            'product' => $product,
            'product_name' => $product_name,
            'origin_name' => $origin_name,
            'destination_name' => $destination_name,
            'rental_id' => $rental_id,
            'service_type_id' => $d->service_type_id,
            'customer_type_list' => $customer_type_list,
            'province_list' => $province_list,
            'branch_office_list' => $branch_office_list,
            'branch_list' => $branch_list,
            'is_required_tax_invoice' => $is_required_tax_invoice,
            'use_customer_billing_address' => $use_customer_billing_address,
            'customer_name' => $customer_name,
            'page_title' => $page_title,
            'customer_zipcode' => $customer_zipcode,
            'check_customer_address' => $check_customer_address,
            'customer_billing_province_name' => $customer_billing_province_name,
            'customer_billing_district_name' => $customer_billing_district_name,
            'customer_billing_subdistrict_name' => $customer_billing_subdistrict_name,
            'customer_billing_zipcode' => $customer_billing_zipcode,
            'customer_district_name' => $customer_district_name,
            'customer_subdistrict_name' => $customer_subdistrict_name,
        ]);
    }

    public function storeInfo(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $today = date("Y-m-d H:i");
        $validator = Validator::make($request->all(), [
            // 'branch_id' => 'required',
            // 'product_id' => 'required',
            // 'pickup_date' => 'required|date_format:Y-m-d H:i|after:' . $today,
            // 'return_date' => 'required|date_format:Y-m-d H:i|after_or_equal:pickup_date',
            // 'origin_id' => 'required',
            'destination_id' => 'required',
            // 'customer_id' => 'required',
            // 'customer_name' => 'required',
        ], [], [
            'branch_id' => __('short_term_rentals.branch'),
            'product_id' => __('short_term_rentals.package'),
            'origin_id' => __('short_term_rentals.origin'),
            'destination_id' => __('short_term_rentals.destination'),
            'pickup_date' => __('short_term_rentals.pickup_date'),
            'return_date' => __('short_term_rentals.return_date'),
            'customer_id' => __('short_term_rentals.customer_id'),
            'customer_name' => __('short_term_rentals.customer_name'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        //dd($request->all());

        $rental_id = $request->rental_id;
        $rental = Rental::findOrFail($request->rental_id);
        $rental->return_date = $request->return_date;
        if (strcmp($request->destination_id, ADDITIONAL) === 0) {
            $rental->destination_id = null;
            $rental->destination_lat = $request->destination_lat;
            $rental->destination_lng = $request->destination_lng;
            $rental->destination_name = $request->destination_name;
            $rental->destination_address = $request->destination_address;
        } else {
            $rental->destination_id = $request->destination_id;
        }

        $rental->rental_state = RentalStateEnum::ASSET_EDIT;
        $rental->save();

        /* $rental_lines = RentalLine::where('rental_id', $rental_id)
            ->where('item_type', Product::class)
            ->whereNotNull('car_id')
            ->get();

        $rental_bill_primary = RentalBill::where('bill_type', RentalBillTypeEnum::PRIMARY)
            ->where('rental_id', $rental_id)
            ->first(); */

        /* $checked_vouchers = RentalTrait::getSelectedVoucher($rental_bill_primary->id);
        $om = new OrderManagement($rental_bill_primary);
        $om->setPromotion($rental_bill_primary->promotion_code, $checked_vouchers);
        $om->isWithholdingTax($rental_bill_primary->check_withholding_tax);
        $om->calculate();
        $summary = $om->getSummary(); */

        $bill_subtotal = 0;
        $bill_total = 0;
        /* foreach ($rental_lines as $key => $rental_line) {
            $rental_line = RentalLine::find($rental_line->id);
            $pm = new ProductManagement($rental->service_type_id);
            $pm->setBranchId($rental->branch_id);
            $pm->setDates($rental->pickup_date, $rental->return_date);
            $price = $pm->findPrice($rental_line->item_id, $rental_line->car_id);
            $rental_line->subtotal = $price;
            $rental_line->total = $price;
            $rental_line->save();

            $bill_subtotal += $price;
            $bill_total += $price;
        }

        if (floatval($bill_total) > floatval($summary['subtotal'])) {
            $rental_bill = RentalBill::where('rental_id', $rental_id)
                ->where('bill_type', RentalBillTypeEnum::SECONDARY)
                ->where('status', RentalStatusEnum::PENDING)
                ->first();
            $require_sap_inform = false;
            if (!$rental_bill) {
                $rental_bill = new RentalBill;
                $require_sap_inform = true;
            }

            $rental_bill->rental_id = $rental->id;
            $rental_bill->status = RentalStatusEnum::PENDING;
            $rental_bill->bill_type = RentalBillTypeEnum::SECONDARY;
            $subtotal_diff = floatval($bill_total) - floatval($summary['subtotal']);
            $total_diff = floatval($bill_total) - floatval($summary['subtotal']);
            $rental_bill->subtotal = $subtotal_diff;
            $rental_bill->vat = calculateVat($total_diff);;
            $rental_bill->total = $total_diff;
            $rental_bill->save();

            $rental_line = RentalLine::firstOrNew(['item_type' => OrderLineTypeEnum::PRODUCT_DIFF]);
            $rental_line->item_type = OrderLineTypeEnum::PRODUCT_DIFF;
            $rental_line->item_id = (string) Str::orderedUuid();
            $rental_line->name = __('short_term_rentals.type_' . OrderLineTypeEnum::PRODUCT_DIFF);
            $rental_line->amount = 1;
            $rental_line->rental_id = $rental->id;
            $rental_line->rental_bill_id = $rental_bill->id;
            $rental_line->subtotal = $subtotal_diff;
            $rental_line->total = $total_diff;
            $rental_line->save();

            if ($require_sap_inform) {
                $qtf = new QuotationFactory($rental_bill);
                $qtf->create();
                // $sap = new SapProcess();
                // $sap->afterServiceInform($rental);
            }
        } */
        $redirect_route = route('admin.short-term-rental.alter.edit-driver', [
            'rental_id' => $rental_id,
        ]);
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ShortTermRental);
        $rental_id = $request->rental_id;
        $d = Rental::find($rental_id);
        if (empty($d)) {
            return redirect()->route('admin.short-term-rentals.index');
        }
        $branch_name = null;
        $customer_code = null;
        $product_name = null;
        $origin_name = null;
        $destination_name = null;

        if ($d->branch_id) {
            $branch = Branch::find($d->branch_id);
            $branch_name = $branch->name;
        }
        if ($d->product_id) {
            $product = Product::find($d->product_id);
            $product_sku = ($product) ? $product->sku : null;
            $product_name = ($product) ? $product->name : null;
            $product_name = $product_sku . ' (' . $product_name . ') ';
        }
        if ($d->customer_id) {
            $customer = Customer::find($d->customer_id);
            $customer_code = ($customer) ? $customer->customer_code : null;
            $d->customer_type = ($customer) ? $customer->customer_type : null;
        }
        if ($d->origin_id) {
            $origin = Location::find($d->origin_id);
            $origin_name = ($origin) ? $origin->name : null;
        }
        if ($d->origin_name) {
            $d->origin_id = ADDITIONAL;
            $origin_name = $d->origin_name;
        }
        if ($d->destination_id) {
            $destination = Location::find($d->destination_id);
            $destination_name = ($destination) ? $destination->name : null;
        }
        if ($d->destination_name) {
            $d->destination_id = ADDITIONAL;
            $destination_name = $d->destination_name;
        }
        $customer_type_list = CustomerTrait::getCustomerType();
        $province_list = Province::select('id', 'name_th as name')->get();
        $branch_office_list = RentalTrait::getBranchOfficeList();
        $branch_list = RentalTrait::getBranchOfServiceType($d->service_type_id);
        $tax_invoice_list = [];
        $check_customer_address = BOOL_TRUE;
        $rental_bill_customer_billing = RentalBill::where('rental_id', $rental_id)->where('bill_type', RentalBillTypeEnum::PRIMARY)->select('id', 'check_customer_address', 'customer_billing_address_id')->first();
        if ($rental_bill_customer_billing) {
            $check_customer_address = $rental_bill_customer_billing->check_customer_address;
            if (strcmp($check_customer_address, BOOL_FALSE) == 0) {
                $tax_invoice_list = CustomerBillingAddress::where('id', $rental_bill_customer_billing->customer_billing_address_id)->get()
                    ->map(function ($item) {
                        $item->tax_customer_type_id = $item->billing_customer_type;
                        $item->tax_customer_name = $item->name;
                        $item->tax_tax_no = $item->tax_no;
                        $item->tax_customer_province_id = $item->province_id;
                        $item->tax_customer_address = $item->address;
                        $item->tax_customer_tel = $item->tel;
                        $item->tax_customer_email = $item->email;
                        $item->tax_customer_zipcode = $item->zipcode;
                        $item->tax_customer_type_text = __('customers.type_' . $item->billing_customer_type);
                        $province = Province::find($item->province_id);
                        $item->tax_customer_province_text = ($province) ? $province->name_th : null;
                        return $item;
                    })->toArray();
            }
        }

        $page_title = __('lang.view') . __('short_term_rentals.sheet');
        return view('admin.short-term-rental-alter.form', [
            'd' => $d,
            'branch_name' => $branch_name,
            'product_name' => $product_name,
            'origin_name' => $origin_name,
            'destination_name' => $destination_name,
            'customer_code' => $customer_code,
            'rental_id' => $rental_id,
            'service_type_id' => $d->service_type_id,
            'customer_type_list' => $customer_type_list,
            'province_list' => $province_list,
            'branch_office_list' => $branch_office_list,
            'branch_list' => $branch_list,
            'tax_invoice_list' => $tax_invoice_list,
            'check_customer_address' => $check_customer_address,
            'page_title' => $page_title,
        ]);
    }
}
