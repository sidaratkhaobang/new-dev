<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarType;
use App\Models\CarBrand;
use App\Models\CarCategory;
use App\Models\CarGroup;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Enums\Actions;
use App\Enums\Resources;

class CarTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CarType);
        // $list = CarType::with(['car_categories','car_brands'])
        // // leftJoin('car_categories', 'car_categories.id', '=', 'car_types.car_category_id')
        // // ->leftJoin('car_brands', 'car_brands.id', '=', 'car_types.car_brand_id')
        // // ->select('car_types.*', 'car_categories.name as car_category_name', 'car_brands.name as car_brands_name')
        // ->sortable('code')
        // ->search($request->s , $request)å
        // ->paginate(PER_PAGE);
        // $category_list = CarCategory::select('name', 'id')->get();
        // $brand_list = CarBrand::select('name', 'id')->get();
        $list = CarType::leftJoin('car_categories', 'car_categories.id', '=', 'car_types.car_category_id')
            ->leftJoin('car_brands', 'car_brands.id', '=', 'car_types.car_brand_id')
            ->leftJoin('car_groups', 'car_groups.id', '=', 'car_types.car_group_id')
            ->select('car_types.*', 'car_categories.name as car_category_name', 'car_brands.name as car_brand_name', 'car_groups.name as car_group_name')
            // ->append('car_brands.name')
            ->sortable('code')
            ->search($request->s, $request)
            ->paginate(PER_PAGE);
        $category_list = CarCategory::select('name', 'id')->get();
        $brand_list = CarBrand::select('name', 'id')->get();
        $group_list = CarGroup::select('name', 'id')->get();
        return view('admin.car-types.index', [
            'list' => $list,
            's' => $request->s,
            'category_list' => $category_list,
            'brand_list' => $brand_list,
            'group_list' => $group_list,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'group_id' => $request->group_id,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarType);
        $d = new CarType();
        $car_brand_lists = CarBrand::all();
        $car_category_lists = CarCategory::all();
        $car_group_lists = CarGroup::all();
        $page_title = __('lang.create') . __('car_types.page_title');
        return view('admin.car-types.form', compact('d', 'page_title', 'car_brand_lists', 'car_category_lists', 'car_group_lists'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => [
            'string', 'max:10',
                Rule::unique('car_types', 'code')->whereNull('deleted_at')->ignore($request->id),
            ],
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('car_types', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],

        ], [], [
            'code' => __('car_types.code'),
            'name' => __('car_types.name'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $car_type = CarType::firstOrNew(['id' => $request->id]);
        $car_type->name = $request->name;
        $car_type->code = $request->code;
        $car_type->car_category_id = $request->car_category_id;
        $car_type->car_brand_id = $request->car_brand_id;
        $car_type->car_group_id = $request->car_group_id;
        $car_type->status = STATUS_ACTIVE;
        $car_type->save();

        $redirect_route = route('admin.car-types.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(CarType $car_type)
    {
        $this->authorize(Actions::View . '_' . Resources::CarType);
        $page_title = __('lang.view') . __('car_types.page_title');
        $car_brand_lists = CarBrand::all();
        $car_category_lists = CarCategory::all();
        $car_group_lists = CarGroup::all();
        $view = true;
        return view('admin.car-types.form', [
            'd' => $car_type,
            'view' => $view,
            'page_title' => $page_title,
            'car_brand_lists' => $car_brand_lists,
            'car_category_lists' => $car_category_lists,
            'car_group_lists' => $car_group_lists,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(CarType $car_type)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarType);
        $page_title = __('lang.edit') . __('car_types.page_title');
        $car_brand_lists = CarBrand::all();
        $car_category_lists = CarCategory::all();
        $car_group_lists = CarGroup::all();
        return view('admin.car-types.form', [
            'd' => $car_type,
            'page_title' => $page_title,
            'car_brand_lists' => $car_brand_lists,
            'car_category_lists' => $car_category_lists,
            'car_group_lists' => $car_group_lists,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarType);
        $car_type = CarType::find($id);
        $car_type->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }

    function getDefaultCarGroup(Request $request)
    {
        $carCategory = CarCategory::where('id', $request->car_category_id)->first();
        return [
            'success' => true,
            'car_category_id' => $request->car_category_id,
            'data' => $carCategory->carGroup
        ];
    }
}
