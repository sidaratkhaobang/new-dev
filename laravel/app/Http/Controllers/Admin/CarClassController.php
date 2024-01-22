<?php

namespace App\Http\Controllers\Admin;

use App\Models\CarPart;
use App\Models\CarTire;
use App\Models\CarType;
use App\Models\CarBrand;
use App\Models\CarClass;
use App\Models\CarWiper;
use App\Models\CarBattery;
use App\Models\CarCategory;
use Illuminate\Http\Request;
use App\Models\CarClassColor;
use App\Enums\CarPartTypeEnum;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\CarClassAccessory;
use Illuminate\Support\Facades\Validator;
use App\Enums\Actions;
use App\Enums\Resources;


class CarClassController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CarClass);
        $list = CarClass::sortable('name')
            ->leftjoin('car_types', 'car_classes.car_type_id', '=', 'car_types.id')
            ->leftJoin('car_brands', 'car_brands.id', '=', 'car_types.car_brand_id')
            ->leftJoin('car_parts', 'car_parts.id', '=', 'car_classes.gear_id')
            ->select('car_classes.*',
            'car_types.name as car_type_name',
            'car_brands.name as car_brand_name',
            'car_parts.name as car_part_gear_name',
            )
            ->search($request->s, $request)
            ->paginate(PER_PAGE);

        $car_part = CarPart::leftJoin('car_part_types', 'car_part_types.id', '=', 'car_parts.car_part_type_id')
            ->select('car_parts.*', 'car_part_types.type as car_part_type')
            ->get();
        $gear = [];
        foreach ($car_part as $item) {
            if ($item->car_part_type == CarPartTypeEnum::GEAR) {
                array_push($gear, $item);
            }
        }

        $car_brand_name = null;
        $car_brand_id = $request->car_brand_id;
        if (!empty($car_brand_id)) {
            $car_brand = CarBrand::find($car_brand_id);
            if ($car_brand) {
                $car_brand_name = $car_brand->name;
            }
        }

        $car_type_name = null;
        $car_type_id = $request->car_type_id;
        if (!empty($car_type_id)) {
            $car_type = CarType::find($car_type_id);
            if ($car_type) {
                $car_type_name = $car_type->name;
            }
        }

        $name_list = CarClass::select('name', 'id')->orderBy('name')->get();
        $full_name_list = CarClass::select('full_name as name', 'id')->orderBy('name')->get();
        $engine_list = CarClass::select('engine_size as name', 'engine_size as id')->groupBy('engine_size')->get();
        $year_list = CarClass::select('manufacturing_year as name', 'manufacturing_year as id')->groupBy('manufacturing_year')->get();

        return view('admin.car-classes.index', [
            'list' => $list,
            's' => $request->s,
            'name_list' => $name_list,
            'full_name_list' => $full_name_list,
            'engine_list' => $engine_list,
            'year_list' => $year_list,
            'gear' => $gear,
            'name' => $request->name,
            'full_name' => $request->full_name,
            'engine' => $request->engine,
            'year' => $request->year,
            'gear_id' => $request->gear_id,
            'car_brand_id' => $car_brand_id,
            'car_type_id' => $car_type_id,
            'car_type_name' => $car_type_name,
            'car_brand_name' => $car_brand_name,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarClass);
        $d = new CarClass();
        $car_wiper = CarWiper::all();
        $car_battery = CarBattery::all();
        $car_tire = CarTire::all();

        $car_part = CarPart::leftJoin('car_part_types', 'car_part_types.id', '=', 'car_parts.car_part_type_id')
            ->select('car_parts.*', 'car_part_types.type as car_part_type')
            ->get();
        $gear = [];
        $drive_system = [];
        $car_seat = [];
        $side_mirror = [];
        $air_bag = [];
        $central_lock = [];
        $front_brake = [];
        $rear_brake = [];
        $abs = [];
        $anti_thift_system = [];
        foreach ($car_part as $item) {
            if ($item->car_part_type == CarPartTypeEnum::GEAR) {
                array_push($gear, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::DRIVE_SYSTEM) {
                array_push($drive_system, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::CAR_SEAT) {
                array_push($car_seat, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::SIDE_MIRROR) {
                array_push($side_mirror, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::AIR_BAG) {
                array_push($air_bag, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::CENTRAL_LOCK) {
                array_push($central_lock, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::FRONT_BRAKE) {
                array_push($front_brake, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::REAR_BRAKE) {
                array_push($rear_brake, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::ABS) {
                array_push($abs, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::ANTI_THIFT_SYSTEM) {
                array_push($anti_thift_system, $item);
            }
        }

        $car_brand_name = null;
        $car_type_name = null;
        $car_category_name = null;
        $car_images_files = [];
        $page_title = __('lang.create') . __('car_classes.page_title');
        return view('admin.car-classes.form', [
            'd' => $d,
            'page_title' => $page_title,
            'gear' => $gear,
            'drive_system' => $drive_system,
            'car_seat' => $car_seat,
            'side_mirror' => $side_mirror,
            'air_bag' => $air_bag,
            'central_lock' => $central_lock,
            'front_brake' => $front_brake,
            'rear_brake' => $rear_brake,
            'abs' => $abs,
            'anti_thift_system' => $anti_thift_system,
            'car_wiper' => $car_wiper,
            'car_battery' => $car_battery,
            'car_tire' => $car_tire,
            'car_brand_name' => $car_brand_name,
            'car_type_name' => $car_type_name,
            'car_category_name' => $car_category_name,
            'car_images_files' => $car_images_files
        ]);
    }

    public function edit(CarClass $car_class)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarClass);
        $car_wiper = CarWiper::all();
        $car_battery = CarBattery::all();
        $car_tire = CarTire::all();

        $car_part = CarPart::leftJoin('car_part_types', 'car_part_types.id', '=', 'car_parts.car_part_type_id')
            ->select('car_parts.*')
            ->get();
        $gear = [];
        $drive_system = [];
        $car_seat = [];
        $side_mirror = [];
        $air_bag = [];
        $central_lock = [];
        $front_brake = [];
        $rear_brake = [];
        $abs = [];
        $anti_thift_system = [];
        foreach ($car_part as $item) {
            if ($item->car_part_type == CarPartTypeEnum::GEAR) {
                array_push($gear, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::DRIVE_SYSTEM) {
                array_push($drive_system, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::CAR_SEAT) {
                array_push($car_seat, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::SIDE_MIRROR) {
                array_push($side_mirror, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::AIR_BAG) {
                array_push($air_bag, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::CENTRAL_LOCK) {
                array_push($central_lock, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::FRONT_BRAKE) {
                array_push($front_brake, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::REAR_BRAKE) {
                array_push($rear_brake, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::ABS) {
                array_push($abs, $item);
            }
            if ($item->car_part_type == CarPartTypeEnum::ANTI_THIFT_SYSTEM) {
                array_push($anti_thift_system, $item);
            }
        }

        $car_class_accessory_list = CarClassAccessory::where('car_class_id', $car_class->id)->get();
        $car_class_accessory_list->map(function ($item) {
            $item->accessory_text = ($item->accessory) ? $item->accessory->name : '';
            $item->accessory_version_text = ($item->accessoryVersion) ? $item->accessoryVersion->version : '';
            return $item;
        });

        $car_class_color_list = CarClassColor::where('car_class_id', $car_class->id)->get();
        $car_class_color_list->map(function ($item) {
            $item->color_text = ($item->color) ? $item->color->name : '';
            $item->car_color_id = ($item->color) ? $item->color->id : '';
            $item->total_price = number_format($item->color_price + $item->standard_price , 2, '.', '');
            return $item;
        });

        $medias = $car_class->getMedia('images');
        $mock_files = get_medias_detail($medias);

        $car_medias = $car_class->getMedia('car_images');
        $car_images_files = get_medias_detail($car_medias);

        $car_brand_name = $car_class->carType ? $car_class->carType->car_brand->name : null;
        $car_type_name = $car_class->carType ? $car_class->carType->name : null;
        $car_category_name = $car_class->carType ? $car_class->carType->car_category->name : null;

        $page_title = __('lang.edit') . __('car_classes.page_title');
        return view('admin.car-classes.form', [
            'd' => $car_class,
            'page_title' => $page_title,
            'gear' => $gear,
            'drive_system' => $drive_system,
            'car_seat' => $car_seat,
            'side_mirror' => $side_mirror,
            'air_bag' => $air_bag,
            'central_lock' => $central_lock,
            'front_brake' => $front_brake,
            'rear_brake' => $rear_brake,
            'abs' => $abs,
            'anti_thift_system' => $anti_thift_system,
            'car_wiper' => $car_wiper,
            'car_battery' => $car_battery,
            'car_tire' => $car_tire,
            'car_brand_name' => $car_brand_name,
            'car_type_name' => $car_type_name,
            'car_category_name' => $car_category_name,
            'class_accessory_list' => $car_class_accessory_list,
            'car_class_color_list' => $car_class_color_list,
            'mock_files' => $mock_files,
            'car_images_files' => $car_images_files
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarClass);
        $car_class = CarClass::find($id);
        $car_class->delete();

        return $this->responseComplete();
    }

    public function store(Request $request)
    {
        $car_class = CarClass::firstOrNew(['id' => $request->id]);
        $car_class->name = $request->name;
        $car_class->full_name = $request->full_name;
        $car_class->manufacturing_year = $request->manufacturing_year;
        $car_class->description = $request->description;
        $car_class->engine_size = $request->engine_size;
        $car_class->oil_type = $request->oil_type;
        $car_class->oil_tank_capacity = $request->oil_tank_capacity;
        $car_class->remark = $request->remark;
        $car_class->car_type_id = $request->car_type_id;
        $car_class->gear_id = $request->gear_id;
        $car_class->drive_system_id = $request->drive_system_id;
        $car_class->drive_system_id = $request->drive_system_id;
        $car_class->central_lock_id = $request->central_lock_id;
        $car_class->car_seat_id = $request->car_seat_id;
        $car_class->air_bag_id = $request->air_bag_id;
        $car_class->side_mirror_id = $request->side_mirror_id;
        $car_class->anti_thift_system_id = $request->anti_thift_system_id;
        $car_class->abs_id = $request->abs_id;
        $car_class->front_brake_id = $request->front_brake_id;
        $car_class->rear_brake_id = $request->rear_brake_id;
        $car_class->car_tire_id = $request->car_tire_id;
        $car_class->car_battery_id = $request->car_battery_id;
        $car_class->car_wiper_id = $request->car_wiper_id;
        $car_class->website = $request->website;
        $car_class->status = STATUS_ACTIVE;
        $car_class->save();

        if ($request->car_images__pending_delete_ids) {
            $pending_delete_ids = $request->car_images__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $car_class->deleteMedia($media_id);
                }
            }
        }

        if ($request->images__pending_delete_ids) {
            $pending_delete_ids = $request->images__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $car_class->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $car_class->addMedia($image)->toMediaCollection('images');
                }
            }
        }

        if ($request->hasFile('car_images')) {
            foreach ($request->file('car_images') as $image) {
                if ($image->isValid()) {
                    $car_class->addMedia($image)->toMediaCollection('car_images');
                }
            }
        }
        if ($car_class->id) {
            $car_class_accessory = $this->saveCarClassAccessory($request, $car_class->id);
            $car_class_color = $this->saveCarClassColor($request, $car_class->id);
        }

        $redirect_route = route('admin.car-classes.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    private function saveCarClassColor($request, $id)
    {
        CarClassColor::where('car_class_id', $id)->delete();
        if (!empty($request->car_class_color)) {
            foreach ($request->car_class_color as $key => $request_car_class_color) {
                $car_class_color = new CarClassColor();
                $car_class_color->car_class_id = $id;
                $car_class_color->car_color_id = $request_car_class_color['car_color_id'];
                $car_class_color->standard_price = $request_car_class_color['standard_price'];
                $car_class_color->color_price = $request_car_class_color['color_price'];
                $car_class_color->remark = $request_car_class_color['remark'];
                $car_class_color->save();
            }
        }
        return true;
    }

    private function saveCarClassAccessory($request, $id)
    {
        CarClassAccessory::where('car_class_id', $id)->delete();
        if (!empty($request->car_class_accessory)) {
            foreach ($request->car_class_accessory as $key => $request_car_class_accessory) {
                $car_class_accessory = new CarClassAccessory();
                $car_class_accessory->car_class_id = $id;
                $car_class_accessory->accessory_id = $request_car_class_accessory['accessory_id'];
                //$car_class_accessory->accessory_version_id = $request_car_class_accessory['accessory_version_id'];
                $car_class_accessory->remark = $request_car_class_accessory['remark'];
                $car_class_accessory->save();
            }
        }
        return true;
    }

    public function show(CarClass $car_class)
    {
        $this->authorize(Actions::View . '_' . Resources::CarClass);
        $car_wiper = CarWiper::all();
        $car_battery = CarBattery::all();
        $car_tire = CarTire::all();

        $car_part = CarPart::leftJoin('car_part_types', 'car_part_types.id', '=', 'car_parts.car_part_type_id')
            ->select('car_parts.*')
            ->get();
        $gear = [];
        $drive_system = [];
        $car_seat = [];
        $side_mirror = [];
        $air_bag = [];
        $central_lock = [];
        $front_brake = [];
        $rear_brake = [];
        $abs = [];
        $anti_thift_system = [];

        $car_class_accessory_list = CarClassAccessory::where('car_class_id', $car_class->id)->get();
        $car_class_accessory_list->map(function ($item) {
            $item->accessory_text = ($item->accessory) ? $item->accessory->name : '';
            $item->accessory_version_text = ($item->accessoryVersion) ? $item->accessoryVersion->version : '';
            return $item;
        });

        $car_class_color_list = CarClassColor::where('car_class_id', $car_class->id)->get();
        $car_class_color_list->map(function ($item) {
            $item->color_text = ($item->color) ? $item->color->name : '';
            $item->car_color_id = ($item->color) ? $item->color->id : '';
            $item->total_price = number_format($item->color_price + $item->standard_price , 2, '.', '');
            return $item;
        });

        $medias = $car_class->getMedia('images');
        $mock_files = get_medias_detail($medias);

        $car_medias = $car_class->getMedia('car_images');
        $car_images_files = get_medias_detail($car_medias);

        $car_brand_name = $car_class->carType ? $car_class->carType->car_brand->name : null;
        $car_type_name = $car_class->carType ? $car_class->carType->name : null;
        $car_category_name = $car_class->carType ? $car_class->carType->car_category->name : null;

        $page_title = __('lang.view') . __('car_classes.page_title');
        return view('admin.car-classes.view', [
            'd' => $car_class,
            'page_title' => $page_title,
            'gear' => $gear,
            'drive_system' => $drive_system,
            'car_seat' => $car_seat,
            'side_mirror' => $side_mirror,
            'air_bag' => $air_bag,
            'central_lock' => $central_lock,
            'front_brake' => $front_brake,
            'rear_brake' => $rear_brake,
            'abs' => $abs,
            'anti_thift_system' => $anti_thift_system,
            'car_brand_name' => $car_brand_name,
            'car_type_name' => $car_type_name,
            'car_category_name' => $car_category_name,
            'class_accessory_list' => $car_class_accessory_list,
            'car_class_color_list' => $car_class_color_list,
            'mock_files' => $mock_files,
            'car_images_files' => $car_images_files,
            'car_wiper' => $car_wiper,
            'car_battery' => $car_battery,
            'car_tire' => $car_tire,
        ]);
    }
}
