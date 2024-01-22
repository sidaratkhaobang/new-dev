<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RepairList;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RepairListController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::RepairList);
        $list = RepairList::search($request)
            ->orderBy('code')
            ->paginate(PER_PAGE);
        $code = $request->code;
        $code_name = null;
        if ($code) {
            $repair_list = RepairList::find($code);
            $code_name = $repair_list ? $repair_list->code : null;
        }
        $name = $request->name;
        $name_text = null;
        if ($name) {
            $repair_list = RepairList::find($name);
            $name_text = $repair_list ? $repair_list->name : null;
        }

        $status_list =
            collect([
                (object) [
                    'id' => STATUS_ACTIVE,
                    'name' => __('repair_lists.status_' . STATUS_ACTIVE),
                    'value' => STATUS_ACTIVE,
                ],
                (object) [
                    'id' => STATUS_INACTIVE,
                    'name' => __('repair_lists.status_' . STATUS_INACTIVE),
                    'value' => STATUS_INACTIVE,
                ],
            ]);
        $page_title = __('repair_lists.page_title');
        return view('admin.repair-lists.index', [
            'list' => $list,
            'page_title' => $page_title,
            'code' => $code,
            'code_name' => $code_name,
            'name' => $name,
            'name_text' => $name_text,
            'status' => $request->status,
            'status_list' => $status_list,
        ]);
    }

    public function create()
    {
        $d = new RepairList();
        $d->status = STATUS_ACTIVE;
        $page_title = __('lang.create') . __('repair_lists.page_title');
        return view('admin.repair-lists.form', [
            'd' => $d,
            'page_title' => $page_title,
        ]);
    }

    public function edit(RepairList $repair_list)
    {
        $this->authorize(Actions::Manage . '_' . Resources::RepairList);
        $page_title =  __('lang.edit') . __('repair_lists.page_title');
        return view('admin.repair-lists.form', [
            'd' => $repair_list,
            'page_title' => $page_title,
        ]);
    }

    public function show(RepairList $repair_list)
    {
        $this->authorize(Actions::View . '_' . Resources::RepairList);
        $page_title =  __('lang.view') . __('repair_lists.page_title');
        return view('admin.repair-lists.form', [
            'd' => $repair_list,
            'page_title' => $page_title,
            'view' => true,
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::RepairList);
        $repair_list = RepairList::find($id);
        $repair_list->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::RepairList);
        $validator = Validator::make($request->all(), [
            'code' => [
                'required',
                Rule::unique('repair_lists', 'code')->whereNull('deleted_at')->ignore($request->id),
            ],
            'name' => [
                'required',
            ],

        ], [], [
            'code' => __('repair_lists.code'),
            'name' =>  __('repair_lists.name'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $repair_list = RepairList::firstOrNew(['id' => $request->id]);
        $repair_list->code = $request->code;
        $repair_list->name = $request->name;
        $price = str_replace(',', '', $request->price);
        $repair_list->price = $price;
        $repair_list->status = $request->status;
        $repair_list->save();

        $redirect_route = route('admin.repair-lists.index');
        return $this->responseValidateSuccess($redirect_route);
    }
}
