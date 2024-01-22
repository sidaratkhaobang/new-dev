<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarTire;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Enums\Actions;
use App\Enums\Resources;

class CarTireController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CarTire);
        $list = CarTire::select('car_tires.*')
        ->sortable('code')
        ->search($request->s)->paginate(PER_PAGE);
        return view('admin.car-tires.index', [
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
        $this->authorize(Actions::Manage . '_' . Resources::CarTire);
        $d = new CarTire();
        $page_title = __('lang.create') . __('car_tires.page_title');
        return view('admin.car-tires.form', compact('d', 'page_title'));
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
                Rule::unique('car_tires', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'version' => [
                'required', 'string', 'max:255',
                Rule::unique('car_tires', 'version')->whereNull('deleted_at')->ignore($request->id),
            ],
            'detail' => [
                'required', 'string', 'max:255',
            ],
            'price' => [
                'required', 'numeric',
            ],

        ], [], [
            'name' => __('car_tires.name'),
            'version' => __('car_tires.version'),
            'detail' => __('car_tires.detail'),
            'price' => __('car_tires.price'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $car_tire = CarTire::firstOrNew(['id' => $request->id]);
        $car_tire->name = $request->name;
        $car_tire->version = $request->version;
        $car_tire->detail = $request->detail;
        $car_tire->price = $request->price;
        $car_tire->status = STATUS_ACTIVE;
        $car_tire->save();

        $redirect_route = route('admin.car-tires.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(CarTire $car_tire)
    {
        $this->authorize(Actions::View . '_' . Resources::CarTire);
        $page_title = __('lang.view') . __('car_tires.page_title');
        $view = true;
        return view('admin.car-tires.form', [
            'd' => $car_tire,
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
    public function edit(CarTire $car_tire)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarTire);
        $page_title = __('lang.edit') . __('car_tires.page_title');
        return view('admin.car-tires.form', [
            'd' => $car_tire,
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
        $this->authorize(Actions::Manage . '_' . Resources::CarTire);
        $car_tire = CarTire::find($id);
        $car_tire->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
