<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Enums\RentalTypeEnum;
use App\Enums\RentalStatusEnum;
use App\Enums\GPSStatusEnum;
use App\Enums\LongTermRentalStatusEnum;
use App\Enums\ReplacementCarStatusEnum;
use App\Enums\ReplacementTypeEnum;
use App\Models\Car;
use App\Models\GpsCheckSignal;
use App\Models\Rental;
use App\Models\LongTermRental;
use App\Models\RentalLine;
use App\Models\Branch;
use App\Models\LongTermRentalPRCar;
use App\Models\ReplacementCar;
use DateTime;
use Illuminate\Support\Facades\Auth;
use App\Traits\GpsTrait;

class GpsCheckSignalAlertController extends Controller
{
    use GpsTrait;

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSCheckSignalAlert);
        $user = Auth::user();
        $license_plate = $request->license_plate;
        $chassis_no = $request->chassis_no;
        $vid = $request->vid;
        $status = $request->status;
        $must_check_date = $request->must_check_date;
        $check_date = $request->check_date;

        $list = GpsCheckSignal::leftJoin('cars', 'cars.id', '=', 'gps_check_signals.car_id')
            ->where('gps_check_signals.branch_id', $user->branch_id)
            ->select(
                'gps_check_signals.id',
                'gps_check_signals.status',
                'gps_check_signals.must_check_date',
                'gps_check_signals.check_date',
                'gps_check_signals.repair_date',
                'gps_check_signals.remark',
                'gps_check_signals.job_type',
                'gps_check_signals.job_id',
                'cars.license_plate',
                'cars.chassis_no',
                'cars.vid',
            )
            ->orderBy('gps_check_signals.created_at', 'desc')
            // ->search($request)
            ->when($vid, function ($query) use ($vid) {
                return $query->where('cars.vid', 'like', '%' . $vid . '%');
            })
            ->when($chassis_no, function ($query) use ($chassis_no) {
                return $query->where('cars.chassis_no', 'like', '%' . $chassis_no . '%');
            })
            ->when($license_plate, function ($query) use ($license_plate) {
                return $query->where('cars.license_plate', 'like', '%' . $license_plate . '%');
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('gps_check_signals.status', $status);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('gps_check_signals.status', $status);
            })
            ->when($must_check_date, function ($query) use ($must_check_date) {
                return $query->where('gps_check_signals.must_check_date', $must_check_date);
            })
            ->when($check_date, function ($query) use ($check_date) {
                return $query->where('gps_check_signals.check_date', $check_date);
            })
            ->paginate(PER_PAGE);
        $list->map(function ($item) {
            $item->rental_date = '';
            if (strcmp($item->job_type, Rental::class) == 0) {
                $rental = Rental::find($item->job_id);
                $item->rental_date = ($rental) ? get_thai_date_format($rental->pickup_date, 'd/m/Y') : null;
            }
            return $item;
        });

        $license_plate_list = Car::select('cars.license_plate as name', 'cars.license_plate as id')->leftJoin('gps_check_signals', 'gps_check_signals.car_id', '=', 'cars.id')->where('gps_check_signals.branch_id', $user->branch_id)->distinct()->get();
        $chassis_no_list = Car::select('cars.chassis_no as name', 'cars.chassis_no as id')->leftJoin('gps_check_signals', 'gps_check_signals.car_id', '=', 'cars.id')->where('gps_check_signals.branch_id', $user->branch_id)->distinct()->get();
        $vid_list = Car::select('cars.vid as name', 'cars.vid as id')->whereNotNull('vid')->leftJoin('gps_check_signals', 'gps_check_signals.car_id', '=', 'cars.id')->where('gps_check_signals.branch_id', $user->branch_id)->distinct()->get();
        $status_list = GpsTrait::getStatus();

        return view('admin.gps-check-signal-alerts.index', [
            'list' => $list,
            'license_plate_list' => $license_plate_list,
            'chassis_no_list' => $chassis_no_list,
            'vid_list' => $vid_list,
            'license_plate' => $license_plate,
            'chassis_no' => $chassis_no,
            'vid' => $vid,
            'status' => $status,
            'must_check_date' => $must_check_date,
            'check_date' => $check_date,
            'status_list' => $status_list
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::GPSCheckSignalAlert);
        $d = new GpsCheckSignal();
        $user = Auth::user();
        $job_list = [];
        if ($user->branch) {
            if (strcmp($user->branch->is_main, BOOL_TRUE) == 0) {
                $job_list = $this->getJobListMain();
            } else {
                $job_list = $this->getJobListSub();
            }
        }
        $d->worksheet_no = null;
        $d->license_plate = null;
        $page_title = __('lang.create') . __('gps.gps_signal');
        return view('admin.gps-check-signal-alerts.form', [
            'd' =>  $d,
            'page_title' => $page_title,
            'job_list' => $job_list,
            'doc_additional_files' => [],
        ]);
    }

    public function edit(GpsCheckSignal $gps_check_signal_alert)
    {
        $this->authorize(Actions::Manage . '_' . Resources::GPSCheckSignalAlert);
        if (strcmp($gps_check_signal_alert->job_type, Rental::class) === 0) {
            $rental = Rental::find($gps_check_signal_alert->job_id);
            if ($rental) {
                $gps_check_signal_alert->worksheet_no = ($rental->worksheet_no) ? $rental->worksheet_no : '';
                $gps_check_signal_alert->service_type = ($rental && $rental->serviceType) ? __('gps.service_type_' . $rental->serviceType->service_type) : null;
                $gps_check_signal_alert->customer = ($rental) ? $rental->customer_name : null;
                $gps_check_signal_alert->pickup_date = ($rental) ? get_thai_date_format($rental->pickup_date, 'd/m/Y H:i') : null;
                $gps_check_signal_alert->return_date = ($rental) ? get_thai_date_format($rental->return_date, 'd/m/Y H:i') : null;
            }
        }
        if (strcmp($gps_check_signal_alert->job_type, LongTermRental::class) === 0) {
            $long_rental = LongTermRental::find($gps_check_signal_alert->job_id);
            if ($long_rental) {
                $gps_check_signal_alert->worksheet_no = ($long_rental->worksheet_no) ? $long_rental->worksheet_no : '';
                $gps_check_signal_alert->long_type = ($long_rental) ?  __('long_term_rentals.type_' . $long_rental->approval_type) : null;
                $gps_check_signal_alert->rental_duration = ($long_rental) ?  $long_rental->rental_duration . 'เดือน' : null;
                $gps_check_signal_alert->delivery_place =  null;
                $gps_check_signal_alert->delivery_date = ($long_rental) ? get_thai_date_format($long_rental->actual_delivery_date, 'd/m/Y') : null;
            }
        }
        if (strcmp($gps_check_signal_alert->job_type, ReplacementCar::class) === 0) {
            $replacement_car = ReplacementCar::find($gps_check_signal_alert->job_id);
            if ($replacement_car) {
                $gps_check_signal_alert->worksheet_no = ($replacement_car) ? $replacement_car->worksheet_no : null;
                $gps_check_signal_alert->replacement_type = ($replacement_car) ? __('replacement_cars.type_' . $replacement_car->replacement_type) : null;
                $gps_check_signal_alert->replacement_customer = ($replacement_car) ? $replacement_car->customer_name : null;
                $gps_check_signal_alert->replacement_date = ($replacement_car) ? get_thai_date_format($replacement_car->replacement_date, 'd/m/Y') : null;
                $gps_check_signal_alert->replacement_place = ($replacement_car) ? $replacement_car->replacement_place : null;
            }
        }
        $car = Car::find($gps_check_signal_alert->car_id);
        if ($car) {
            $gps_check_signal_alert->license_plate = ($car) ? $car->license_plate : null;
            $gps_check_signal_alert->engine_no = ($car) ? $car->engine_no : null;
            $gps_check_signal_alert->chassis_no = ($car) ? $car->chassis_no : null;;
            $gps_check_signal_alert->car_class = ($car && $car->carClass) ? $car->carClass->full_name : null;
            $gps_check_signal_alert->car_color = ($car && $car->carColor) ? $car->carColor->name : null;
            $gps_check_signal_alert->fleet = ($car) ? $car->fleet : null;
            $gps_check_signal_alert->vid = ($car) ? $car->vid : null;
            $gps_check_signal_alert->sim = ($car) ? $car->sim : null;
        }
        $user = Auth::user();
        $job_list = [];
        if ($user->branch) {
            if (strcmp($user->branch->is_main, BOOL_TRUE) == 0) {
                $job_list = $this->getJobListMain();
            } else {
                $job_list = $this->getJobListSub();
            }
        }

        $doc_additional_files = $gps_check_signal_alert->getMedia('doc_additional_files');
        $doc_additional_files = get_medias_detail($doc_additional_files);

        $page_title = __('lang.edit') . __('gps.gps_signal');
        return view('admin.gps-check-signal-alerts.form',  [
            'page_title' => $page_title,
            'd' => $gps_check_signal_alert,
            'job_list' => $job_list,
            'doc_additional_files' => $doc_additional_files,
        ]);
    }

    public function show(GpsCheckSignal $gps_check_signal_alert)
    {
        $this->authorize(Actions::View . '_' . Resources::GPSCheckSignalAlert);
        if (strcmp($gps_check_signal_alert->job_type, Rental::class) === 0) {
            $rental = Rental::find($gps_check_signal_alert->job_id);
            if ($rental) {
                $gps_check_signal_alert->worksheet_no = ($rental->worksheet_no) ? $rental->worksheet_no : '';
                $gps_check_signal_alert->service_type = ($rental && $rental->serviceType) ? __('gps.service_type_' . $rental->serviceType->service_type) : null;
                $gps_check_signal_alert->customer = ($rental) ? $rental->customer_name : null;
                $gps_check_signal_alert->pickup_date = ($rental) ? get_thai_date_format($rental->pickup_date, 'd/m/Y H:i') : null;
                $gps_check_signal_alert->return_date = ($rental) ? get_thai_date_format($rental->return_date, 'd/m/Y H:i') : null;
            }
        }
        if (strcmp($gps_check_signal_alert->job_type, LongTermRental::class) === 0) {
            $long_rental = LongTermRental::find($gps_check_signal_alert->job_id);
            if ($long_rental) {
                $gps_check_signal_alert->worksheet_no = ($long_rental->worksheet_no) ? $long_rental->worksheet_no : '';
                $gps_check_signal_alert->long_type = ($long_rental) ?  __('long_term_rentals.type_' . $long_rental->approval_type) : null;
                $gps_check_signal_alert->rental_duration = ($long_rental) ?  $long_rental->rental_duration . 'เดือน' : null;
                $gps_check_signal_alert->delivery_place =  null;
                $gps_check_signal_alert->delivery_date = ($long_rental) ? get_thai_date_format($long_rental->actual_delivery_date, 'd/m/Y') : null;
            }
        }
        if (strcmp($gps_check_signal_alert->job_type, ReplacementCar::class) === 0) {
            $replacement_car = ReplacementCar::find($gps_check_signal_alert->job_id);
            if ($replacement_car) {
                $gps_check_signal_alert->worksheet_no = ($replacement_car) ? $replacement_car->worksheet_no : null;
                $gps_check_signal_alert->replacement_type = ($replacement_car) ? __('replacement_cars.type_' . $replacement_car->replacement_type) : null;
                $gps_check_signal_alert->replacement_customer = ($replacement_car) ? $replacement_car->customer_name : null;
                $gps_check_signal_alert->replacement_date = ($replacement_car) ? get_thai_date_format($replacement_car->replacement_date, 'd/m/Y') : null;
                $gps_check_signal_alert->replacement_place = ($replacement_car) ? $replacement_car->replacement_place : null;
            }
        }
        $car = Car::find($gps_check_signal_alert->car_id);
        if ($car) {
            $gps_check_signal_alert->license_plate = ($car) ? $car->license_plate : null;
            $gps_check_signal_alert->engine_no = ($car) ? $car->engine_no : null;
            $gps_check_signal_alert->chassis_no = ($car) ? $car->chassis_no : null;;
            $gps_check_signal_alert->car_class = ($car && $car->carClass) ? $car->carClass->full_name : null;
            $gps_check_signal_alert->car_color = ($car && $car->carColor) ? $car->carColor->name : null;
            $gps_check_signal_alert->fleet = ($car) ? $car->fleet : null;
            $gps_check_signal_alert->vid = ($car) ? $car->vid : null;
            $gps_check_signal_alert->sim = ($car) ? $car->sim : null;
        }
        $user = Auth::user();
        $job_list = [];
        if ($user->branch) {
            if (strcmp($user->branch->is_main, BOOL_TRUE) == 0) {
                $job_list = $this->getJobListMain();
            } else {
                $job_list = $this->getJobListSub();
            }
        }
        $doc_additional_files = $gps_check_signal_alert->getMedia('doc_additional_files');
        $doc_additional_files = get_medias_detail($doc_additional_files);

        $page_title = __('lang.view') . __('gps.gps_signal');
        return view('admin.gps-check-signal-alerts.form',  [
            'page_title' => $page_title,
            'd' => $gps_check_signal_alert,
            'job_list' => $job_list,
            'doc_additional_files' => $doc_additional_files,
            'view' => true,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_type' => ['required'],
            'job_id' => ['required'],
        ], [], [
            'job_type' => __('gps.job_type'),
            'job_id' => __('gps.job_id'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $check_signal = GpsCheckSignal::firstOrNew(['id' => $request->id]);
        $check_signal->job_type = $request->job_type;
        $check_signal->job_id = $request->job_id;
        $check_signal->car_id = $request->car_id;
        $check_signal->must_check_date = $request->must_check_date;
        $check_signal->status = GPSStatusEnum::PENDING;
        $check_signal->save();
        if (strcmp($check_signal->job_type, Rental::class) == 0) {
            $rental = Rental::find($check_signal->job_id);
            if ($rental) {
                $check_signal->branch_id = $rental->branch_id;
                $check_signal->save();
            }
        } else if (in_array($check_signal->job_type, [LongTermRental::class, ReplacementCar::class])) {
            $branch = Branch::where('code', '0500')->where('is_main', STATUS_ACTIVE)->first();
            if ($branch) {
                $check_signal->branch_id = $branch->id;
                $check_signal->save();
            }
        }
        if ($request->doc_additional__pending_delete_ids) {
            $pending_delete_ids = $request->doc_additional__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $check_signal->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('doc_additional')) {
            foreach ($request->file('doc_additional') as $file) {
                if ($file->isValid()) {
                    $check_signal->addMedia($file)->toMediaCollection('doc_additional_files');
                }
            }
        }

        $redirect_route = route('admin.gps-check-signal-alerts.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    private function getJobListMain()
    {
        return collect([
            (object)[
                'id' => Rental::class,
                'value' => Rental::class,
                'name' => __('gps.job_type_' . Rental::class),
            ],
            (object)[
                'id' => LongTermRental::class,
                'value' => LongTermRental::class,
                'name' => __('gps.job_type_' . LongTermRental::class),
            ],
            (object)[
                'id' => ReplacementCar::class,
                'value' => ReplacementCar::class,
                'name' => __('gps.job_type_' . ReplacementCar::class),
            ],
        ]);
    }

    private function getJobListSub()
    {
        return collect([
            (object)[
                'id' => Rental::class,
                'value' => Rental::class,
                'name' => __('gps.job_type_' . Rental::class),
            ],
            (object)[
                'id' => ReplacementCar::class,
                'value' => ReplacementCar::class,
                'name' => __('gps.job_type_' . ReplacementCar::class),
            ],
        ]);
    }

    public function getDefaultJobID(Request $request)
    {
        $job_type = $request->parent_id;
        $data = [];
        $user = Auth::user();
        if (strcmp($job_type, Rental::class) == 0) {
            $data = Rental::select('id', 'worksheet_no')
                ->where('status', RentalStatusEnum::PAID)
                ->where('branch_id', $user->branch_id)
                ->where(function ($query) use ($request) {
                    if (!empty($request->s)) {
                        $query->where('worksheet_no', 'like', '%' . $request->s . '%');
                    }
                })
                ->orderBy('worksheet_no')->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'text' => $item->worksheet_no
                    ];
                });
        }
        if (strcmp($job_type, LongTermRental::class) == 0) {
            $data = LongTermRental::select('id', 'worksheet_no')
                ->where('status', LongTermRentalStatusEnum::COMPLETE)
                ->where(function ($query) use ($request) {
                    if (!empty($request->s)) {
                        $query->where('worksheet_no', 'like', '%' . $request->s . '%');
                    }
                })
                ->orderBy('worksheet_no')->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'text' => $item->worksheet_no
                    ];
                });
        }
        if (strcmp($job_type, ReplacementCar::class) == 0) {
            $data = ReplacementCar::select('id', 'worksheet_no')
                ->whereIn('status', [ReplacementCarStatusEnum::PENDING, ReplacementCarStatusEnum::IN_PROCESS, ReplacementCarStatusEnum::COMPLETE])
                ->where(function ($query) use ($request) {
                    if (!empty($request->s)) {
                        $query->where('worksheet_no', 'like', '%' . $request->s . '%');
                    }
                })
                ->orderBy('worksheet_no')->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'text' => $item->worksheet_no
                    ];
                });
        }
        return response()->json($data);
    }

    public function getDefaultDataJob(Request $request)
    {
        $job_type = $request->job_type;
        $job_id = $request->job_id;
        $data = [];
        if (strcmp($job_type, Rental::class) == 0) {
            $rental = Rental::find($job_id);
            $must_check_date = new DateTime($rental->pickup_date);
            $must_check_date->modify('-1 day');
            $data['worksheet_no'] = ($rental) ? $rental->worksheet_no : null;
            $data['service_type'] = ($rental && $rental->serviceType) ? __('gps.service_type_' . $rental->serviceType->service_type) : null;
            $data['customer'] = ($rental) ? $rental->customer_name : null;
            $data['pickup_date'] = ($rental) ? get_thai_date_format($rental->pickup_date, 'd/m/Y H:i') : null;
            $data['return_date'] = ($rental) ? get_thai_date_format($rental->return_date, 'd/m/Y H:i') : null;
            $data['must_check_date'] = ($must_check_date) ? $must_check_date->format('Y-m-d') : null;
        }
        if (strcmp($job_type, LongTermRental::class) == 0) {
            $long_rental = LongTermRental::find($job_id);
            $must_check_date = new DateTime($long_rental->actual_delivery_date);
            $must_check_date->modify('-1 day');
            $data['worksheet_no'] = ($long_rental) ? $long_rental->worksheet_no : null;
            $data['long_type'] = ($long_rental) ? __('long_term_rentals.type_' . $long_rental->approval_type) : null;
            $data['rental_duration'] = ($long_rental) ? $long_rental->rental_duration . 'เดือน' : null;
            $data['delivery_place'] = '-';
            $data['delivery_date'] = ($long_rental) ? get_thai_date_format($long_rental->actual_delivery_date, 'd/m/Y') : null;
            $data['must_check_date'] = ($must_check_date) ? $must_check_date->format('Y-m-d') : null;
        }
        if (strcmp($job_type, ReplacementCar::class) == 0) {
            $replacement_car = ReplacementCar::find($job_id);
            $must_check_date = new DateTime($replacement_car->replacement_date);
            $must_check_date->modify('-1 day');
            $data['worksheet_no'] = ($replacement_car) ? $replacement_car->worksheet_no : null;
            $data['replacement_type'] = ($replacement_car) ? __('replacement_cars.type_' . $replacement_car->replacement_type) : null;
            $data['replacement_customer'] = ($replacement_car) ? $replacement_car->customer_name : null;
            $data['replacement_date'] = ($replacement_car) ? get_thai_date_format($replacement_car->replacement_date, 'd/m/Y') : null;
            $data['replacement_place'] = ($replacement_car) ? $replacement_car->replacement_place : null;
            $data['must_check_date'] = ($must_check_date) ? $must_check_date->format('Y-m-d') : null;
        }
        return [
            'success' => true,
            'data' => $data,
            'job_type' => $job_type,
        ];
    }

    public function getDefaultCarID(Request $request)
    {
        $job_id = $request->parent_id;
        $job_type = $request->parent_type;
        $data = [];
        if (strcmp($job_type, Rental::class) == 0) {
            $rental_line_car_id = RentalLine::where('rental_id', $job_id)->pluck('car_id')->toArray();
            if ($rental_line_car_id) {
                $data = Car::whereIn('id', $rental_line_car_id)
                    ->select('id', 'license_plate')
                    ->where(function ($query) use ($request) {
                        if (!empty($request->s)) {
                            $query->where('license_plate', 'like', '%' . $request->s . '%');
                        }
                    })
                    ->orderBy('license_plate')->get()->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'text' => $item->license_plate
                        ];
                    });
            }
        }
        if (strcmp($job_type, LongTermRental::class) == 0) {
            $rental_car_id = LongTermRentalPRCar::leftJoin('lt_rental_pr_lines', 'lt_rental_pr_lines.id', '=', 'lt_rental_pr_lines_cars.lt_rental_pr_line_id')
                ->leftJoin('lt_rentals', 'lt_rentals.id', '=', 'lt_rental_pr_lines.lt_rental_id')
                ->where('lt_rentals.id', $job_id)
                ->pluck('lt_rental_pr_lines_cars.car_id')->toArray();
            if ($rental_car_id) {
                $data = Car::whereIn('id', $rental_car_id)
                    ->select('id', 'license_plate')
                    ->where(function ($query) use ($request) {
                        if (!empty($request->s)) {
                            $query->where('license_plate', 'like', '%' . $request->s . '%');
                        }
                    })
                    ->orderBy('license_plate')->get()->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'text' => $item->license_plate
                        ];
                    });
            }
        }

        if (strcmp($job_type, ReplacementCar::class) == 0) {
            $replacement_car = ReplacementCar::find($job_id);
            $car_id = null;
            if ($replacement_car) {
                if (in_array($replacement_car->replacement_type, [ReplacementTypeEnum::SEND_MAIN_RECEIVE_REPLACE, ReplacementTypeEnum::SEND_MAIN])) {
                    $car_id = $replacement_car->main_car_id;
                }
                if (in_array($replacement_car->replacement_type, [ReplacementTypeEnum::SEND_REPLACE_RECEIVE_MAIN, ReplacementTypeEnum::SEND_REPLACE])) {
                    $car_id = $replacement_car->replacement_car_id;
                }
                if ($car_id) {
                    $data = Car::where('id', $car_id)
                        ->select('id', 'license_plate')
                        ->where(function ($query) use ($request) {
                            if (!empty($request->s)) {
                                $query->where('license_plate', 'like', '%' . $request->s . '%');
                            }
                        })
                        ->orderBy('license_plate')->get()->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'text' => $item->license_plate
                            ];
                        });
                }
            }
        }
        return response()->json($data);
    }

    public function getDefaultDataCar(Request $request)
    {
        $car_id = $request->car_id;
        $data = [];
        $car = Car::find($car_id);
        $data['engine_no'] = ($car) ? $car->engine_no : null;
        $data['chassis_no'] = ($car) ? $car->chassis_no : null;;
        $data['car_class'] = ($car && $car->carClass) ? $car->carClass->full_name : null;
        $data['car_color'] = ($car && $car->carColor) ? $car->carColor->name : null;
        $data['fleet'] = ($car) ? $car->fleet : null;
        $data['vid'] = ($car) ? $car->vid : null;
        $data['sim'] = ($car) ? $car->sim : null;
        return [
            'success' => true,
            'data' => $data,
        ];
    }
}
