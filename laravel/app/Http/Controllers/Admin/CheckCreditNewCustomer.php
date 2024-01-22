<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\CheckCreditStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CheckCredits;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\CustomerGroupRelation;
use App\Traits\CheckCreditTrait;
use App\Traits\CustomerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CheckCreditNewCustomer extends Controller
{

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ContractCheckCreditNewCustomer);

        $list = CheckCredits::search($request)->sortable(['worksheet_no' => 'desc'])->paginate(PER_PAGE);

        $branch_list = CheckCredits::select(['branches.id', 'branches.name'])
            ->leftjoin('branches', 'branches.id', '=', 'check_credits.branch_id')
            ->groupBy(['branches.id', 'branches.name'])
            ->get();

        $customer_list = CheckCredits::select(['name as id', 'name'])->groupBy(['id', 'name'])->get();
        $customer_type_list = CustomerTrait::getCustomerType();
        $status_list = CheckCreditTrait::getStatusList();

        return view('admin.check-credit-new-customers.index', [
            'list' => $list,
            'customer_type' => $request->customer_type,
            'customer_type_list' => $customer_type_list,
            'customer_name' => $request->customer_name,
            'customer_name_list' => $customer_list,
            'branch_id' => $request->branch_id,
            'branch_list' => $branch_list,
            'status' => $request->status,
            'status_list' => $status_list,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::ContractCheckCreditNewCustomer);

        $customer_type_list = CustomerTrait::getCustomerType();
        $customer_grade_list = CustomerTrait::getCustomerGrade();
        $customer_group_list = CustomerGroup::all();
        $branch_list = Branch::get(['id', 'name']);
        $page_title = __('lang.create') . __('check_credit.index.page_title');
        return view('admin.check-credit-new-customers.form', [
            'd' => new CheckCredits(),
            'customer_type_list' => $customer_type_list,
            'customer_grade_list' => $customer_grade_list,
            'customer_group_list' => $customer_group_list,
            'branch_list' => $branch_list,
            'page_title' => $page_title
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ContractCheckCreditNewCustomer);

        if (isset($request->status_create_customer)) {
            $validator = Validator::make($request->all(), [
                'customer_code' => ['max:20'],
                'customer_tax_number' => ['max:255'],
                'customer_prefix_name_th' => ['max:255'],
                'customer_full_name_th' => ['max:255'],
                'customer_prefix_name_en' => ['max:255'],
                'customer_full_name_en' => ['max:255'],
                'customer_email' => ['max:255'],
                'customer_fax' => ['max:20'],
                'customer_mobile_number' => ['max:20'],
                'customer_phone_number' => ['max:20'],
                'customer_type' => ['required'],
                'customer_name' => ['required', 'string', 'max:255'],
            ], [], [
                'customer_code' => __('check_credit.form.customer_code'),
                'customer_tax_number' => __('check_credit.form.customer_tax_number'),
                'customer_prefix_name_th' => __('check_credit.form.customer_prefix_name_th'),
                'customer_full_name_th' => __('check_credit.form.customer_full_name_th'),
                'customer_prefix_name_en' => __('check_credit.form.customer_prefix_name_en'),
                'customer_full_name_en' => __('check_credit.form.customer_full_name_en'),
                'customer_email' => __('check_credit.form.customer_email'),
                'customer_fax' => __('check_credit.form.customer_fax'),
                'customer_mobile_number' => __('check_credit.form.customer_mobile_number'),
                'customer_phone_number' => __('check_credit.form.customer_phone_number'),
                'customer_type' => __('customers.customer_type'),
                'customer_name' => __('customers.name'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        } else {
            // check file exists
            $files_exists = false;
            $check_credit_new_customer = CheckCredits::firstOrNew(['id' => $request->id]);
            if (!empty($check_credit_new_customer)) {
                $check_credit_file = $this->getMediaCheckCredit($check_credit_new_customer);
                if (sizeof($check_credit_file) > 0) {
                    $files_exists = true;
                }
            }

            $rules = [
                'document_file' => ['required'],
                'document_file.*.file' => ['file'],
            ];
            if ($files_exists) {
                $rules = [
                    'document_file' => ['nullable'],
                    'document_file.*.file' => ['file'],
                ];
            }
            $validator = Validator::make($request->all(), $rules, [], [
                'document_file' => __('check_credit.form.document_file'),
                'document_file.*.file' => __('check_credit.form.document_file'),
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        $model = CheckCredits::firstOrNew(['id' => $request->id]);
        $model->worksheet_no = $model->worksheet_no ?? CheckCreditTrait::getWorkSheetNumber();
        $model->branch_id = $request->branch_id;
        $model->customer_code = $request->customer_code;
        $model->customer_type = $request->customer_type;

        if (isset($request->customer_group)) {
            $model->customer_group = json_encode($request->customer_group);
        } else {
            $model->customer_group = '[]';
        }

        $model->customer_grade = $request->customer_grade;
        $model->name = $request->customer_name;
        $model->tax_no = $request->customer_tax_number;
        $model->prefixname_th = $request->customer_prefix_name_th;
        $model->fullname_th = $request->customer_full_name_th;
        $model->prefixname_en = $request->customer_prefix_name_en;
        $model->fullname_en = $request->customer_full_name_en;
        $model->email = $request->customer_email;
        $model->fax = $request->customer_fax;
        $model->tel = $request->customer_phone_number;
        $model->phone = $request->customer_mobile_number;
        $model->address = $request->customer_address;

        if ($model->status != CheckCreditStatusEnum::CONFIRM) {
            $model->status = $request->status_pending_approve ? CheckCreditStatusEnum::PENDING_REVIEW : CheckCreditStatusEnum::DRAFT;
        }

        if (!$model->is_create_customer && isset($request->status_create_customer)) {
            $this->createNewCustomer($request);
            $model->is_create_customer = true;
            $model->save();
        } else {
            $model->save();
        }

        if ($request->delete_media_file_ids) {
            $delete_media_file_ids = $request->delete_media_file_ids;
            if ((is_array($delete_media_file_ids)) && (sizeof($delete_media_file_ids) > 0)) {
                foreach ($delete_media_file_ids as $media_id) {
                    $model->deleteMedia($media_id);
                }
            }
        }

        if ($request->document_file) {
            foreach ($request->document_file as $item) {
                if ($item['file']->isValid()) {
                    $model->addMedia($item['file'])
                        ->usingName($item['file_name'])
                        ->toMediaCollection('check_credit_images');
                }
            }
        }

        return $this->responseValidateSuccess(route('admin.check-credit-new-customers.index'));
    }

    private function createNewCustomer($request)
    {
        $customer = new Customer();
        $customer->branch_id = $request->branch_id;
        $customer->customer_code = $request->customer_code;
        $customer->customer_type = $request->customer_type;
        $customer->customer_grade = $request->customer_grade;
        $customer->name = $request->customer_name;
        $customer->email = $request->customer_email;
        $customer->tax_no = $request->customer_tax_number;
        $customer->fullname_th = $request->customer_full_name_th;
        $customer->fullname_en = $request->customer_full_name_en;
        $customer->prefixname_th = $request->customer_prefix_name_th;
        $customer->prefixname_en = $request->customer_prefix_name_en;
        $customer->address = $request->customer_address;
        $customer->fax = $request->customer_fax;
        $customer->tel = $request->customer_phone_number;
        $customer->phone = $request->customer_mobile_number;
        $customer->status = STATUS_ACTIVE;
        $customer->save();

        CustomerGroupRelation::where('customer_id', $customer->id)->delete();
        if (!empty($request->customer_group)) {
            foreach ($request->customer_group as $customer_group) {
                $customer_group_relation = new CustomerGroupRelation();
                $customer_group_relation->customer_id = $customer->id;
                $customer_group_relation->customer_group_id = $customer_group;
                $customer_group_relation->save();
            }
        }
    }

    public function show(CheckCredits $check_credit_new_customer)
    {
        $this->authorize(Actions::View . '_' . Resources::ContractCheckCreditNewCustomer);

        $customer_type_list = CustomerTrait::getCustomerType();
        $customer_grade_list = CustomerTrait::getCustomerGrade();
        $customer_group_list = CustomerGroup::all();
        $branch_list = Branch::get(['id', 'name']);
        $listApproveStatus = CheckCreditTrait::getListStatusRadio();
        $check_credit_file = $this->getMediaCheckCredit($check_credit_new_customer);
        $check_credit_approve_file = $check_credit_new_customer->getMedia('check_credit_approve_images');
        $check_credit_approve_file = get_medias_detail($check_credit_approve_file);
        $page_title = __('lang.view') . __('check_credit.index.page_title');

        return view('admin.check-credit-new-customers.form', [
            'd' => $check_credit_new_customer,
            'customer_type_list' => $customer_type_list,
            'customer_grade_list' => $customer_grade_list,
            'customer_group_list' => $customer_group_list,
            'branch_list' => $branch_list,
            'listApproveStatus' => $listApproveStatus,
            'check_credit_file' => $check_credit_file,
            'check_credit_approve_file' => $check_credit_approve_file,
            'page_title' => $page_title
        ]);
    }

    public function edit(CheckCredits $check_credit_new_customer)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ContractCheckCreditNewCustomer);

        if ($check_credit_new_customer->status == CheckCreditStatusEnum::REJECT) {
            return redirect()->action(
                [CheckCreditNewCustomer::class, 'show'],
                ['check_credit_new_customer' => $check_credit_new_customer]
            );
        }

        $customer_type_list = CustomerTrait::getCustomerType();
        $customer_grade_list = CustomerTrait::getCustomerGrade();
        $customer_group_list = CustomerGroup::all();
        $branch_list = Branch::get(['id', 'name']);
        $listApproveStatus = CheckCreditTrait::getListStatusRadio();
        $check_credit_file = $this->getMediaCheckCredit($check_credit_new_customer);
        $check_credit_approve_file = $check_credit_new_customer->getMedia('check_credit_approve_images');
        $check_credit_approve_file = get_medias_detail($check_credit_approve_file);
        $page_title = __('lang.edit') . __('check_credit.index.page_title');
        return view('admin.check-credit-new-customers.form', [
            'd' => $check_credit_new_customer,
            'customer_type_list' => $customer_type_list,
            'customer_grade_list' => $customer_grade_list,
            'customer_group_list' => $customer_group_list,
            'branch_list' => $branch_list,
            'listApproveStatus' => $listApproveStatus,
            'check_credit_file' => $check_credit_file,
            'check_credit_approve_file' => $check_credit_approve_file,
            'page_title' => $page_title
        ]);
    }

    function getMediaCheckCredit($check_credit_new_customer)
    {
        $media = $check_credit_new_customer->getMedia('check_credit_images');
        $media = get_medias_detail($media);
        $media = collect($media)->map(function ($item) {
            $item['formated'] = true;
            $item['saved'] = true;
            $item['raw_file'] = null;
            return $item;
        })->toArray();
        return $media;
    }

    public function update(CheckCredits $check_credit_new_customer, $id)
    {
        //
    }

    public function destroy(CheckCredits $check_credit_new_customer)
    {
        $check_credit_new_customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
