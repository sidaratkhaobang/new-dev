<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CustomerGroup;
use App\Models\GLAccount;
use App\Models\GLAccountCustomerGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class GeneralLedgerAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::GlAccouunt);
        $s = $request->s;
        $customer_group_list = DB::table('gl_accounts_customer_groups')
            ->leftJoin('customer_groups', 'customer_groups.id', '=', 'gl_accounts_customer_groups.customer_group_id')
            ->select('gl_accounts_customer_groups.gl_account_id', DB::raw("group_concat(customer_groups.name SEPARATOR ', ') as customer_group_name"))
            ->groupBy('gl_accounts_customer_groups.gl_account_id');

        $list = GLAccount::select('gl_accounts.id', 'gl_accounts.name', 'gl_accounts.description', 'gl_accounts.account', 'gl_accounts.type', 'branches.name as branch_name', 'customer_group_list.customer_group_name')
            ->leftjoin('branches', 'branches.id', '=', 'gl_accounts.branch_id')
            ->leftJoin('gl_accounts_customer_groups as gl_acc_group', 'gl_acc_group.gl_account_id', '=', 'gl_accounts.id')
            ->leftJoin('customer_groups as customer_groups2', 'customer_groups2.id', '=', 'gl_acc_group.customer_group_id')
            ->leftjoinSub($customer_group_list, 'customer_group_list', function ($join) {
                $join->on('customer_group_list.gl_account_id', '=', 'gl_accounts.id');
            })
            ->when($s, function ($q) use ($s) {
                $q->where('gl_accounts.name', 'like', '%' . $s . '%');
                $q->orWhere('gl_accounts.description', 'like', '%' . $s . '%');
                $q->orWhere('gl_accounts.account', 'like', '%' . $s . '%');
                $q->orWhere('branches.name', 'like', '%' . $s . '%');
                $q->orWhere('customer_groups2.name', 'like', '%' . $s . '%');
            })
            ->sortable('name')
            ->groupBy(
                'gl_accounts.name',
                'gl_accounts.id',
                'customer_group_list.customer_group_name',
                'gl_accounts.description',
                'gl_accounts.account',
                'gl_accounts.type',
                'branch_name'
            )
            ->paginate(PER_PAGE);
        return view('admin.general-ledger-accounts.index', [
            'list' => $list,
            's' => $request->s,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::GlAccouunt);
        $d = new GLAccount();
        $customer_group = [];
        $branch_list = Branch::select('name', 'id')->get();
        $page_title = __('lang.create') . __('general_ledger_accounts.page_title');
        $customer_group_list = CustomerGroup::select('name', 'id')->get();
        return view('admin.general-ledger-accounts.form', compact('d', 'page_title', 'branch_list', 'customer_group_list', 'customer_group'));
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::GlAccouunt);
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('gl_accounts', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'account' => [
                'required'
            ],

        ], [], [
            'name' => __('general_ledger_accounts.name'),
            'account' => __('general_ledger_accounts.account'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $gl_account = GLAccount::firstOrNew(['id' => $request->id]);
        $gl_account->name = $request->name;
        $gl_account->branch_id = $request->branch_id;
        $gl_account->account = $request->account;
        $gl_account->description = $request->description;
        $gl_account->save();

        if ($gl_account->id) {
            $customer_group_relation = $this->saveCustomerGroupRelation($request, $gl_account->id);
        }

        $redirect_route = route('admin.general-ledger-accounts.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    private function saveCustomerGroupRelation($request, $gl_account_id)
    {
        GLAccountCustomerGroup::where('gl_account_id', $gl_account_id)->delete();
        if (!empty($request->customer_group)) {
            foreach ($request->customer_group as $customer_group) {
                $customer_group_relation = new GLAccountCustomerGroup();
                $customer_group_relation->gl_account_id = $gl_account_id;
                $customer_group_relation->customer_group_id = $customer_group;
                $customer_group_relation->save();
            }
        }
        return true;
    }

    public function show(GLAccount $general_ledger_account)
    {
        $this->authorize(Actions::View . '_' . Resources::GlAccouunt);
        $customer_group = $this->getCustomerGroupArray($general_ledger_account->id);
        $page_title = __('lang.view') . __('general_ledger_accounts.page_title');
        $branch_list = Branch::select('name', 'id')->get();
        $customer_group_list = CustomerGroup::select('name', 'id')->get();
        $view = true;
        return view('admin.general-ledger-accounts.form', [
            'd' => $general_ledger_account,
            'view' => $view,
            'page_title' => $page_title,
            'branch_list' => $branch_list,
            'customer_group_list' => $customer_group_list,
            'customer_group' => $customer_group,
        ]);
    }

    public function edit(GLAccount $general_ledger_account)
    {
        $this->authorize(Actions::Manage . '_' . Resources::GlAccouunt);
        $customer_group = $this->getCustomerGroupArray($general_ledger_account->id);
        $page_title = __('lang.edit') . __('general_ledger_accounts.page_title');
        $branch_list = Branch::select('name', 'id')->get();
        $customer_group_list = CustomerGroup::select('name', 'id')->get();
        return view('admin.general-ledger-accounts.form', [
            'd' => $general_ledger_account,
            'page_title' => $page_title,
            'branch_list' => $branch_list,
            'customer_group_list' => $customer_group_list,
            'customer_group' => $customer_group,
        ]);
    }

    public function getCustomerGroupArray($customer_group)
    {
        return GLAccountCustomerGroup::leftJoin('customer_groups', 'customer_groups.id', '=', 'gl_accounts_customer_groups.customer_group_id')
            ->select('customer_groups.id as id', 'customer_groups.name as name')
            ->where('gl_accounts_customer_groups.gl_account_id', $customer_group)
            ->pluck('gl_accounts.id')
            ->toArray();
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::GlAccouunt);
        $general_ledger_account = GLAccount::find($id);
        $general_ledger_account->delete();

        return $this->responseComplete();
    }
}
