<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\CarTypeAccidentEnum;
use App\Enums\GarageTypeEnum;
use App\Enums\Resources;
use App\Enums\ZoneEnum;
use App\Http\Controllers\Controller;
use App\Models\Cradle;
use App\Models\CradleType;
use App\Models\District;
use App\Models\Location;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GarageController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Garage);
        $province_id = $request->province_id;
        $garage_type = $request->garage_type;
        $garage = $request->garage;
        $status = $request->status;
        $province_list = $this->getProvinceList();
        $garage_type_list = $this->getGarageType();
        $garage_list = Cradle::select('id', 'name')->get();
        $status_list = $this->getListStatusSelect();
        $list = Cradle::leftJoin('cradle_types', 'cradle_types.cradle_id', '=', 'cradles.id')
            ->select(
                'cradles.id',
                'cradles.name',
                'cradles.cradle_type',
                'cradles.province',
                'cradles.district',
                'cradles.cradle_tel',
                'cradles.is_onsite_service',
                // 'cradles.status',
                'cradles.cradle_email',
                'cradles.emcs',
                'cradles.address',
                'cradles.region',
                'cradles.subdistrict',
                'cradles.remark',
                'cradles.id_line',
                'cradles.website',
                'cradles.coordinator_name',
                'cradles.coordinator_tel',
                'cradles.coordinator_email',
                'cradles.status',
                'cradles.created_at',
                'cradles.updated_at',
                'cradles.deleted_at',
                'cradles.created_by',
                'cradles.updated_by',
                'cradles.deleted_by',
                DB::raw("GROUP_CONCAT(
            CASE cradle_types.type
                WHEN '" . CarTypeAccidentEnum::SEDAN_CAR . "' THEN '" . __('garages.car_type_' . CarTypeAccidentEnum::SEDAN_CAR) . "'
                WHEN '" . CarTypeAccidentEnum::PICKUP_CAR . "' THEN '" . __('garages.car_type_' . CarTypeAccidentEnum::PICKUP_CAR) . "'
                WHEN '" . CarTypeAccidentEnum::VAN . "' THEN '" . __('garages.car_type_' . CarTypeAccidentEnum::VAN) . "'
                WHEN '" . CarTypeAccidentEnum::BUS . "' THEN '" . __('garages.car_type_' . CarTypeAccidentEnum::BUS) . "'
                WHEN '" . CarTypeAccidentEnum::ALL_TYPE . "' THEN '" . __('garages.car_type_' . CarTypeAccidentEnum::ALL_TYPE) . "'
                WHEN '" . CarTypeAccidentEnum::TRUCK . "' THEN '" . __('garages.car_type_' . CarTypeAccidentEnum::TRUCK) . "'
                WHEN '" . CarTypeAccidentEnum::GENERAL . "' THEN '" . __('garages.car_type_' . CarTypeAccidentEnum::GENERAL) . "'
                ELSE ''
            END
        ) AS cradle_types")
            )
            ->groupBy(
                'cradles.id',
                'cradles.name',
                'cradles.cradle_type',
                'cradles.province',
                'cradles.district',
                'cradles.cradle_tel',
                'cradles.is_onsite_service',
                // 'cradles.status',
                'cradles.cradle_email',
                'cradles.emcs',
                'cradles.address',
                'cradles.region',
                'cradles.subdistrict',
                'cradles.remark',
                'cradles.id_line',
                'cradles.website',
                'cradles.coordinator_name',
                'cradles.coordinator_tel',
                'cradles.coordinator_email',
                'cradles.status',
                'cradles.created_at',
                'cradles.updated_at',
                'cradles.deleted_at',
                'cradles.created_by',
                'cradles.updated_by',
                'cradles.deleted_by',
            )
            ->sortable('name')
            ->search($request)
            ->paginate(PER_PAGE);

        return view('admin.garages.index', [
            'list' => $list,
            's' => $request->s,
            'province_id' => $province_id,
            'province_list' => $province_list,
            'status' => $status,
            'garage_type_list' => $garage_type_list,
            'garage_type' => $garage_type,
            'garage_list' => $garage_list,
            'status_list' => $status_list,
            'garage' => $garage,
        ]);
    }

    public function create()
    {
        $d = new Cradle();
        $d->province = null;
        $d->district = null;
        $d->subdistrict = null;
        $car_type = [];
        $d->status = STATUS_ACTIVE;
        $page_title = __('lang.create') . __('garages.page_title');
        $listStatus = $this->getListStatus();
        $province_name = null;
        $amphure_name =  null;
        $district_name =  null;
        $garage_type_list = $this->getGarageType();
        $car_type_list = $this->getCarType();
        $zone = $this->getZoneType();
        return view('admin.garages.form', [
            'page_title' => $page_title,
            'd' => $d,
            'listStatus' => $listStatus,
            'province_name' => $province_name,
            'amphure_name' => $amphure_name,
            'district_name' => $district_name,
            'garage_type_list' => $garage_type_list,
            'car_type_list' => $car_type_list,
            'zone' => $zone,
            'car_type' => $car_type,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tel' => [
                'required', 'numeric', 'digits:10',
            ],
            'coordinator_tel' => [
                'required', 'numeric', 'digits:10',
            ],
        ], [], [

            'tel' => __('garages.tel'),
            'coordinator_tel' => __('garages.tel'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $garage = Cradle::firstOrNew(['id' => $request->id]);
        $garage->name = $request->garage_name;
        $garage->cradle_type = $request->garage_type;
        $garage->cradle_email = $request->email;
        $garage->cradle_tel = $request->tel;
        $garage->emcs = boolval($request->emcs);
        $garage->is_onsite_service = $request->onsite_service;
        $garage->address = $request->address;
        $garage->region = $request->sector;
        $garage->province = $request->province;
        $garage->district = $request->district;
        $garage->subdistrict = $request->subdistrict;
        $garage->coordinator_name = $request->coordinator_name;
        $garage->coordinator_email = $request->coordinator_email;
        $garage->coordinator_tel = $request->coordinator_tel;
        $garage->status = $request->status;
        $garage->save();


        $deleted_car_types = CradleType::where('cradle_id', $garage->id)->delete();
        if (!empty($request->car_type)) {
            foreach ($request->car_type as $ct) {
                $cardle_type = new CradleType();
                $cardle_type->cradle_id = $garage->id;
                $cardle_type->type = $ct;
                $cardle_type->save();
            }
        }

        $redirect_route = route('admin.garages.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(Cradle $garage)
    {
        $this->authorize(Actions::View . '_' . Resources::Garage);
        $page_title = __('lang.view') . __('garages.page_title');
        $listStatus = $this->getListStatus();
        $province_name = $garage->Province ? $garage->Province->name_th : null;
        $amphure_name = $garage->District ? $garage->District->name_th  : null;
        $district_name = $garage->SubDistrict ? $garage->SubDistrict->name_th  : null;
        $garage_type_list = $this->getGarageType();
        $car_type_list = $this->getCarType();
        $zone = $this->getZoneType();
        $car_type = $this->getCarTypeArray($garage->id);
        return view('admin.garages.form', [
            'page_title' => $page_title,
            'd' => $garage,
            'listStatus' => $listStatus,
            'province_name' => $province_name,
            'amphure_name' => $amphure_name,
            'district_name' => $district_name,
            'garage_type_list' => $garage_type_list,
            'car_type_list' => $car_type_list,
            'zone' => $zone,
            'view' => true,
            'car_type' => $car_type,
        ]);
    }

    public function edit(Cradle $garage)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Garage);
        $page_title = __('lang.edit') . __('garages.page_title');
        $listStatus = $this->getListStatus();
        $province_name = $garage->Province ? $garage->Province->name_th : null;
        $amphure_name = $garage->District ? $garage->District->name_th  : null;
        $district_name = $garage->SubDistrict ? $garage->SubDistrict->name_th  : null;
        $garage_type_list = $this->getGarageType();
        $car_type_list = $this->getCarType();
        $zone = $this->getZoneType();
        $garage->car_type = $this->getCarTypeArray($garage->id);
        $car_type = $this->getCarTypeArray($garage->id);
        return view('admin.garages.form', [
            'page_title' => $page_title,
            'd' => $garage,
            'listStatus' => $listStatus,
            'province_name' => $province_name,
            'amphure_name' => $amphure_name,
            'district_name' => $district_name,
            'garage_type_list' => $garage_type_list,
            'car_type_list' => $car_type_list,
            'zone' => $zone,
            'car_type' => $car_type,
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Garage);
        $cradle = Cradle::find($id);
        $cradle->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }

    private function getListStatusSelect()
    {
        return collect([
            (object) [
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => __('garages.status_job_' . STATUS_ACTIVE),
            ],
            (object) [
                'id' => STATUS_DEFAULT,
                'value' => STATUS_DEFAULT,
                'name' => __('garages.status_job_' . STATUS_DEFAULT),
            ],
        ]);
    }


    function getZipCode(Request $request)
    {
        $data = District::find($request->id);
        return [
            'success' => true,
            'data' => $data,
        ];
    }

    public static function getGarageType()
    {
        $garage_type = collect([
            (object) [
                'id' => GarageTypeEnum::GENERAL_GARAGE,
                'name' => __('garages.garage_type_' . GarageTypeEnum::GENERAL_GARAGE),
                'value' => GarageTypeEnum::GENERAL_GARAGE,
            ],
            (object) [
                'id' => GarageTypeEnum::GLASS_GARAGE,
                'name' => __('garages.garage_type_' . GarageTypeEnum::GLASS_GARAGE),
                'value' => GarageTypeEnum::GLASS_GARAGE,
            ],
        ]);
        return $garage_type;
    }

    public static function getCarType()
    {
        $car_type = collect([
            (object) [
                'id' => CarTypeAccidentEnum::SEDAN_CAR,
                'name' => __('garages.car_type_' . CarTypeAccidentEnum::SEDAN_CAR),
                'value' => CarTypeAccidentEnum::SEDAN_CAR,
            ],
            (object) [
                'id' => CarTypeAccidentEnum::PICKUP_CAR,
                'name' => __('garages.car_type_' . CarTypeAccidentEnum::PICKUP_CAR),
                'value' => CarTypeAccidentEnum::PICKUP_CAR,
            ],
            (object) [
                'id' => CarTypeAccidentEnum::VAN,
                'name' => __('garages.car_type_' . CarTypeAccidentEnum::VAN),
                'value' => CarTypeAccidentEnum::VAN,
            ],
            (object) [
                'id' => CarTypeAccidentEnum::BUS,
                'name' => __('garages.car_type_' . CarTypeAccidentEnum::BUS),
                'value' => CarTypeAccidentEnum::BUS,
            ],
            (object) [
                'id' => CarTypeAccidentEnum::ALL_TYPE,
                'name' => __('garages.car_type_' . CarTypeAccidentEnum::ALL_TYPE),
                'value' => CarTypeAccidentEnum::ALL_TYPE,
            ],
            (object) [
                'id' => CarTypeAccidentEnum::TRUCK,
                'name' => __('garages.car_type_' . CarTypeAccidentEnum::TRUCK),
                'value' => CarTypeAccidentEnum::TRUCK,
            ],
            (object) [
                'id' => CarTypeAccidentEnum::GENERAL,
                'name' => __('garages.car_type_' . CarTypeAccidentEnum::GENERAL),
                'value' => CarTypeAccidentEnum::GENERAL,
            ],
        ]);
        return $car_type;
    }

    public static function getZoneType()
    {
        $zone_type = collect([
            (object) [
                'id' => ZoneEnum::NORTH,
                'name' => __('garages.zone_type_' . ZoneEnum::NORTH),
                'value' => ZoneEnum::NORTH,
            ],
            (object) [
                'id' => ZoneEnum::NORTHEAST,
                'name' => __('garages.zone_type_' . ZoneEnum::NORTHEAST),
                'value' => ZoneEnum::NORTHEAST,
            ],
            (object) [
                'id' => ZoneEnum::WESTERN,
                'name' => __('garages.zone_type_' . ZoneEnum::WESTERN),
                'value' => ZoneEnum::WESTERN,
            ],
            (object) [
                'id' => ZoneEnum::CENTRAL,
                'name' => __('garages.zone_type_' . ZoneEnum::CENTRAL),
                'value' => ZoneEnum::CENTRAL,
            ],
            (object) [
                'id' => ZoneEnum::SOUTH,
                'name' => __('garages.zone_type_' . ZoneEnum::SOUTH),
                'value' => ZoneEnum::SOUTH,
            ],
        ]);
        return $zone_type;
    }


    public function getCarTypeArray($cradle_id)
    {
        return CradleType::leftJoin('cradles', 'cradles.id', '=', 'cradle_types.cradle_id')
            ->select('cradle_types.type as id')
            ->where('cradle_types.cradle_id', $cradle_id)
            ->pluck('id')
            ->toArray();
    }

    public function getProvinceList()
    {
        $list = Province::select('id', 'name_th as name')
            ->get();
        return $list;
    }
}
