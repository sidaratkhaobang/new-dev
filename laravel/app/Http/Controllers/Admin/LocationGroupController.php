<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LocationGroup;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class LocationGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::LocationGroup);
        $list = LocationGroup::select('location_groups.*')
        ->sortable('name')
        ->search($request->s)
        ->paginate(PER_PAGE);
        return view('admin.location-groups.index', [
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
        $this->authorize(Actions::Manage . '_' . Resources::LocationGroup);
        $d = new LocationGroup();
        $page_title = __('lang.create') . __('location_groups.page_title');
        return view('admin.location-groups.form', compact('d', 'page_title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::LocationGroup);
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('location_groups', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],

        ], [], [
            'name' => __('location_groups.name'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $location_group = LocationGroup::firstOrNew(['id' => $request->id]);
        $location_group->name = $request->name;
        $location_group->status = STATUS_ACTIVE;
        $location_group->save();

        $redirect_route = route('admin.location-groups.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(LocationGroup $location_group)
    {
        $this->authorize(Actions::View . '_' . Resources::LocationGroup);
        $page_title = __('lang.view') . __('location_groups.page_title');
        $view = true;
        return view('admin.location-groups.form', [
            'd' => $location_group,
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
    public function edit(LocationGroup $location_group)
    {
        $this->authorize(Actions::Manage . '_' . Resources::LocationGroup);
        $page_title = __('lang.edit') . __('location_groups.page_title');
        return view('admin.location-groups.form', [
            'd' => $location_group,
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
        $this->authorize(Actions::Manage . '_' . Resources::LocationGroup);
        $location_group = LocationGroup::find($id);
        $location_group->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
