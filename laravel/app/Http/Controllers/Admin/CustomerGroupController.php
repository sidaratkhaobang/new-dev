<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\CustomerGroup;
use App\Enums\Actions;
use App\Enums\Resources;

class CustomerGroupController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CustomerGroup);
        $list = CustomerGroup::sortable('name')
            ->search($request->s)->paginate(PER_PAGE);

        return view('admin.customer-groups.index', [
            's' => $request->s,
            'list' => $list,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::CustomerGroup);
        $d = new CustomerGroup();
        $page_title = __('lang.create') . __('customer_groups.page_title');

        return view('admin.customer-groups.form', [
            'd' => $d,
            'page_title' => $page_title
        ]);
    }

    public function edit(CustomerGroup $customer_group)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CustomerGroup);
        $page_title = __('lang.edit') . __('customer_groups.page_title');
        return view('admin.customer-groups.form', [
            'd' => $customer_group,
            'page_title' => $page_title,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('customer_groups', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
        ], [], [
            'name' => __('customer_groups.name')
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $customer_group = CustomerGroup::firstOrNew(['id' => $request->id]);
        $customer_group->name = $request->name;
        $customer_group->status = STATUS_ACTIVE;
        $customer_group->save();

        $redirect_route = route('admin.customer-groups.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(CustomerGroup $customer_group)
    {
        $this->authorize(Actions::View . '_' . Resources::CustomerGroup);
        $page_title = __('lang.view') . __('customer_groups.page_title');
        return view('admin.customer-groups.form', [
            'd' => $customer_group,
            'page_title' => $page_title,
            'view' => true
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CustomerGroup);
        $customer_group = CustomerGroup::find($id);
        $customer_group->delete();

        return $this->responseComplete();
    }
}
