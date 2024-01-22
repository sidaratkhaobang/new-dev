<?php

namespace App\Http\Controllers\Admin\Util;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarClass;
use App\Models\ChangeRegistration;
use App\Models\InsuranceLot;
use App\Models\OwnershipTransfer;
use App\Models\Register;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Select2ChangeRegistrationController extends Controller
{

    function getCarLicensePlate(Request $request)
    {
        $list = ChangeRegistration::leftJoin('cars', 'cars.id', '=', 'change_registrations.car_id')
            ->select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
            // $list = Car::select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no')
            //     ->leftJoin('import_car_lines', 'import_car_lines.id', '=', 'cars.id')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('cars.license_plate', 'like', '%' . $request->s . '%');
                    $query->orWhere('cars.engine_no', 'like', '%' . $request->s . '%');
                    $query->orWhere('cars.chassis_no', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                if ($item->license_plate) {
                    $text = $item->license_plate;
                } else if ($item->engine_no) {
                    $text = __('inspection_cars.engine_no') . ' ' . $item->engine_no;
                } else if ($item->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $item->chassis_no;
                }
                $item->id = $item->id;
                $item->text = $text;
                return $item;
            });

        return response()->json($list);
    }

    public function getDefaultDataCar(Request $request)
    {
        $car_id = $request->car_id;
        $data = [];
        $car = Car::find($car_id);
        $data['branch'] = null;
        // if ($car) {
        //     $branch = Branch::find($car->branch_id);
        //     $data['branch'] = ($branch) ? $branch->name : null;
        // }
        $data['engine_no'] = ($car) ? $car->engine_no : null;
        $data['chassis_no'] = ($car) ? $car->chassis_no : null;
        $data['cc'] = ($car) ? $car->engine_size : null;;
        $data['car_class'] = ($car && $car->carClass) ? $car->carClass->full_name : null;
        $data['car_color'] = ($car && $car->carColor) ? $car->carColor->name : null;
        $data['car_characteristic_transport'] = ($car && $car->register && $car->register->carCharacteristicTransport) ? $car->register->carCharacteristicTransport->name : null;
        $data['color_registered'] = ($car && $car->register) ? __('registers.registered_color_' . $car->register->color_registered) : null;
        $data['registered_sign'] = ($car && $car->register) ? __('registers.registered_sign_type_' . $car->register->registered_sign) : null;
        $data['car_category'] = ($car && $car->carCategory) ? $car->carCategory->name : null;
        $data['leasing'] = ($car && $car->creditor) ? $car->creditor->name : null;
        $data['status'] = ($car && $car->status) ? __('cars.status_' . $car->status) : null;
        return [
            'success' => true,
            'data' => $data,
        ];
    }

    public function getUserDetail(Request $request)
    {
        $user_id = $request->user_id;
        $data = [];
        $user = User::find($user_id);
        if ($user) {
            $data['department'] = ($user && $user->departmentUser) ? $user->departmentUser->name : null;
            $data['role'] = ($user && $user->role) ? $user->role->name : null;
        }
        // dd($data);

        return [
            'success' => true,
            'data' => $data,
        ];
    }
}
