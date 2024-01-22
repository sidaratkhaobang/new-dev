<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\CheckCreditStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CheckCredits;
use App\Models\CustomerGroup;
use App\Traits\CheckCreditTrait;
use App\Traits\CustomerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CheckCreditApproveController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ContractCheckCreditApprove);

        $list = CheckCredits::search($request)->where('check_credits.status', '!=', CheckCreditStatusEnum::DRAFT)->sortable(['worksheet_no' => 'desc'])->paginate(PER_PAGE);

        $branch_list = CheckCredits::select(['branches.id', 'branches.name'])
            ->leftjoin('branches', 'branches.id', '=', 'check_credits.branch_id')
            ->groupBy(['branches.id', 'branches.name'])
            ->get();

        $customer_list = CheckCredits::select(['name as id', 'name'])->groupBy(['id', 'name'])->get();
        $customer_type_list = CustomerTrait::getCustomerType();
        $status_list = CheckCreditTrait::getStatusList();

        return view('admin.check-credit-approves.index', [
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
        //
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ContractCheckCreditApprove);

        $model = CheckCredits::find($request->id);

        if ($model->status != CheckCreditStatusEnum::CONFIRM && $model->status != CheckCreditStatusEnum::REJECT) {
            $validator = Validator::make($request->all(), [
                'approve_status' => ['required'],
            ], [], [
                'approve_status' => __('check_credit.form.result_check_credit'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        if ($model->status == CheckCreditStatusEnum::CONFIRM || $request->approve_status == CheckCreditStatusEnum::CONFIRM) {
            $validator = Validator::make($request->all(), [
                'approved_amount' => ['required'],
                'approved_days' => ['required', 'integer'],
            ], [], [
                'approved_amount' => __('check_credit.form.approved_amount'),
                'approved_days' => __('check_credit.form.approved_days'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }



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
        $model->status = $request->approve_status;
        $model->approved_amount = $request->approved_amount;
        $model->approved_days = $request->approved_days;
        $model->reason = $request->reason;
        $model->save();

        if ($request->approve_other_file__pending_delete_ids) {
            $pending_delete_ids = $request->approve_other_file__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $model->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('approve_other_file')) {
            foreach ($request->file('approve_other_file') as $image) {
                if ($image->isValid()) {
                    $model->addMedia($image)->toMediaCollection('check_credit_approve_images');
                }
            }
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

        return $this->responseValidateSuccess(route('admin.check-credit-approves.index'));
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

    public function show(CheckCredits $check_credit_approve)
    {
        $this->authorize(Actions::View . '_' . Resources::ContractCheckCreditApprove);

        $customer_type_list = CustomerTrait::getCustomerType();
        $customer_grade_list = CustomerTrait::getCustomerGrade();
        $customer_group_list = CustomerGroup::all();
        $branch_list = Branch::get(['id', 'name']);
        $listApproveStatus = CheckCreditTrait::getListStatusRadio();
        $check_credit_file = $this->getMediaCheckCredit($check_credit_approve);
        $check_credit_approve_file = $check_credit_approve->getMedia('check_credit_approve_images');
        $check_credit_approve_file = get_medias_detail($check_credit_approve_file);
        $page_title = __('lang.view') . __('check_credit.index.title.approve');
        return view('admin.check-credit-approves.form', [
            'd' => $check_credit_approve,
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

    public function edit(CheckCredits $check_credit_approve)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ContractCheckCreditApprove);
        $customer_type_list = CustomerTrait::getCustomerType();
        $customer_grade_list = CustomerTrait::getCustomerGrade();
        $customer_group_list = CustomerGroup::all();
        $branch_list = Branch::get(['id', 'name']);
        $listApproveStatus = CheckCreditTrait::getListStatusRadio();
        $check_credit_file = $this->getMediaCheckCredit($check_credit_approve);
        $check_credit_approve_file = $check_credit_approve->getMedia('check_credit_approve_images');
        $check_credit_approve_file = get_medias_detail($check_credit_approve_file);
        $page_title = __('lang.view') . __('check_credit.index.title.approve');

        return view('admin.check-credit-approves.form', [
            'd' => $check_credit_approve,
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

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
