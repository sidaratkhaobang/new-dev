<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Creditor;
use App\Models\CreditorType;
use App\Models\CreditorTypeRelation;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Enums\Actions;
use App\Enums\Resources;

class CreditorController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Creditor);
        $s = $request->s;
        $creditor_types = $request->creditor_types;
        $name = $request->name;
        $province_id = $request->province_id;
        $creditor_type_list = $this->getCreditorTypeList();
        $province_list = $this->getProvinceList();

        $list = Creditor::leftJoin('creditors_types_relation', 'creditors_types_relation.creditor_id', '=', 'creditors.id')
            ->leftJoin('creditor_types', 'creditor_types.id', '=', 'creditors_types_relation.creditor_type_id')
            ->leftJoin('provinces', 'provinces.id', '=', 'creditors.province_id')
            ->sortable('code')
            ->select(
                'creditors.id',
                'creditors.code',
                'creditors.name',
                'creditors.tel',
                'creditors.credit_terms',
                'provinces.name_th as province',
                DB::raw("group_concat(creditor_types.name  SEPARATOR ', ')  as creditor_types")
            )
            ->groupBy(
                'creditors.id',
                'creditors.code',
                'creditors.name',
                'creditors.tel',
                'creditors.credit_terms',
                'province'
            )
            ->when(!empty($name), function ($query) use ($name) {
                return $query->where('creditors.name', 'like', '%' . $name . '%');;
            })
            ->when(!empty($creditor_types), function ($query) use ($creditor_types) {
                return $query->whereIn('creditors_types_relation.creditor_type_id', $creditor_types);
            })
            ->when(!empty($province_id), function ($query) use ($province_id) {
                return $query->where('creditors.province_id', $province_id);
            })
            ->search($request->s)
            ->paginate(PER_PAGE);

        return view('admin.creditors.index', [
            'list' => $list,
            's' => $s,
            'creditor_types' => $creditor_types,
            'name' => $name,
            'province_id' => $province_id,
            'creditor_type_list' => $creditor_type_list,
            'province_list' => $province_list
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::Creditor);
        $d = new Creditor();
        $page_title = __('lang.create') . __('creditors.page_title');
        $creditor_type_list = $this->getCreditorTypeList();
        $province_list = $this->getProvinceList();
        $creditor_types = [];

        return view('admin.creditors.form', [
            'd' => $d,
            'page_title' => $page_title,
            'creditor_type_list' => $creditor_type_list,
            'creditor_types' => $creditor_types,
            'province_list' => $province_list
        ]);
    }

    public function edit(Creditor $creditor)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Creditor);
        $creditor_types = $this->getCreditorTypeArrayOfCreditor($creditor->id);
        $creditor_type_list = $this->getCreditorTypeList();
        $province_list = $this->getProvinceList();
        $page_title = __('lang.edit') . __('creditors.page_title');
        return view('admin.creditors.form', [
            'd' => $creditor,
            'page_title' => $page_title,
            'creditor_type_list' => $creditor_type_list,
            'creditor_types' => $creditor_types,
            'province_list' => $province_list
        ]);
    }

    public function show(Creditor $creditor)
    {
        $this->authorize(Actions::View . '_' . Resources::Creditor);
        $creditor_types = $this->getCreditorTypeArrayOfCreditor($creditor->id);
        $creditor_type_list = $this->getCreditorTypeList();
        $province_list = $this->getProvinceList();
        $page_title = __('lang.view') . __('creditors.page_title');
        return view('admin.creditors.form', [
            'd' => $creditor,
            'page_title' => $page_title,
            'creditor_type_list' => $creditor_type_list,
            'creditor_types' => $creditor_types,
            'province_list' => $province_list,
            'view' => true
        ]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => [
                'required', 'string', 'max:20',
            ],
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('creditors', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'email' => [
                'nullable', 'email:rfc,dns',
            ],
            'contact_name' => [
                'required', 'string', 'max:255',
            ],
            'credit_terms' => ['nullable', 'int']

        ], [], [
            'code' => __('creditors.code'),
            'name' => __('creditors.name'),
            'email' => __('creditors.email'),
            'contact_name' => __('creditors.contact_name'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $creditor = Creditor::firstOrNew(['id' => $request->id]);
        $creditor->name = $request->name;
        $creditor->code = $request->code;
        $creditor->province_id = $request->province_id;
        $creditor->email = $request->email;
        $creditor->address = $request->address;
        $creditor->tel = $request->tel;
        $creditor->mobile = $request->mobile;
        $creditor->fax = $request->fax;
        $creditor->contact_name = $request->contact_name;
        $creditor->contact_position = $request->contact_position;
        $creditor->contact_address = $request->contact_address;
        $creditor->tax_no = $request->tax_no;
        $creditor->install_duration = $request->install_duration;
        $creditor->credit_terms = $request->credit_terms;
        $creditor->payment_condition = $request->payment_condition;
        $creditor->authorized_sign = $request->authorized_sign;
        $creditor->remark = $request->remark;
        $creditor->status = STATUS_ACTIVE;
        $creditor->save();

        if ($creditor->id) {
            $creditor_type = $this->saveCreditorType($request, $creditor->id);
        }
        $redirect_route = route('admin.creditors.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    private function getCreditorTypeList()
    {
        $creditor_type_list = CreditorType::select('id', 'name')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'value' => $item->id,
                ];
            });
        return $creditor_type_list;
    }

    private function saveCreditorType($request, $creditor_id)
    {
        CreditorTypeRelation::where('creditor_id', $creditor_id)->delete();
        if (!empty($request->creditor_types)) {
            foreach ($request->creditor_types as $creditor_type) {
                $creditor_type_relation = new CreditorTypeRelation();
                $creditor_type_relation->creditor_id = $creditor_id;
                $creditor_type_relation->creditor_type_id = $creditor_type;
                $saved = $creditor_type_relation->save();
            }
        }
        return true;
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Creditor);
        $creditor = Creditor::find($id);
        $creditor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }

    public function getProvinceList()
    {
        $list = Province::select('id', 'name_th as name')
            ->get();
        return $list;
    }

    public function getCreditorTypeArrayOfCreditor($creditor_id)
    {
        return CreditorTypeRelation::join('creditor_types', 'creditor_types.id', '=', 'creditors_types_relation.creditor_type_id')
            ->select('creditor_types.id as id', 'creditor_types.name as name')
            ->where('creditors_types_relation.creditor_id', $creditor_id)
            ->pluck('creditor_types.id')
            ->toArray();
    }
}
