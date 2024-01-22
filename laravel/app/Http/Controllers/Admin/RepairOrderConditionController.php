<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\RepairStatusEnum;
use App\Enums\RepairEnum;
use App\Enums\ConditionGroupEnum;
use App\Models\RepairOrder;
use App\Models\Repair;
use App\Models\ConditionQuotation;
use App\Models\ConditionQuotationChecklist;
use App\Models\Quotation;
use App\Models\QuotationForm;
use App\Models\QuotationFormChecklist;
use App\Traits\ConditionQuotationTrait;
use Illuminate\Support\Facades\Validator;

class RepairOrderConditionController extends Controller
{
    public function edit(RepairOrder $repair_order_condition)
    {
        $quotation = Quotation::where('reference_type', RepairOrder::class)->where('reference_id', $repair_order_condition->id)->first();
        if ($quotation) {
            $condition_repair = QuotationForm::where('quotation_id', $quotation->id)->orderBy('seq', 'asc')
                ->get()->map(function ($item) {
                    $sub_query = QuotationFormChecklist::where('quotation_form_id', $item->id)
                        ->orderBy('seq', 'asc')
                        ->get();
                    $item->sub_condition_repair = $sub_query;
                    return $item;
                });
        } else {
            $condition_group = ConditionQuotationTrait::getConditionGroup(ConditionGroupEnum::REPAIR_SERVICE);
            $condition_repair =  ConditionQuotation::where('condition_group_id', $condition_group->id)
                ->orderBy('seq', 'asc')
                ->get()->map(function ($item) {
                    $sub_query = ConditionQuotationChecklist::where('condition_quotations_id', $item->id)
                        ->orderBy('seq', 'asc')
                        ->get();
                    $item->sub_condition_repair = $sub_query;
                    return $item;
                });
            $quotation = new Quotation();
        }

        $repair = Repair::find($repair_order_condition->repair_id);
        if (strcmp($repair->open_by, RepairEnum::REPAIR_DEPARTMENT) == 0) {
            $redirect_route = 'admin.repair-orders.edit';
            $param = 'repair_order';
        } elseif (strcmp($repair->open_by, RepairEnum::CALL_CENTER) == 0) {
            $redirect_route = 'admin.call-center-repair-orders.edit';
            $param = 'call_center_repair_order';
        }

        $route_group = [
            'tab_repair_order' => route($redirect_route, [$param => $repair_order_condition]),
            'tab_condition' => route('admin.repair-order-conditions.edit', ['repair_order_condition' => $repair_order_condition]),
        ];
        $page_title =  __('lang.edit') . __('repair_orders.page_title');
        return view('admin.repair-orders.condition-form', [
            'd' => $repair_order_condition,
            'page_title' => $page_title,
            'route_group' => $route_group,
            'condition_repair' => $condition_repair,
            'quotation' => $quotation,
        ]);
    }

    public function show(RepairOrder $repair_order_condition)
    {
        $quotation = Quotation::where('reference_type', RepairOrder::class)->where('reference_id', $repair_order_condition->id)->first();
        if ($quotation) {
            $condition_repair = QuotationForm::where('quotation_id', $quotation->id)->orderBy('seq', 'asc')
                ->get()->map(function ($item) {
                    $sub_query = QuotationFormChecklist::where('quotation_form_id', $item->id)
                        ->orderBy('seq', 'asc')
                        ->get();
                    $item->sub_condition_repair = $sub_query;
                    return $item;
                });
        } else {
            $condition_group = ConditionQuotationTrait::getConditionGroup(ConditionGroupEnum::REPAIR_SERVICE);
            $condition_repair =  ConditionQuotation::where('condition_group_id', $condition_group->id)
                ->orderBy('seq', 'asc')
                ->get()->map(function ($item) {
                    $sub_query = ConditionQuotationChecklist::where('condition_quotations_id', $item->id)
                        ->orderBy('seq', 'asc')
                        ->get();
                    $item->sub_condition_repair = $sub_query;
                    return $item;
                });
            $quotation = new Quotation();
        }

        $repair = Repair::find($repair_order_condition->repair_id);
        if (strcmp($repair->open_by, RepairEnum::REPAIR_DEPARTMENT) == 0) {
            $redirect_route = 'admin.repair-orders.show';
            $param = 'repair_order';
        }
        if (strcmp($repair->open_by, RepairEnum::CALL_CENTER) == 0) {
            $redirect_route = 'admin.call-center-repair-orders.show';
            $param = 'call_center_repair_order';
        }
        if (in_array($repair_order_condition->status, [RepairStatusEnum::WAIT_APPROVE_QUOTATION, RepairStatusEnum::REJECT_QUOTATION])) {
            $redirect_route = 'admin.repair-quotation-approves.show';
            $param = 'repair_quotation_approve';
        }

        $route_group = [
            'tab_repair_order' => route($redirect_route, [$param => $repair_order_condition]),
            'tab_condition' => route('admin.repair-order-conditions.show', ['repair_order_condition' => $repair_order_condition]),
        ];
        $page_title =  __('lang.view') . __('repair_orders.page_title');
        return view('admin.repair-orders.condition-form', [
            'd' => $repair_order_condition,
            'page_title' => $page_title,
            'route_group' => $route_group,
            'condition_repair' => $condition_repair,
            'quotation' => $quotation,
            'view' => true,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data_repair_service.*.seq' => [
                'required',
            ],
            'data_repair_service.*.name' => [
                'required',
            ],
            'data_repair_service.*.sub_condition_repair.*.seq' => [
                'required',
            ],
            'data_repair_service.*.sub_condition_repair.*.name' => [
                'required',
            ],

        ], [], [
            'data_repair_service.*.seq' => __('condition_quotations.condition_seq'),
            'data_repair_service.*.name' => __('condition_quotations.condition_name'),
            'data_repair_service.*.sub_condition_repair.*.seq' => __('condition_quotations.checklist_seq'),
            'data_repair_service.*.sub_condition_repair.*.name' => __('condition_quotations.checklist_name'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        if ($request->del_section != null) {
            QuotationForm::whereIn('id', $request->del_section)->delete();
        }
        if ($request->del_checklist != null) {
            QuotationFormChecklist::whereIn('id', $request->del_checklist)->delete();
        }
        if (!empty($request->data_repair_service)) {
            $quotation = Quotation::firstOrNew(['id' => $request->quotation_id]);
            $quotation->qt_type = 'DRAFT';
            $quotation->reference_type = RepairOrder::class;
            $quotation->reference_id = $request->id;
            $quotation->save();

            foreach ($request->data_repair_service as $key => $request_repair_service) {
                $quotation_form = QuotationForm::find($request_repair_service['id']);
                if (!isset($quotation_form)) {
                    $quotation_form = new QuotationForm();
                }
                // save quotation
                $quotation_form->quotation_id = $quotation->id;
                $quotation_form->name = $request_repair_service['name'];
                $quotation_form->seq = $request_repair_service['seq'];
                $quotation_form->save();

                // save quotation checklist
                if (isset($request_repair_service['sub_condition_repair']) && sizeof($request_repair_service['sub_condition_repair']) > 0) {
                    foreach ($request_repair_service['sub_condition_repair'] as $index => $sub_condition) {
                        $quotation_form_checklist = QuotationFormChecklist::find($sub_condition['id']);
                        if (!isset($quotation_form_checklist)) {
                            $quotation_form_checklist = new QuotationFormChecklist();
                        }
                        $quotation_form_checklist->quotation_form_id = $quotation_form->id;
                        $quotation_form_checklist->name = $sub_condition['name'];
                        $quotation_form_checklist->seq = $sub_condition['seq'];
                        $quotation_form_checklist->save();
                    }
                }
            }
        }

        $redirect_route = route('admin.repair-orders.index');
        return $this->responseValidateSuccess($redirect_route);
    }
}
