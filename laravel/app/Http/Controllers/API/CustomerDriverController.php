<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CustomerDriver;
use App\Models\Customer;

class CustomerDriverController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;
        $list = CustomerDriver::select('customer_drivers.id', 'customer_drivers.name', 'customer_drivers.tel', 'customer_drivers.email', 'customer_drivers.citizen_id')
            ->leftJoin('customers', 'customers.id', '=', 'customer_drivers.customer_id')
            ->where('customer_drivers.customer_id', $request->customer_id)
            ->when(!empty($s), function ($query) use ($s) {
                return $query->where(function ($q) use ($s) {
                    $q->where('customer_drivers.name', 'like', '%' . $s . '%');
                    $q->orWhere('customer_drivers.citizen_id', 'like', '%' . $s . '%');
                    $q->orWhere('customer_drivers.tel', 'like', '%' . $s . '%');
                    $q->orWhere('customer_drivers.email', 'like', '%' . $s . '%');
                });
            })
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read(Request $request)
    {

        $data = CustomerDriver::leftJoin('customers', 'customers.id', '=', 'customer_drivers.customer_id')
            ->select(
                'customer_drivers.id',
                'customers.id as customer_id',
                'customers.name as customer_name',
                'customer_drivers.name',
                'customer_drivers.citizen_id',
                'customer_drivers.tel',
                'customer_drivers.email'
            )
            ->where('customer_drivers.id', $request->id)
            ->where('customer_drivers.customer_id', $request->customer_id)
            ->first();
        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        $data->driving_license_url = null;
        $data->id_card_url = null;

        $medias = $data->getMedia('driver_license');
        $files = get_medias_detail($medias);
        if (isset($files[0]['url'])) {
            $data->driving_license_url = $files[0]['url'];
        }
        $medias = $data->getMedia('driver_citizen');
        $files = get_medias_detail($medias);
        if (isset($files[0]['url'])) {
            $data->id_card_url = $files[0]['url'];
        }
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'tel' => ['required'],
            'driving_license_file' => ['mimes:jpg,png,pdf,doc,docx'],
            'id_card_file' => ['mimes:jpg,png,pdf,doc,docx'],
        ], [], [
            'name' => __('customers.name'),
            'tel' => __('customers.tel'),
            'driving_license_file' => __('customers.driving_license_file'),
            'id_card_file' => __('customers.citizen_file'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $customer = Customer::find($request->customer_id);
        if (empty($customer)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        $customer_driver = new CustomerDriver();
        $customer_driver->customer_id = $request->customer_id;
        $customer_driver->name = $request->name;
        $customer_driver->citizen_id = $request->citizen_id;
        $customer_driver->email = $request->email;
        $customer_driver->tel = $request->tel;
        $customer_driver->save();

        if (!empty($request->driving_license_file)) {
            if ($request->driving_license_file->isValid()) {
                $customer_driver->addMedia($request->driving_license_file)->toMediaCollection('driver_license');
            }
        }

        if (!empty($request->id_card_file)) {
            if ($request->id_card_file->isValid()) {
                $customer_driver->addMedia($request->id_card_file)->toMediaCollection('driver_citizen');
            }
        }

        return response()->json(['success' => true, 'id' => $customer_driver->id], 200);
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

        $customer_driver = CustomerDriver::where('id', $request->id)->where('customer_id', $request->customer_id)->first();
        if (empty($customer_driver)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        $customer_driver->fill($request->all());
        $customer_driver->save();

        if (!empty($request->driving_license_file)) {
            if ($request->driving_license_file->isValid()) {
                $customer_driver->clearMediaCollection('driver_license');
                $customer_driver->addMedia($request->driving_license_file)->toMediaCollection('driver_license');
            }
        }

        if (!empty($request->id_card_file)) {
            if ($request->id_card_file->isValid()) {
                $customer_driver->clearMediaCollection('driver_citizen');
                $customer_driver->addMedia($request->id_card_file)->toMediaCollection('driver_citizen');
            }
        }

        return $this->responseWithCode(true, DATA_SUCCESS, $customer_driver->id, 200);
    }

    public function destroy(Request $request)
    {
        $customer_driver = CustomerDriver::where('id', $request->id)->where('customer_id', $request->customer_id)->first();
        if (empty($customer_driver)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        $customer_driver->delete();
        return $this->responseWithCode(true, DATA_SUCCESS, $customer_driver->id, 200);
    }
}
