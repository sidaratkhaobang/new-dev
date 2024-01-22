<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverWageCategory;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class DriverWageCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::DriverWageCategory);
        $list = DriverWageCategory::select('driver_wage_categories.id', 'driver_wage_categories.name', 'driver_wage_categories.status')
            ->sortable('name')
            ->search($request->s)
            ->paginate(PER_PAGE);
        return view('admin.driver-wage-categories.index', [
            'list' => $list,
            's' => $request->s,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::DriverWageCategory);
        $d = new DriverWageCategory();
        $d->status = STATUS_ACTIVE;
        $listStatus = $this->getListStatus();
        $page_title = __('lang.create') . __('driver_wage_categories.page_title');
        return view('admin.driver-wage-categories.form', [
            'd' => $d,
            'page_title' => $page_title,
            'listStatus' => $listStatus,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::DriverWageCategory);
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('driver_wage_categories', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'status' => [
                'required'
            ],
        ], [], [
            'name' => __('driver_wage_categories.name'),
            'status' => __('driver_wage_categories.status'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $driver_wage_categories = DriverWageCategory::firstOrNew(['id' => $request->id]);
        $driver_wage_categories->name = $request->name;
        $driver_wage_categories->status = $request->status;
        $driver_wage_categories->save();

        $redirect_route = route('admin.driver-wage-categories.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(DriverWageCategory $driver_wage_category)
    {
        $this->authorize(Actions::View . '_' . Resources::DriverWageCategory);
        $page_title = __('lang.view') . __('driver_wage_categories.page_title');
        $listStatus = $this->getListStatus();
        $view = true;
        return view('admin.driver-wage-categories.form', [
            'd' => $driver_wage_category,
            'view' => $view,
            'page_title' => $page_title,
            'listStatus' => $listStatus,

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(DriverWageCategory $driver_wage_category)
    {
        $this->authorize(Actions::Manage . '_' . Resources::DriverWageCategory);
        $page_title = __('lang.edit') . __('driver_wage_categories.page_title');
        $listStatus = $this->getListStatus();
        return view('admin.driver-wage-categories.form', [
            'd' => $driver_wage_category,
            'page_title' => $page_title,
            'listStatus' => $listStatus,

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
        $this->authorize(Actions::Manage . '_' . Resources::DriverWageCategory);
        $driver_wage_categories = DriverWageCategory::find($id);
        $driver_wage_categories->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
