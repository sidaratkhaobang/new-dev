<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CustomerBillingAddress;
use App\Models\Province;
use App\Models\Customer;

class CustomerBillingAddressController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;
        $list = CustomerBillingAddress::leftJoin('provinces', 'provinces.id', '=', 'customer_billing_addresses.province_id')
            ->leftJoin('customers', 'customers.id', '=', 'customer_billing_addresses.customer_id')
            ->where('customer_billing_addresses.customer_id', $request->customer_id)
            ->select('customer_billing_addresses.id', 'customer_billing_addresses.name', 'customer_billing_addresses.tax_no')
            ->when(!empty($s), function ($query) use ($s) {
                return $query->where(function ($q) use ($s) {
                    $q->where('customer_billing_addresses.name', 'like', '%' . $s . '%');
                    $q->orWhere('customer_billing_addresses.id', 'like', '%' . $s . '%');
                    $q->orWhere('customer_billing_addresses.tax_no', 'like', '%' . $s . '%');
                    $q->orWhere('customer_billing_addresses.province_id', 'like', '%' . $s . '%');
                    $q->orWhere('customer_billing_addresses.address', 'like', '%' . $s . '%');
                    $q->orWhere('customer_billing_addresses.tel', 'like', '%' . $s . '%');
                    $q->orWhere('customer_billing_addresses.email', 'like', '%' . $s . '%');
                });
            })
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read(Request $request)
    {

        $data = CustomerBillingAddress::leftJoin('provinces', 'provinces.id', '=', 'customer_billing_addresses.province_id')
            ->leftJoin('customers', 'customers.id', '=', 'customer_billing_addresses.customer_id')
            ->select(
                'customer_billing_addresses.id',
                'customers.id as customer_id',
                'customers.name as customer_name',
                'customer_billing_addresses.name',
                'customer_billing_addresses.tax_no',
                'provinces.name_th as province_name',
                'customer_billing_addresses.address',
                'customer_billing_addresses.tel',
                'customer_billing_addresses.email'
            )
            ->where('customer_billing_addresses.id', $request->id)
            ->where('customer_billing_addresses.customer_id', $request->customer_id)
            ->first();
        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'tax_no' => ['required'],
            'province_id' => ['required', 'integer', 'exists:provinces,id'],
            'address' => ['required'],
            'tel' => ['required'],
            'email' => ['required', 'email'],
        ], [], [
            'name' => __('customers.name'),
            'tax_no' => __('customers.tax_no'),
            'province_id' => __('customers.province'),
            'address' => __('customers.address'),
            'tel' => __('customers.tel'),
            'email' => __('customers.email')
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $customer = Customer::find($request->customer_id);
        if (empty($customer)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        $province_id = Province::find($request->province_id);
        if (empty($province_id->id)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        $customer_billing_address = new CustomerBillingAddress();
        $customer_billing_address->customer_id = $request->customer_id;
        $customer_billing_address->name = $request->name;
        $customer_billing_address->tax_no = $request->tax_no;
        $customer_billing_address->address = $request->address;
        $customer_billing_address->province_id = $request->province_id;
        $customer_billing_address->email = $request->email;
        $customer_billing_address->tel = $request->tel;
        $customer_billing_address->save();

        return response()->json(['success' => true, 'id' => $customer_billing_address->id], 200);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required'],
        ], [], [
            'id' => __('customers.id'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $customer_billing_address = CustomerBillingAddress::where('id', $request->id)->where('customer_id', $request->customer_id)->first();
        if (empty($customer_billing_address)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        $customer_billing_address->fill($request->all());
        $customer_billing_address->save();

        return $this->responseWithCode(true, DATA_SUCCESS, $customer_billing_address->id, 200);
    }

    public function destroy(Request $request)
    {
        $customer_billing_address = CustomerBillingAddress::where('id', $request->id)->where('customer_id', $request->customer_id)->first();
        if (empty($customer_billing_address)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        $customer_billing_address->delete();
        return $this->responseWithCode(true, DATA_SUCCESS, $customer_billing_address->id, 200);
    }
}
