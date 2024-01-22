<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceType;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Enums\TransportationTypeEnum;

class ServiceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ServiceType);
        $list = ServiceType::select('service_types.*')
            ->sortable('name')
            ->search($request->s)
            ->paginate(PER_PAGE);
        return view('admin.service-types.index', [
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
        $this->authorize(Actions::Manage . '_' . Resources::ServiceType);
        $d = new ServiceType();
        $list = $this->getListType();
        $listcan = $this->getListCan();
        $count_files = 0;
        $service_images_files = [];
        $page_title = __('lang.create') . __('service_types.page_title');
        return view('admin.service-types.form', [
            'd' => $d,
            'page_title' => $page_title,
            'list' => $list,
            'listcan' => $listcan,
            'count_files' => $count_files,
            'service_images_files' => $service_images_files,
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
        $this->authorize(Actions::Manage . '_' . Resources::ServiceType);
        // $validator = Validator::make($request->all(), [
            // 'name' => [
            //     'required', 'string', 'max:255',
            //     Rule::unique('service_types', 'name')->whereNull('deleted_at')->ignore($request->id),
            // ],
            // 'transportation_type' => ['required'],
            // 'can_rental_over_days' => ['required'],
            // 'can_add_stopover' => ['required'],
            // 'can_add_driver' => ['required'],
            // 'can_add_products' => ['required'],
            // 'can_add_transport_goods' => ['required'],
            // 'can_add_passengers' => ['required'],

        // ], [], [
            // 'name' => __('service_types.name'),
            // 'transportation_type' => __('service_types.transportation_type'),
            // 'can_rental_over_days' => __('service_types.can_rental_over_days'),
            // 'can_add_stopover' => __('service_types.can_add_stopover'),
            // 'can_add_driver' => __('service_types.can_add_driver'),
            // 'can_add_products' => __('service_types.can_add_products'),
            // 'can_add_transport_goods' => __('service_types.can_add_transport_goods'),
            // 'can_add_passengers' => __('service_types.can_add_passengers'),
        // ]);

        // if ($validator->stopOnFirstFailure()->fails()) {
        //     return $this->responseValidateFailed($validator);
        // }

        if ($request->count_files <= 0) {
            $validator = Validator::make($request->all(), [
                'service_images' => [
                    'required'
                ],
            ], [], [
                'service_images' => __('service_types.images'),
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        $service_type = ServiceType::firstOrNew(['id' => $request->id]);
        // $service_type->name = $request->name;
        // $service_type->transportation_type = $request->transportation_type;
        // $service_type->can_rental_over_days = $request->can_rental_over_days;
        // $service_type->can_add_stopover = $request->can_add_stopover;
        // $service_type->can_add_driver = $request->can_add_driver;
        // $service_type->can_add_products = $request->can_add_products;
        // $service_type->can_add_transport_goods = $request->can_add_transport_goods;
        // $service_type->can_add_passengers = $request->can_add_passengers;
        $service_type->status = STATUS_ACTIVE;
        $service_type->save();

        if ($request->service_images__pending_delete_ids) {
            $pending_delete_ids = $request->service_images__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $service_type->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('service_images')) {
            foreach ($request->file('service_images') as $image) {
                if ($image->isValid()) {
                    $service_type->addMedia($image)->toMediaCollection('service_images');
                }
            }
        }

        $redirect_route = route('admin.service-types.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ServiceType $service_type)
    {
        $this->authorize(Actions::View . '_' . Resources::ServiceType);
        $page_title = __('lang.view') . __('service_types.page_title');
        $view = true;
        $list = $this->getListType();
        $listcan = $this->getListCan();
        $service_images_files = $service_type->getMedia('service_images');
        $service_images_files = get_medias_detail($service_images_files);
        $count_files = count($service_images_files);
        return view('admin.service-types.form', [
            'd' => $service_type,
            'view' => $view,
            'page_title' => $page_title,
            'list' => $list,
            'listcan' => $listcan,
            'service_images_files' => $service_images_files,
            'view_only' => true,
            'count_files' => $count_files,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(ServiceType $service_type)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ServiceType);
        $page_title = __('lang.edit') . __('service_types.page_title');
        $list = $this->getListType();
        $listcan = $this->getListCan();
        $service_images_files = $service_type->getMedia('service_images');
        $service_images_files = get_medias_detail($service_images_files);
        $count_files = count($service_images_files);
        return view('admin.service-types.form', [
            'd' => $service_type,
            'page_title' => $page_title,
            'list' => $list,
            'listcan' => $listcan,
            'service_images_files' => $service_images_files,
            'count_files' => $count_files,
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
        $this->authorize(Actions::Manage . '_' . Resources::ServiceType);
        $service_type = ServiceType::find($id);
        $service_type->delete();

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

    private function getListCan()
    {
        return collect([
            [
                'id' => 'YES',
                'value' => 1,
                'name' => __('service_types.status_yes_no_' . '1'),
            ],
            [
                'id' => 'NO',
                'value' => 0,
                'name' => __('service_types.status_yes_no_' . '0'),
            ],
        ]);
    }
}
