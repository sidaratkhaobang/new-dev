<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\ConditionGroupEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\ConditionGroup;
use App\Traits\ConditionQuotationTrait;
use Illuminate\Http\Request;
use App\Models\ConditionQuotation;
use App\Models\ConditionQuotationChecklist;
use Illuminate\Support\Facades\Validator;

class ConditionQuotationShortTermRentalController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ShortTermConditionQuotation);
        $condition_group = ConditionQuotationTrait::getConditionGroup(ConditionGroupEnum::SHORT_TERM_RENTAL);
        $list = [];
        if ($condition_group) {
            $list = ConditionQuotation::select('condition_type')
                ->where('condition_group_id', $condition_group->id)
                ->groupBy('condition_type')
                ->paginate(PER_PAGE);

             $list->map(function ($item) use ($request, $condition_group) {
                $item->condition_type_name = __('condition_quotations.type_' . $item->condition_type);
                $sub_query = ConditionQuotation::where('condition_type', $item->condition_type)
                    ->where('condition_group_id', $condition_group->id)
                    ->orderBy('seq', 'asc')
                    ->search($request->s)
                    ->get();
                $item->child_list = $sub_query;
                return $item;
            });
        }
        $create_route = 'admin.condition-quotation-short-terms.create';
        $edit_route = 'admin.condition-quotation-short-terms.edit';
        $show_route = 'admin.condition-quotation-short-terms.show';
        $delete_route = 'admin.condition-quotation-short-terms.destroy';
        $param = 'condition_quotation_short_term';
        $view_permission = Actions::View . '_' . Resources::ShortTermConditionQuotation;
        $manage_permission = Actions::Manage . '_' . Resources::ShortTermConditionQuotation;
        $page_title = __('condition_quotations.short_term_page_title');
        return view('admin.condition-quotations.index', [
            'list' => $list,
            's' => $request->s,
            'create_route' => $create_route,
            'edit_route' => $edit_route,
            'delete_route' => $delete_route,
            'show_route' => $show_route,
            'param' => $param,
            'page_title' => $page_title,
            'view_permission' => $view_permission,
            'manage_permission' => $manage_permission,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermConditionQuotation);
        $condition_group = ConditionQuotationTrait::getConditionGroup(ConditionGroupEnum::SHORT_TERM_RENTAL);
        $d = new ConditionQuotation();
        $d->status = STATUS_ACTIVE;
        $d->condition_group_id = ($condition_group) ? $condition_group->id : null;
        $sub_condition_checklist = new ConditionQuotationChecklist();
        $list_type = ConditionQuotationTrait::getShortTermServiceType();
        $page_title =  __('lang.create') . __('condition_quotations.short_term_page_title');
        $redirect_route = route('admin.condition-quotation-short-terms.index');
        $store_route = route('admin.condition-quotation-short-terms.store');
        $manage_permission = Actions::Manage . '_' . Resources::ShortTermConditionQuotation;
        return view('admin.condition-quotations.form', [
            'd' => $d,
            'page_title' => $page_title,
            'sub_condition_checklist' => $sub_condition_checklist,
            'list_type' => $list_type,
            'redirect_route' => $redirect_route,
            'store_route' => $store_route,
            'manage_permission' => $manage_permission
        ]);
    }

    public function edit(ConditionQuotation $condition_quotation_short_term)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermConditionQuotation);
        $sub_condition_checklist = ConditionQuotationChecklist::where('condition_quotations_id', $condition_quotation_short_term->id)->orderBy('seq', 'asc')->get();
        $sub_condition_checklist->map(function ($item) {
            $item->status  = $item->status == STATUS_INACTIVE ? false : true;
            return $item;
        });
        $list_type = ConditionQuotationTrait::getShortTermServiceType();

        $page_title =  __('lang.edit') . __('condition_quotations.short_term_page_title');
        $redirect_route = route('admin.condition-quotation-short-terms.index');
        $store_route = route('admin.condition-quotation-short-terms.store');
        $manage_permission = Actions::Manage . '_' . Resources::ShortTermConditionQuotation;
        return view('admin.condition-quotations.form', [
            'd' => $condition_quotation_short_term,
            'page_title' => $page_title,
            'sub_condition_checklist' => $sub_condition_checklist,
            'list_type' => $list_type,
            'redirect_route' => $redirect_route,
            'store_route' => $store_route,
            'manage_permission' => $manage_permission
        ]);
    }

    public function show(ConditionQuotation $condition_quotation_short_term)
    {
        $this->authorize(Actions::View . '_' . Resources::ShortTermConditionQuotation);
        $sub_condition_checklist = ConditionQuotationChecklist::where('condition_quotations_id', $condition_quotation_short_term->id)->orderBy('seq', 'asc')->get();
        $sub_condition_checklist->map(function ($item) {
            $item->status  = $item->status == STATUS_INACTIVE ? false : true;
            return $item;
        });
        $list_type = ConditionQuotationTrait::getShortTermServiceType();

        $page_title =  __('lang.edit') . __('condition_quotations.short_term_page_title');
        $redirect_route = route('admin.condition-quotation-short-terms.index');
        $store_route = route('admin.condition-quotation-short-terms.store');
        return view('admin.condition-quotations.form', [
            'd' => $condition_quotation_short_term,
            'page_title' => $page_title,
            'sub_condition_checklist' => $sub_condition_checklist,
            'view' => true,
            'list_type' => $list_type,
            'redirect_route' => $redirect_route,
            'store_route' => $store_route,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermConditionQuotation);
        $validator = Validator::make($request->all(), [
            'condition_type' => [
                'required',
            ],
            'name' => [
                'required', 'max:255',
            ],
            'seq' => [
                'required', 'integer',
            ],
            'sub_condition_checklist.*.name' => [
                'required', 'max:255',
            ],
            'sub_condition_checklist.*.seq' => [
                'required', 'integer', 'distinct'
            ],

        ], [], [
            'condition_type' => __('condition_quotations.condition_type'),
            'seq' => __('condition_quotations.condition_seq'),
            'sub_condition_checklist.*.name' => __('condition_quotations.checklist_name'),
            'sub_condition_checklist.*.seq' => __('condition_quotations.checklist_seq'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        if (!empty($request->sub_condition_checklist)) {
            $arr = [];
            foreach ($request->sub_condition_checklist as $data) {
                array_push($arr, $data['seq']);
            }
            if (count($arr) != count(array_unique($arr))) {
                return $this->responseWithCode(false, 'ลำดับรายการ ต้องไม่ซ้ำกัน', null, 422);
            }
        }

        $condition_group = ConditionQuotationTrait::getConditionGroup(ConditionGroupEnum::SHORT_TERM_RENTAL);
        $condition_quotation = ConditionQuotation::firstOrNew(['id' => $request->id]);
        $condition_quotation->condition_type = $request->condition_type;
        $condition_quotation->name = $request->name;
        $condition_quotation->condition_group_id = $condition_group->id;
        $condition_quotation->status = ($request->status) ? STATUS_ACTIVE : STATUS_INACTIVE;
        $condition_quotation->seq = $request->seq;
        $condition_quotation->save();

        if ($condition_quotation->id) {
            if ($request->del_checklist != null) {
                ConditionQuotationChecklist::whereIn('id', $request->del_checklist)->delete();
            }
            if (!empty($request->sub_condition_checklist)) {
                foreach ($request->sub_condition_checklist as $item_checklist) {
                    if ($item_checklist['id'] != null) {
                        $condition_quotation_checklist = ConditionQuotationChecklist::firstOrNew(['id' => $item_checklist['id']]);
                    } else {
                        $condition_quotation_checklist = new ConditionQuotationChecklist();
                    }
                    $condition_quotation_checklist->name = $item_checklist['name'];
                    $condition_quotation_checklist->seq = $item_checklist['seq'];
                    $condition_quotation_checklist->condition_quotations_id = $condition_quotation->id;
                    if ($item_checklist['status'] === 'true') {
                        $condition_quotation_checklist->status = STATUS_ACTIVE;
                    } else {
                        $condition_quotation_checklist->status = STATUS_INACTIVE;
                    }
                    $condition_quotation_checklist->save();
                }
            }
        }

        $redirect_route = route('admin.condition-quotation-short-terms.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermConditionQuotation);
        $condition_quotation = ConditionQuotation::find($id);
        $condition_quotation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
