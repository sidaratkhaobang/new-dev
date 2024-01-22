<?php

namespace App\Http\Controllers\Admin\Util;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerBillingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Select2CustomerController extends Controller
{
    function getCustomerCode(Request $request)
    {
        $list = Customer::select('id', 'customer_code', 'name')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('customer_code', 'like', '%' . $request->s . '%');
                    $query->orWhere('name', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('customer_type', $request->parent_id);
                }
            })
            ->limit(30)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->customer_code . ' - ' . $item->name
                ];
            });
        return response()->json($list);
    }

    function getCustomerDetail(Request $request)
    {
        $customer = Customer::select('customers.id', 'customers.name', 'customers.customer_code', 'customers.debtor_code')
            ->addSelect(DB::raw("CONCAT(customers.customer_code, ' - ', customers.name) as customer_code_name"))
            ->addSelect('customers.account_code', 'customers.tax_no', 'customers.customer_type', 'customers.customer_grade')
            ->addSelect('customers.prefixname_th', 'customers.fullname_th', 'customers.prefixname_en', 'customers.fullname_en')
            ->addSelect('customers.province_id', 'customers.district_id', 'customers.subdistrict_id')
            ->addSelect('customers.address', 'customers.tel', 'customers.phone', 'customers.fax', 'customers.email')
            ->addSelect('provinces.name_th as province_name', 'amphures.name_th as district_name', 'districts.name_th as subdistrict_name', 'districts.zip_code')
            ->leftJoin('provinces', 'provinces.id', '=', 'customers.province_id')
            ->leftJoin('amphures', 'amphures.id', '=', 'customers.district_id')
            ->leftJoin('districts', 'districts.id', '=', 'customers.subdistrict_id')
            ->where('customers.id', $request->customer_id)
            ->first();
        return [
            'success' => true,
            'data' => $customer
        ];
    }

    function getCustomers(Request $request)
    {
        $list = Customer::select('id', 'name')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    // $query->where('customer_code', 'like', '%' . $request->s . '%');
                    $query->orWhere('name', 'like', '%' . $request->s . '%');
                }
            })
            ->limit(30)
            ->orderBy('name')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getCustomerBillingAddress(Request $request)
    {
        $customer_id = $request->parent_id;
        $list = CustomerBillingAddress::select('id', 'name')
            ->where('customer_id', $customer_id)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    // $query->where('customer_code', 'like', '%' . $request->s . '%');
                    $query->where('name', 'like', '%' . $request->s . '%');
                }
            })
            ->limit(30)
            ->orderBy('name')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getCustomerBillingDetail(Request $request)
    {
        $customer = CustomerBillingAddress::select('customer_billing_addresses.id', 'name', 'tax_no')
            ->addSelect('customer_billing_addresses.province_id', 'district_id', 'subdistrict_id', 'address', 'tel', 'email')
            ->addSelect('provinces.name_th as province_name', 'amphures.name_th as district_name', 'districts.name_th as subdistrict_name', 'districts.zip_code')
            ->leftJoin('provinces', 'provinces.id', '=', 'province_id')
            ->leftJoin('amphures', 'amphures.id', '=', 'district_id')
            ->leftJoin('districts', 'districts.id', '=', 'subdistrict_id')
            ->where('customer_billing_addresses.id', $request->customer_billing_address_id)
            ->first();
        return [
            'success' => true,
            'data' => $customer
        ];
    }
}
