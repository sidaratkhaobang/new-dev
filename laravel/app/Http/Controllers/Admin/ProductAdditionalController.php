<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\ProductAdditional;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ProductAdditionalController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ProductAdditional);
        $list = ProductAdditional::select('product_additionals.*')
            ->sortable('name')
            ->search($request->s)->paginate(PER_PAGE);
        return view('admin.product-additionals.index', [
            'list' => $list,
            's' => $request->s,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::ProductAdditional);
        $d = new ProductAdditional();
        $yes_no_list = getYesNoList();
        $page_title = __('lang.create') . __('product_additionals.page_title');
        return view('admin.product-additionals.form', [
            'd' => $d,
            'page_title' => $page_title,
            'yes_no_list' => $yes_no_list,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ProductAdditional);
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('product_additionals', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'price' => [
                'required',
            ],

        ], [], [
            'name' => __('product_additionals.name'),
            'price' => __('product_additionals.price'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $product_additional = ProductAdditional::firstOrNew(['id' => $request->id]);
        $product_additional->name = $request->name;
        $price = str_replace(',', '', $request->price);
        $product_additional->price = $price;
        $product_additional->is_stock = boolval($request->is_stock);
        $product_additional->amount = $request->amount;
        $product_additional->status = STATUS_ACTIVE;
        $product_additional->save();

        $redirect_route = route('admin.product-additionals.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function edit(ProductAdditional $product_additional)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ProductAdditional);
        $yes_no_list = getYesNoList();
        $page_title = __('lang.edit') . __('product_additionals.page_title');
        return view('admin.product-additionals.form', [
            'd' => $product_additional,
            'page_title' => $page_title,
            'yes_no_list' => $yes_no_list,
        ]);
    }

    public function show(ProductAdditional $product_additional)
    {
        $this->authorize(Actions::View . '_' . Resources::ProductAdditional);
        $page_title = __('lang.view') . __('product_additionals.page_title');
        $yes_no_list = getYesNoList();
        return view('admin.product-additionals.form', [
            'd' => $product_additional,
            'view' => true,
            'page_title' => $page_title,
            'yes_no_list' => $yes_no_list,
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ProductAdditional);
        $product_additional = ProductAdditional::find($id);
        $product_additional->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
