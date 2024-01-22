<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarGroup;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Enums\Actions;
use App\Enums\Resources;

class CarGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CarGroup);
        $list = CarGroup::select('car_groups.*')
        ->sortable('name')
        ->search($request->s)
        ->paginate(PER_PAGE);
        return view('admin.car-groups.index', [
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
        $this->authorize(Actions::Manage . '_' . Resources::CarGroup);
        $d = new CarGroup();
        $page_title = __('lang.create') . __('car_groups.page_title');
        return view('admin.car-groups.form', compact('d', 'page_title'));
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
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('car_groups', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],

        ], [], [
            'name' => __('car_groups.name'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $car_group = CarGroup::firstOrNew(['id' => $request->id]);
        $car_group->name = $request->name;
        $car_group->save();

        $redirect_route = route('admin.car-groups.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(CarGroup $car_group)
    {
        $this->authorize(Actions::View . '_' . Resources::CarGroup);
        $page_title = __('lang.view') . __('car_groups.page_title');
        $view = true;
        return view('admin.car-groups.form', [
            'd' => $car_group,
            'view' => $view,
            'page_title' => $page_title,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(CarGroup $car_group)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarGroup);
        $page_title = __('lang.view') . __('car_groups.page_title');
        return view('admin.car-groups.form', [
            'd' => $car_group,
            'page_title' => $page_title,
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
        $this->authorize(Actions::Manage . '_' . Resources::CarGroup);
        $car_group = CarGroup::find($id);
        $car_group->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
