<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarBattery;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Enums\Actions;
use App\Enums\Resources;

class CarBatteryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CarBattery);
        $list = CarBattery::select('car_batteries.*')
        ->sortable('code')
        ->search($request->s)->paginate(PER_PAGE);
        return view('admin.car-batteries.index', [
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
        $this->authorize(Actions::Manage . '_' . Resources::CarBattery);
        $d = new CarBattery();
        $page_title = __('lang.create') . __('car_batteries.page_title');
        return view('admin.car-batteries.form', compact('d', 'page_title'));
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
                Rule::unique('car_batteries', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'version' => [
                'required', 'string', 'max:255',
                Rule::unique('car_batteries', 'version')->whereNull('deleted_at')->ignore($request->id),
            ],
            'detail' => [
                'required', 'string', 'max:255',
            ],
            'price' => [
                'required', 'numeric',
            ],

        ], [], [
            'name' => __('car_batteries.name'),
            'version' => __('car_batteries.version'),
            'detail' => __('car_batteries.detail'),
            'price' => __('car_batteries.price'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $car_battery = CarBattery::firstOrNew(['id' => $request->id]);
        $car_battery->name = $request->name;
        $car_battery->version = $request->version;
        $car_battery->detail = $request->detail;
        $car_battery->price = $request->price;
        $car_battery->status = STATUS_ACTIVE;
        $car_battery->save();

        $redirect_route = route('admin.car-batteries.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(CarBattery $car_battery)
    {
        $this->authorize(Actions::View . '_' . Resources::CarBattery);
        $page_title = __('lang.view') . __('car_batteries.page_title');
        $view = true;
        return view('admin.car-batteries.form', [
            'd' => $car_battery,
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
    public function edit(CarBattery $car_battery)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarBattery);
        $page_title = __('lang.edit') . __('car_batteries.page_title');
        return view('admin.car-batteries.form', [
            'd' => $car_battery,
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
        $this->authorize(Actions::Manage . '_' . Resources::CarBattery);
        $car_battery = CarBattery::find($id);
        $car_battery->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
