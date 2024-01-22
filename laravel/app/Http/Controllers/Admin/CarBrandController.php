<?php

namespace App\Http\Controllers\Admin;

use App\Models\CarBrand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Enums\Actions;
use App\Enums\Resources;

class CarBrandController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CarBrand);
        $list = CarBrand::leftJoin('users as u1', 'u1.id', '=', 'car_brands.created_by')
            ->leftJoin('users as u2', 'u2.id', '=', 'car_brands.updated_by')
            ->sortable('code')
            ->select(
                'car_brands.id',
                'car_brands.name',
                'car_brands.code',
                'car_brands.created_at',
                'car_brands.updated_at',
                'u1.name as creator_name',
                'u2.name as updater_name',
            )
            ->search($request->s)->paginate(PER_PAGE);

        return view('admin.car-brands.index', [
            'list' => $list,
            's' => $request->s,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarBrand);
        $d = new CarBrand();
        $car_brand_images = [];
        $page_title = __('lang.create') . __('car_brands.page_title');

        return view('admin.car-brands.form', [
            'd' => $d,
            'page_title' => $page_title,
            'car_brand_images' => $car_brand_images
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => [
                'required', 'string', 'max:3',
                Rule::unique('car_brands', 'code')->ignore($request->id),
            ],
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('car_brands', 'name')->ignore($request->id),
            ],
        ], [], [
            'code' => __('car_brands.code'),
            'name' => __('car_brands.name')
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $car_brand = CarBrand::firstOrNew(['id' => $request->id]);
        $car_brand->name = $request->name;
        $car_brand->code = $request->code;
        $car_brand->status = STATUS_ACTIVE;
        $car_brand->save();

        if ($request->car_brand_images__pending_delete_ids) {
            $pending_delete_ids = $request->car_brand_images__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $car_brand->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('car_brand_images')) {
            foreach ($request->file('car_brand_images') as $image) {
                if ($image->isValid()) {
                    $car_brand->addMedia($image)->toMediaCollection('car_brand_images');
                }
            }
        }

        $redirect_route = route('admin.car-brands.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(CarBrand $car_brand)
    {
        $this->authorize(Actions::View . '_' . Resources::CarBrand);
        $page_title = __('lang.view') . __('car_brands.page_title');
        $car_brand_images = $car_brand->getMedia('car_brand_images');
        $car_brand_images = get_medias_detail($car_brand_images);
        $view = true;
        return view('admin.car-brands.form', [
            'd' => $car_brand,
            'view' => $view,
            'page_title' => $page_title,
            'car_brand_images' => $car_brand_images
        ]);
    }

    public function edit(CarBrand $car_brand)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarBrand);
        $page_title = __('lang.edit') . __('car_brands.page_title');
        $car_brand_images = $car_brand->getMedia('car_brand_images');
        $car_brand_images = get_medias_detail($car_brand_images);
        return view('admin.car-brands.form', [
            'd' => $car_brand,
            'page_title' => $page_title,
            'car_brand_images' => $car_brand_images
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarBrand);
        $car_brand = CarBrand::find($id);
        $car_brand->delete();

        return $this->responseComplete();
    }
}
