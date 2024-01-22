<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Customer;
use App\Models\Province;
use App\Models\User;
use App\Models\CustomerGroup;
use App\Models\CustomerGroupRelation;
use App\Models\CustomerBillingAddress;
use App\Models\CustomerDriver;
use App\Enums\CustomerTypeEnum;
use App\Traits\CustomerTrait;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Models\Amphure;
use App\Models\District;

class CustomerController extends Controller
{
    use CustomerTrait;
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Customer);
        $sale_id = $request->sale_id;
        $customer_type = $request->customer_type;
        $name = $request->name;
        $province_id = $request->province_id;

        $customer_type_list = CustomerTrait::getCustomerType();
        $sale_list = $this->getSaleList();

        $list = Customer::leftJoin('provinces', 'provinces.id', '=', 'customers.province_id')
            ->leftJoin('users', 'users.id', '=', 'customers.sale_id')
            ->sortable(['created_at' => 'desc'])
            ->select(
                'customers.id',
                'customers.customer_code',
                'customers.debtor_code',
                'customers.name',
                'customers.customer_type',
                'provinces.name_th as province',
                'users.name as sale_name',
            )
            ->groupBy(
                'customers.id',
                'customers.customer_code',
                'customers.debtor_code',
                'customers.name',
                'customers.customer_type',
                'province',
                'sale_name'
            )
            ->when(!empty($name), function ($query) use ($name) {
                return $query->where('customers.id', $name);
            })
            ->when(!empty($customer_type), function ($query) use ($customer_type) {
                return $query->where('customers.customer_type', $customer_type);
            })
            ->when(!empty($province_id), function ($query) use ($province_id) {
                return $query->where('customers.province_id', $province_id);
            })
            ->when(!empty($sale_id), function ($query) use ($sale_id) {
                return $query->where('customers.sale_id', $sale_id);
            })
            ->search($request->s)
            ->paginate(PER_PAGE);

        $province_name = null;
        if (!empty($province_id)) {
            $province = Province::find($province_id);
            $province_name = $province->name_th;
        }

        $customer_name = null;
        if (!empty($name)) {
            $customer = Customer::find($name);
            $customer_name = $customer->name;
        }

        return view('admin.customers.index', [
            's' => $request->s,
            'list' => $list,
            'customer_type' => $customer_type,
            'name' => $name,
            'province_id' => $province_id,
            'sale_id' => $sale_id,
            'customer_type_list' => $customer_type_list,
            'sale_list' => $sale_list,
            'province_name' => $province_name,
            'customer_name' => $customer_name
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::Customer);
        $d = new Customer();
        $customer_type_list = CustomerTrait::getCustomerType();
        $customer_grade_list = $this->getCustomerGrade();
        $sale_list = User::all();
        $customer_group_list = CustomerGroup::all();
        $customer_group = [];
        $province_name = null;
        $district_name = null;
        $subdistrict_name = null;

        $page_title = __('lang.create') . __('customers.page_title');
        return view('admin.customers.form', [
            'd' => $d,
            'page_title' => $page_title,
            'customer_type_list' => $customer_type_list,
            'customer_grade_list' => $customer_grade_list,
            'sale_list' => $sale_list,
            'customer_group_list' => $customer_group_list,
            'customer_group' => $customer_group,
            'province_name' => $province_name,
            'district_name' => $district_name,
            'subdistrict_name' => $subdistrict_name,
        ]);
    }

    public function edit(Customer $customer)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Customer);
        $customer_group = $this->getCustomerGroupArray($customer->id);
        $customer_type_list = CustomerTrait::getCustomerType();
        $customer_grade_list = $this->getCustomerGrade();
        $sale_list = User::all();
        $customer_group_list = CustomerGroup::all();
        $province_name = find_name_by_id($customer->province_id, Province::class, 'name_th');
        $district_name = find_name_by_id($customer->district_id, Amphure::class, 'name_th');
        $subdistrict_name = find_name_by_id($customer->subdistrict_id, District::class, 'name_th');
        $customer_billing_address_list = $this->getCustomerBillingAddresList($customer->id);
        $customer_driver_list = $this->getCustomerDriverList($customer->id);

        $page_title = __('lang.edit') . __('customers.page_title');
        return view('admin.customers.form', [
            'd' => $customer,
            'page_title' => $page_title,
            'customer_type_list' => $customer_type_list,
            'customer_grade_list' => $customer_grade_list,
            'sale_list' => $sale_list,
            'customer_group_list' => $customer_group_list,
            'customer_group' => $customer_group,
            'province_name' => $province_name,
            'district_name' => $district_name,
            'subdistrict_name' => $subdistrict_name,
            'customer_billing_address_list' => $customer_billing_address_list,
            'customer_driver_list' => $customer_driver_list,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_type' => ['required'],
            'name' => ['required', 'string', 'max:255'],
        ], [], [
            'customer_type' => __('customers.customer_type'),
            'name' => __('customers.name')
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $customer = Customer::firstOrNew(['id' => $request->id]);
        $customer->customer_code = $request->customer_code;
        $customer->debtor_code = $request->debtor_code;
        $customer->customer_type = $request->customer_type;
        $customer->customer_grade = $request->customer_grade;
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->tax_no = $request->tax_no;
        $customer->fullname_th = $request->fullname_th;
        $customer->fullname_en = $request->fullname_en;
        $customer->prefixname_th = $request->prefixname_th;
        $customer->prefixname_en = $request->prefixname_en;
        $customer->address = $request->address;
        $customer->province_id = $request->province_id;
        $customer->district_id = $request->district_id;
        $customer->subdistrict_id = $request->subdistrict_id;
        $customer->fax = $request->fax;
        $customer->tel = $request->tel;
        $customer->phone = $request->phone;
        $customer->sale_id = $request->sale_id;
        $customer->status = STATUS_ACTIVE;
        $customer->save();

        if ($customer->id) {
            $customer_group_relation = $this->saveCustomerGroupRelation($request, $customer->id);
            $customer_billing_address = $this->saveCustomerBillingAddress($request, $customer->id);
            $customer_driver = $this->saveCustomerDriver($request, $customer->id);
        }

        $redirect_route = route('admin.customers.index');
        if ($request->no_redirect) {
            return $this->responseComplete();
        }
        return $this->responseValidateSuccess($redirect_route);
    }

    private function saveCustomerGroupRelation($request, $customer_id)
    {
        CustomerGroupRelation::where('customer_id', $customer_id)->delete();
        if (!empty($request->customer_group)) {
            foreach ($request->customer_group as $customer_group) {
                $customer_group_relation = new CustomerGroupRelation();
                $customer_group_relation->customer_id = $customer_id;
                $customer_group_relation->customer_group_id = $customer_group;
                $customer_group_relation->save();
            }
        }
        return true;
    }

    private function saveCustomerBillingAddress($request, $customer_id)
    {
        $customer = Customer::find($customer_id);
        $id_arr = [];
        if (!empty($request->customer_billing_address)) {
            foreach ($request->customer_billing_address as $request_customer_billing_address) {
                $customer_billing_address = CustomerBillingAddress::firstOrNew(['id' => $request_customer_billing_address['id']]);
                $customer_billing_address->customer_id = $customer->id;
                $customer_billing_address->billing_customer_type = $customer->customer_type;
                $customer_billing_address->name = $request_customer_billing_address['name'];
                $customer_billing_address->tax_no = $request_customer_billing_address['tax_no'];
                $customer_billing_address->address = $request_customer_billing_address['address'];
                $customer_billing_address->province_id = $request_customer_billing_address['province_id'];
                $customer_billing_address->district_id = $request_customer_billing_address['district_id'];
                $customer_billing_address->subdistrict_id = $request_customer_billing_address['subdistrict_id'];
                $customer_billing_address->email = $request_customer_billing_address['email'];
                $customer_billing_address->tel = $request_customer_billing_address['tel'];
                $customer_billing_address->save();
                array_push($id_arr, $customer_billing_address->id);
            }
            CustomerBillingAddress::where('customer_id', $customer_id)->whereNotIn('id', $id_arr)->delete();
        }
        return true;
    }

    private function saveCustomerDriver($request, $customer_id)
    {
        //// delete customer driver and media
        $delete_driver_ids = $request->delete_driver_ids;
        if ((!empty($delete_driver_ids)) && (is_array($delete_driver_ids))) {
            foreach ($delete_driver_ids as $delete_id) {
                $customer_driver_delete = CustomerDriver::find($delete_id);
                $driving_license_medias = $customer_driver_delete->getMedia('driver_license');
                foreach ($driving_license_medias as $driving_license_media) {
                    $driving_license_media->delete();
                }
                $driving_citizen_medias = $customer_driver_delete->getMedia('driver_citizen');
                foreach ($driving_citizen_medias as $driving_citizen_media) {
                    $driving_citizen_media->delete();
                }
                $customer_driver_delete->delete();
            }
        }

        //// create + update customer driver data
        $pending_delete_license_files = $request->pending_delete_license_files;
        $pending_delete_citizen_files = $request->pending_delete_citizen_files;
        if (!empty($request->customer_driver)) {
            foreach ($request->customer_driver as $key => $request_customer_driver) {
                $customer_driver = CustomerDriver::firstOrNew(['id' => $request_customer_driver['id']]);
                if (!$customer_driver->exists) {
                    //
                }
                $customer_driver->customer_id = $customer_id;
                $customer_driver->name = $request_customer_driver['name'];
                $customer_driver->citizen_id = $request_customer_driver['citizen_id'];
                $customer_driver->email = $request_customer_driver['email'];
                $customer_driver->tel = $request_customer_driver['tel'];
                $customer_driver->save();
                // delete license and delete citizen
                if ((!empty($pending_delete_license_files)) && (sizeof($pending_delete_license_files) > 0)) {
                    foreach ($pending_delete_license_files as $license_media_id) {
                        $license_media = Media::find($license_media_id);
                        if ($license_media && $license_media->model_id) {
                            $license_model = CustomerDriver::find($license_media->model_id);
                            $license_model->deleteMedia($license_media->id);
                        }
                    }
                }

                if ((!empty($pending_delete_citizen_files)) && (sizeof($pending_delete_citizen_files) > 0)) {
                    foreach ($pending_delete_citizen_files as $citizen_media_id) {
                        $citizen_media = Media::find($citizen_media_id);
                        if ($citizen_media && $citizen_media->model_id) {
                            $citizen_model = CustomerDriver::find($citizen_media->model_id);
                            $citizen_model->deleteMedia($citizen_media->id);
                        }
                    }
                }
                // insert + update customer driver license and citizen
                if ((!empty($request->driver_license_file)) && (sizeof($request->driver_license_file) > 0)) {
                    foreach ($request->driver_license_file as $table_row_index => $driver_license_files) {
                        foreach ($driver_license_files as $driver_license_file) {
                            if ($driver_license_file->isValid() && (strcmp($table_row_index, 'table_row_' . $key) == 0)) {
                                $customer_driver->addMedia($driver_license_file)->toMediaCollection('driver_license');
                            }
                        }
                    }
                }
                if ((!empty($request->driver_citizen_files)) && (sizeof($request->driver_citizen_files) > 0)) {
                    foreach ($request->driver_citizen_files as $table_row_index => $driver_citizen_files) {
                        foreach ($driver_citizen_files as $driver_citizen_file) {
                            if ($driver_citizen_file->isValid() && (strcmp($table_row_index, 'table_row_' . $key) == 0)) {
                                $customer_driver->addMedia($driver_citizen_file)->toMediaCollection('driver_citizen');
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    public function show(Customer $customer)
    {
        $this->authorize(Actions::View . '_' . Resources::Customer);
        $customer_group = $this->getCustomerGroupArray($customer->id);
        $customer_type_list = CustomerTrait::getCustomerType();
        $customer_grade_list = $this->getCustomerGrade();
        $sale_list = User::all();
        $customer_group_list = CustomerGroup::all();
        $province_name = ($customer->province) ? $customer->province->name_th : null;
        $customer_billing_address_list = $this->getCustomerBillingAddresList($customer->id);
        $customer_driver_list = $this->getCustomerDriverList($customer->id);
        $province_name = find_name_by_id($customer->province_id, Province::class, 'name_th');
        $district_name = find_name_by_id($customer->district_id, Amphure::class, 'name_th');
        $subdistrict_name = find_name_by_id($customer->subdistrict_id, District::class, 'name_th');

        $page_title = __('lang.view') . __('customers.page_title');
        return view('admin.customers.form', [
            'd' => $customer,
            'page_title' => $page_title,
            'view' => true,
            'province_name' => $province_name,
            'district_name' => $district_name,
            'subdistrict_name' => $subdistrict_name,
            'customer_type_list' => $customer_type_list,
            'customer_grade_list' => $customer_grade_list,
            'sale_list' => $sale_list,
            'customer_group_list' => $customer_group_list,
            'customer_group' => $customer_group,
            'customer_billing_address_list' => $customer_billing_address_list,
            'customer_driver_list' => $customer_driver_list,
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Customer);
        $customer = Customer::find($id);
        $customer->delete();

        return $this->responseComplete();
    }

    public function getCustomerGroupArray($customer_id)
    {
        return CustomerGroupRelation::join('customer_groups', 'customer_groups.id', '=', 'customers_groups_relation.customer_group_id')
            ->select('customer_groups.id as id', 'customer_groups.name as name')
            ->where('customers_groups_relation.customer_id', $customer_id)
            ->pluck('customer_groups.id')
            ->toArray();
    }

    public function getCustomerBillingAddresList($customer_id)
    {
        $customer_billing_address_list = CustomerBillingAddress::select('customer_billing_addresses.*')
            ->addSelect('provinces.name_th as province_name', 'amphures.name_th as district_name', 'districts.name_th as subdistrict_name', 'districts.zip_code')
            ->leftJoin('provinces', 'provinces.id', '=', 'province_id')
            ->leftJoin('amphures', 'amphures.id', '=', 'district_id')
            ->leftJoin('districts', 'districts.id', '=', 'subdistrict_id')
            ->where('customer_id', $customer_id)->whereNull('deleted_at')->get();
        return $customer_billing_address_list;
    }

    public function getCustomerDriverList($customer_id)
    {
        $customer_driver_list = CustomerDriver::where('customer_id', $customer_id)->whereNull('deleted_at')->get();
        $customer_driver_list->map(function ($item) {
            $item->name = ($item->name) ? $item->name : '';
            $item->tel = ($item->tel) ? $item->tel : '';
            $item->citizen_id = ($item->citizen_id) ? $item->citizen_id : '';
            $item->email = ($item->email) ? $item->email : '';
            // get driver license files
            $driver_license_medias = $item->getMedia('driver_license');
            $license_files = get_medias_detail($driver_license_medias);
            $license_files = collect($license_files)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $item->license_files = $license_files;
            $item->pending_delete_license_files = [];
            // get driver citizen files
            $driver_citizen_medias = $item->getMedia('driver_citizen');
            $citizen_files = get_medias_detail($driver_citizen_medias);
            $citizen_files = collect($citizen_files)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $item->citizen_files = $citizen_files;
            $item->pending_delete_citizen_files = [];
            return $item;
        });

        return $customer_driver_list;
    }

    public function getSaleList()
    {
        $list = User::select('id', 'name')->get();
        return $list;
    }

    public function getCustomerNameList()
    {
        $list = Customer::select('id', 'name')->get();
        return $list;
    }

    public static function getCustomerGrade()
    {
        $customer_grade = collect([
            (object) [
                'id' => 1,
                'name' => __('customers.grade_1'),
                'value' => 1,
            ],
            (object) [
                'id' => 2,
                'name' => __('customers.grade_2'),
                'value' => 2,
            ],
            (object) [
                'id' => 3,
                'name' => __('customers.grade_3'),
                'value' => 3,
            ],
            (object) [
                'id' => 4,
                'name' => __('customers.grade_4'),
                'value' => 4,
            ],
        ]);
        return $customer_grade;
    }
}
