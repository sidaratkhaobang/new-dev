<?php

namespace App\Http\Controllers\API;

use App\Enums\ConsentType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Customer;
use App\Models\Province;
use App\Models\User;
use App\Models\CustomerGroup;
use App\Models\CustomerGroupRelation;
use App\Enums\CustomerTypeEnum;
use App\Models\CustomerConsent;
use App\Models\Pdpa;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->s;

        $list = Customer::leftJoin('provinces', 'provinces.id', '=', 'customers.province_id')
            ->sortable('name')
            ->select(
                'customers.id',
                'customers.customer_code',
                'customers.debtor_code',
                'customers.name',
                'customers.phone',
                'customers.tel',
                'customers.email',
            )
            ->when(!empty($s), function ($query) use ($s) {
                return $query->where(function ($q) use ($s) {
                    $q->where('customers.customer_code', 'like', '%' . $s . '%');
                    $q->orWhere('customers.debtor_code', 'like', '%' . $s . '%');
                    $q->orWhere('customers.name', 'like', '%' . $s . '%');
                    $q->orWhere('customers.email', 'like', '%' . $s . '%');
                    $q->orWhere('customers.phone', 'like', '%' . $s . '%');
                });
            })
            ->when(!empty($request->phone), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('customers.phone', 'like', '%' . $request->phone . '%');
                    $q->orWhere('customers.tel', 'like', '%' . $request->phone . '%');
                });
            })
            ->when(!empty($request->email), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('customers.email', 'like', '%' . $request->email . '%');
                });
            })
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read(Request $request)
    {
        $data = Customer::select('customers.id', 'customers.name', 'customers.customer_code', 'customers.debtor_code', 'customers.tax_no', 'customer_type', 'customer_grade')
            ->addSelect('prefixname_th', 'fullname_th', 'prefixname_en', 'fullname_en', 'provinces.name_th as province_name')
            ->addSelect('address', 'tel', 'phone', 'fax', 'email')
            ->leftJoin('provinces', 'provinces.id', '=', 'customers.province_id')
            ->where('customers.id', $request->id)->first();
        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        $consents = [
            ConsentType::MARKETING => [
                'status' => false,
                'channel' => null,
                'version' => null,
                'timestamp' => null,
            ],
            ConsentType::PRIVACY => [
                'status' => false,
                'channel' => null,
                'version' => null,
                'timestamp' => null,
            ],
            ConsentType::SENSITIVE => [
                'status' => false,
                'channel' => null,
                'version' => null,
                'timestamp' => null,
            ]
        ];
        $margeting_consent = Pdpa::where('consent_type', ConsentType::MARKETING)->orderBy('created_at', 'DESC')->first();
        if ($margeting_consent) {
            $cc_margeting = CustomerConsent::where('customer_id', $data->id)->where('pdpa_id', $margeting_consent->id)->orderBy('created_at', 'DESC')->first();
            if ($cc_margeting) {
                $consents[ConsentType::MARKETING] = [
                    'status' => boolval($cc_margeting->was_accepted),
                    'channel' => $cc_margeting->channel,
                    'version' => $margeting_consent->version,
                    'timestamp' => $cc_margeting->created_at,
                ];
            }
        }
        $privacy_consent = Pdpa::where('consent_type', ConsentType::PRIVACY)->orderBy('created_at', 'DESC')->first();
        if ($privacy_consent) {
            $cc_privacy = CustomerConsent::where('customer_id', $data->id)->where('pdpa_id', $privacy_consent->id)->orderBy('created_at', 'DESC')->first();
            if ($cc_privacy) {
                $consents[ConsentType::PRIVACY] = [
                    'status' => boolval($cc_privacy->was_accepted),
                    'channel' => $cc_privacy->channel,
                    'version' => $privacy_consent->version,
                    'timestamp' => $cc_privacy->created_at,
                ];
            }
        }
        $sensitive_consent = Pdpa::where('consent_type', ConsentType::SENSITIVE)->orderBy('created_at', 'DESC')->first();
        if ($sensitive_consent) {
            $cc_sensitive = CustomerConsent::where('customer_id', $data->id)->where('pdpa_id', $sensitive_consent->id)->orderBy('created_at', 'DESC')->first();
            if ($cc_sensitive) {
                $consents[ConsentType::SENSITIVE] = [
                    'status' => boolval($cc_sensitive->was_accepted),
                    'channel' => $cc_sensitive->channel,
                    'version' => $sensitive_consent->version,
                    'timestamp' => $cc_sensitive->created_at,
                ];
            }
        }

        $data->pdpa = $consents;
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'customer_type' => ['required', Rule::in([CustomerTypeEnum::GOVERNMENT, CustomerTypeEnum::CORPORATION, CustomerTypeEnum::PERSONAL, CustomerTypeEnum::ANTICIPATE])],
            'customer_grade' => ['required', Rule::in(['0', '1', '2', '3', '4'])],
            'tel' => ['required'],
            'email' => ['required', 'email', 'unique:customers,email'],
        ], [], [
            'customer_type' => __('customers.customer_type'),
            'customer_grade' => __('customers.customer_grade'),
            'name' => __('customers.name'),
            'tel' => __('customers.tel'),
            'email' => __('customers.email')
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $customer = new Customer;
        $customer->customer_code = $request->customer_code;
        $customer->debtor_code = $request->debtor_code;
        $customer->customer_type = $request->customer_type;
        $customer->customer_grade = $request->customer_grade;
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->prefixname_th = $request->prefixname_th;
        $customer->fullname_th = $request->fullname_th;
        $customer->prefixname_en = $request->prefixname_en;
        $customer->fullname_en = $request->fullname_en;
        $customer->address = $request->address;
        $customer->province_id = $request->province_id;
        $customer->fax = $request->fax;
        $customer->tel = $request->tel;
        $customer->phone = $request->phone;
        $customer->status = STATUS_ACTIVE;
        $customer->save();
        return $this->responseWithCode(true, DATA_SUCCESS, $customer->id, 201);
    }

    function consent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'exists:customers,id'],
            'pdpa_id' => ['required', 'exists:pdpas,id'],
            'accepted' => ['required', 'boolean'],
            'channel' => ['nullable', 'string', 'max:20'],
        ], [], [
            'id' => __('customers.id'),
            'pdpa_id' => __('pdpas.id'),
            'accepted' => __('pdpas.was_accepted'),
            'channel' => __('pdpas.channel'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $was_accepted = boolval($request->accepted);
        $channel = $request->channel;
        $channel = Str::limit($channel, 20, '');
        $d = new CustomerConsent();
        $d->customer_id = $request->id;
        $d->pdpa_id = $request->pdpa_id;
        $d->channel = $channel;
        $d->was_accepted = $was_accepted;
        $d->save();
        return $this->responseWithCode(true, DATA_SUCCESS, $d->id, 200);
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

        $customer = Customer::find($request->id);
        if (empty($customer)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        $customer->fill($request->all());
        $customer->save();

        return $this->responseWithCode(true, DATA_SUCCESS, $customer->id, 200);
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);
        if (empty($customer)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        $customer->email = null;
        $customer->save();
        $customer->delete();
        return $this->responseWithCode(true, DATA_SUCCESS, $customer->id, 200);
    }
}
