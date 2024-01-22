<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Enums\AccidentRepairStatusEnum;
use App\Enums\AccidentSlideEnum;
use App\Enums\AccidentStatusEnum;
use App\Enums\Actions;
use App\Enums\CarEnum;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\LongTermRentalStatusEnum;
use App\Enums\MistakeTypeEnum;
use App\Enums\RentalStatusEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Mail\AccidentRepairOrderMail;
use App\Models\Accident;
use App\Models\AccidentClaimLines;
use App\Models\AccidentExpense;
use App\Models\AccidentRepairLinePrice;
use App\Models\AccidentRepairOrder;
use App\Models\AccidentRepairOrderLine;
use App\Models\AccidentSlide;
use App\Models\Amphure;
use App\Models\Car;
use App\Models\ClaimList;
use App\Models\Contracts;
use App\Models\Cradle;
use App\Models\District;
use App\Models\LongTermRentalLine;
use App\Models\LongTermRentalPRCar;
use App\Models\Province;
use App\Models\RentalLine;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use App\Traits\RepairTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\AccidentTrait;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Traits\HistoryTrait;
use Illuminate\Support\Facades\Mail;
use App\Mail\RepairOrderMail;
use App\Models\GpsRemoveStopSignal;
use App\Models\Insurer;
use App\Models\RepairOrderDate;
use Illuminate\Support\Facades\App;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Traits\GpsTrait;
use App\Enums\GPSJobTypeEnum;
use App\Enums\InsuranceCarStatusEnum;
use App\Models\ReplacementCar;
use App\Models\Slide;
use App\Models\VMI;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccidentOrderController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::AccidentOrder);
        $worksheet = $request->repair_worksheet_no;
        $accident_worksheet_no = $request->accident_worksheet_no;
        $license_plate = $request->license_plate;
        $license_plate_text = null;
        if ($license_plate) {
            $license_plate_model = Car::find($license_plate);
            if ($license_plate_model->license_plate) {
                $license_plate_text = $license_plate_model->license_plate;
            } else if ($license_plate_model->engine_no) {
                $license_plate_text = __('inspection_cars.engine_no') . ' ' . $license_plate_model->engine_no;
            } else if ($license_plate_model->chassis_no) {
                $license_plate_text = __('inspection_cars.chassis_no') . ' ' . $license_plate_model->chassis_no;
            }
        }
        $accident = Accident::find($accident_worksheet_no);
        $accident_worksheet_text = $accident && $accident->worksheet_no ? $accident->worksheet_no : null;
        $status = $request->status;
        $accident_repair_order = AccidentRepairOrder::find($worksheet);
        $worksheet_text = $accident_repair_order && $accident_repair_order->worksheet_no ? $accident_repair_order->worksheet_no : null;
        $list = AccidentRepairOrder::leftJoin('accidents', 'accidents.id', '=', 'accident_repair_orders.accident_id')
            ->leftJoin('cradles', 'cradles.id', '=', 'accident_repair_orders.cradle_id')
            ->leftJoin('cars', 'cars.id', '=', 'accidents.car_id')
            ->sortable(['worksheet_no' => 'desc'])
            ->search($request)
            ->select('accident_repair_orders.*', 'accidents.worksheet_no as accident_worksheet', 'cars.license_plate', 'accidents.case', 'cradles.name as cradle_name', 'cars.id as car_id')
            ->paginate(PER_PAGE);
        $list->map(function ($item) {
            $car = Car::find($item->car_id);
            if ($car) {
                if ($car->license_plate) {
                    $text = $car->license_plate;
                } else if ($car->engine_no) {
                    $text = __('inspection_cars.engine_no') . ' ' . $car->engine_no;
                } else if ($car->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
                }
                $item->license_plate = $text;
            }

            return $item;
        });

        $repair_status_list = AccidentTrait::getAccidentRepairStatus();

        return view('admin.accident-orders.index', [
            'list' => $list,
            'license_plate' => $license_plate,
            'worksheet' => $worksheet,
            'accident_worksheet_no' => $accident_worksheet_no,
            'worksheet_text' => $worksheet_text,
            'repair_status_list' => $repair_status_list,
            'status' => $status,
            'license_plate_text' => $license_plate_text,
            'accident_worksheet_text' => $accident_worksheet_text,
        ]);
    }

    public function create()
    {
        $step_approve_management = new StepApproveManagement();
        $is_configured_accident_order = $step_approve_management->isApproveConfigured(ConfigApproveTypeEnum::ACCIDENT_REPAIR_ORDER);
        if (!$is_configured_accident_order) {
            return redirect()->back()->with('warning',  __('menu.accident_order_approve'));
        }
        $is_configured_accident_order_sheet = $step_approve_management->isApproveConfigured(ConfigApproveTypeEnum::ACCIDENT_REPAIR_ORDER_SHEET);
        if (!$is_configured_accident_order_sheet) {
            return redirect()->back()->with('warning',  __('menu.accident_order_sheet_approve'));
        }
        $is_configured_accident_order_sheet_ttl = $step_approve_management->isApproveConfigured(ConfigApproveTypeEnum::ACCIDENT_REPAIR_ORDER_SHEET_TTL);
        if (!$is_configured_accident_order_sheet_ttl) {
            return redirect()->back()->with('warning', __('menu.accident_order_sheet_ttl_approve'));
        }
        $d = new Accident();
        $date = new DateTime();
        $d->repair_date = $date->format('Y-m-d H:i:s');
        $replacement_type_list = RepairTrait::getReplacementTypeList();
        $check_list = RepairTrait::getCheckList();
        $repair_type_list = RepairTrait::getRepairType();
        $informer_type_list = RepairTrait::getInformer();
        $service_center_list = RepairTrait::getServiceCenter();
        $is_need_driver = RepairTrait::getIsNeedDriver();
        $informers = User::select('id', 'name')->orderBy('name')->get();
        $car_license = null;
        $car_data = new Car;
        $rental = 0;

        $page_title = __('lang.create') . __('accident_orders.page_title');
        return view('admin.accident-orders.form', [
            'page_title' => $page_title,
            'd' => $d,
            'replacement_type_list' => $replacement_type_list,
            'check_list' => $check_list,
            'repair_type_list' => $repair_type_list,
            'informer_type_list' => $informer_type_list,
            'service_center_list' => $service_center_list,
            'is_need_driver' => $is_need_driver,
            'informers' => $informers,
            'car_license' => $car_license,
            'car_data' => $car_data,
            'rental' => $rental,
            'condotion_lt_rental' => [],

        ]);
    }

    public function store(Request $request)
    {
      
        $validator = Validator::make($request->all(), [
            'car_id' => [
                'required',
            ],
            'order' => [
                'required'
            ],
            'order.*.due_date' => [
                'max:10',
            ],
        ], [], [
            'car_id' => __('accident_informs.license_plate_chassis_engine'),
            'order' => __('accident_orders.accident_open_list'),
            'order.*.due_date' => __('accident_orders.due_date'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        foreach ($request->order as $index => $item) {
            $accident_repair_order = AccidentRepairOrder::firstOrNew(['id' => $request->id]);
            $tc_count = AccidentRepairOrder::count() + 1;
            $prefix = 'RP';
            if (!($accident_repair_order->exists)) {
                $accident_repair_order->worksheet_no = generateRecordNumber($prefix, $tc_count);
                $accident_repair_order->accident_id = $item['accident_id'];
                $accident = Accident::find($item['accident_id']);
                if ($accident) {
                    $accident->status = AccidentStatusEnum::OPEN_ORDER;
                    $accident->save();
                }
                $accident_repair_order->status = AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR_LIST;
                $accident_repair_order->repair_date = $item['send_repair_date'];
                $due_date = str_replace(',', '', $item['due_date']);
                $accident_repair_order->amount_completed = intval($due_date);
                $accident_repair_order->cradle_id = $item['garage_id'];
                $accident_repair_order->is_appointment = STATUS_DEFAULT;
                $cradle = Cradle::find($item['garage_id']);
                if ($cradle) {
                    $accident_repair_order->cradle_area_id = $cradle->province;
                }
            }
            $accident_repair_order->save();
            foreach ($item as $item2) {
                if (is_array($item2) && isset($item2['id'])) {
                    $accident_repair_order_line = new AccidentRepairOrderLine();
                    $accident_repair_order_line->accident_repair_order_id = $accident_repair_order->id;
                    $accident_repair_order_line->accident_claim_line_id = $item2['id'];
                    $accident_repair_order_line->save();
                }
            }

            $rp_check = AccidentRepairOrder::find($request->id);
            if (!$rp_check) {
                $step_approve_management = new StepApproveManagement();
                $step_approve_management->createModelApproval(ConfigApproveTypeEnum::ACCIDENT_REPAIR_ORDER, AccidentRepairOrder::class, $accident_repair_order->id);
            }
        }
        $redirect_route = route('admin.accident-orders.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(AccidentRepairOrder $accident_order)
    {
        $this->authorize(Actions::View . '_' . Resources::AccidentOrder);
        $accident = Accident::find($accident_order->accident_id);
        $car = Car::find($accident->car_id);
        $car_license = null;
        if ($car) {
            if ($car->license_plate) {
                $car_license = $car->license_plate;
            } else if ($car->engine_no) {
                $car_license = __('inspection_cars.engine_no') . ' ' . $car->engine_no;
            } else if ($car->chassis_no) {
                $car_license = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
            }
        }
        $gps_remove_stop_signal = GpsRemoveStopSignal::where('car_id', $accident->car_id)->first();
        if ($gps_remove_stop_signal) {
            $gps_remove_stop_signal->is_check_gps = $gps_remove_stop_signal->is_check_gps == STATUS_ACTIVE  ? STATUS_ACTIVE : $gps_remove_stop_signal->is_check_gps;
        } else {
            $gps_remove_stop_signal = new GpsRemoveStopSignal();
            $gps_remove_stop_signal->is_check_gps = STATUS_ACTIVE;
        }
        $car_data = RepairTrait::getDataCar($accident->car_id);
        $rental = $car_data->rental;
        $condotion_lt_rental =  RepairTrait::getConditionQuotation($accident);
        $need_list = AccidentTrait::getNeedList();
        $replace_list = AccidentTrait::getReplacementList();
        $province_list = AccidentTrait::getProvinceList();
        $case_list = AccidentTrait::getCaseList();
        $region = AccidentTrait::getZoneType();
        $status_list = AccidentTrait::getStatusList();
        $mistake_list = AccidentTrait::getMistakeTypeList();
        $garage_list = Cradle::select('name', 'id')->get();

        $accident_type_list = AccidentTrait::getAccidentTypeList();
        $claim_type_list = AccidentTrait::getCliamTypeList();
        $claimant_list = AccidentTrait::getCliamantList();

        $province = Province::find($accident->province_id);
        $province_name = $province->name_th;
        $amphure =  Amphure::find($accident->district_id);
        $amphure_name = $amphure ? $amphure->name_th : null;
        $district =  District::find($accident->subdistrict_id);
        $district_name =  $district ? $district->name_th : null;
        $cradle = Cradle::find($accident->cradle);
        $replacement_car_files = $accident->getMedia('replacement_car_files');
        $replacement_car_files = get_medias_detail($replacement_car_files);
        $slide_list = $this->getSlideList($accident);
        $cost_list = $this->getCostList($accident);

        $approve_line = HistoryTrait::getHistory(AccidentRepairOrder::class, $accident_order->id);
        $approve_line_logs = $approve_line['approve_line_logs'];
        $total_loss_files = $accident->getMedia('total_loss_files');
        $total_loss_files = get_medias_detail($total_loss_files);

        $replacement_list = $this->getReplacementList($accident, $accident_order);


        $cost_list = $this->getCostList($accident_order);
        $accident_slide_list = AccidentTrait::getAccidentSlideList();

        $receive_status_list = $this->getStatusReceiveList();

        $slide_worksheet_list = Slide::where('job_type', Accident::class)->where('job_id', $accident_order->id)->select('id', 'worksheet_no as name')->get();

        $page_title = $page_title = __('lang.view') . __('accident_orders.page_title');
        return view('admin.accident-orders.form-accident-edit',  [
            'car_license' => $car_license,
            'car_data' => $car_data,
            'd' => $accident,
            'accident_order' => $accident_order,
            'rental' => $rental,
            'need_list' => $need_list,
            'replace_list' => $replace_list,
            'page_title' => $page_title,
            'condotion_lt_rental' => $condotion_lt_rental,
            'replacement_car_files' => $replacement_car_files,
            'province_list' => $province_list,
            'case_list' => $case_list,
            'region' => $region,
            'province_name' => $province_name,
            'amphure_name' => $amphure_name,
            'district_name' => $district_name,
            'status_list' => $status_list,
            'garage_list' => $garage_list,
            'mistake_list' => $mistake_list,
            'accident_type_list' => $accident_type_list,
            'claim_type_list' => $claim_type_list,
            'claimant_list' => $claimant_list,
            'cradle' => $cradle,
            'slide_list' => $slide_list,
            'cost_list' => $cost_list,
            'view' => true,
            'approve_line_logs' => $approve_line_logs,
            'total_loss_files' => $total_loss_files,
            'gps_remove_stop_signal' => $gps_remove_stop_signal,
            'receive_status_list' => $receive_status_list,
            'accident_slide_list' => $accident_slide_list,
            'replacement_list' => $replacement_list,
            'receive_status_list' => $receive_status_list,
            'slide_worksheet_list' => $slide_worksheet_list,
        ]);
    }

    public function edit(AccidentRepairOrder $accident_order)
    {
        $this->authorize(Actions::Manage . '_' . Resources::AccidentOrder);
        $accident = Accident::find($accident_order->accident_id);
        $car = Car::find($accident->car_id);
        $car_license = null;
        if ($car) {
            if ($car->license_plate) {
                $car_license = $car->license_plate;
            } else if ($car->engine_no) {
                $car_license = __('inspection_cars.engine_no') . ' ' . $car->engine_no;
            } else if ($car->chassis_no) {
                $car_license = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
            }
        }
        $gps_remove_stop_signal = GpsRemoveStopSignal::where('car_id', $accident->car_id)->first();
        if ($gps_remove_stop_signal) {
            $gps_remove_stop_signal->is_check_gps = $gps_remove_stop_signal->is_check_gps == STATUS_ACTIVE  ? STATUS_ACTIVE : $gps_remove_stop_signal->is_check_gps;
        } else {
            $gps_remove_stop_signal = new GpsRemoveStopSignal();
            $gps_remove_stop_signal->is_check_gps = STATUS_ACTIVE;
        }
        $car_data = RepairTrait::getDataCar($accident->car_id);
        $rental = $car_data->rental;
        $condotion_lt_rental =  RepairTrait::getConditionQuotation($accident);
        $need_list = AccidentTrait::getNeedList();
        $replace_list = AccidentTrait::getReplacementList();
        $province_list = AccidentTrait::getProvinceList();
        $case_list = AccidentTrait::getCaseList();
        $region = AccidentTrait::getZoneType();
        $status_list = AccidentTrait::getStatusList();
        $mistake_list = AccidentTrait::getMistakeTypeList();
        $garage_list = Cradle::select('name', 'id')->get();

        $accident_type_list = AccidentTrait::getAccidentTypeList();
        $claim_type_list = AccidentTrait::getCliamTypeList();
        $claimant_list = AccidentTrait::getCliamantList();

        $province = Province::find($accident->province_id);
        $province_name = $province->name_th;
        $amphure =  Amphure::find($accident->district_id);
        $amphure_name = $amphure ? $amphure->name_th : null;
        $district =  District::find($accident->subdistrict_id);
        $district_name =  $district ? $district->name_th : null;

        $cradle = Cradle::find($accident->cradle);

        $replacement_car_files = $accident->getMedia('replacement_car_files');
        $replacement_car_files = get_medias_detail($replacement_car_files);

        $slide_list = $this->getSlideList($accident);
        $cost_list = $this->getCostList($accident);

        $approve_line = HistoryTrait::getHistory(AccidentRepairOrder::class, $accident_order->id);
        $approve_line_logs = $approve_line['approve_line_logs'];
        $total_loss_files = $accident->getMedia('total_loss_files');
        $total_loss_files = get_medias_detail($total_loss_files);

        $replacement_list = $this->getReplacementList($accident, $accident_order);
        $cost_list = $this->getCostList($accident_order);
        $accident_slide_list = AccidentTrait::getAccidentSlideList();

        $receive_status_list = $this->getStatusReceiveList();

        $slide_worksheet_list = Slide::where('job_type', Accident::class)->where('job_id', $accident->id)->select('id', 'worksheet_no as name')->get();

        $page_title = $page_title = __('lang.edit') . __('accident_orders.page_title');
        return view('admin.accident-orders.form-accident-edit',  [
            'car_license' => $car_license,
            'car_data' => $car_data,
            'd' => $accident,
            'accident_order' => $accident_order,
            'rental' => $rental,
            'need_list' => $need_list,
            'replace_list' => $replace_list,
            'page_title' => $page_title,
            'condotion_lt_rental' => $condotion_lt_rental,
            'replacement_car_files' => $replacement_car_files,
            'province_list' => $province_list,
            'case_list' => $case_list,
            'region' => $region,
            'province_name' => $province_name,
            'amphure_name' => $amphure_name,
            'district_name' => $district_name,
            'status_list' => $status_list,
            'garage_list' => $garage_list,
            'mistake_list' => $mistake_list,
            'accident_type_list' => $accident_type_list,
            'claim_type_list' => $claim_type_list,
            'claimant_list' => $claimant_list,
            'cradle' => $cradle,
            'slide_list' => $slide_list,
            'cost_list' => $cost_list,
            'approve_line_logs' => $approve_line_logs,
            'total_loss_files' => $total_loss_files,
            'gps_remove_stop_signal' => $gps_remove_stop_signal,
            'receive_status_list' => $receive_status_list,
            'accident_slide_list' => $accident_slide_list,
            'replacement_list' => $replacement_list,
            'receive_status_list' => $receive_status_list,
            'slide_worksheet_list' => $slide_worksheet_list,
        ]);
    }

    public function getReplacementList($accident_model, $accident_order)
    {
        $replacement_list = ReplacementCar::where('job_type', Accident::class)->where('job_id', $accident_model->id)->get();
        $replacement_list->map(function ($item) use ($accident_model, $accident_order) {
            $item->replacement_type = ($item->replacement_type) ? $item->replacement_type : '';
            $item->replacement_pickup_date = ($item->replacement_date) ? $item->replacement_date : '';
            $item->slide_worksheet = ($item->slide_id) ? $item->slide_id : '';
            $item->place = ($item->replacement_place) ? $item->replacement_place : '';
            $item->customer_receive = ($item->is_cust_receive_replace) ? $item->is_cust_receive_replace : STATUS_DEFAULT;
            $item->accident_id = $accident_model->id;
            $item->car_id = $accident_model->car_id;
            $item->id = $item->id;
            $item->worksheet = $item->worksheet_no;
            $item->accident_order_id = $accident_order->id;
            $main_car = Car::find($item->main_car_id);
            $item->main_car = $main_car->license_plate;
            $item->replacement_url = route('admin.replacement-cars.show', ['replacement_car' => $item->id]);
            $item->replacement_type_text = __('accident_informs.replace_type_' . $item->replacement_type);
            $slide = Slide::find($item->slide_id);
            if ($slide) {
                $slide_worksheet_no = $slide->worksheet_no;
            } else {
                $slide_worksheet_no = null;
            }
            $item->customer_receive_text = ($item->is_cust_receive_replace) && $item->is_cust_receive_replace == STATUS_ACTIVE ?  __('accident_informs.customer_receive_self') : 'รถสไลด์ : ' . $slide_worksheet_no;

            $replacement_medias = $item->getMedia('replacement_car_files');
            $replacement_medias = get_medias_detail($replacement_medias);
            $replacement_medias = collect($replacement_medias)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $item->replacement_files = $replacement_medias;
            $item->pending_delete_replacement_files = [];

            // if($item->job_type == Slide::class){
            //     $slide = Slide::find($item->job_id);
            //     $item->slide_id = $slide->id;
            //     $item->slide_worksheet = $slide->worksheet_no;
            //     $item->slide_type = __('accident_informs.slide_' . AccidentSlideEnum::TLS_SLIDE);
            //     $item->origin_place = $slide->origin_place;
            //     $item->origin_contact = $slide->origin_contact;
            //     $item->origin_tel = $slide->origin_tel;
            //     $item->destination_place = $slide->destination_place;
            //     $item->destination_contact = $slide->destination_contact;
            //     $item->destination_tel = $slide->destination_tel;
            //     $item->slide_type_id = AccidentSlideEnum::TLS_SLIDE;

            // }else{
            //     $item->slide_type = __('accident_informs.slide_' . AccidentSlideEnum::THIRD_PARTY_SLIDE);
            //     $item->slide_type_id = AccidentSlideEnum::THIRD_PARTY_SLIDE;
            // }

            return $item;
        });

        return $replacement_list;
    }

    public function getDefaultCarID(Request $request)
    {
        $data = [];
        $data = Car::select('cars.id', 'cars.license_plate', 'cars.engine_no', 'cars.chassis_no', 'cars.status')
            ->leftJoin('accidents', 'accidents.car_id', '=', 'cars.id')
            ->whereNotIn('cars.status', [CarEnum::SOLD_OUT])
            ->where('accidents.status', AccidentStatusEnum::WAITING_OPEN_ORDER)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('license_plate', 'like', '%' . $request->s . '%');
                    $query->orWhere('engine_no', 'like', '%' . $request->s . '%');
                    $query->orWhere('chassis_no', 'like', '%' . $request->s . '%');
                }
            })
            ->limit(30)->distinct('cars.id')
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

        return response()->json($data);
    }

    public function getDataCar(Request $request)
    {
        $car_id = $request->car_id;
        $data = [];
        $car = Car::find($car_id);
        $data['car_class'] = ($car && $car->carClass) ? $car->carClass->full_name : null;
        $data['chassis_no'] = ($car) ? $car->chassis_no : null;
        $data['license_plate'] = ($car) ? $car->license_plate : null;
        $data['current_mileage'] = ($car) ? $car->current_mileage : null;
        $data['car_status'] = ($car) ? $car->status : null;
        $data['status_class'] = __('cars.class_' . $car->status);
        $data['status_text'] = __('cars.status_' . $car->status);
        $data['rental'] = 0;
        $data['contract'] = 0;
        $rental_type = null;

        if (strcmp($car->rental_type, RentalTypeEnum::SHORT) == 0) {
            $rental = RentalLine::leftJoin('rentals', 'rentals.id', '=', 'rental_lines.rental_id')
                ->where('rental_lines.car_id', $car->id)
                ->where('rentals.status', RentalStatusEnum::PAID)
                ->select('rentals.id', 'rentals.worksheet_no', 'rentals.customer_name')
                ->first();
            if ($rental) {
                $rental_type = Rental::class;
                $contract = Contracts::leftJoin('contract_lines', 'contract_lines.contract_id', '=', 'contracts.id')
                    ->where('contracts.job_type', Rental::class)
                    ->where('contracts.job_id', $rental->id)
                    ->where('contract_lines.car_id', $car->id)
                    ->select('contracts.id', 'contracts.worksheet_no', 'contract_lines.pick_up_date', 'contract_lines.return_date')
                    ->first();
            }
        }
        if (strcmp($car->rental_type, RentalTypeEnum::LONG) == 0) {
            $rental = LongTermRentalPRCar::leftJoin('lt_rental_pr_lines', 'lt_rental_pr_lines.id', '=', 'lt_rental_pr_lines_cars.lt_rental_pr_line_id')
                ->leftJoin('lt_rentals', 'lt_rentals.id', '=', 'lt_rental_pr_lines.lt_rental_id')
                ->where('lt_rental_pr_lines_cars.car_id', $car->id)
                ->select('lt_rentals.id', 'lt_rentals.worksheet_no', 'lt_rentals.customer_name')
                ->first();
            if ($rental) {
                $rental_type = LongTermRental::class;
                $contract = Contracts::leftJoin('contract_lines', 'contract_lines.contract_id', '=', 'contracts.id')
                    ->where('contracts.job_type', LongTermRental::class)
                    ->where('contracts.job_id', $rental->id)
                    ->where('contract_lines.car_id', $car->id)
                    ->select('contracts.id', 'contracts.worksheet_no', 'contract_lines.pick_up_date', 'contract_lines.return_date')
                    ->first();
            }
        }

        $data['rental_type'] = $rental_type;
        if (isset($rental)) {
            $data['rental'] = 1;
            $data['rental_id'] = ($rental) ? $rental->id : null;
            $data['rental_worksheet_no'] = ($rental) ? $rental->worksheet_no : null;
            $data['rental_customer_name'] = ($rental) ? $rental->customer_name : null;

            if ($contract) {
                $data['contract'] = 1;
                $data['contract_worksheet_no'] = ($contract) ? $contract->worksheet_no : null;
                $data['contract_pick_up_date'] = ($contract) ? date('d/m/Y H:i', strtotime($contract->pick_up_date)) : null;
                $data['contract_return_date'] = ($contract) ? date('d/m/Y H:i', strtotime($contract->return_date)) : null;
            }
        }

        $accident_data = Accident::where('car_id', $car_id)->get();
        $accident_data->map(function ($item) {
            $accident_line = AccidentClaimLines::where('accident_id', $item->id)->get();
            $item->count_accident_line = count($accident_line);
            $item->case = __('accident_informs.case_' . $item->case);
            $accident_date = new DateTime($item->accident_date);
            $item->accident_date = $accident_date->format('d/m/Y H:i');
            $accident_arr = [];
            foreach ($accident_line as $data) {
                $accident_line_arr = [];
                $before_medias = $data->getMedia('before_file');
                $before_medias = get_medias_detail($before_medias);
                $before_medias = collect($before_medias)->map(function ($item) {
                    $item['formated'] = true;
                    $item['saved'] = true;
                    $item['raw_file'] = null;
                    return $item;
                })->toArray();
                if (count($before_medias) > 0) {
                    $accident_line_arr['before_files'] = $before_medias[0];
                }

                $after_medias = $data->getMedia('after_file');
                $after_medias = get_medias_detail($after_medias);
                $after_medias = collect($after_medias)->map(function ($item) {
                    $item['formated'] = true;
                    $item['saved'] = true;
                    $item['raw_file'] = null;
                    return $item;
                })->toArray();

                if (count($after_medias) > 0) {
                    $accident_line_arr['after_files'] = $after_medias[0];
                }

                $accident_line_arr['wound_characteristics'] = $data['wound_characteristics'] ? __('accident_informs.wound_type_' . $data['wound_characteristics']) : '';
                $accident_line_arr['accident_claim'] = ($data['accident_claim_list_id']) ? ClaimList::find($data['accident_claim_list_id'])->value('name') : '';
                $accident_line_arr['supplier'] = (!is_null($data['supplier'])) ? __('accident_informs.spare_part_status_' . $data['supplier']) : '';

                if (count($accident_line_arr) > 0) {
                    $accident_arr[] = $accident_line_arr;
                }
            }
            $item->accident_line = $accident_arr;
            return $item;
        });

        return [
            'success' => true,
            'data' => $data,
            'accident_data' => $accident_data,
        ];
    }

    public function getAccidentList(Request $request)
    {
        $accident_repair_order_lines = AccidentRepairOrderLine::leftJoin('accident_claim_lines', 'accident_claim_lines.id', '=', 'accident_repair_order_lines.accident_claim_line_id')
            ->where('accident_claim_lines.accident_id', $request->id)
            ->pluck('accident_repair_order_lines.accident_claim_line_id')
            ->toArray();

        $accident_line = AccidentClaimLines::where('accident_id', $request->id)->whereNotIn('id', $accident_repair_order_lines)->get();

        $accident_arr = [];
        foreach ($accident_line as $data) {
            $accident_line_arr = [];
            $before_medias = $data->getMedia('before_file');
            $before_medias = get_medias_detail($before_medias);
            $before_medias = collect($before_medias)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();

            if (count($before_medias) > 0) {
                $accident_line_arr['before_files'] = $before_medias[0];
            }

            $after_medias = $data->getMedia('after_file');
            $after_medias = get_medias_detail($after_medias);
            $after_medias = collect($after_medias)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();

            if (count($after_medias) > 0) {
                $accident_line_arr['after_files'] = $after_medias[0];
            }

            $accident_line_arr['wound_characteristics'] = $data['wound_characteristics'] ? __('accident_informs.wound_type_' . $data['wound_characteristics']) : '';
            $accident_line_arr['accident_claim'] = ($data['accident_claim_list_id']) ? ClaimList::find($data['accident_claim_list_id'])->value('name') : '';
            $accident_line_arr['supplier'] = (!is_null($data['supplier'])) ? __('accident_informs.spare_part_status_' . $data['supplier']) : '';
            $accident_line_arr['id'] = $data['id'];
            $accident_line_arr['is_check'] = false;

            if (count($accident_line_arr) > 0) {
                $accident_arr[] = $accident_line_arr;
            }
        }
        $accident_line = $accident_arr;

        return [
            'success' => true,
            'data' => $accident_line,
        ];
    }

    public function getSlideList($accident_model)
    {
        $slide_list = AccidentSlide::where('accident_id', $accident_model->id)->get();
        $slide_list->map(function ($item) {
            $item->lift_date = ($item->slide_date) ? $item->slide_date : '';
            $item->slide_driver = ($item->slide_driver) ? $item->slide_driver : '';
            $item->lift_price = ($item->slide_price) ? $item->slide_price : '';
            $item->lift_date = ($item->slide_date) ? $item->slide_date : '';
            $item->lift_from = ($item->slide_from) ? $item->slide_from : '';
            $item->lift_to = ($item->slide_to) ? $item->slide_to : '';

            $slide_medias = $item->getMedia('slide');
            $slide_medias = get_medias_detail($slide_medias);
            $slide_medias = collect($slide_medias)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $item->slide_files = $slide_medias;
            $item->pending_delete_slide_files = [];

            if ($item->job_type == Slide::class) {
                $slide = Slide::find($item->job_id);
                $item->slide_id = $slide->id;
                $item->slide_worksheet = $slide->worksheet_no;
                $item->slide_type = __('accident_informs.slide_' . AccidentSlideEnum::TLS_SLIDE);
                $item->origin_place = $slide->origin_place;
                $item->origin_contact = $slide->origin_contact;
                $item->origin_tel = $slide->origin_tel;
                $item->destination_place = $slide->destination_place;
                $item->destination_contact = $slide->destination_contact;
                $item->destination_tel = $slide->destination_tel;
                $item->slide_type_id = AccidentSlideEnum::TLS_SLIDE;
            } else {
                $item->slide_type = __('accident_informs.slide_' . AccidentSlideEnum::THIRD_PARTY_SLIDE);
                $item->slide_type_id = AccidentSlideEnum::THIRD_PARTY_SLIDE;
            }

            return $item;
        });
        return $slide_list;
    }

    public function getCostList($cost_model)
    {
        $cost_list = AccidentExpense::where('accident_id', $cost_model->accident_id)->get();
        $cost_list->map(function ($item) {
            $item->cost_name = ($item->list) ? $item->list : '';
            $item->cost_price = ($item->price) ? $item->price : '';
            $item->cost_remark = ($item->remark) ? $item->remark : '';
            $item->cost_date = ($item->created_at) ? $item->created_at : '';
            return $item;
        });

        return $cost_list;
    }

    public function storeEditAccident(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'accident_type' => [
                'required',
            ],
            'report_date' => [
                'required',
            ],
            'reporter' => [
                'required',
            ],
            'report_tel' => [
                'required', 'numeric', 'digits:10',
            ],
            'accident_date' => [
                'required',
            ],
            'driver' => [
                'required',
            ],
            'main_area' => [
                'required',
            ],
            'case' => [
                'required',
            ],
            'accident_description' => [
                'required',
            ],
            'accident_place' => [
                'required',
            ],
            'region' => [
                'required',
            ],
            'province' => [
                'required',
            ],
            'amount_wounded_driver' => [Rule::when($request->is_wounded == STATUS_ACTIVE, ['required'])],
            'amount_wounded_parties' => [Rule::when($request->is_wounded == STATUS_ACTIVE, ['required'])],
            'amount_deceased_driver' => [Rule::when($request->is_deceased == STATUS_ACTIVE, ['required'])],
            'amount_deceased_parties' => [Rule::when($request->is_deceased == STATUS_ACTIVE, ['required'])],
            'cradle' => [Rule::when($request->is_repair == STATUS_ACTIVE, ['required'])],
            'first_lifter' => [Rule::when($request->first_lifting == STATUS_ACTIVE, ['required'])],
            'first_lift_date' => [Rule::when($request->first_lifting == STATUS_ACTIVE, ['required'])],
            'first_lift_price' => [Rule::when($request->first_lifting == STATUS_ACTIVE, ['required'])],
            'first_lift_tel' => [Rule::when($request->first_lifting == STATUS_ACTIVE, ['required'])],
            'lift_date' => [Rule::when($request->need_folklift == STATUS_ACTIVE, ['required'])],
            'lift_from' => [Rule::when($request->need_folklift == STATUS_ACTIVE, ['required'])],
            'lift_to' => [Rule::when($request->need_folklift == STATUS_ACTIVE, ['required'])],
            'lift_price' => [Rule::when($request->need_folklift == STATUS_ACTIVE, ['required'])],
            'replacement_expect_date' => [Rule::when($request->is_replacement == STATUS_ACTIVE, ['required'])],
            'replacement_type' => [Rule::when($request->is_replacement == STATUS_ACTIVE, ['required'])],
            'is_driver_replacement' => [Rule::when($request->is_replacement == STATUS_ACTIVE, ['required'])],
            'replacement_expect_place' => [Rule::when($request->is_replacement == STATUS_ACTIVE, ['required'])],
            'inform_date' => [Rule::when(isset($request->is_stop_gps[0]) && $request->is_stop_gps[0] == STATUS_ACTIVE, ['required'])],
            'is_check_gps' => [Rule::when(isset($request->is_stop_gps[0]) && $request->is_stop_gps[0] == STATUS_ACTIVE, ['required'])],
        ], [], [
            'accident_type' => __('accident_informs.accident_type'),
            'claim_type' => __('accident_informs.claim_type'),
            'claim_by' => __('accident_informs.claim_by'),
            'report_date' => __('accident_informs.report_date'),
            'reporter' => __('accident_informs.reporter'),
            'report_tel' => __('accident_informs.report_tel'),
            'license_plate' => __('accident_informs.license_plate_chassis_engine'),
            'accident_date' => __('accident_informs.accident_date'),
            'driver' => __('accident_informs.driver'),
            'main_area' => __('accident_informs.main_area'),
            'case' => __('accident_informs.case'),
            'accident_description' => __('accident_informs.accident_description'),
            'accident_place' => __('accident_informs.accident_place'),
            'current_place' => __('accident_informs.current_place'),
            'region' => __('accident_informs.sector'),
            'province' => __('accident_informs.province'),
            'district' => __('accident_informs.amphure'),
            'subdistrict' => __('accident_informs.district'),
            'wrong_type' => __('accident_informs.wrong_type'),
            'amount_wounded_driver' => __('accident_informs.amount_wounded_driver'),
            'amount_wounded_parties' => __('accident_informs.amount_wounded_parties'),
            'amount_deceased_driver' => __('accident_informs.amount_deceased_driver'),
            'amount_deceased_parties' => __('accident_informs.amount_deceased_parties'),
            'cradle' => __('accident_informs.cradle_recommend'),

            'first_lifter' => __('accident_informs.first_lifter'),
            'first_lift_date' => __('accident_informs.first_lift_date'),
            'first_lift_price' => __('accident_informs.first_lift_price'),
            'first_lift_tel' => __('accident_informs.lift_tel'),

            'lift_date' => __('accident_informs.lift_date'),
            'lift_from' => __('accident_informs.lift_from'),
            'lift_to' => __('accident_informs.lift_to'),
            'lift_price' => __('accident_informs.lift_price'),

            'replacement_expect_date' => __('accident_informs.replacement_date'),
            'replacement_type' => __('accident_informs.replacement_type'),
            'is_driver_replacement' => __('accident_informs.need_driver_replacement'),
            'replacement_expect_place' => __('accident_informs.replacement_place'),
            'inform_date' => __('accident_orders.noti_remove_stop_gps_date'),
            'is_check_gps' => __('accident_orders.noti_remove_stop'),

        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $accident_informs = Accident::find($request->accident_id);
        if ($accident_informs) {
            $accident_informs->accident_type = $request->accident_type;
            $accident_informs->claim_type = $request->claim_type;
            $accident_informs->claim_by = $request->claim_by;
            $accident_informs->report_date = $request->report_date;
            $accident_informs->reporter = $request->reporter;
            $accident_informs->report_tel = $request->report_tel;
            $accident_informs->report_no = $request->report_no;
            $accident_informs->job_type = $request->job_type;
            $accident_informs->job_id = $request->job_id;
            $accident_informs->accident_date = $request->accident_date;
            $accident_informs->driver = $request->driver;
            $accident_informs->main_area = $request->main_area;
            $accident_informs->case = $request->case;
            $accident_informs->accident_date = $request->accident_date;
            $accident_informs->accident_description = $request->accident_description;
            $accident_informs->accident_place = $request->accident_place;
            $accident_informs->current_place = $request->current_place;
            $accident_informs->region = $request->region;
            $accident_informs->province_id = $request->province;
            $accident_informs->district_id = $request->district;
            $accident_informs->subdistrict_id = $request->subdistrict;
            $accident_informs->is_parties = $request->is_parties;
            $accident_informs->is_wounded = $request->is_wounded;
            $accident_informs->is_deceased = $request->is_deceased;
            $accident_informs->is_repair = $request->is_repair;
            $accident_informs->wrong_type = $request->wrong_type;
            $accident_informs->amount_wounded_driver = $request->amount_wounded_driver;
            $accident_informs->amount_wounded_parties = $request->amount_wounded_parties;
            $accident_informs->amount_deceased_driver = $request->amount_deceased_driver;
            $accident_informs->amount_deceased_parties = $request->amount_deceased_parties;
            $accident_informs->cradle = $request->cradle;
            $accident_informs->remark = $request->remark;

            if ($request->optional_files__pending_delete_ids) {
                $pending_delete_ids = $request->optional_files__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $accident_informs->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('optional_files')) {
                foreach ($request->file('optional_files') as $image) {
                    if ($image->isValid()) {
                        $accident_informs->addMedia($image)->toMediaCollection('optional_files');
                    }
                }
            }


            if ($request->total_loss_files__pending_delete_ids) {
                $pending_delete_ids = $request->total_loss_files__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $accident_informs->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('total_loss_files')) {
                foreach ($request->file('total_loss_files') as $image) {
                    if ($image->isValid()) {
                        $accident_informs->addMedia($image)->toMediaCollection('total_loss_files');
                    }
                }
            }

            // replacement_car
            // $accident_informs->is_replacement = $request->is_replacement;
            // $accident_informs->is_driver_replacement = $request->is_driver_replacement;
            // $accident_informs->replacement_expect_date = $request->replacement_expect_date;
            // $accident_informs->replacement_type = $request->replacement_type;
            // $accident_informs->replacement_expect_place = $request->replacement_expect_place;

            // if ($request->replacement_car_files__pending_delete_ids) {
            //     $pending_delete_ids = $request->replacement_car_files__pending_delete_ids;
            //     if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
            //         foreach ($pending_delete_ids as $media_id) {
            //             $accident_informs->deleteMedia($media_id);
            //         }
            //     }
            // }

            // if ($request->hasFile('replacement_car_files')) {
            //     foreach ($request->file('replacement_car_files') as $image) {
            //         if ($image->isValid()) {
            //             $accident_informs->addMedia($image)->toMediaCollection('replacement_car_files');
            //         }
            //     }
            // }
            $accident_informs->status = AccidentStatusEnum::WAITING_CLAIM;
            $compensation = str_replace(',', '', $request->compensation);
            $accident_informs->compensation = $compensation ? $compensation : null;
            $carcass_cost = str_replace(',', '', $request->carcass_cost);
            $accident_informs->carcass_cost = $carcass_cost ? $carcass_cost : null;
            $accident_informs->is_stop_gps = isset($request->is_stop_gps[0]) ? $request->is_stop_gps[0] : null;
            $accident_informs->is_status_rental_car = isset($request->is_status_rental_car[0]) ? $request->is_status_rental_car[0] : null;
            $accident_informs->is_pick_up_book = isset($request->is_pick_up_book[0]) ? $request->is_pick_up_book[0] : null;
            $accident_informs->save();

            // delete replacement
            $delete_replacement_ids = $request->delete_replacement_ids;
            if ((!empty($delete_replacement_ids)) && (is_array($delete_replacement_ids))) {
                foreach ($delete_replacement_ids as $delete_id) {
                    $replacement_delete = ReplacementCar::find($delete_id);
                    $replacement_medias = $replacement_delete->getMedia('replacement_car_files');
                    foreach ($replacement_medias as $replacement_media) {
                        $replacement_media->delete();
                    }
                    $replacement_delete->delete();
                }
            }

            $slide = $this->saveSlide($request, $accident_informs);
            $cost = $this->saveCost($request, $accident_informs);
        }

        $accident_repair_order = AccidentRepairOrder::find($request->accident_order_id);
        if (strcmp($accident_repair_order->status, AccidentRepairStatusEnum::TTL) === 0) {
            $car = Car::find($accident_informs->car_id);
            if ($car && strcmp($car->have_gps, STATUS_ACTIVE) === 0) {
                $gps_remove_stop_signal = GpsRemoveStopSignal::where('car_id', $accident_informs->car_id)->first();
                if (!$gps_remove_stop_signal) {
                    if (isset($request->is_stop_gps[0]) && $request->is_stop_gps[0] == STATUS_ACTIVE) {
                        $create_gps = GpsTrait::createGPSRemoveStopSignal(GPSJobTypeEnum::TOTAL_LOSS, $accident_informs->car_id, $request->inform_date, $request->is_check_gps);
                    }
                }
            }
        }
        if ($accident_repair_order) {
            if (strcmp($accident_repair_order->status, AccidentRepairStatusEnum::REJECT) === 0) {
                $approve_clear_status = new StepApproveManagement();
                $approve_return = $approve_clear_status->clearStatus(AccidentRepairOrder::class, $request->accident_order_id, ConfigApproveTypeEnum::ACCIDENT_REPAIR_ORDER);
                $accident_repair_order->status = AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR_LIST;
                $accident_repair_order->save();
            }
        }
        $redirect_route = route('admin.accident-orders.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    private function saveSlide($request, $slide_model)
    {
        $delete_slide_ids = $request->delete_slide_ids;
        if ((!empty($delete_slide_ids)) && (is_array($delete_slide_ids))) {
            foreach ($delete_slide_ids as $delete_id) {
                $slide_delete = AccidentSlide::find($delete_id);
                $slide_medias = $slide_delete->getMedia('slide');
                foreach ($slide_medias as $slide_media) {
                    $slide_media->delete();
                }
                $slide_delete->delete();
            }
        }

        $pending_delete_slide_files = $request->slide_file__pending_delete_ids;
        if (!empty($request->slide)) {
            foreach ($request->slide as $key => $request_slide) {
                $acciden_slide = AccidentSlide::firstOrNew(['id' => $request_slide['id']]);
                if (!$acciden_slide->exists) {
                    //
                }
                $acciden_slide->accident_id = $slide_model->id;
                $acciden_slide->slide_date = $request_slide['lift_date'];
                $acciden_slide->slide_driver = $request_slide['slide_driver'];
                $acciden_slide->slide_from = $request_slide['lift_from'];
                $acciden_slide->slide_to = $request_slide['lift_to'];

                $slide_price = str_replace(',', '', $request_slide['lift_price']);
                $acciden_slide->slide_price = $slide_price ? $slide_price : null;

                $acciden_slide->save();

                // delete file driver skill
                if ((!empty($pending_delete_slide_files)) && (sizeof($pending_delete_slide_files) > 0)) {
                    foreach ($pending_delete_slide_files as $slide_media_id) {
                        $slide_media = Media::find($slide_media_id);
                        if ($slide_media && $slide_media->model_id) {
                            $skill_model = AccidentSlide::find($slide_media->model_id);
                            $skill_model->deleteMedia($slide_media->id);
                        }
                    }
                }

                // insert + update driver skill
                if ((!empty($request->slide_file)) && (sizeof($request->slide_file) > 0)) {
                    foreach ($request->slide_file as $table_row_index => $slide_files) {
                        foreach ($slide_files as $slide_file) {
                            if ($slide_file->isValid() && (strcmp($table_row_index, 'table_row_' . $key) == 0)) {
                                $acciden_slide->addMedia($slide_file)->toMediaCollection('slide');
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    private function saveCost($request, $cost_model)
    {
        $delete_cost_ids = $request->delete_cost_ids;
        if ((!empty($delete_cost_ids)) && (is_array($delete_cost_ids))) {
            foreach ($delete_cost_ids as $delete_id) {
                $cost_delete = AccidentExpense::find($delete_id);
                $cost_delete->delete();
            }
        }
        if (!empty($request->cost)) {
            foreach ($request->cost as $key => $request_cost) {
                $acciden_cost = AccidentExpense::firstOrNew(['id' => $request_cost['id']]);
                if (!$acciden_cost->exists) {
                    //
                }
                $acciden_cost->accident_id = $cost_model->id;
                $acciden_cost->list = $request_cost['cost_name'];
                $cost_price = str_replace(',', '', $request_cost['cost_price']);
                $acciden_cost->price = $cost_price ? $cost_price : null;
                $acciden_cost->remark = $request_cost['cost_remark'];
                $acciden_cost->save();
            }
        }
        return true;
    }

    public function editClaim(AccidentRepairOrder $accident_order)
    {
        $this->authorize(Actions::Manage . '_' . Resources::AccidentOrder);
        $accident_inform = Accident::find($accident_order->accident_id);
        $repair_list = [];
        $spare_part_list = AccidentTrait::getStatusList();
        $repair_list = AccidentTrait::getRepairList();
        $claim_type_list = AccidentTrait::getCliamTypeList();
        $getClaimList = $this->getClaimList($accident_inform, $accident_order->id);
        $claim_list_data = $getClaimList['claim_list'];
        $is_withdraw_true = $getClaimList['is_withdraw_true'];
        $tls_cost_total = $getClaimList['tls_cost_total'];
        $claim_list = ClaimList::select('name', 'id')->get();
        $wound_list = AccidentTrait::getWoundList();
        $responsible_list = AccidentTrait::getResponsibleList();
        $rights_list = AccidentTrait::getRightsList();
        $approve_line = HistoryTrait::getHistory(AccidentRepairOrder::class, $accident_order->id);
        $approve_line_logs = $approve_line['approve_line_logs'];
        $page_title = $page_title = __('lang.edit') . __('accident_orders.page_title');
        return view('admin.accident-orders.form-claim-edit',  [
            'page_title' => $page_title,
            'd' => $accident_inform,
            'accident_order' => $accident_order,
            'repair_list' => $repair_list,
            'spare_part_list' => $spare_part_list,
            'claim_list' => $claim_list,
            'wound_list' => $wound_list,
            'claim_list_data' => $claim_list_data,
            'is_withdraw_true' => $is_withdraw_true,
            'tls_cost_total' => $tls_cost_total,
            'repair_list' => $repair_list,
            'claim_type_list' => $claim_type_list,
            'responsible_list' => $responsible_list,
            'rights_list' => $rights_list,
            'approve_line_logs' => $approve_line_logs,
        ]);
    }

    public function showClaim(AccidentRepairOrder $accident_order)
    {
        $this->authorize(Actions::View . '_' . Resources::AccidentOrder);
        $accident_inform = Accident::find($accident_order->accident_id);
        $repair_list = [];
        $spare_part_list = AccidentTrait::getStatusList();
        $repair_list = AccidentTrait::getRepairList();
        $claim_type_list = AccidentTrait::getCliamTypeList();
        $getClaimList = $this->getClaimList($accident_inform, $accident_order->id);
        $claim_list_data = $getClaimList['claim_list'];
        $is_withdraw_true = $getClaimList['is_withdraw_true'];
        $tls_cost_total = $getClaimList['tls_cost_total'];
        $claim_list = ClaimList::select('name', 'id')->get();
        $wound_list = AccidentTrait::getWoundList();
        $responsible_list = AccidentTrait::getResponsibleList();
        $rights_list = AccidentTrait::getRightsList();
        $approve_line = HistoryTrait::getHistory(AccidentRepairOrder::class, $accident_order->id);
        $approve_line_logs = $approve_line['approve_line_logs'];
        $page_title = $page_title = __('lang.view') . __('accident_orders.page_title');
        return view('admin.accident-orders.form-claim-edit',  [
            'page_title' => $page_title,
            'd' => $accident_inform,
            'accident_order' => $accident_order,
            'repair_list' => $repair_list,
            'spare_part_list' => $spare_part_list,
            'claim_list' => $claim_list,
            'wound_list' => $wound_list,
            'claim_list_data' => $claim_list_data,
            'is_withdraw_true' => $is_withdraw_true,
            'tls_cost_total' => $tls_cost_total,
            'repair_list' => $repair_list,
            'claim_type_list' => $claim_type_list,
            'responsible_list' => $responsible_list,
            'rights_list' => $rights_list,
            'view' => true,
            'approve_line_logs' => $approve_line_logs,
        ]);
    }

    public function editRepairPrice(AccidentRepairOrder $accident_order)
    {
        $this->authorize(Actions::Manage . '_' . Resources::AccidentOrder);
        $page_title = $page_title = __('lang.edit') . __('accident_orders.page_title');
        $garage_list = Cradle::select('id', 'name')->get();
        $province_list = Province::select('id', 'name_th as name')->get();
        if ($accident_order->is_appointment == STATUS_ACTIVE) {
            $accident_order->is_appointment =  $accident_order->is_appointment;
        } else {
            $accident_order->is_appointment =  STATUS_DEFAULT;
        }

        $garage_quotation_file = $accident_order->getMedia('garage_quotation_file');
        $garage_quotation_file = get_medias_detail($garage_quotation_file);

        $spare_list =  AccidentRepairLinePrice::where('accident_repair_order_id', $accident_order->id)->get();
        $spare_list->map(function ($item) {
            $total = $item->spare_parts - $item->discount_spare_parts;
            $item->total = number_format($total);

            return $item;
        });

        $approve_line = HistoryTrait::getHistory(AccidentRepairOrder::class, $accident_order->id);
        $approve_line_logs = $approve_line['approve_line_logs'];
        $user_list = User::select('id', 'name')->get();
        $insurer_list = Insurer::select('id', 'insurance_name_th as name')->get();

        return view('admin.accident-orders.form-repair-price-edit',  [
            'page_title' => $page_title,
            'd' => $accident_order,
            'accident_order' => $accident_order,
            'garage_list' => $garage_list,
            'province_list' => $province_list,
            'garage_quotation_file' => $garage_quotation_file,
            'spare_list' => $spare_list,
            'approve_line_logs' => $approve_line_logs,
            'user_list' => $user_list,
            'insurer_list' => $insurer_list,
        ]);
    }

    public function showRepairPrice(AccidentRepairOrder $accident_order)
    {
        $this->authorize(Actions::View . '_' . Resources::AccidentOrder);
        $page_title = $page_title = __('lang.view') . __('accident_orders.page_title');
        $garage_list = Cradle::select('id', 'name')->get();
        $province_list = Province::select('id', 'name_th as name')->get();

        $garage_quotation_file = $accident_order->getMedia('garage_quotation_file');
        $garage_quotation_file = get_medias_detail($garage_quotation_file);

        $spare_list =  AccidentRepairLinePrice::where('accident_repair_order_id', $accident_order->id)->get();
        $spare_list->map(function ($item) {
            $total = $item->spare_parts - $item->discount_spare_parts;
            $item->total = number_format($total);

            return $item;
        });

        $approve_line = HistoryTrait::getHistory(AccidentRepairOrder::class, $accident_order->id);
        $approve_line_logs = $approve_line['approve_line_logs'];

        return view('admin.accident-orders.form-repair-price-edit',  [
            'page_title' => $page_title,
            'd' => $accident_order,
            'accident_order' => $accident_order,
            'garage_list' => $garage_list,
            'province_list' => $province_list,
            'garage_quotation_file' => $garage_quotation_file,
            'spare_list' => $spare_list,
            'view' => true,
            'approve_line_logs' => $approve_line_logs,
        ]);
    }

    public function getClaimList($claim_model, $accident_order_id)
    {
        $accident_repair_order_lines = AccidentRepairOrderLine::where('accident_repair_order_id', $accident_order_id)->pluck('accident_claim_line_id')->toArray();
        $claim_list = AccidentClaimLines::whereIn('id', $accident_repair_order_lines)->get();
        $is_withdraw_true = 0;
        $tls_cost_total = 0;
        $claim_list->map(function ($item) use (&$is_withdraw_true, &$tls_cost_total) {
            if ($item->accident_claim_list_id) {
                $accident_claim_text = ClaimList::find($item->accident_claim_list_id);
                $item->accident_claim_text = $accident_claim_text->name;
            }
            $item->accident_claim_id = ($item->accident_claim_list_id) ? $item->accident_claim_list_id : '';
            $item->wound_characteristics_text = ($item->wound_characteristics) ? __('accident_informs.wound_type_' . $item->wound_characteristics) : '';
            $item->wound_characteristics = ($item->wound_characteristics) ? $item->wound_characteristics : '';
            $item->wound_characteristics_id = ($item->wound_characteristics) ? $item->wound_characteristics : '';
            $item->supplier_text = (!is_null($item->supplier)) ? __('accident_informs.spare_part_status_' . $item->supplier) : '';
            $item->tls_cost = ($item->cost) ? $item->cost : '';

            $before_medias = $item->getMedia('before_file');
            $before_medias = get_medias_detail($before_medias);
            $before_medias = collect($before_medias)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $item->before_files = $before_medias;

            $after_medias = $item->getMedia('after_file');
            $after_medias = get_medias_detail($after_medias);
            $after_medias = collect($after_medias)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $item->after_files = $after_medias;

            $item->pending_delete_slide_files = [];

            if ($item->is_withdraw_true == 1) {
                $is_withdraw_true += 1;
                $tls_cost_total = $tls_cost_total + intval($item->tls_cost);
            }

            return $item;
        });

        return [
            'claim_list' => $claim_list,
            'is_withdraw_true' => $is_withdraw_true,
            'tls_cost_total' => $tls_cost_total
        ];
    }

    public function storeEditClaim(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::AccidentOrder);
        $accident_informs = Accident::find($request->id);

        if ($accident_informs) {
            $claim = $this->saveClaim($request, $accident_informs);
        }

        $accident_repair_order = AccidentRepairOrder::find($request->accident_order_id);
        if ($accident_repair_order) {
            if (strcmp($accident_repair_order->status, AccidentRepairStatusEnum::REJECT) === 0) {

                $approve_clear_status = new StepApproveManagement();
                $approve_return = $approve_clear_status->clearStatus(AccidentRepairOrder::class, $request->accident_order_id, ConfigApproveTypeEnum::ACCIDENT_REPAIR_ORDER);

                $accident_repair_order->status = AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR_LIST;
                $accident_repair_order->save();
            }
        }

        $redirect_route = route('admin.accident-orders.index');
        return $this->responseValidateSuccess($redirect_route);
    }


    public function storeEditRepairPrice(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::AccidentOrder);
        $accident_repair_order = AccidentRepairOrder::firstOrNew(['id' => $request->id]);
        $garage_quotation_file = $accident_repair_order->getMedia('garage_quotation_file');
        $validator = Validator::make($request->all(), [
            'garage_quotation_file' => [Rule::when(!$garage_quotation_file, ['required'])],
            'repair_date' => [
                'required',
            ],
            'bidding_date' => [
                'required'
            ],
            'wage' => [
                'required'
            ],
            'spare_parts' => [
                'required'
            ],
            'discount_spare_parts' => [
                'required'
            ],

            'actual_repair_date' => [Rule::when($accident_repair_order->status == AccidentRepairStatusEnum::PROCESS_REPAIR, ['required'])],
        ], [], [
            'garage_quotation_file' => __('accident_orders.garage_quotation_file'),
            'repair_date' => __('accident_orders.repair_date'),
            'bidding_date' => __('accident_orders.garage_bidding'),
            'wage' => __('accident_orders.wage'),
            'spare_parts' => __('accident_orders.spare_part_cost'),
            'discount_spare_parts' => __('accident_orders.spare_part_discount'),
            'actual_repair_date' => __('accident_orders.actual_repair_date'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        if ($accident_repair_order) {

            $accident_repair_order->appointment_date = $request->appointment_date_hidden;
            $accident_repair_order->appointment_place = $request->appointment_place_hidden;
            if (($request->appointment_date_hidden && $request->appointment_place_hidden) || ($accident_repair_order->appointment_date && $accident_repair_order->appointment_place)) {
                $accident_repair_order->is_appointment = STATUS_ACTIVE;
            }

            $accident_repair_order->contacts_tls = $request->contacts_tls;
            $accident_repair_order->contacts_insurance = $request->contacts_insurance;
            $accident_repair_order->contacts_customer = $request->contacts_customer;
            // $accident_repair_order->cradle_area_id = $request->cradle_area_id;
            // $accident_repair_order->cradle_id = $request->cradle_id;
            $accident_repair_order->bidding_date = $request->bidding_date;

            $accident_repair_order->repair_date = $request->repair_date;
            $accident_repair_order->amount_completed = $request->amount_completed;
            $accident_repair_order->actual_repair_date = $request->actual_repair_date;

            $wage = str_replace(',', '', $request->wage);
            $accident_repair_order->wage = $wage ? $wage : null;
            $spare_parts = str_replace(',', '', $request->spare_parts);
            $accident_repair_order->spare_parts = $spare_parts ? $spare_parts : null;
            $discount_spare_parts = str_replace(',', '', $request->discount_spare_parts);
            $accident_repair_order->discount_spare_parts = $discount_spare_parts ? $discount_spare_parts : null;
            // create approve when status is WAITING_CRADLE_QUOTATION
            if ($accident_repair_order->status == AccidentRepairStatusEnum::WAITING_CRADLE_QUOTATION) {
                $step_approve_management = new StepApproveManagement();
                $step_approve_management->createModelApproval(ConfigApproveTypeEnum::ACCIDENT_REPAIR_ORDER_SHEET, AccidentRepairOrder::class, $accident_repair_order->id);
            }

            if ($accident_repair_order->status == AccidentRepairStatusEnum::OFFER_NEW_PRICE) {
                $approve_clear_status = new StepApproveManagement();
                $approve_return = $approve_clear_status->clearStatus(AccidentRepairOrder::class, $request->id, ConfigApproveTypeEnum::ACCIDENT_REPAIR_ORDER_SHEET);
                $accident_repair_order->status = AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR;
            }

            // change status 
            if (in_array($accident_repair_order->status, [AccidentRepairStatusEnum::WAITING_CRADLE_QUOTATION, AccidentRepairStatusEnum::OFFER_NEW_PRICE])) {
                $accident_repair_order->status = AccidentRepairStatusEnum::WAITING_APPROVE_REPAIR;
            }

            if ($request->actual_repair_date) {
                $accident_repair_order->status = AccidentRepairStatusEnum::SUCCESS_REPAIR;
            }

            $accident_repair_order->save();

            if ($request->garage_quotation_file__pending_delete_ids) {
                $pending_delete_ids = $request->garage_quotation_file__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $accident_repair_order->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('garage_quotation_file')) {
                foreach ($request->file('garage_quotation_file') as $image) {
                    if ($image->isValid()) {
                        $accident_repair_order->addMedia($image)->toMediaCollection('garage_quotation_file');
                    }
                }
            }
        }

        if ($request->spare) {
            AccidentRepairLinePrice::where('accident_repair_order_id', $accident_repair_order->id)->delete();
            if (!empty($request->spare)) {
                foreach ($request->spare as $index => $spare) {
                    $accident_repair_line_price = new AccidentRepairLinePrice();
                    $accident_repair_line_price->supplier = $spare['supplier'];
                    $spare_parts_line = isset($spare['spare_parts']) ? str_replace(',', '', $spare['spare_parts']) : null;
                    $accident_repair_line_price->spare_parts = $spare_parts_line;
                    $spare_part_discount_line = isset($spare['discount_spare_parts']) ? str_replace(',', '', $spare['discount_spare_parts']) : null;
                    $accident_repair_line_price->discount_spare_parts = $spare_part_discount_line;
                    $accident_repair_line_price->accident_repair_order_id = $accident_repair_order->id;
                    $accident_repair_line_price->save();
                }
            }
        }

        $redirect_route = route('admin.accident-orders.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    private function saveClaim($request, $claim_model)
    {
        $delete_claim_ids = $request->delete_claim_ids;
        if ((!empty($delete_claim_ids)) && (is_array($delete_claim_ids))) {
            foreach ($delete_claim_ids as $delete_id) {
                $claim_delete = AccidentClaimLines::find($delete_id);
                $claim_delete->delete();
            }
        }

        $pending_delete_before_files = $request->before_files__pending_delete_ids;
        $pending_delete_after_files = $request->after_files__pending_delete_ids;
        if (!empty($request->repair)) {
            foreach ($request->repair as $key => $request_repair) {
                $acciden_claim_line = AccidentClaimLines::firstOrNew(['id' => $request_repair['id']]);
                if (!$acciden_claim_line->exists) {
                    //
                }
                $acciden_claim_line->accident_id = $claim_model->id;
                $acciden_claim_line->accident_claim_list_id = $request_repair['accident_claim_id'];
                $acciden_claim_line->supplier = $request_repair['supplier'];
                $acciden_claim_line->is_withdraw_true = filter_var($request_repair['is_withdraw_true'], FILTER_VALIDATE_BOOLEAN);
                $acciden_claim_line->wound_characteristics = $request_repair['wound_characteristics_id'];
                $tls_cost = str_replace(',', '', $request_repair['tls_cost']);
                $acciden_claim_line->cost = $tls_cost ? $tls_cost : null;
                $acciden_claim_line->save();

                if ((!empty($pending_delete_before_files)) && (sizeof($pending_delete_before_files) > 0)) {
                    foreach ($pending_delete_before_files as $before_media_id) {
                        $before_media = Media::find($before_media_id);
                        if ($before_media && $before_media->model_id) {
                            $before_media->delete();
                        }
                    }
                }

                // insert + update before
                if ((!empty($request->before_files)) && (sizeof($request->before_files) > 0)) {
                    foreach ($request->before_files as $table_row_index => $before_files) {
                        foreach ($before_files as $before_file) {
                            if ($before_file->isValid() && (strcmp($table_row_index, 'table_row_' . $key) == 0)) {
                                $acciden_claim_line->addMedia($before_file)->toMediaCollection('before_file');
                            }
                        }
                    }
                }

                // delete file after
                if ((!empty($pending_delete_after_files)) && (sizeof($pending_delete_after_files) > 0)) {
                    foreach ($pending_delete_after_files as $after_media_id) {
                        $after_media = Media::find($after_media_id);
                        if ($after_media && $after_media->model_id) {
                            $after_media->delete();
                        }
                    }
                }

                // insert + update after
                if ((!empty($request->after_files)) && (sizeof($request->after_files) > 0)) {
                    foreach ($request->after_files as $table_row_index => $after_files) {
                        foreach ($after_files as $after_file) {
                            if ($after_file->isValid() && (strcmp($table_row_index, 'table_row_' . $key) == 0)) {
                                $acciden_claim_line->addMedia($after_file)->toMediaCollection('after_file');
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    public function getDataCarAccident(Request $request)
    {
        $report_id = $request->report_id;
        $data = [];
        $accident_data = Accident::where('id', $report_id)->first();
        $accident_data->case = __('accident_informs.case_' . $accident_data->case);
        $accident_data->claim_no = $accident_data->claim_no;
        $accident_date = new DateTime($accident_data->accident_date);
        $accident_data->accident_date = $accident_date ? $accident_date->format('d/m/Y H:i') : null;
        $accident_data->worksheet_no = $accident_data->worksheet_no;
        $accident_data->accident_description = $accident_data->accident_description;
        $accident_data->repair_type = $accident_data->repair_type;
        return [
            'success' => true,
            'data' => $data,
            'accident_data' => $accident_data,
        ];
    }

    public static function getStatusReceiveList()
    {
        return collect([
            [
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => __('accident_informs.receive_status_' . STATUS_ACTIVE),
            ],
            [
                'id' => STATUS_DEFAULT,
                'value' => STATUS_DEFAULT,
                'name' => __('accident_informs.receive_status_' . STATUS_DEFAULT),
            ],
        ]);
    }

    public function sendMail(Request $request)
    {
        $accident_repair_order_id = $request->accident_repair_order_id;
        $accident_repair_order = AccidentRepairOrder::find($accident_repair_order_id);
        $accident_repair_order->appointment_date = $request->appointment_date;
        $accident_repair_order->appointment_place = $request->appointment_place;
        if (($request->appointment_date && $request->appointment_place) || ($accident_repair_order->appointment_date && $accident_repair_order->appointment_place)) {
            $accident_repair_order->is_appointment = STATUS_ACTIVE;
        }
        $accident_repair_order->save();
        $accident = Accident::find($accident_repair_order->accident_id);
        $car_id = $accident->car_id;
        if (!$car_id) {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found')
            ]);
        }
        $car = Car::find($car_id);
        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found')
            ]);
        }
        $car_class = $car->carClass ? $car->carClass->name : null;
        $car_brand = $car->carBrand ? $car->carBrand->name : null;

        $cradle = Cradle::find($accident_repair_order->cradle_id);

        $accident_repair_order_lines = AccidentRepairOrderLine::where('accident_repair_order_id', $request->accident_repair_order_id)->pluck('accident_claim_line_id')->toArray();

        $before_medias_arr = [];
        foreach ($accident_repair_order_lines as $index => $order_lines) {
            $accident_claim_line = AccidentClaimLines::find($order_lines);
            $before_medias = $accident_claim_line->getMedia('before_file');
            $before_medias = get_medias_detail($before_medias);
            $before_medias = collect($before_medias)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $before_medias_arr[] = $before_medias[0]['url'];
        }

        // $before_medias_arr = [
        //     "https://production-smartcar.obs.ap-southeast-2.myhuaweicloud.com/logo-mail.png"
        // ];

        $garage_quotation_file = $accident_repair_order->getMedia('garage_quotation_file');
        $garage_quotation_files = get_medias_detail($garage_quotation_file);

        $files_url = [];
        foreach ($garage_quotation_files as $index => $garage_quotation_file) {
            // $files_url[] = $garage_quotation_file['url'];
            $files_url_data = [];
            $files_url_data['url'] = $garage_quotation_file['url'];
            $files_url_data['name'] = $garage_quotation_file['file_name'];
            $files_url_data['file_path'] = $garage_quotation_file['file_path'];
            $files_url_data['mime'] = $garage_quotation_file['mime_type'];
            $files_url_data['file_path_storage'] = $garage_quotation_file['file_path_storage'];
            $files_url[] = $files_url_data;
        }

        // $files_url = [
        //     [
        //         'url' => 'https://production-smartcar.obs.ap-southeast-2.myhuaweicloud.com/logo-mail.png',
        //         'name' => 'test'
        //     ],
        //     [
        //         'url' => 'https://www-cdn.eumetsat.int/files/styles/16_9_large/s3/2023-04/mtg-i1.jpg?h=d1cb525d&itok=O-COkB2i',
        //         'name' => 'test2'
        //     ]
        // ];
        $file_array = [];

        if ($files_url) {
            foreach ($files_url as $index => $file_url) {
                // $file = str_replace(config('app.url'), '', $file_url['url']); // local
                // $file = str_replace('https://' . env('OBS_CDN_DOMAIN'), '', $file_url['url']); // uat
                // $file = str_replace('/storage/', '', $file); // local
                // $file = 'public/' . $file; // no use in local

                if (Storage::exists($files_url_data['file_path_storage'])) {
                    $file_url_data = Storage::get($files_url_data['file_path_storage']);
                } else {
                    $file_url_data = null;
                }

                // $file_url_data = file_get_contents($file_url['url']);
                $file_array[] = [
                    'data' => $file_url_data,
                    'name' => $file_url['name'],
                    'mime' => $file_url['mime'],
                ];
            }
        }


        if (!$accident_repair_order) {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found')
            ]);
        }

        $image = 'https://uat-smartcar.obs.ap-southeast-2.myhuaweicloud.com/logo-mail.png';
        if (App::environment('production')) {
            $image = 'https://production-smartcar.obs.ap-southeast-2.myhuaweicloud.com/logo-mail.png';
        }
        $mails = [];
        if ($request->true_leasing_email) {
            $mails['true_leasing_email'] = $request->true_leasing_email;
            $user = User::find($request->true_leasing);
            $mails['true_leasing'] = null;
            if ($user) {
                $mails['true_leasing'] = $user->name;
            }
        }
        if ($request->insurance_email) {
            $mails['insurance_email'] = $request->insurance_email;
            $insurer = Insurer::find($request->insurance);
            $mails['insurance'] = null;
            if ($insurer) {
                $mails['insurance'] = $insurer->insurance_name_th;
            }
        }
        if ($request->customer_email) {
            $mails['customer_email'] = $request->customer_email;
            $mails['customer'] = $request->customer;
        }
        // $mails = $request->tags;
        if (empty($mails)) {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found')
            ]);
        }

        $appointment_date = new DateTime($request->appointment_date);
        $appointment_date = $appointment_date->format('d/m/Y');
        $appointment_time = new DateTime($request->appointment_date);
        $appointment_time = $appointment_time->format('H:i');

        $mail_data = [
            'before_medias_arr' => $before_medias_arr,
            'car_class' => $car_class,
            'car_brand' => $car_brand,
            'license_plate' => ($car->license_plate) ? $car->license_plate : null,
            'cradle_name' => ($cradle && $cradle->name) ? $cradle->name : null,
            'appointment_time' => $appointment_date ? $appointment_date : '-',
            'appointment_date' => $appointment_time ? $appointment_time : '-',
            'topic' => $request->topic ? $request->topic : null,
            'remark' => $request->remark ? $request->remark : null,
            'image' => $image,
        ];


        // foreach ($mails as $mail) {
        if ($request->true_leasing_email) {
            Mail::to($mails['true_leasing_email'])->send(new AccidentRepairOrderMail($mail_data, $file_array, $mails['true_leasing']));
        }
        if ($request->insurance_email) {
            Mail::to($mails['insurance_email'])->send(new AccidentRepairOrderMail($mail_data, $file_array, $mails['insurance']));
        }
        if ($request->customer_email) {
            Mail::to($mails['customer_email'])->send(new AccidentRepairOrderMail($mail_data, $file_array, $mails['customer']));
        }
        // }

        return response()->json([
            'success' => true,
        ]);
    }

    function getDefaultUser(Request $request)
    {
        $user_id = $request->user;
        $data = User::find($user_id);
        return [
            'success' => true,
            'data' => $data->email,
        ];
    }

    function getDefaultInsurer(Request $request)
    {
        $insurance_id = $request->insurance;
        $data = Insurer::find($insurance_id);
        return [
            'success' => true,
            'data' => $data->insurance_email,
        ];
    }

    public function createCarReplacementEdit(Request $request)
    {
        if (!$request->id) {
            $user = Auth::user();
            $replacement_car_count = DB::table('replacement_cars')->count() + 1;
            $replacement_car = new ReplacementCar();
            $prefix = 'RC-';
            $replacement_car->worksheet_no = generateRecordNumber($prefix, $replacement_car_count);
            $replacement_car->replacement_type = $request['replacement_type'];
            $replacement_car->job_type = Accident::class;
            $replacement_car->job_id = $request['accident_id'];
            $replacement_car->branch_id = $user ? $user->branch_id : null;
            $replacement_car->main_car_id = $request['car_id'];
            $replacement_car->replacement_expect_date = isset($request['replacement_expect_date']) ? $request['replacement_expect_date'] : null;
            $replacement_car->replacement_date = isset($request['replacement_pickup_date']) ? $request['replacement_pickup_date'] : null;
            $replacement_car->is_need_driver = isset($request['is_driver_replacement']) ? $request['is_driver_replacement'] : STATUS_DEFAULT;
            $replacement_car->is_need_slide = STATUS_DEFAULT;
            $replacement_car->customer_name = isset($request['customer_name']) ? $request['customer_name'] : null;
            $replacement_car->tel = isset($request['tel']) ? $request['tel'] : null;
            $replacement_car->remark = isset($request['remark']) ? $request['remark'] : null;
            $replacement_car->slide_id = isset($request['slide_worksheet']) ? $request['slide_worksheet'] : null;
            $replacement_car->replacement_place = isset($request['place']) ? $request['place'] : null;
            $replacement_car->is_cust_receive_replace = isset($request['customer_receive']) ? $request['customer_receive'] : null;

            if ($request->replacment_file__pending_delete_ids) {
                $pending_delete_ids = $request->replacment_file__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $replacement_car->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('replacment_file')) {
                foreach ($request->file('replacment_file') as $image) {
                    if ($image->isValid()) {
                        $replacement_car->addMedia($image)->toMediaCollection('replacement_car_files');
                    }
                }
            }

            $replacement_car->save();
        } else {
            $user = Auth::user();
            // $replacement_car_count = DB::table('replacement_cars')->count() + 1;
            $replacement_car = ReplacementCar::find($request->id);
            // $prefix = 'RC-';
            // $replacement_car->worksheet_no = generateRecordNumber($prefix, $replacement_car_count);
            $replacement_car->replacement_type = $request['replacement_type'];
            // $replacement_car->job_type = Accident::class;
            // $replacement_car->job_id = $request['accident_id'];
            $replacement_car->branch_id = $user ? $user->branch_id : null;
            $replacement_car->main_car_id = $request['car_id'];
            $replacement_car->replacement_expect_date = isset($request['replacement_expect_date']) ? $request['replacement_expect_date'] : null;
            $replacement_car->replacement_date = isset($request['replacement_pickup_date']) ? $request['replacement_pickup_date'] : null;
            $replacement_car->is_need_driver = isset($request['is_driver_replacement']) ? $request['is_driver_replacement'] : STATUS_DEFAULT;
            $replacement_car->is_need_slide = STATUS_DEFAULT;
            $replacement_car->customer_name = isset($request['customer_name']) ? $request['customer_name'] : null;
            $replacement_car->tel = isset($request['tel']) ? $request['tel'] : null;
            $replacement_car->remark = isset($request['remark']) ? $request['remark'] : null;
            $replacement_car->slide_id = isset($request['slide_worksheet']) ? $request['slide_worksheet'] : null;
            $replacement_car->replacement_place = isset($request['place']) ? $request['place'] : null;
            $replacement_car->is_cust_receive_replace = isset($request['customer_receive']) ? $request['customer_receive'] : null;

            if ($request->replacment_file__pending_delete_ids) {
                $pending_delete_ids = $request->replacment_file__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $replacement_car->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('replacment_file')) {
                foreach ($request->file('replacment_file') as $image) {
                    if ($image->isValid()) {
                        $replacement_car->addMedia($image)->toMediaCollection('replacement_car_files');
                    }
                }
            }

            $replacement_car->save();
        }

        $redirect_route = route('admin.accident-orders.edit', ['accident_order' => $request['accident_order_id']]);
        return $this->responseValidateSuccess($redirect_route);
    }

    public function printAccidentOrderPdf(Request $request)
    {
        $accident_repair_order = AccidentRepairOrder::find($request->accident_order);
        $accident_repair_line_price = AccidentRepairLinePrice::where('accident_repair_order_id', $accident_repair_order->id)->get();
        $total_spare_parts = $accident_repair_line_price->sum(function ($line_price) {
            return $line_price->spare_parts - $line_price->discount_spare_parts;
        });
        $accident = Accident::find($accident_repair_order->accident_id);
        $contract = Contracts::where('job_type', $accident->job_type)->where('job_id', $accident->job_type)->first();
        if ($contract) {
            $contract->date_send_contract = $contract->date_send_contract ? $contract->date_send_contract->format('d/m/Y') : null;
            $contract->date_return_contract = $contract->date_return_contract ? $contract->date_return_contract->format('d/m/Y') : null;
        }
        $page_title = __('accident_orders.car_claim_sheet') . ' ' . $accident_repair_order->worksheet_no;
        $car = Car::find($accident->car_id);

        // $registered_date = Carbon::createFromFormat('Y/m/d', $car->registered_date);
        $current_date = Carbon::now();

        $diff_years = $current_date->diffInYears($car->registered_date);
        $diff_months = $current_date->diffInMonths($car->registered_date);
        $diff_days = $current_date->diffInDays($car->registered_date);
        $car_age = $diff_years . ' ปี ' . $diff_months . ' เดือน ' . $diff_days .' วัน ';
        // $car->registered_date = $car->registered_date ? get_date_time_by_format($car->registered_date, 'd/m/Y') : null;
        $car->registered_date = date('d/m/Y', strtotime($car->registered_date));

     

        $rental_type = null;
        $rental = null;
        $vmi = VMI::where('car_id', $car->id)
            ->where('status_vmi', InsuranceCarStatusEnum::UNDER_POLICY)
            ->orderBy('year', 'desc')
            ->first();

        if (strcmp($car->rental_type, RentalTypeEnum::SHORT) == 0) {
            $rental = RentalLine::leftJoin('rentals', 'rentals.id', '=', 'rental_lines.rental_id')
                ->where('rental_lines.car_id', $car->id)
                ->where('rentals.status', RentalStatusEnum::PAID)
                ->select('rentals.id', 'rentals.worksheet_no', 'rentals.customer_name')
                ->first();
            if ($rental) {
                $rental_type = Rental::class;
                $contract = Contracts::leftJoin('contract_lines', 'contract_lines.contract_id', '=', 'contracts.id')
                    ->where('contracts.job_type', Rental::class)
                    ->where('contracts.job_id', $rental->id)
                    ->where('contract_lines.car_id', $car->id)
                    ->select('contracts.id', 'contracts.worksheet_no', 'contract_lines.pick_up_date', 'contract_lines.return_date')
                    ->first();
            }
        }
        if (strcmp($car->rental_type, RentalTypeEnum::LONG) == 0) {
            $rental = LongTermRentalPRCar::leftJoin('lt_rental_pr_lines', 'lt_rental_pr_lines.id', '=', 'lt_rental_pr_lines_cars.lt_rental_pr_line_id')
                ->leftJoin('lt_rentals', 'lt_rentals.id', '=', 'lt_rental_pr_lines.lt_rental_id')
                ->where('lt_rental_pr_lines_cars.car_id', $car->id)
                ->select('lt_rentals.id', 'lt_rentals.worksheet_no', 'lt_rentals.customer_name')
                ->first();
            if ($rental) {
                $rental_type = LongTermRental::class;
                $contract = Contracts::leftJoin('contract_lines', 'contract_lines.contract_id', '=', 'contracts.id')
                    ->where('contracts.job_type', LongTermRental::class)
                    ->where('contracts.job_id', $rental->id)
                    ->where('contract_lines.car_id', $car->id)
                    ->select('contracts.id', 'contracts.worksheet_no', 'contract_lines.pick_up_date', 'contract_lines.return_date')
                    ->first();
            }
        }

        $accident_repair_order_lines = AccidentRepairOrderLine::where('accident_repair_order_id', $request->accident_order)->pluck('accident_claim_line_id')->toArray();
        $claim_list = AccidentClaimLines::whereIn('id', $accident_repair_order_lines)->get();
        $is_withdraw_true = 0;
        $tls_cost_total = 0;
        $claim_list->map(function ($item) use (&$is_withdraw_true, &$tls_cost_total) {
            if ($item->accident_claim_list_id) {
                $accident_claim_text = ClaimList::find($item->accident_claim_list_id);
                $item->accident_claim_text = $accident_claim_text->name;
            }
            $item->accident_claim_id = ($item->accident_claim_list_id) ? $item->accident_claim_list_id : '';
            $item->wound_characteristics_text = ($item->wound_characteristics) ? __('accident_informs.wound_type_' . $item->wound_characteristics) : '';
            $item->wound_characteristics = ($item->wound_characteristics) ? $item->wound_characteristics : '';
            $item->wound_characteristics_id = ($item->wound_characteristics) ? $item->wound_characteristics : '';
            $item->supplier_text = (!is_null($item->supplier)) ? __('accident_informs.spare_part_status_' . $item->supplier) : '';
            $item->tls_cost = ($item->cost) ? $item->cost : '';

            $before_medias = $item->getMedia('before_file');
            $before_medias = get_medias_detail($before_medias);
            $before_medias = collect($before_medias)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $item->before_files = $before_medias;

            $after_medias = $item->getMedia('after_file');
            $after_medias = get_medias_detail($after_medias);
            $after_medias = collect($after_medias)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $item->after_files = $after_medias;

            $item->pending_delete_slide_files = [];

            if ($item->is_withdraw_true == 1) {
                $is_withdraw_true += 1;
                $tls_cost_total = $tls_cost_total + intval($item->tls_cost);
            }

            return $item;
        });



        $pdf = PDF::loadView(
            'admin.accident-orders.accident-order-pdf.pdf',
            [
                'd' => $claim_list,
                'page_title' => $page_title,
                'car' => $car,
                'contract' => $contract,
                'accident_repair_line_price' => $accident_repair_line_price,
                'rental' => $rental,
                'vmi' => $vmi,
                'car_age' => $car_age,
                'accident' => $accident,
                'total_spare_parts' => $total_spare_parts,
                // 'lt_rental_lines' => $lt_rental_lines,
                // 'lt_rental_accessory' => $lt_rental_accessory,
                // 'lt_rental_line_month' => $lt_rental_line_month,
                // 'lt_rental_month' => $lt_rental_month,
                // 'quotation_form' => $quotation_form,
                // 'lt_rental' => $lt_rental,
            ]
        );
        return  $pdf->stream();
        // dd($claim_list);

        // }

        // if ($request->quotation) {
        //     $quotation_id = $request->quotation;
        //     $quotation = Quotation::find($quotation_id);
        //     $page_title = $quotation->qt_no;
        //     $reference_id = $quotation->reference_id;
        //     if (strcmp($quotation->reference_type, LongTermRental::class) == 0) {
        //         $lt_rental = LongTermRental::find($reference_id);
        //         if ($lt_rental) {
        //             $lt_rental_lines = LongTermRentalLine::where('lt_rental_id', $lt_rental->id)
        //                 ->get();
        //             $lt_rental_lines->map(function ($item) {
        //                 $item->car_class_text = ($item->carClass) ? $item->carClass->full_name : '';
        //                 $item->car_color_text = ($item->color) ? $item->color->name : '';
        //                 $item->remark_quotation = $item->remark_quotation;
        //                 return $item;
        //             });

        //             $lt_rental_line_accessory = LongTermRentalLineAccessory::whereIn('lt_rental_line_id', $lt_rental_lines->pluck('id'))->get();
        //             $lt_rental_accessory = [];
        //             $index = 0;
        //             foreach ($lt_rental_lines as $car_index => $car_item) {
        //                 foreach ($lt_rental_line_accessory as $accessory_index => $accessory_item) {
        //                     if (strcmp($car_item->id, $accessory_item->lt_rental_line_id) == 0) {
        //                         $lt_rental_accessory[$index]['accessory_text'] = ($accessory_item->accessory) ? $accessory_item->accessory->name : '';
        //                         $lt_rental_accessory[$index]['car_index'] = $car_index;
        //                         $index++;
        //                     }
        //                 }
        //             }

        //             $lt_rental_month = LongTermRentalLineMonth::whereIn('lt_rental_lines_months.lt_rental_line_id', $lt_rental_lines->pluck('id'))
        //                 ->leftJoin('lt_rental_month', 'lt_rental_month.id', '=', 'lt_rental_lines_months.lt_rental_month_id')->select(
        //                     'lt_rental_month.month',
        //                     'lt_rental_lines_months.lt_rental_month_id',
        //                 )->groupBy(
        //                     'lt_rental_month.month',
        //                     'lt_rental_lines_months.lt_rental_month_id',
        //                 )
        //                 ->get();

        //             $lt_rental_line_month = LongTermRentalLineMonth::whereIn('lt_rental_lines_months.lt_rental_line_id', $lt_rental_lines->pluck('id'))
        //                 ->select(
        //                     'lt_rental_lines_months.lt_rental_line_id',
        //                     'lt_rental_lines_months.subtotal_price',
        //                     'lt_rental_lines_months.total_price',
        //                     'lt_rental_lines_months.total_purchase_options',
        //                 )->get();

        //             $quotation_form = QuotationForm::where('quotation_id', $quotation->id)->where('status', STATUS_ACTIVE)->orderBy('seq', 'asc')->get();
        //             $list2 = $quotation_form->pluck('id')->toArray();
        //             $quotation_form_checklists = QuotationFormChecklist::whereIn('quotation_form_id', $list2)->where('status', STATUS_ACTIVE)->orderBy('seq', 'asc')->get();
        //             $quotation_form_checklists->map(function ($item) {
        //                 $item->quotation_form_checklist_name  = $item->name;
        //                 $item->quotation_form_checklist_id  = $item->id;
        //                 return $item;
        //             });
        //             $quotation_form->map(function ($item) use ($quotation_form_checklists) {
        //                 $quotation_form_checklist = $quotation_form_checklists->where('quotation_form_id', $item->id)->values();
        //                 $item->sub_quotation_form_checklist  = $quotation_form_checklist;
        //                 $item->quotation_form_id  = $item->id;
        //                 return $item;
        //             });

        //             $user = User::find($quotation->created_by);
        //             $quotation->user_name = $user ? $user->name : null;
        //             $quotation->user_tel = $user ? $user->tel : null;
        //             $quotation->user_email = $user ? $user->email : null;

        //             $pdf = PDF::loadView(
        //                 'admin.quotations.long-term-rental-pdf.pdf',
        //                 [
        //                     'd' => $quotation,
        //                     'page_title' => $page_title,
        //                     'lt_rental_lines' => $lt_rental_lines,
        //                     'lt_rental_accessory' => $lt_rental_accessory,
        //                     'lt_rental_line_month' => $lt_rental_line_month,
        //                     'lt_rental_month' => $lt_rental_month,
        //                     'quotation_form' => $quotation_form,
        //                     'lt_rental' => $lt_rental,
        //                 ]
        //             );
        //             return  $pdf->stream();
        //         } else {
        //             return  redirect()->route('admin.quotations.index');
        //         }
        //     }
        // } else {
        //     return  redirect()->route('admin.quotations.index');
        // }
    }
}
