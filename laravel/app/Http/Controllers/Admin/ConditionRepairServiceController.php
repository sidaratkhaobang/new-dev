<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\ConditionGroupEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConditionQuotation;
use App\Models\ConditionQuotationChecklist;
use Illuminate\Support\Facades\Validator;
use App\Traits\ConditionQuotationTrait;

class ConditionRepairServiceController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ConditionRepairService);
        $s = $request->s;
        $condition_group = ConditionQuotationTrait::getConditionGroup(ConditionGroupEnum::REPAIR_SERVICE);
        $list = ConditionQuotation::where('condition_group_id', $condition_group->id)
            ->when($s, function ($query) use ($s) {
                return $query->where('name', 'like', '%' . $s . '%');
            })
            ->orderBy('seq', 'asc')
            ->paginate(PER_PAGE);

        $list->map(function ($item) {
            $sub_query = ConditionQuotationChecklist::where('condition_quotations_id', $item->id)
                ->orderBy('seq', 'asc')
                ->get();
            $item->child_list = $sub_query;
            return $item;
        });

        $page_title = __('condition_quotations.repair_page_title');
        return view('admin.condition-repair-services.index', [
            'list' => $list,
            's' => $request->s,
            'page_title' => $page_title,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::ConditionRepairService);
        $condition_group = ConditionQuotationTrait::getConditionGroup(ConditionGroupEnum::REPAIR_SERVICE);
        $d = new ConditionQuotation();
        $page_title =  __('lang.create') . __('condition_quotations.repair_page_title');
        return view('admin.condition-repair-services.form', [
            'd' => $d,
            'page_title' => $page_title,
            'create' => true,
        ]);
    }

    public function edit(ConditionQuotation $condition_repair_service)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ConditionRepairService);
        $condition_service = ConditionQuotation::where('id', $condition_repair_service->id)
            ->orderBy('seq')
            ->get();
        $condition_service->map(function ($item) {
            $sub_condition_service = ConditionQuotationChecklist::where('condition_quotations_id', $item->id)
                ->orderBy('seq')
                ->get();
            $item->sub_condition_service = $sub_condition_service;
            return $item;
        });

        $page_title =  __('lang.edit') . __('condition_quotations.repair_page_title');
        return view('admin.condition-repair-services.form', [
            'd' => $condition_repair_service,
            'page_title' => $page_title,
            'condition_service' => $condition_service,
        ]);
    }

    public function show(ConditionQuotation $condition_repair_service)
    {
        $this->authorize(Actions::View . '_' . Resources::ConditionRepairService);
        $condition_service = ConditionQuotation::where('id', $condition_repair_service->id)
            ->orderBy('seq')
            ->get();
        $condition_service->map(function ($item) {
            $sub_condition_service = ConditionQuotationChecklist::where('condition_quotations_id', $item->id)
                ->orderBy('seq')
                ->get();
            $item->sub_condition_service = $sub_condition_service;
            return $item;
        });

        $page_title =  __('lang.view') . __('condition_quotations.repair_page_title');
        return view('admin.condition-repair-services.form', [
            'd' => $condition_repair_service,
            'page_title' => $page_title,
            'condition_service' => $condition_service,
            'view' => true,
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ConditionRepairService);
        $condition_quotation = ConditionQuotation::find($id);
        $condition_quotation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data_repair_service' => [
                'required', 'array', 'min:1'
            ],
            'data_repair_service.*.seq' => [
                'required',
            ],
            'data_repair_service.*.name' => [
                'required',
            ],
            // 'data_repair_service.*.sub_condition_service' => [
            //     'required', 'array', 'min:1'
            // ],
            'data_repair_service.*.sub_condition_service.*.seq' => [
                'required',
            ],
            'data_repair_service.*.sub_condition_service.*.name' => [
                'required',
            ],

        ], [], [
            'data_repair_service' => __('condition_quotations.condition_table'),
            'data_repair_service.*.seq' => __('condition_quotations.condition_seq'),
            'data_repair_service.*.name' => __('condition_quotations.condition_name'),
            // 'data_repair_service.*.sub_condition_service' => __('condition_quotations.checklist_table'),
            'data_repair_service.*.sub_condition_service.*.seq' => __('condition_quotations.checklist_seq'),
            'data_repair_service.*.sub_condition_service.*.name' => __('condition_quotations.checklist_name'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        // if ($request->del_section != null) {
        //     ConditionQuotation::whereIn('id', $request->del_section)->delete();
        // }
        if ($request->del_checklist != null) {
            ConditionQuotationChecklist::whereIn('id', $request->del_checklist)->delete();
        }
        if (!empty($request->data_repair_service)) {
            $condition_group = ConditionQuotationTrait::getConditionGroup(ConditionGroupEnum::REPAIR_SERVICE);
            foreach ($request->data_repair_service as $key => $request_repair_service) {
                if (isset($request_repair_service['id'])) {
                    $repair_service = ConditionQuotation::find($request_repair_service['id']);
                } else {
                    $repair_service = new ConditionQuotation();
                }
                // save condition
                $repair_service->condition_group_id = $condition_group->id;
                $repair_service->condition_type = ConditionGroupEnum::REPAIR_SERVICE;
                $repair_service->seq = $request_repair_service['seq'];
                $repair_service->name = $request_repair_service['name'];
                $repair_service->save();

                // save condition checklist
                if (isset($request_repair_service['sub_condition_service']) && sizeof($request_repair_service['sub_condition_service']) > 0) {
                    foreach ($request_repair_service['sub_condition_service'] as $index => $sub_condition) {
                        if (isset($sub_condition['id'])) {
                            $sub_repair_service = ConditionQuotationChecklist::find($sub_condition['id']);
                        } else {
                            $sub_repair_service = new ConditionQuotationChecklist();
                        }
                        $sub_repair_service->condition_quotations_id = $repair_service->id;
                        $sub_repair_service->seq = $sub_condition['seq'];
                        $sub_repair_service->name = $sub_condition['name'];
                        $sub_repair_service->save();
                    }
                }
            }
        }

        $redirect_route = route('admin.condition-repair-services.index');
        return $this->responseValidateSuccess($redirect_route);
    }
}
