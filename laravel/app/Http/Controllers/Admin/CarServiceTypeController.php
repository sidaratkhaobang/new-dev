<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\CarEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarServiceType;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarServiceTypeController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CarServiceType);
        $s = $request->s;
        $license_plate = $request->license_plate;
        $license_plate_text = null;
        if (!empty($license_plate)) {
            $car = Car::find($license_plate);
            $license_plate_text = $car->license_plate;
        }

        $engine_no = $request->engine_no;
        $engine_no_text = null;
        if (!empty($engine_no)) {
            $car = Car::find($engine_no);
            $engine_no_text = $car->engine_no;
        }
        $chassis_no = $request->chassis_no;
        $chassis_no_text = null;
        if (!empty($chassis_no)) {
            $car = Car::find($chassis_no);
            $chassis_no_text = $car->chassis_no;
        }

        $type = RentalTypeEnum::SHORT;

        $list = Car::select('cars.engine_no', 'cars.chassis_no', 'cars.license_plate', 'cars.id', 'car_classes.name as car_class_name', 'car_classes.full_name as class_name')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->where('cars.rental_type', RentalTypeEnum::SHORT)
            ->whereNotIn('cars.status', [CarEnum::NEWCAR, CarEnum::PENDING_SALE, CarEnum::SOLD_OUT])
            ->where(function ($query) use ($s) {
                if (!empty($s)) {
                    $query->where('car_classes.full_name', 'like', '%' . $s . '%');
                    $query->orWhere('car_classes.name', 'like', '%' . $s . '%');
                }
            })
            ->sortable('engine_no')
            ->search($s, $request)
            ->paginate(PER_PAGE);

        return view('admin.car-service-types.index', [
            'car_id' => $request->car_id,
            'engine_no_id' => $request->engine_no,
            'chassis_no_id' => $request->chassis_no,
            'license_plate' => $license_plate,
            'engine_no' => $engine_no,
            'chassis_no' => $chassis_no,
            'list' => $list,
            's' => $request->s,
            'license_plate_text' => $license_plate_text,
            'engine_no_text' => $engine_no_text,
            'chassis_no_text' => $chassis_no_text,
            'type' => $type,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarServiceType);
        $validator = Validator::make($request->all(), [
            'service_types' => ['required'],
        ], [], [
            'service_types' => __('car_service_types.service_type')
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $deleted_car_service_types = CarServiceType::where('car_id', $request->id)->delete();
        if (!empty($request->service_types)) {
            foreach ($request->service_types as $service_type) {
                $car_service_type = new CarServiceType();
                $car_service_type->car_id = $request->id;
                $car_service_type->service_type_id = $service_type;
                $car_service_type->save();
            }
        }

        $car = Car::find($request->id);
        if ($request->car_images__pending_delete_ids) {
            $pending_delete_ids = $request->car_images__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $car->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('car_images')) {
            foreach ($request->file('car_images') as $image) {
                if ($image->isValid()) {
                    $car->addMedia($image)->toMediaCollection('car_images');
                }
            }
        }

        $redirect_route = route('admin.car-service-types.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(Car $car_service_type)
    {
        $this->authorize(Actions::View . '_' . Resources::CarServiceType);
        $car_service_type_array = $this->getSelfServiceTypeArray($car_service_type->id);
        $service_type_list = ServiceType::all();
        $car_images_files = $car_service_type->getMedia('car_images');
        $car_images_files = get_medias_detail($car_images_files);
        $page_title = __('lang.view') . __('car_service_types.page_title');
        $view = true;
        return view('admin.car-service-types.form', [
            'd' => $car_service_type,
            'view' => $view,
            'page_title' => $page_title,
            'service_type_list' => $service_type_list,
            'car_service_type_array' => $car_service_type_array,
            'car_images_files' => $car_images_files
        ]);
    }

    public function edit(Car $car_service_type)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarServiceType);
        $car_service_type_array = $this->getSelfServiceTypeArray($car_service_type->id);
        $service_type_list = ServiceType::all();
        $car_images_files = $car_service_type->getMedia('car_images');
        $car_images_files = get_medias_detail($car_images_files);
        $page_title = __('lang.edit') . __('car_service_types.page_title');
        return view('admin.car-service-types.form', [
            'd' => $car_service_type,
            'page_title' => $page_title,
            'service_type_list' => $service_type_list,
            'car_service_type_array' => $car_service_type_array,
            'car_images_files' => $car_images_files
        ]);
    }

    public function getSelfServiceTypeArray($car_id)
    {
        return CarServiceType::leftJoin('cars', 'cars.id', '=', 'cars_service_types.car_id')
            ->leftJoin('service_types', 'service_types.id', '=', 'cars_service_types.service_type_id')
            ->select('service_types.id as id', 'service_types.name as name')
            ->where('cars_service_types.car_id', $car_id)
            ->pluck('service_types.id')
            ->toArray();
    }
}
