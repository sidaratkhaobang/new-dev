<?php

namespace App\Http\Controllers\Admin;

use App\Models\CarColor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Enums\Actions;
use App\Enums\Resources;

class CarColorController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CarColor);
        $list = CarColor::sortable('code')
            ->search($request->s)->paginate(PER_PAGE);

        return view('admin.car-colors.index', [
            'list' => $list,
            's' => $request->s,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarColor);
        $d = new CarColor();
        $page_title = __('lang.create') . __('car_colors.page_title');

        return view('admin.car-colors.form', [
            'd' => $d,
            'page_title' => $page_title
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => [
                'required', 'string', 'max:10',
                Rule::unique('car_colors', 'code')->whereNull('deleted_at')->ignore($request->id),
            ],
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('car_colors', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
        ], [], [
            'code' => __('car_colors.code'),
            'name' => __('car_colors.name')
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $car_color = CarColor::firstOrNew(['id' => $request->id]);
        $car_color->name = $request->name;
        $car_color->code = $request->code;
        $car_color->status = STATUS_ACTIVE;
        $car_color->save();

        $redirect_route = route('admin.car-colors.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(CarColor $car_color)
    {
        $this->authorize(Actions::View . '_' . Resources::CarColor);
        $page_title = __('lang.view') . __('car_colors.page_title');
        $view = true;
        return view('admin.car-colors.form', [
            'd' => $car_color,
            'view' => $view,
            'page_title' => $page_title,
        ]);
    }

    public function edit(CarColor $car_color)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarColor);
        $page_title = __('lang.edit') . __('car_colors.page_title');
        return view('admin.car-colors.form', [
            'd' => $car_color,
            'page_title' => $page_title,
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarColor);
        $car_brand = CarColor::find($id);
        $car_brand->delete();

        return $this->responseComplete();
    }
}
