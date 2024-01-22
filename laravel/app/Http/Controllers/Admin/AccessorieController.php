<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccessoryTypeEnum;
use App\Enums\Actions;
use App\Enums\CreditorTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Accessories;
use App\Models\Creditor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AccessorieController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Accessory);
        $creditor_id = $request->creditor_id;
        $list = Accessories::leftjoin('creditors', 'creditors.id', '=', 'accessories.creditor_id')
            ->leftJoin('creditors_types_relation', 'creditors_types_relation.creditor_id', '=', 'creditors.id')
            ->leftJoin('creditor_types', 'creditor_types.id', '=', 'creditors_types_relation.creditor_type_id')
            ->select('accessories.*', 'creditors.name as dealer_name')
            ->where('creditor_types.type', CreditorTypeEnum::DEALER)
            ->when($creditor_id, function ($query) use ($creditor_id) {
                $query->where('creditors.id', $creditor_id);
            })
            ->sortable('code')
            ->search($request->s, $request)
            ->paginate(PER_PAGE);

        $dealer_lists = $this->getDealers();
        return view('admin.accessories.index', [
            'list' => $list,
            's' => $request->s,
            'dealer_lists' => $dealer_lists,
            'creditor_id' => $request->creditor_id,
        ]);
    }

    public function getDealers()
    {
        return Creditor::leftjoin('creditors_types_relation', 'creditors_types_relation.creditor_id', '=', 'creditors.id')
            ->leftjoin('creditor_types', 'creditor_types.id', '=', 'creditors_types_relation.creditor_type_id')
            ->select('creditors.id', 'creditors.name')
            ->where('creditor_types.type', CreditorTypeEnum::DEALER)
            ->where('creditors.status', STATUS_ACTIVE)
            ->orderBy('creditors.name')
            ->get();
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::Accessory);
        $d = new Accessories();
        $dealers = $this->getDealers();
        $type_list = $this->getAccessoryTypeList();
        $page_title = __('lang.create') . __('accessories.page_title');
        return view('admin.accessories.form', compact('d', 'page_title', 'dealers','type_list'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => [
                'nullable', 'string', 'max:10',
                Rule::unique('accessories', 'code')->whereNull('deleted_at')->ignore($request->id),
            ],
            'name' => [
                'required', 'string', 'max:255',
                // Rule::unique('accessories', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'version' => [
                'required', 'string', 'max:255',
                // Rule::unique('accessories')->where(function ($query) use($request) {
                //     return $query->where('name', $request->name)
                //     ->where('version', $request->version);
                // }),
            ],
            'price' => [
                'required', 'numeric',
            ],

        ], [], [
            'code' => __('accessories.code'),
            'name' => __('accessories.name'),
            'version' => __('accessories.version'),
            'price' => __('accessories.price'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $accessories = Accessories::firstOrNew(['id' => $request->id]);
        $accessories->name = $request->name;
        $accessories->code = $request->code;
        $accessories->version = $request->version;
        $accessories->price = $request->price;
        $accessories->creditor_id = $request->creditor_id;
        $accessories->accessory_type = $request->accessory_type;
        $accessories->status = STATUS_ACTIVE;
        $accessories->save();

        $redirect_route = route('admin.accessories.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(Accessories $accessory)
    {
        $this->authorize(Actions::View . '_' . Resources::Accessory);
        $page_title = __('lang.view') . __('accessories.page_title');
        $dealers = $this->getDealers();
        $type_list = $this->getAccessoryTypeList();
        $view = true;
        return view('admin.accessories.form', [
            'd' => $accessory,
            'view' => $view,
            'page_title' => $page_title,
            'dealers' => $dealers,
            'type_list' => $type_list,
        ]);
    }

    function getAccessoryTypeList()
    {
        return collect([
            [
                'id' => AccessoryTypeEnum::GPS,
                'value' => AccessoryTypeEnum::GPS,
                'name' => AccessoryTypeEnum::GPS,
            ],
            [
                'id' => AccessoryTypeEnum::DVR,
                'value' => AccessoryTypeEnum::DVR,
                'name' => AccessoryTypeEnum::DVR,
            ],
            [
                'id' => AccessoryTypeEnum::NONE,
                'value' => AccessoryTypeEnum::NONE,
                'name' => AccessoryTypeEnum::NONE,
            ],
        ]);
    }

    public function edit(Accessories $accessory)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Accessory);
        $page_title = __('lang.edit') . __('accessories.page_title');
        $dealers = $this->getDealers();
        $type_list = $this->getAccessoryTypeList();
        return view('admin.accessories.form', [
            'd' => $accessory,
            'page_title' => $page_title,
            'dealers' => $dealers,
            'type_list' => $type_list,
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Accessory);
        $accessories = Accessories::find($id);
        $accessories->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
