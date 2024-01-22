<?php

namespace App\Http\Controllers\Admin\Util;

use App\Enums\ResponsibleSignYellowTicketEnum;
use App\Enums\TrainingSignYellowTicketEnum;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarClass;
use App\Models\ChangeRegistration;
use App\Models\InsuranceLot;
use App\Models\OwnershipTransfer;
use App\Models\Province;
use App\Models\Register;
use App\Models\SignYellowTicket;
use App\Models\TaxRenewal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Select2SignYellowTicketController extends Controller
{

    function getCarLicensePlate(Request $request)
    {
        $list = SignYellowTicket::leftJoin('cars', 'cars.id', '=', 'sign_yellow_tickets.car_id')
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

    function getCarClasses(Request $request)
    {
        $car_class = SignYellowTicket::leftJoin('cars', 'cars.id', '=', 'sign_yellow_tickets.car_id')
        ->leftJoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
        ->select('car_classes.id', 'car_classes.name', 'car_classes.full_name')
            // ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('car_classes.name', 'like', '%' . $request->s . '%');
                    $query->orWhere('car_classes.full_name', 'like', '%' . $request->s . '%');
                }
            })
            // ->orderBy('seq')
            ->orderBy('name')
            ->distinct('car_classes.id')
            ->limit(30)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->full_name . ' - ' . $item->name
                ];
            });
        return response()->json($car_class);
    }

    function getProvince(Request $request)
    {
        $list = Province::select('id', 'name_th as name')
            // ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name_th', 'like', '%' . $request->s . '%');
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getResponsible(Request $request)
    {
        return collect([
            (object) [
                'id' => ResponsibleSignYellowTicketEnum::DRIVER,
                'text' => __('sign_yellow_tickets.responsible_' . ResponsibleSignYellowTicketEnum::DRIVER),
                'value' => ResponsibleSignYellowTicketEnum::DRIVER,
            ],
            (object) [
                'id' => ResponsibleSignYellowTicketEnum::LADKRABANG,
                'text' => __('sign_yellow_tickets.responsible_' . ResponsibleSignYellowTicketEnum::LADKRABANG),
                'value' => ResponsibleSignYellowTicketEnum::LADKRABANG,
            ],
            (object) [
                'id' => ResponsibleSignYellowTicketEnum::PHUKET,
                'text' => __('sign_yellow_tickets.responsible_' . ResponsibleSignYellowTicketEnum::PHUKET),
                'value' => ResponsibleSignYellowTicketEnum::PHUKET,
            ],
            (object) [
                'id' => ResponsibleSignYellowTicketEnum::CHAINGRAI,
                'text' => __('sign_yellow_tickets.responsible_' . ResponsibleSignYellowTicketEnum::CHAINGRAI),
                'value' => ResponsibleSignYellowTicketEnum::CHAINGRAI,
            ],
            (object) [
                'id' => ResponsibleSignYellowTicketEnum::CHAINGMAI,
                'text' => __('sign_yellow_tickets.responsible_' . ResponsibleSignYellowTicketEnum::CHAINGMAI),
                'value' => ResponsibleSignYellowTicketEnum::CHAINGMAI,
            ],
            (object) [
                'id' => ResponsibleSignYellowTicketEnum::KRABI,
                'text' => __('sign_yellow_tickets.responsible_' . ResponsibleSignYellowTicketEnum::KRABI),
                'value' => ResponsibleSignYellowTicketEnum::KRABI,
            ],
            (object) [
                'id' => ResponsibleSignYellowTicketEnum::PATTAYA,
                'text' => __('sign_yellow_tickets.responsible_' . ResponsibleSignYellowTicketEnum::PATTAYA),
                'value' => ResponsibleSignYellowTicketEnum::PATTAYA,
            ],
            (object) [
                'id' => ResponsibleSignYellowTicketEnum::PRAPADAENG,
                'text' => __('sign_yellow_tickets.responsible_' . ResponsibleSignYellowTicketEnum::PRAPADAENG),
                'value' => ResponsibleSignYellowTicketEnum::PRAPADAENG,
            ],

        ]);
    }

    function getTraining(Request $request)
    {
        return collect([
            (object) [
                'id' => STATUS_ACTIVE,
                'text' => __('sign_yellow_tickets.training_' . STATUS_ACTIVE),
                'value' => STATUS_ACTIVE,
            ],
            (object) [
                'id' => STATUS_DEFAULT,
                'text' => __('sign_yellow_tickets.training_' . STATUS_DEFAULT),
                'value' => STATUS_DEFAULT,
            ],
    
        ]);
    }

    function getMistake(Request $request)
    {
        return collect([
            (object) [
                'id' => STATUS_ACTIVE,
                'text' => __('sign_yellow_tickets.mistake_' . STATUS_ACTIVE),
                'value' => STATUS_ACTIVE,
            ],
            (object) [
                'id' => STATUS_DEFAULT,
                'text' => __('sign_yellow_tickets.mistake_' . STATUS_DEFAULT),
                'value' => STATUS_DEFAULT,
            ],

        ]);
    }

    function getPayment(Request $request)
    {
        return collect([
            (object) [
                'id' => STATUS_ACTIVE,
                'text' => __('sign_yellow_tickets.payment_' . STATUS_ACTIVE),
                'value' => STATUS_ACTIVE,
            ],
            (object) [
                'id' => STATUS_DEFAULT,
                'text' => __('sign_yellow_tickets.payment_' . STATUS_DEFAULT),
                'value' => STATUS_DEFAULT,
            ],

        ]);
    }
    
}
