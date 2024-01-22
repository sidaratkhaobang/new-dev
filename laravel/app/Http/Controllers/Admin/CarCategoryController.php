<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\CarCategory;
use App\Models\CarCategoryType;
use App\Models\CarGroup;
use App\Enums\Actions;
use App\Enums\Resources;

class CarCategoryController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CarCategory);
        $list = CarCategory::leftJoin('car_category_types', 'car_category_types.id', '=', 'car_categories.car_category_type_id')
        ->leftjoin('car_groups','car_groups.id', '=', 'car_categories.car_group_id')
        ->select('car_categories.*', 'car_category_types.name as car_category_type_name','car_groups.name as car_group_name')
        ->sortable('code')
        ->search($request->s, $request)->paginate(PER_PAGE);
        $code_list = CarCategory::select('code as name', 'id')->get();
        $name_list = CarCategory::select('name', 'id')->get();
        $car_group_name_list = CarGroup::select('name', 'id')->get();


        return view('admin.car-categories.index', [
            'list' => $list,
            's' => $request->s,
            'code_list' => $code_list,
            'name_list' => $name_list,
            'code' => $request->code,
            'name' => $request->name,
            'car_group_id' => $request->car_group_id,
            'car_group_name_list' => $car_group_name_list
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarCategory);
        $d = new CarCategory();
        $car_category_types = CarCategoryType::all();
        $car_group_name_list = CarGroup::select('name', 'id')->get();
        $page_title = __('lang.create') . __('car_categories.page_title');
        return view('admin.car-categories.form',  [
            'd' => $d,
            'page_title' => $page_title,
            'car_category_types' => $car_category_types,
            'car_group_name_list' => $car_group_name_list,
        ]);
    }

    public function show(CarCategory $car_category)
    {
        $this->authorize(Actions::View . '_' . Resources::CarCategory);
        $page_title = __('lang.edit') . __('car_categories.page_title');
        $car_category_types = CarCategoryType::all();
        $view = true;
        $car_group_name_list = CarGroup::select('name', 'id')->get();
        return view('admin.car-categories.form', [
            'd' => $car_category,
            'view' => $view,
            'page_title' => $page_title,
            'car_category_types' => $car_category_types,
            'car_group_name_list' => $car_group_name_list,
        ]);
    }

    public function edit(CarCategory $car_category)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarCategory);
        $page_title = __('lang.edit') . __('car_categories.page_title');
        $car_category_types = CarCategoryType::all();
        $car_group_name_list = CarGroup::select('name', 'id')->get();
        return view('admin.car-categories.form', [
            'd' => $car_category,
            'page_title' => $page_title,
            'car_category_types' => $car_category_types,
            'car_group_name_list' => $car_group_name_list,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => [
                'required', 'string', 'max:3',
                Rule::unique('car_categories', 'code')->whereNull('deleted_at')->ignore($request->id),
            ],
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('car_categories', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'car_group_id' => [
                'required'
            ],
            'reserve_small_size' => [
                'required', 'string', 'max:100',
            ],
            'reserve_big_size' => [
                'required', 'string', 'max:100',
            ],

        ], [], [
            'code' => __('car_categories.code'),
            'name' => __('car_categories.name'),
            'car_group_id' => __('car_categories.car_group'),
            'reserve_small_size' => __('car_categories.reserve_small_size'),
            'reserve_big_size' => __('car_categories.reserve_big_size'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $car_category = CarCategory::firstOrNew(['id' => $request->id]);
        $car_category->name = $request->name;
        $car_category->code = $request->code;
        $car_category->car_category_type_id = $request->car_category_type_id;
        $car_category->car_group_id = $request->car_group_id;
        $car_category->reserve_small_size = $request->reserve_small_size;
        $car_category->reserve_big_size = $request->reserve_big_size;
        $car_category->status = STATUS_ACTIVE;
        $car_category->save();

        $redirect_route = route('admin.car-categories.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarCategory);
        $car_category = CarCategory::find($id);
        $car_category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
