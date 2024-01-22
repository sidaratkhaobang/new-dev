<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarWiper;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Enums\Actions;
use App\Enums\Resources;

class CarWiperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CarWiper);
        $list = CarWiper::select('car_wipers.*')
        ->sortable('code')
        ->search($request->s)->paginate(PER_PAGE);
        return view('admin.car-wipers.index', [
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
        $this->authorize(Actions::Manage . '_' . Resources::CarWiper);
        $d = new CarWiper();
        $page_title = __('lang.create') . __('car_wipers.page_title');
        return view('admin.car-wipers.form', compact('d', 'page_title'));
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
                Rule::unique('car_wipers', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'version' => [
                'required', 'string', 'max:255',
                // Rule::unique('car_wipers', 'version')->whereNull('deleted_at')->ignore($request->id),
            ],
            'detail' => [
                'required', 'string', 'max:255',
            ],
            'price' => [
                'required', 'numeric',
            ],

        ], [], [
            'name' => __('car_wipers.name'),
            'version' => __('car_wipers.version'),
            'detail' => __('car_wipers.detail'),
            'price' => __('car_wipers.price'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $car_wiper = CarWiper::firstOrNew(['id' => $request->id]);
        $car_wiper->name = $request->name;
        $car_wiper->version = $request->version;
        $car_wiper->detail = $request->detail;
        $car_wiper->price = $request->price;
        $car_wiper->status = STATUS_ACTIVE;
        $car_wiper->save();

        $redirect_route = route('admin.car-wipers.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(CarWiper $car_wiper)
    {
        $this->authorize(Actions::View . '_' . Resources::CarWiper);
        $page_title = __('lang.view') . __('car_wipers.page_title');
        $view = true;
        return view('admin.car-wipers.form', [
            'd' => $car_wiper,
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
    public function edit(CarWiper $car_wiper)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarWiper);
        $page_title = __('lang.edit') . __('car_wipers.page_title');
        return view('admin.car-wipers.form', [
            'd' => $car_wiper,
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
        $this->authorize(Actions::Manage . '_' . Resources::CarWiper);
        $car_wiper = CarWiper::find($id);
        $car_wiper->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
