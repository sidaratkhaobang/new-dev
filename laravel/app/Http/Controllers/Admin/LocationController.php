<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\LocationGroup;
use App\Models\Province;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Enums\TransportationTypeEnum;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Location);
        $list = Location::select('locations.*', 'location_groups.name as name_th')
            ->leftjoin('location_groups', 'location_groups.id', '=', 'locations.location_group_id')
            ->sortable('name')
            ->search($request->s)
            ->paginate(PER_PAGE);
        return view('admin.locations.index', [
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
        $this->authorize(Actions::Manage . '_' . Resources::Location);
        $d = new Location();
        $d->status = STATUS_ACTIVE;
        $province_list = Province::select('name_th as name', 'id')->orderBy('name_th')->get();
        $location_group_lists = LocationGroup::all();
        $listStatus = $this->getListStatus();
        $list = $this->getListType();
        $page_title = __('lang.create') . __('locations.page_title');
        return view('admin.locations.form', compact('d', 'page_title', 'province_list', 'location_group_lists', 'listStatus', 'list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Location);
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('locations', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'location_group_id' => [
                'required'
            ],
            'status' => [
                'required'
            ],
            'province' => [
                'required'
            ],
            // 'lat' => [
            //     'required'
            // ],
            // 'lng' => [
            //     'required'
            // ],
            'transportation_types' => [
                'required'
            ],

        ], [], [
            'name' => __('locations.name'),
            'location_group_id' => __('locations.location_group'),
            'status' => __('locations.status'),
            'province' => __('locations.province'),
            // 'lat' => __('locations.lat'),
            // 'lng' => __('locations.lng'),
            'transportation_types' => __('locations.transportation_type'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $locations = Location::firstOrNew(['id' => $request->id]);
        $locations->name = $request->name;
        $locations->can_transportation_car = 0;
        $locations->can_transportation_boat = 0;
        if (!empty($request->transportation_types)) {
            foreach ($request->transportation_types as $transportation_type) {
                if ($transportation_type == TransportationTypeEnum::CAR) {
                    $locations->can_transportation_car = TransportationTypeEnum::CAR;
                } else if ($transportation_type == TransportationTypeEnum::BOAT) {
                    $locations->can_transportation_boat = TransportationTypeEnum::BOAT;
                }
            }
        }
        $locations->location_group_id = $request->location_group_id;
        $locations->province_id = $request->province;
        $locations->lat = $request->lat;
        $locations->lng = $request->lng;
        $locations->status = $request->status;
        $locations->save();

        $redirect_route = route('admin.locations.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Location $location)
    {
        $this->authorize(Actions::View . '_' . Resources::Location);
        $page_title = __('lang.view') . __('locations.page_title');
        $province_list = Province::select('name_th as name', 'id')->orderBy('name_th')->get();
        $location_group_lists = LocationGroup::all();
        $listStatus = $this->getListStatus();
        $list = $this->getListType();
        $view = true;
        return view('admin.locations.form', [
            'd' => $location,
            'view' => $view,
            'page_title' => $page_title,
            'province_list' => $province_list,
            'location_group_lists' => $location_group_lists,
            'listStatus' => $listStatus,
            'list' => $list,

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $location)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Location);
        $page_title = __('lang.edit') . __('locations.page_title');
        $province_list = Province::select('name_th as name', 'id')->orderBy('name_th')->get();
        $location_group_lists = LocationGroup::all();
        $listStatus = $this->getListStatus();
        $list = $this->getListType();
        $view = true;
        return view('admin.locations.form', [
            'd' => $location,
            'page_title' => $page_title,
            'province_list' => $province_list,
            'location_group_lists' => $location_group_lists,
            'listStatus' => $listStatus,
            'list' => $list,
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
        $this->authorize(Actions::Manage . '_' . Resources::Location);
        $location = Location::find($id);
        $location->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }

    private function getListType()
    {
        return collect([
            [
                'id' => 'CAR',
                'value' => TransportationTypeEnum::CAR,
                'name' => __('service_types.car'),
            ],
            [
                'id' => 'BOAT',
                'value' => TransportationTypeEnum::BOAT,
                'name' => __('service_types.boat'),
            ],
        ]);
    }
}
