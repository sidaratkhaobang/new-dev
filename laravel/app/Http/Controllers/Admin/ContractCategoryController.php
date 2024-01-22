<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\ConditionGroupEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\ConditionGroup;
use App\Models\ConditionQuotation;
use App\Models\ConditionQuotationChecklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContractCategoryController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ContractMasterDataCategory);

        $list = ConditionGroup::search($request)->where('condition_group', '=', ConditionGroupEnum::CONTRACT)
            ->orderBy('created_at', 'desc')
            ->sortable()
            ->paginate(PER_PAGE);

        return view('admin.contract-category.index', [
            'list' => $list,
            'category_name' => $request->category_name
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::ContractMasterDataCategory);

        $data = new ConditionGroup();
        return view('admin.contract-category.form', [
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ContractMasterDataCategory);
        $validator = Validator::make($request->all(), [
            'condition_name' => [
                'required',
                'max:255',
                // Rule::unique('inspection_forms', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'data.*.seq' => [
                'required',
                'integer',
                'distinct',
            ],
            'data.*.name' => [
                'required',
                'string',
            ],
            'data.*.condition_qoutation_checklists.*.seq' => [
                'required',
                'integer',
            ],
            'data.*.condition_qoutation_checklists.*.name' => [
                'required',
                'string',
            ],
        ], [], [
            'condition_name' => __('contract_category.form.condition_name'),
            'data.*.seq' => __('contract_category.form.table.seq'),
            'data.*.name' => __('contract_category.form.table.name'),
            'data.*.condition_qoutation_checklists.*.seq' => __('contract_category.form.table.sub.seq'),
            'data.*.condition_qoutation_checklists.*.name' => __('contract_category.form.table.sub.name'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        foreach ($request->data as $data) {
            if (!empty($data['condition_qoutation_checklists'])) {
                $temp = array_unique(array_column($data['condition_qoutation_checklists'], 'seq'));
                if (count($data['condition_qoutation_checklists']) != count($temp)) {
                    return $this->responseWithCode(false, __('contract_category.form.validate.seq', ['name' => $data['name']]), null, 422);
                }
            }
        }

        $condition_group = ConditionGroup::firstOrNew(['id' => $request->id]);
        $condition_group->name = $request->condition_name;
        $condition_group->condition_group = ConditionGroupEnum::CONTRACT;
        $condition_group->status = STATUS_ACTIVE;
        $condition_group->save();

        if (!empty($request->data)) {
            foreach ($request->data as $data) {
                $condition_qoutation = ConditionQuotation::firstOrNew([
                    'id' => $data['id'],
                ]);
                $condition_qoutation->name = $data['name'];
                $condition_qoutation->seq = $data['seq'];
                $condition_qoutation->condition_group_id = $condition_group->id;
                $condition_qoutation->condition_type = ConditionGroupEnum::CONTRACT;
                if ($data['status'] == 'false') {
                    $condition_qoutation->status = STATUS_DEFAULT;
                } else {
                    $condition_qoutation->status = $data['status'] ? STATUS_ACTIVE : STATUS_DEFAULT;
                }
                $condition_qoutation->save();

                if (!empty($data['condition_qoutation_checklists'])) {
                    foreach ($data['condition_qoutation_checklists'] as $list) {
                        $condition_qoutation_list = ConditionQuotationChecklist::firstOrNew([
                            'id' => $list['id'],
                        ]);
                        $condition_qoutation_list->name = $list['name'];
                        $condition_qoutation_list->seq = $list['seq'];
                        $condition_qoutation_list->condition_quotations_id = $condition_qoutation->id;
                        if ($list['status'] == 'false') {
                            $condition_qoutation_list->status = STATUS_DEFAULT;
                        } else {
                            $condition_qoutation_list->status = $list['status'] ? STATUS_ACTIVE : STATUS_DEFAULT;
                        }
                        $condition_qoutation_list->save();
                    }
                }
            }
        }

        if ($request->has('del_section')) {
            foreach ($request->del_section as $id) {
                ConditionQuotation::find($id)->delete();
            }
        }

        if ($request->has('del_checklist')) {
            foreach ($request->del_checklist as $id) {
                ConditionQuotationChecklist::find($id)->delete();
            }
        }

        $redirect_route = route('admin.contract-category.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(ConditionGroup $contract_category)
    {
        $this->authorize(Actions::View . '_' . Resources::ContractMasterDataCategory);
        $data = ConditionGroup::where('id', $contract_category->id)->with('condition_qoutations.condition_qoutation_checklists')->first();
        return view('admin.contract-category.form', [
            'data' => $data
        ]);
    }

    public function edit(ConditionGroup $contract_category)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ContractMasterDataCategory);
        $data = ConditionGroup::where('id', $contract_category->id)->with('condition_qoutations.condition_qoutation_checklists')->first();
        return view('admin.contract-category.form', [
            'data' => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ContractMasterDataCategory);
    }

    public function destroy(ConditionGroup $contract_category)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ContractMasterDataCategory);

        $contract_category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
