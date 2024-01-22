<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\MFlowStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\AccidentRepairOrder;
use Illuminate\Support\Facades\Validator;
use App\Models\Car;
use App\Models\ContractLines;
use App\Models\Contracts;
use App\Models\Cradle;
use App\Models\CustomerGroup;
use App\Models\CustomerGroupRelation;
use App\Models\DrivingJob;
use App\Models\ExpressWay;
use App\Models\MFlow;
use App\Models\Rental;
use App\Models\RentalDriver;
use App\Models\Repair;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MFlowController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::MFlow);
        $m_flow_id = $request->m_flow_id;
        $worksheet_no = null;
        if ($m_flow_id) {
            $m_flow = MFlow::find($m_flow_id);
            $worksheet_no = $m_flow ? $m_flow->worksheet_no : null;
        }
        $car_id = $request->car_id;
        $license_plate = null;
        if ($car_id) {
            $car = Car::find($car_id);
            $license_plate = $car ? $car->license_plate : null;
        }

        $expressway_id = $request->expressway_id;
        $m_flow_station_text = null;
        if ($expressway_id) {
            $expressway = ExpressWay::find($expressway_id);
            $m_flow_station_text = $expressway ? $expressway->name : null;
        }
        $list = MFlow::leftJoin('cars', 'cars.id', '=', 'm_flows.car_id')
            ->leftJoin('expressways', 'expressways.id', '=', 'm_flows.expressway_id')
            ->select(
                'm_flows.*',
                'cars.license_plate',
                'expressways.name as expressway_name',
            )
            ->search($request->s, $request)
            ->orderBy('m_flows.worksheet_no', 'DESC')
            ->paginate(PER_PAGE);

        $list->map(function ($item) {
            $item->offensedate = date('d/m/Y', strtotime($item->offense_date));
            return $item;
        });

        $status_list = $this->getStatusList();
        return view('admin.m-flows.index', [
            'list' => $list,
            's' => $request->s,
            'offense_date' => $request->offense_date,
            'status' => $request->status,
            'm_flow_id' => $m_flow_id,
            'worksheet_no' => $worksheet_no,
            'car_id' => $car_id,
            'license_plate' => $license_plate,
            'expressway_id' => $expressway_id,
            'm_flow_station_text' => $m_flow_station_text,
            'status_list' => $status_list
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::MFlow);
        $d = new MFlow();
        $d->status = MFlowStatusEnum::DRAFT;
        $d->is_payment = BOOL_TRUE;
        $express_way_list = ExpressWay::where('is_expressway', BOOL_FALSE)->select('id', 'name')->get()->map(function ($item) {
            $item->id = $item->id;
            $item->text = $item->name;
            return $item;
        });
        $payment_list = $this->getPaymentList();
        $step = $this->setProgressStep($d->status);
        $page_title =  __('m_flows.add_new');
        return view('admin.m-flows.form', [
            'd' => $d,
            'page_title' => $page_title,
            'express_way_list' => $express_way_list,
            'payment_list' => $payment_list,
            'step' => $step,
        ]);
    }

    public function edit(MFlow $m_flow)
    {
        $m_flow->overdue_date = date('Y-m-d', strtotime($m_flow->offense_date));
        $m_flow->offense_time = date('H:i', strtotime($m_flow->offense_date));

        $data_form = $this->getDataForm($m_flow->car_id, $m_flow->overdue_date, $m_flow->offense_time, $m_flow->job_type, $m_flow->job_id);
        $m_flow->rental_no = $data_form['rental_no'];
        $m_flow->rental_name = $data_form['rental_name'];
        $m_flow->customer_address = $data_form['customer_address'];
        $m_flow->contract_no = $data_form['contract_no'];
        $m_flow->contract_start_date = $data_form['contract_start_date'];
        $m_flow->contract_end_date = $data_form['contract_end_date'];
        $m_flow->full_name = $data_form['full_name'];
        $m_flow->driver_tel = $data_form['driver_tel'];
        $m_flow->car_type = $data_form['car_type'];
        $m_flow->customer_group = $data_form['customer_group'];

        $express_way_list = ExpressWay::where('is_expressway', BOOL_FALSE)->select('id', 'name')->get()->map(function ($item) {
            $item->id = $item->id;
            $item->text = $item->name;
            return $item;
        });
        $payment_list = $this->getPaymentList();
        $car_license = null;
        $car = Car::find($m_flow->car_id);
        if ($car) {
            if ($car->license_plate) {
                $car_license = $car->license_plate;
            } else if ($car->engine_no) {
                $car_license = __('inspection_cars.engine_no') . ' ' . $car->engine_no;
            } else if ($car->chassis_no) {
                $car_license = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
            }
        }
        $m_flow_list = $this->getMflow($m_flow->car_id, $m_flow->id, $m_flow->overdue_date);
        $overdue_file_medias = $m_flow->getMedia('overdue_file');
        $overdue_file = get_medias_detail($overdue_file_medias);
        $payment_file_medias = $m_flow->getMedia('payment_file');
        $payment_file = get_medias_detail($payment_file_medias);
        $step = $this->setProgressStep($m_flow->status);

        $page_title =  __('lang.edit') . __('m_flows.sheet') . ' ' . __('m_flows.page_title');
        return view('admin.m-flows.form-edit', [
            'd' => $m_flow,
            'page_title' => $page_title,
            'express_way_list' => $express_way_list,
            'payment_list' => $payment_list,
            'car_license' => $car_license,
            'm_flow_list' => $m_flow_list,
            'overdue_file' => $overdue_file,
            'payment_file' => $payment_file,
            'step' => $step,
        ]);
    }

    public function show(MFlow $m_flow)
    {
        $m_flow->overdue_date = date('Y-m-d', strtotime($m_flow->offense_date));
        $m_flow->offense_time = date('H:i', strtotime($m_flow->offense_date));

        $data_form = $this->getDataForm($m_flow->car_id, $m_flow->overdue_date, $m_flow->offense_time, $m_flow->job_type, $m_flow->job_id);
        $m_flow->rental_no = $data_form['rental_no'];
        $m_flow->rental_name = $data_form['rental_name'];
        $m_flow->customer_address = $data_form['customer_address'];
        $m_flow->contract_no = $data_form['contract_no'];
        $m_flow->contract_start_date = $data_form['contract_start_date'];
        $m_flow->contract_end_date = $data_form['contract_end_date'];
        $m_flow->full_name = $data_form['full_name'];
        $m_flow->driver_tel = $data_form['driver_tel'];
        $m_flow->car_type = $data_form['car_type'];
        $m_flow->customer_group = $data_form['customer_group'];

        $express_way_list = ExpressWay::where('is_expressway', BOOL_FALSE)->select('id', 'name')->get()->map(function ($item) {
            $item->id = $item->id;
            $item->text = $item->name;
            return $item;
        });
        $payment_list = $this->getPaymentList();
        $car_license = null;
        $car = Car::find($m_flow->car_id);
        if ($car) {
            if ($car->license_plate) {
                $car_license = $car->license_plate;
            } else if ($car->engine_no) {
                $car_license = __('inspection_cars.engine_no') . ' ' . $car->engine_no;
            } else if ($car->chassis_no) {
                $car_license = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
            }
        }
        $m_flow_list = $this->getMflow($m_flow->car_id, $m_flow->id, $m_flow->overdue_date);
        $overdue_file_medias = $m_flow->getMedia('overdue_file');
        $overdue_file = get_medias_detail($overdue_file_medias);
        $payment_file_medias = $m_flow->getMedia('payment_file');
        $payment_file = get_medias_detail($payment_file_medias);
        $step = $this->setProgressStep($m_flow->status);

        $page_title =  __('lang.view') . __('m_flows.sheet') . ' ' .  __('m_flows.page_title');
        return view('admin.m-flows.form-edit', [
            'd' => $m_flow,
            'page_title' => $page_title,
            'express_way_list' => $express_way_list,
            'payment_list' => $payment_list,
            'car_license' => $car_license,
            'm_flow_list' => $m_flow_list,
            'overdue_file' => $overdue_file,
            'payment_file' => $payment_file,
            'step' => $step,
            'view' => true,
        ]);
    }

    public function store(Request $request)
    {
        $custom_rules = [];
        if (strcmp($request->create, BOOL_TRUE) == 0) {
            $custom_rules = [
                'car_id' => ['required',],
                'overdue_date' => ['required',],
                'offense_data' => ['required', 'array', 'min:1'],
                'offense_data.*.offense_time' => ['required',],
            ];

            if (strcmp($request->status, MFlowStatusEnum::PENDING) == 0) {
                $custom_rules = [
                    'car_id' => ['required',],
                    'overdue_date' => ['required',],
                    'document_date' => ['required',],
                    'offense_data' => ['required', 'array', 'min:1'],
                    'offense_data.*.offense_time' => ['required',],
                    'offense_data.*.expressway_id' => ['required',],
                    'offense_data.*.fine' => ['required', 'min:0'],
                ];
            }
        } else {
            if (strcmp($request->status, MFlowStatusEnum::PENDING) == 0) {
                $custom_rules = [
                    'document_date' => ['required',],
                    'expressway_id' => ['required',],
                ];
            }

            if (strcmp($request->status, MFlowStatusEnum::IN_PROCESS) == 0) {
                $custom_rules = [
                    'notification_date' => ['required',],
                ];
            }

            if (strcmp($request->status, MFlowStatusEnum::COMPLETE) == 0) {
                $custom_rules = [
                    'payment_date' => ['required',],
                ];
            }
        }
        $validator = Validator::make($request->all(), $custom_rules, [], [
            'car_id' => __('m_flows.license_plate'),
            'overdue_date' => __('m_flows.overdue_date'),
            'offense_data' => __('m_flows.offense_list'),
            'offense_data.*.offense_time' => __('m_flows.offense_time'),
            'car_id' => __('m_flows.license_plate'),
            'overdue_date' => __('m_flows.overdue_date'),
            'document_date' => __('m_flows.document_date'),
            'offense_data' => __('m_flows.offense_list'),
            'offense_data.*.offense_time' => __('m_flows.offense_time'),
            'offense_data.*.expressway_id' => __('m_flows.station_place'),
            'offense_data.*.fine' => __('m_flows.fine'),
            'document_date' => __('m_flows.document_date'),
            'expressway_id' => __('m_flows.station_place'),
            'notification_date' => __('m_flows.notification_date'),
            'payment_date' => __('m_flows.payment_date'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        if (strcmp($request->status, MFlowStatusEnum::PENDING) == 0 && !in_array($request->create, [BOOL_TRUE])) {
            if ($request->fine <= 0) {
                return $this->responseWithCode(false,  __('m_flows.fine_gt_zero'), null, 422);
            }
        }
        $this->trimComma($request, ['fee', 'fine', 'maximum_fine']);
        $status = MFlowStatusEnum::DRAFT;
        if (in_array($request->status, [MFlowStatusEnum::PENDING, MFlowStatusEnum::IN_PROCESS, MFlowStatusEnum::COMPLETE])) {
            $status = $request->status;
        }

        $files_arr = $request->file('files_arr');

        if (strcmp($request->create, BOOL_TRUE) == 0) {
            if ($request->offense_data) {
                foreach ($request->offense_data as $index => $item) {
                    $offense_datetime = Carbon::parse($request->overdue_date . ' ' . $item['offense_time'])->format('Y-m-d H:i:s');
                    $m_flow_nw = new MFlow();
                    $m_flow_nw->worksheet_no = generate_worksheet_no(MFlow::class, false);
                    $m_flow_nw->offense_date = $offense_datetime;
                    $m_flow_nw->car_id = $request->car_id;
                    $m_flow_nw->job_type = $item['job_type']  ?? null;
                    $m_flow_nw->job_id = $item['job_id']  ?? null;
                    $m_flow_nw->expressway_id = $item['expressway_id']  ?? null;
                    $m_flow_nw->document_date = $request->document_date;
                    $m_flow_nw->fee = transform_float($item['fee'])  ?? '0';
                    $m_flow_nw->fine = transform_float($item['fine'])  ?? '0';
                    $m_flow_nw->maximum_fine = $request->maximum_fine;
                    $m_flow_nw->is_payment = $request->is_payment;
                    if ((strcmp($request->is_payment, BOOL_FALSE) == 0) && (strcmp($request->status, MFlowStatusEnum::PENDING) == 0)) {
                        $m_flow_nw->status = MFlowStatusEnum::COMPLETE;
                    } else {
                        $m_flow_nw->status = $status;
                    }
                    $m_flow_nw->save();

                    // if ($request->overdue_file__pending_delete_ids) {
                    //     $pending_delete_ids = $request->overdue_file__pending_delete_ids;
                    //     if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    //         foreach ($pending_delete_ids as $media_id) {
                    //             $m_flow_nw->deleteMedia($media_id);
                    //         }
                    //     }
                    // }
                    if ($files_arr) {
                        foreach ($files_arr[$index] as $file) {
                            $m_flow_nw->addMedia($file)->toMediaCollection('overdue_file');
                        }
                    }
                }
                // dd($request->file());
            }
        } else {
            $m_flow = MFlow::firstOrNew(['id' => $request->id]);
            if ((strcmp($request->status, MFlowStatusEnum::PENDING) == 0) || (strcmp($request->is_draft, BOOL_TRUE) == 0)) {
                $m_flow->expressway_id = $request->expressway_id;
                $m_flow->document_date = $request->document_date;
                $m_flow->fee = $request->fee;
                $m_flow->fine = $request->fine;
                $m_flow->maximum_fine = $request->maximum_fine;
                $m_flow->is_payment = $request->is_payment;
            }
            if (strcmp($request->status, MFlowStatusEnum::IN_PROCESS) == 0) {
                $m_flow->notification_date = $request->notification_date;
                $m_flow->remark = $request->remark;
            }
            if (strcmp($request->status, MFlowStatusEnum::COMPLETE) == 0) {
                $m_flow->payment_date = $request->payment_date;
                $m_flow->is_payment = BOOL_FALSE;
            }
            if ((strcmp($request->is_payment, BOOL_FALSE) == 0) && (strcmp($request->status, MFlowStatusEnum::PENDING) == 0)) {
                $m_flow->status = MFlowStatusEnum::COMPLETE;
            } else {
                $m_flow->status = $status;
            }
            $m_flow->save();

            $this->saveOverdueImage($request, $m_flow);
            $this->savePaymentImage($request, $m_flow);
            if (strcmp($m_flow->status, MFlowStatusEnum::COMPLETE) == 0) {
                $overdue_date = date('Y-m-d', strtotime($m_flow->offense_date));
                $m_flows = $this->getMflow($m_flow->car_id, $m_flow->id, $overdue_date);
                $m_flow_ids = $m_flows->pluck('id')->toArray();
                MFlow::whereIn('id', $m_flow_ids)->update([
                    'status' => MFlowStatusEnum::COMPLETE,
                    'is_payment' => BOOL_FALSE,
                ]);
            }
        }

        $redirect_route = route('admin.m-flows.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    function savePaymentImage($request, $m_flow)
    {
        if ($request->payment_file__pending_delete_ids) {
            $pending_delete_ids = $request->payment_file__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $m_flow->deleteMedia($media_id);
                }
            }
        }
        if ($request->hasFile('payment_file')) {
            foreach ($request->file('payment_file') as $file) {
                if ($file->isValid()) {
                    $m_flow->addMedia($file)->toMediaCollection('payment_file');
                }
            }
        }
    }

    function saveOverdueImage($request, $m_flow)
    {
        if ($request->overdue_file__pending_delete_ids) {
            $pending_delete_ids = $request->overdue_file__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $m_flow->deleteMedia($media_id);
                }
            }
        }
        if ($request->hasFile('overdue_file')) {
            foreach ($request->file('overdue_file') as $file) {
                if ($file->isValid()) {
                    $m_flow->addMedia($file)->toMediaCollection('overdue_file');
                }
            }
        }
    }

    public function storeClose(Request $request)
    {
        if (strcmp($request->status, MFlowStatusEnum::CLOSE) == 0) {
            $m_flow = MFlow::find($request->id);
            $m_flow->notification_date = $request->notification_date;
            $m_flow->remark = $request->remark;
            $m_flow->payment_date = $request->payment_date;
            $m_flow->status = MFlowStatusEnum::CLOSE;
            $m_flow->save();

            $this->savePaymentImage($request, $m_flow);
        }

        $redirect_route = route('admin.m-flows.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    private function getMflow($car_id, $id, $overdue_date)
    {
        return MFlow::where('car_id', $car_id)->whereDate('offense_date', $overdue_date)
            ->whereNotIn('id', [$id])
            ->whereNotIn('status', [MFlowStatusEnum::CLOSE, MFlowStatusEnum::COMPLETE])->get()->map(function ($item) {
                $item->exprssway_name = $item->expressWay ? $item->expressWay->name : null;
                $item->offense_format = date('d/m/Y', strtotime($item->offense_date));
                return $item;
            });
    }

    public function getStatusList()
    {
        return collect([
            (object) [
                'id' => MFlowStatusEnum::PENDING,
                'name' => __('m_flows.status_' . MFlowStatusEnum::PENDING),
                'value' => MFlowStatusEnum::PENDING,
            ],
            (object) [
                'id' => MFlowStatusEnum::IN_PROCESS,
                'name' => __('m_flows.status_' . MFlowStatusEnum::IN_PROCESS),
                'value' => MFlowStatusEnum::IN_PROCESS,
            ],
            (object) [
                'id' => MFlowStatusEnum::COMPLETE,
                'name' => __('m_flows.status_' . MFlowStatusEnum::COMPLETE),
                'value' => MFlowStatusEnum::COMPLETE,
            ],
            (object) [
                'id' => MFlowStatusEnum::CLOSE,
                'name' => __('m_flows.status_' . MFlowStatusEnum::CLOSE),
                'value' => MFlowStatusEnum::CLOSE,
            ],
        ]);
    }

    public static function getPaymentList()
    {
        return collect([
            [
                'id' => BOOL_FALSE,
                'value' => BOOL_FALSE,
                'name' => __('m_flows.is_payment_' . BOOL_FALSE),
            ],
            [
                'id' => BOOL_TRUE,
                'value' => BOOL_TRUE,
                'name' => __('m_flows.is_payment_' . BOOL_TRUE),
            ],
        ]);
    }

    public function getCarData(Request $request)
    {
        $offense_time = $request->offense_time;
        $car_id = $request->car_id;
        $overdue_date = $request->overdue_date;
        $data = [];
        $offense_datetime = Carbon::parse($overdue_date . ' ' . $offense_time)->format('Y-m-d H:i:s');

        $driving_job = DrivingJob::where('actual_start_date', '<=', $offense_datetime)->where('actual_end_date', '>=', $offense_datetime)->where('car_id', $car_id)->first();
        if (!empty($driving_job)) {
            if (strcmp($driving_job->job_type, Rental::class) == 0) {
                $rental = Rental::find($driving_job->job_id);
                $data['job_id'] = $rental ? $rental->id : null;
                $data['job_type'] = $rental ? Rental::class : null;
                $data['rental_no'] = $rental ? $rental->worksheet_no : null;
                $data['rental_name'] = $rental ? $rental->customer_name : null;
                $data['customer_address'] = $rental ? $rental->customer_address : null;
                $contract_line = ContractLines::where('pick_up_date', '<=', $offense_datetime)->where('return_date', '>=', $offense_datetime)->where('car_id', $car_id)->first();
                if (!empty($contract_line)) {
                    $contract = Contracts::find($contract_line->contract_id);
                    $data['contract_no'] = $contract ? $contract->worksheet_no : null;
                    $data['contract_start_date'] = $contract_line ? $contract_line->pick_up_date : null;
                    $data['contract_end_date'] = $contract_line ? $contract_line->return_date : null;
                }
                $rental_driver = RentalDriver::where('rental_id', $rental->id)->first();
                if (!empty($rental_driver)) {
                    $data['full_name'] = $rental_driver ? $rental_driver->name : null;
                    $data['driver_tel'] = $rental_driver ? $rental_driver->tel : null;
                } else {
                    $data['full_name'] = $rental ? $rental->customer_name : null;
                    $data['driver_tel'] = $rental ? $rental->customer_tel : null;
                }
                $customer_group_ids = CustomerGroupRelation::where('customer_id', $rental->customer_id)->pluck('customer_group_id');
                $customer_group = CustomerGroup::whereIn('id', $customer_group_ids)
                    ->select(DB::raw("group_concat(name  SEPARATOR ', ')  as customer_group"))
                    ->value('customer_group');
                $data['customer_group'] = $customer_group;
                $car = Car::find($car_id);
                $data['car_type'] = $car ? __('cars.rental_type_' . $car->rental_type) : null;
            }
        } else {
            $repair = Repair::whereDate('in_center_date', '=', $overdue_date)->where('car_id', $car_id)->first();
            if ($repair) {
                $data['job_id'] = $repair ? $repair->id : null;
                $data['job_type'] = $repair ? Repair::class : null;
                $data['full_name'] = $repair ? $repair->contact : null;
                $data['driver_tel'] = $repair ? $repair->tel : null;
            }
            $accident = AccidentRepairOrder::leftJoin('accidents', 'accidents.id', '=', 'accident_repair_orders.accident_id')
                ->where('accidents.car_id', $car_id)
                ->whereDate('accident_repair_orders.repair_date', '<=', $overdue_date)
                ->whereDate(
                    DB::raw('DATE_ADD(accident_repair_orders.repair_date, INTERVAL accident_repair_orders.amount_completed DAY)'),
                    '>=',
                    $overdue_date
                )
                ->select('accident_repair_orders.*')
                ->first();
            if ($accident) {
                $cradle = Cradle::find($accident->cradle_id);
                if ($cradle) {
                    $data['job_id'] = $accident ? $accident->id : null;
                    $data['job_type'] = $accident ? AccidentRepairOrder::class : null;
                    $data['full_name'] = $cradle ?  $cradle->name : null;
                    $data['driver_tel'] = $cradle ? $cradle->cradle_tel : null;
                }
            }
        }
        return [
            'success' => true,
            'data' => $data,
        ];
    }

    public function getDataForm($car_id, $offense_time, $overdue_date, $job_type = null, $job_id = null)
    {
        $rental_no = null;
        $rental_name = null;
        $customer_address = null;
        $contract_no = null;
        $contract_start_date = null;
        $contract_end_date = null;
        $full_name = null;
        $driver_tel = null;
        $car_type = null;
        $customer_group = null;
        $offense_datetime = Carbon::parse($overdue_date . ' ' . $offense_time)->format('Y-m-d H:i:s');
        if (strcmp($job_type, Rental::class) == 0) {
            $rental = Rental::find($job_id);
            if ($rental) {
                $rental_no = $rental ? $rental->worksheet_no : null;
                $rental_name = $rental ? $rental->customer_name : null;
                $customer_address = $rental ? $rental->customer_address : null;
                $contract_line = ContractLines::where('pick_up_date', '<=', $offense_datetime)->where('return_date', '>=', $offense_datetime)->where('car_id', $car_id)->first();
                if (!empty($contract_line)) {
                    $contract = Contracts::find($contract_line->contract_id);
                    $contract_no = $contract ? $contract->worksheet_no : null;
                    $contract_start_date = $contract_line ? $contract_line->pick_up_date : null;
                    $contract_end_date = $contract_line ? $contract_line->return_date : null;
                }
                $rental_driver = RentalDriver::where('rental_id', $rental->id)->first();
                if (!empty($rental_driver)) {
                    $full_name = $rental_driver ? $rental_driver->name : null;
                    $driver_tel = $rental_driver ? $rental_driver->tel : null;
                } else {
                    $full_name = $rental ? $rental->customer_name : null;
                    $driver_tel = $rental ? $rental->customer_tel : null;
                }
                $customer_group_ids = CustomerGroupRelation::where('customer_id', $rental->customer_id)->pluck('customer_group_id');
                $customer_group = CustomerGroup::whereIn('id', $customer_group_ids)
                    ->select(DB::raw("group_concat(name  SEPARATOR ', ')  as customer_group"))
                    ->value('customer_group');
                $customer_group = $customer_group;
                $car = Car::find($car_id);
                $car_type = $car ? __('cars.rental_type_' . $car->rental_type) : null;
            }
        } else {
            $repair = Repair::whereDate('in_center_date', '=', $overdue_date)->where('car_id', $car_id)->first();
            if ($repair) {
                $full_name = $repair ? $repair->contact : null;
                $driver_tel = $repair ? $repair->tel : null;
            }
            $accident = AccidentRepairOrder::leftJoin('accidents', 'accidents.id', '=', 'accident_repair_orders.accident_id')
                ->where('accidents.car_id', $car_id)
                ->whereDate('accident_repair_orders.repair_date', '<=', $overdue_date)
                ->whereDate(
                    DB::raw('DATE_ADD(accident_repair_orders.repair_date, INTERVAL accident_repair_orders.amount_completed DAY)'),
                    '>=',
                    $overdue_date
                )
                ->select('accident_repair_orders.*')
                ->first();
            if ($accident) {
                $cradle = Cradle::find($accident->cradle_id);
                if ($cradle) {
                    $full_name = $cradle ?  $cradle->name : null;
                    $driver_tel = $cradle ? $cradle->cradle_tel : null;
                }
            }
        }
        return [
            'rental_no' => $rental_no,
            'rental_name' => $rental_name,
            'customer_address' => $customer_address,
            'contract_no' => $contract_no,
            'contract_start_date' => $contract_start_date,
            'contract_end_date' => $contract_end_date,
            'full_name' => $full_name,
            'driver_tel' => $driver_tel,
            'car_type' => $car_type,
            'customer_group' => $customer_group,
        ];
    }

    private function setProgressStep($status_step)
    {
        $step = -1;
        if (in_array($status_step, [MFlowStatusEnum::DRAFT])) {
            $step = 0;
        } elseif (in_array($status_step, [MFlowStatusEnum::PENDING])) {
            $step = 1;
        } elseif (in_array($status_step, [MFlowStatusEnum::IN_PROCESS])) {
            $step = 2;
        } elseif (in_array($status_step, [MFlowStatusEnum::COMPLETE])) {
            $step = 3;
        }
        return $step;
    }
}
