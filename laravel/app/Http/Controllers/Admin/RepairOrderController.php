<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Enums\Actions;
use App\Enums\ConditionGroupEnum;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\InspectionTypeEnum;
use App\Enums\ReplacementJobTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\ReplacementCar;
use Illuminate\Http\Request;
use App\Enums\RepairStatusEnum;
use App\Enums\RepairEnum;
use App\Enums\SelfDriveTypeEnum;
use App\Enums\TransferTypeEnum;
use App\Models\RepairOrder;
use App\Models\Repair;
use App\Models\Car;
use App\Models\CheckDistance;
use App\Models\CheckDistanceLine;
use App\Models\RepairLine;
use App\Models\Creditor;
use App\Models\RepairList;
use App\Models\RepairOrderLine;
use App\Models\Quotation;
use App\Models\QuotationForm;
use App\Models\QuotationFormChecklist;
use App\Models\ConditionQuotation;
use App\Models\ConditionQuotationChecklist;
use App\Traits\ConditionQuotationTrait;
use Illuminate\Support\Facades\Validator;
use App\Traits\RepairTrait;
use App\Traits\ReplacementCarTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\RepairOrderMail;
use App\Models\DrivingJob;
use App\Models\InspectionJob;
use App\Models\RepairOrderDate;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class RepairOrderController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::RepairOrder);
        $worksheet_no = $request->worksheet_no;
        $repair_type = $request->repair_type;
        $license_plate = $request->license_plate;
        $contact = $request->contact;
        $order_worksheet_no = $request->order_worksheet_no;
        $status = $request->status;
        $center = $request->center;
        $alert_date = $request->alert_date;
        $repair_order_date = $request->repair_order_date;
        $district_center = $request->district_center;
        $list = RepairOrder::leftJoin('repairs', 'repairs.id', '=', 'repair_orders.repair_id')
            ->leftJoin('cars', 'cars.id', '=', 'repairs.car_id')
            ->leftJoin('creditors', 'creditors.id', '=', 'repair_orders.center_id')
            ->leftJoin('provinces', 'provinces.id', '=', 'creditors.province_id')
            ->leftJoin('geographies', 'geographies.id', '=', 'provinces.geography_id')
            ->select(
                'repairs.id as repairs_id',
                'repairs.worksheet_no as repair_worksheet_no',
                'repairs.repair_type',
                'repairs.repair_date',
                'repairs.contact',
                'cars.license_plate',
                'repairs.in_center_date',
                'repair_orders.id',
                'repair_orders.worksheet_no',
                'repair_orders.expected_repair_date',
                'repair_orders.repair_date as completed_date',
                'creditors.name as center',
                'repair_orders.status',
                'provinces.geography_id'
            )
            ->search($request)
            ->when($worksheet_no, function ($query) use ($worksheet_no) {
                $query->where('repair_orders.repair_id', $worksheet_no);
            })
            ->when($license_plate, function ($query) use ($license_plate) {
                $query->where('repairs.car_id', $license_plate);
            })
            ->when($repair_type, function ($query) use ($repair_type) {
                $query->where('repairs.repair_type', $repair_type);
            })
            ->when($contact, function ($query) use ($contact) {
                $query->where('repairs.contact', $contact);
            })
            ->when($alert_date, function ($query) use ($alert_date) {
                $query->whereDate('repairs.repair_date', $alert_date);
            })
            ->when($district_center, function ($query) use ($district_center) {
                $query->where('provinces.geography_id', $district_center);
            })
            ->when($repair_order_date, function ($query) use ($repair_order_date) {
                $query->whereDate('repair_orders.created_at', $repair_order_date);
            })
            ->orderBy('repair_orders.created_at', 'desc')
            ->paginate(PER_PAGE);

        $worksheet_no_list = RepairOrder::select('repairs.id', 'repairs.worksheet_no as name')
            ->leftJoin('repairs', 'repairs.id', '=', 'repair_orders.repair_id')->orderBy('repairs.worksheet_no')->get();
        $repair_type_list = RepairTrait::getRepairType();
        $license_plate_list = Car::select('id', 'license_plate as name')->get();
        $contact_list = RepairOrder::select('repairs.contact as id', 'repairs.contact as name')
            ->leftJoin('repairs', 'repairs.id', '=', 'repair_orders.repair_id')->orderBy('repairs.contact')->distinct()->get();
        $order_worksheet_no_list = RepairOrder::select('id', 'worksheet_no as name')->orderBy('worksheet_no')->get();
        $status_list = RepairTrait::getStatus();
        $center_list = RepairOrder::leftJoin('creditors', 'creditors.id', '=', 'repair_orders.center_id')
            ->select('creditors.id', 'creditors.name')->orderBy('creditors.name')->distinct()->get();
        $district_center_list = RepairTrait::getDistrict();

        $create_uri = route('admin.repair-orders.create');
        $edit_uri = 'admin.repair-orders.edit';
        $view_uri = 'admin.repair-orders.show';
        $param = 'repair_order';
        $view_permission = Actions::View . '_' . Resources::RepairOrder;
        $manage_permission = Actions::Manage . '_' . Resources::RepairOrder;
        $open_by = false;
        $page_title = __('repair_orders.page_title');
        return view('admin.repair-orders.index', [
            'list' => $list,
            'page_title' => $page_title,
            'create_uri' => $create_uri,
            'edit_uri' => $edit_uri,
            'view_uri' => $view_uri,
            'param' => $param,
            'view_permission' => $view_permission,
            'manage_permission' => $manage_permission,
            'worksheet_no' => $worksheet_no,
            'worksheet_no_list' => $worksheet_no_list,
            'repair_type' => $repair_type,
            'repair_type_list' => $repair_type_list,
            'license_plate' => $license_plate,
            'license_plate_list' => $license_plate_list,
            'contact' => $contact,
            'contact_list' => $contact_list,
            'order_worksheet_no' => $order_worksheet_no,
            'order_worksheet_no_list' => $order_worksheet_no_list,
            'status' => $status,
            'center' => $center,
            'center_list' => $center_list,
            'status_list' => $status_list,
            'alert_date' => $alert_date,
            'district_center_list' => $district_center_list,
            'repair_order_date' => $repair_order_date,
            'district_center' => $district_center,
            'open_by' => $open_by,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::RepairOrder);
        $step_approve_management = new StepApproveManagement();
        $is_configured = $step_approve_management->isApproveConfigured(ConfigApproveTypeEnum::REPAIR_ORDER);
//        if (!$is_configured) {
//            return redirect()->back()->with('warning', __('lang.config_approve_warning') . __('repair_orders.page_title'));
//        }
        $d = new RepairOrder();
        $d->status = RepairStatusEnum::WAIT_OPEN_REPAIR_ORDER;
        $replacement_type_list = ReplacementCarTrait::getReplacementTypeList();
        $yes_no_list = getYesNoList();
        $check_list = RepairTrait::getCheckList();
        $repair_type_list = RepairTrait::getRepairType();
        $service_center_list = RepairTrait::getServiceCenter();
        $is_need_driver = RepairTrait::getIsNeedDriver();
        $repair_ids = RepairOrder::pluck('repair_id')->toArray();
        $repair_no_list = Repair::select('id', 'worksheet_no as name')
            ->whereNotIn('id', $repair_ids)->get();
        $center_list = RepairTrait::getCenterList();
        $repair_lists = RepairTrait::getRepairListId();
        $car_data = new Car;
        $rental = 0;
        $index_uri = 'admin.repair-orders.index';

        $page_title = __('lang.create') . __('repair_orders.page_title');
        return view('admin.repair-orders.form', [
            'd' => $d,
            'page_title' => $page_title,
            'replacement_type_list' => $replacement_type_list,
            'check_list' => $check_list,
            'repair_type_list' => $repair_type_list,
            'service_center_list' => $service_center_list,
            'is_need_driver' => $is_need_driver,
            'repair_no_list' => $repair_no_list,
            'condotion_lt_rental' => [],
            'car_data' => $car_data,
            'rental' => $rental,
            'center_list' => $center_list,
            'repair_lists' => $repair_lists,
            'have_expenses_list' => [],
            'create' => true,
            'route_group' => [
                'tab_repair_order' => '',
                'tab_condition' => '',
            ],
            'index_uri' => $index_uri,
            'user_file' => null,
            'yes_no_list' => $yes_no_list,
            'replacement_list' => [],
            'driving_job_in' => null,
            'inspection_job_in' => null,
            'driving_job_out' => null,
            'inspection_job_out' => null,
            'mode' => MODE_CREATE
        ]);
    }

    public function edit(RepairOrder $repair_order)
    {
        $this->authorize(Actions::Manage . '_' . Resources::RepairOrder);
        $replacement_type_list = ReplacementCarTrait::getReplacementTypeList();
        $yes_no_list = getYesNoList();
        $check_list = RepairTrait::getCheckList();
        $repair_type_list = RepairTrait::getRepairType();
        $service_center_list = RepairTrait::getServiceCenter();
        $is_need_driver = RepairTrait::getIsNeedDriver();
        $repair_no_list = Repair::select('id', 'worksheet_no as name')->get();
        $center_list = RepairTrait::getCenterList();
        // $repair_lists = RepairTrait::getRepairListId();
        $have_expenses_list = RepairTrait::getHaveExpensesList();
        $car_data = new Car;
        $rental = 0;
        $repair_order->car_license = null;
        $repair = Repair::find($repair_order->repair_id);
        $condotion_lt_rental = [];
        $driving_job_in = null;
        $inspection_job_in = null;
        $driving_job_out = null;
        $inspection_job_out = null;
        $replacement_list = RepairTrait::getReplacementList($repair?->id);

        if ($repair) {
            $repair_order->car_id = $repair->car_id;
            $repair_order->contact = $repair->contact;
            $repair_order->tel = $repair->tel;
            $condotion_lt_rental = RepairTrait::getConditionQuotation($repair);
            $car_data = RepairTrait::getDataCar($repair->car_id);
            if ($car_data) {
                $rental = $car_data->rental;
                if ($car_data->license_plate) {
                    $repair_order->car_license = $car_data->license_plate;
                } else if ($car_data->engine_no) {
                    $repair_order->car_license = __('inspection_cars.engine_no') . ' ' . $car_data->engine_no;
                } else if ($car_data->chassis_no) {
                    $repair_order->car_license = __('inspection_cars.chassis_no') . ' ' . $car_data->chassis_no;
                }
            }
            $repair_order->is_replacement = $repair->is_replacement;
            $repair_order->replacement_date = $repair->replacement_date;
            $repair_order->replacement_type = $repair->replacement_type;
            $repair_order->replacement_place = $repair->replacement_place;
            $repair_order->in_center = $repair->in_center;
            $repair_order->in_center_date = $repair->in_center_date;
            $repair_order->is_driver_in_center = $repair->is_driver_in_center;
            $repair_order->out_center = $repair->out_center;
            $repair_order->out_center_date = $repair->out_center_date;
            $repair_order->is_driver_out_center = $repair->is_driver_out_center;

            $repair_documents_media = $repair->getMedia('repair_documents');
            $repair_documents_files = get_medias_detail($repair_documents_media);

            if ((strcmp($repair->in_center, BOOL_FALSE) == 0) && (strcmp($repair->is_driver_in_center, BOOL_TRUE) == 0)) {
                $driving_job_in = RepairTrait::getDrivingJob($repair_order->id, SelfDriveTypeEnum::PICKUP);
                $inspection_job_in = RepairTrait::getInspectionJob($repair_order->id, InspectionTypeEnum::MAINTENANCE_RC);
            }
            if ((strcmp($repair->out_center, BOOL_FALSE) == 0) && (strcmp($repair->is_driver_out_center, BOOL_TRUE) == 0)) {
                $driving_job_out = RepairTrait::getDrivingJob($repair_order->id, SelfDriveTypeEnum::SEND);
                $inspection_job_out = RepairTrait::getInspectionJob($repair_order->id, InspectionTypeEnum::MAINTENANCE_DC);
            }
        }

        $creditor = Creditor::find($repair_order->center_id);
        $repair_order->center_address = null;
        if ($creditor) {
            $address = ($creditor->address) ? $creditor->address : null;
            $mobile = ($creditor->mobile) ? $creditor->mobile : null;
            $repair_order->center_address = ($address) ? $address . '(' . $mobile . ')' : null;
            $repair_order->center_mail = ($creditor->email) ? $creditor->email : null;
        }

        $repair_line = RepairLine::where('repair_id', $repair_order->repair_id)
            ->get()->map(function ($item) {
                $item->check_text = $item->check ? __('check_distances.type_text_' . $item->check) : null;
                return $item;
            });

        $repair_order_line = RepairOrderLine::where('repair_order_id', $repair_order->id)
            ->get()->map(function ($item) {
                $item->check_text = $item->check ? __('check_distances.type_text_' . $item->check) : null;
                $item->date = ($item->created_at) ? date('Y-m-d', strtotime($item->created_at)) : null;
                $repair_list = RepairList::find($item->repair_list_id);
                $item->code_name = null;
                if ($repair_list) {
                    $item->code_name = $repair_list->code . ' ' . $repair_list->name;
                }
                return $item;
            });

        $user_file = null;
        $expense_file_media = $repair_order->getMedia('expense_files');
        $expense_files = get_medias_detail($expense_file_media);
        if (isset($expense_file_media[0]->custom_properties['created_by'])) {
            $user_id = $expense_file_media[0]->custom_properties['created_by'];
            $user = User::find($user_id);
            if ($user) {
                $user_file = $user->name;
            }
        }

        $route_group = [
            'tab_repair_order' => route('admin.repair-orders.edit', ['repair_order' => $repair_order]),
            'tab_condition' => route('admin.repair-order-conditions.edit', ['repair_order_condition' => $repair_order]),
        ];
        $index_uri = 'admin.repair-orders.index';
        $page_title = __('lang.edit') . __('repair_orders.page_title');
        return view('admin.repair-orders.form', [
            'd' => $repair_order,
            'page_title' => $page_title,
            'replacement_type_list' => $replacement_type_list,
            'check_list' => $check_list,
            'repair_type_list' => $repair_type_list,
            'service_center_list' => $service_center_list,
            'is_need_driver' => $is_need_driver,
            'repair_no_list' => $repair_no_list,
            'condotion_lt_rental' => $condotion_lt_rental,
            'car_data' => $car_data,
            'rental' => $rental,
            'center_list' => $center_list,
            // 'repair_lists' => $repair_lists,
            'edit' => true,
            'repair' => $repair,
            'repair_line' => $repair_line,
            'repair_order_line_list' => $repair_order_line,
            'have_expenses_list' => $have_expenses_list,
            'route_group' => $route_group,
            'index_uri' => $index_uri,
            'repair_documents_files' => $repair_documents_files,
            'user_file' => $user_file,
            'expense_files' => $expense_files,
            'yes_no_list' => $yes_no_list,
            'replacement_list' => $replacement_list,
            'driving_job_in' => $driving_job_in,
            'inspection_job_in' => $inspection_job_in,
            'driving_job_out' => $driving_job_out,
            'inspection_job_out' => $inspection_job_out,
            'mode' => MODE_UPDATE
        ]);
    }

    public function show(RepairOrder $repair_order)
    {
        $this->authorize(Actions::View . '_' . Resources::RepairOrder);
        $replacement_type_list = ReplacementCarTrait::getReplacementTypeList();
        $check_list = RepairTrait::getCheckList();
        $repair_type_list = RepairTrait::getRepairType();
        $service_center_list = RepairTrait::getServiceCenter();
        $is_need_driver = RepairTrait::getIsNeedDriver();
        $repair_no_list = Repair::select('id', 'worksheet_no as name')->get();
        $center_list = RepairTrait::getCenterList();
        $repair_lists = RepairTrait::getRepairListId();
        $have_expenses_list = RepairTrait::getHaveExpensesList();
        $car_data = new Car;
        $rental = 0;
        $repair_order->car_license = null;
        $repair = Repair::find($repair_order->repair_id);
        $condotion_lt_rental = [];
        $driving_job_in = null;
        $inspection_job_in = null;
        $driving_job_out = null;
        $inspection_job_out = null;

        if ($repair) {
            $repair_order->car_id = $repair->car_id;
            $repair_order->contact = $repair->contact;
            $repair_order->tel = $repair->tel;
            $condotion_lt_rental = RepairTrait::getConditionQuotation($repair);
            $car_data = RepairTrait::getDataCar($repair->car_id);
            if ($car_data) {
                $rental = $car_data->rental;
                if ($car_data->license_plate) {
                    $repair_order->car_license = $car_data->license_plate;
                } else if ($car_data->engine_no) {
                    $repair_order->car_license = __('inspection_cars.engine_no') . ' ' . $car_data->engine_no;
                } else if ($car_data->chassis_no) {
                    $repair_order->car_license = __('inspection_cars.chassis_no') . ' ' . $car_data->chassis_no;
                }
            }
            $repair_order->is_replacement = $repair->is_replacement;
            $repair_order->replacement_date = $repair->replacement_date;
            $repair_order->replacement_type = $repair->replacement_type;
            $repair_order->replacement_place = $repair->replacement_place;
            $repair_order->in_center = $repair->in_center;
            $repair_order->in_center_date = $repair->in_center_date;
            $repair_order->is_driver_in_center = $repair->is_driver_in_center;
            $repair_order->out_center = $repair->out_center;
            $repair_order->out_center_date = $repair->out_center_date;
            $repair_order->is_driver_out_center = $repair->is_driver_out_center;

            $repair_documents_media = $repair->getMedia('repair_documents');
            $repair_documents_files = get_medias_detail($repair_documents_media);

            if ((strcmp($repair->in_center, BOOL_FALSE) == 0) && (strcmp($repair->is_driver_in_center, BOOL_TRUE) == 0)) {
                $driving_job_in = RepairTrait::getDrivingJob($repair_order->id, SelfDriveTypeEnum::PICKUP);
                $inspection_job_in = RepairTrait::getInspectionJob($repair_order->id, InspectionTypeEnum::MAINTENANCE_RC);
            }
            if ((strcmp($repair->out_center, BOOL_FALSE) == 0) && (strcmp($repair->is_driver_out_center, BOOL_TRUE) == 0)) {
                $driving_job_out = RepairTrait::getDrivingJob($repair_order->id, SelfDriveTypeEnum::SEND);
                $inspection_job_out = RepairTrait::getInspectionJob($repair_order->id, InspectionTypeEnum::MAINTENANCE_DC);
            }
        }

        $creditor = Creditor::find($repair_order->center_id);
        $repair_order->center_address = null;
        if ($creditor) {
            $address = ($creditor->address) ? $creditor->address : null;
            $mobile = ($creditor->mobile) ? $creditor->mobile : null;
            $repair_order->center_address = ($address) ? $address . '(' . $mobile . ')' : null;
            $repair_order->center_mail = ($creditor->email) ? $creditor->email : null;
        }

        $repair_line = RepairLine::where('repair_id', $repair_order->repair_id)
            ->get()->map(function ($item) {
                $item->check_text = $item->check ? __('check_distances.type_text_' . $item->check) : null;
                return $item;
            });

        $repair_order_line = RepairOrderLine::where('repair_order_id', $repair_order->id)
            ->get()->map(function ($item) {
                $item->check_text = $item->check ? __('check_distances.type_text_' . $item->check) : null;
                $item->date = ($item->created_at) ? date('Y-m-d', strtotime($item->created_at)) : null;
                $repair_list = RepairList::find($item->repair_list_id);
                $item->code_name = null;
                if ($repair_list) {
                    $item->code_name = $repair_list->code . ' ' . $repair_list->name;
                }
                return $item;
            });

        $user_file = null;
        $expense_file_media = $repair_order->getMedia('expense_files');
        $expense_files = get_medias_detail($expense_file_media);
        if (isset($expense_file_media[0]->custom_properties['created_by'])) {
            $user_id = $expense_file_media[0]->custom_properties['created_by'];
            $user = User::find($user_id);
            if ($user) {
                $user_file = $user->name;
            }
        }
        $yes_no_list = getYesNoList();
        $replacement_list = RepairTrait::getReplacementList($repair_order->id);
        $route_group = [
            'tab_repair_order' => route('admin.repair-orders.show', ['repair_order' => $repair_order]),
            'tab_condition' => route('admin.repair-order-conditions.show', ['repair_order_condition' => $repair_order]),
        ];
        $index_uri = 'admin.repair-orders.index';
        $page_title = __('lang.view') . __('repair_orders.page_title');
        return view('admin.repair-orders.form', [
            'd' => $repair_order,
            'page_title' => $page_title,
            'replacement_type_list' => $replacement_type_list,
            'check_list' => $check_list,
            'repair_type_list' => $repair_type_list,
            'service_center_list' => $service_center_list,
            'is_need_driver' => $is_need_driver,
            'repair_no_list' => $repair_no_list,
            'condotion_lt_rental' => $condotion_lt_rental,
            'car_data' => $car_data,
            'rental' => $rental,
            'center_list' => $center_list,
            'repair_lists' => $repair_lists,
            'edit' => true,
            'repair' => $repair,
            'repair_line' => $repair_line,
            'repair_order_line_list' => $repair_order_line,
            'have_expenses_list' => $have_expenses_list,
            'route_group' => $route_group,
            'view' => true,
            'index_uri' => $index_uri,
            'repair_documents_files' => $repair_documents_files,
            'expense_files' => $expense_files,
            'user_file' => $user_file,
            'yes_no_list' => $yes_no_list,
            'replacement_list' => $replacement_list,
            'driving_job_in' => $driving_job_in,
            'inspection_job_in' => $inspection_job_in,
            'driving_job_out' => $driving_job_out,
            'inspection_job_out' => $inspection_job_out,
            'mode' => MODE_VIEW
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'repair_no' => [
                'required',
            ],
            // 'check_distance' => [
            //     'required',
            // ],
            'center' => [
                'required',
            ],
            'is_driver_in_center' => [
                Rule::when($request->in_center == BOOL_FALSE, ['required'])
            ],
            'is_driver_out_center' => [
                Rule::when($request->out_center == BOOL_FALSE, ['required'])
            ],
            'replacement_date' => [
                Rule::when($request->is_replacement == BOOL_TRUE, ['required'])
            ],
            'replacement_type' => [
                Rule::when($request->is_replacement == BOOL_TRUE, ['required'])
            ],
            'replacement_place' => [
                Rule::when($request->is_replacement == BOOL_TRUE, ['required'])
            ],

        ], [], [
            'repair_no' => __('repairs.worksheet_no'),
            // 'check_distance' =>  __('repair_orders.check_distance'),
            'center' => __('repair_orders.center'),
            'is_driver_in_center' => __('repairs.is_driver_in_center'),
            'is_driver_out_center' => __('repairs.is_driver_out_center'),
            'replacement_date' => __('repairs.replacement_date'),
            'replacement_type' => __('repairs.replacement_type'),
            'replacement_place' => __('repairs.replacement_place'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $distance = CheckDistance::find($request->check_distance);
        $repair_order = RepairOrder::firstOrNew(['id' => $request->id]);
        $repair_order_count = RepairOrder::all()->count() + 1;
        $prefix = 'JO';
        if (!($repair_order->exists)) {
            $repair_order->worksheet_no = generateRecordNumber($prefix, $repair_order_count);
        }

        $repair_order->repair_id = $request->repair_no;
        if ($distance) {
            $repair_order->check_distance = $distance->distance;
        }
        $repair_order->expected_repair_date = $request->expected_date;
        $repair_order->repair_date = $request->completed_date;
        $repair_order->center_id = $request->center;
        $repair_order->remark = $request->remark;
        $repair_order->receive_repair_order_date = $request->receive_repair_order_date;
        $repair_order->receive_quotation = $request->receive_quotation;
        $repair_order->is_expenses = intval($request->is_expenses);
        $repair_order_status = $request->status;
        if (strcmp($request->status, RepairStatusEnum::WAIT_OPEN_REPAIR_ORDER) == 0) {
            $repair_order_status = RepairStatusEnum::PENDING_REPAIR;
        }
        if ((in_array($request->status, [RepairStatusEnum::IN_PROCESS, RepairStatusEnum::REJECT_QUOTATION])) && (strcmp($request->is_expenses, STATUS_ACTIVE) == 0)) {
            $repair_order_status = RepairStatusEnum::WAIT_APPROVE_QUOTATION;
        } elseif ((in_array($request->status, [RepairStatusEnum::REJECT_QUOTATION])) && (strcmp($request->is_expenses, STATUS_DEFAULT) == 0)) {
            $repair_order_status = RepairStatusEnum::IN_PROCESS;
        }
        $repair_order->status = $repair_order_status;
        $repair_order->save();

        //logApprove
        $approve_line_logs = [];
        $approve_line_management = new StepApproveManagement();
        $approve_return = $approve_line_management->logApprove(RepairOrder::class, $repair_order->id);
        $approve_line_logs = $approve_line_management->getHistoryLogs();
        if (sizeof($approve_line_logs) > 0) {
            $repair_order->status = $request->status;
            $repair_order->save();
        }

        $repair = Repair::find($request->repair_no);
        if ($repair) {
            $repair->status = $repair_order->status;
            $repair->in_center_date = ($request->center_date) ? $request->center_date : $request->in_center_date;
            if (in_array($request->in_center, [BOOL_TRUE, BOOL_FALSE])) {
                $repair->in_center = $request->in_center;
                $repair->is_driver_in_center = $request->is_driver_in_center;
            }
            if (in_array($request->out_center, [BOOL_TRUE, BOOL_FALSE])) {
                $repair->out_center = $request->out_center;
                $repair->out_center_date = $request->out_center_date;
                $repair->is_driver_out_center = $request->is_driver_out_center;
            }
            if (in_array($request->is_replacement, [BOOL_TRUE, BOOL_FALSE])) {
                $repair->is_replacement = $request->is_replacement;
                $repair->replacement_type = $request->replacement_type;
                $repair->replacement_date = $request->replacement_date;
                $repair->replacement_place = $request->replacement_place;
            }
            $repair->save();

            if (strcmp($repair->is_replacement, BOOL_TRUE) == 0) {
                $replacement_car = RepairTrait::createReplacementCar($repair_order->id, $repair);
            }
            if ((strcmp($repair->in_center, BOOL_FALSE) == 0) && (strcmp($repair->is_driver_in_center, BOOL_TRUE) == 0)) {
                $driving_job = RepairTrait::createDrivingJob($repair_order->id, SelfDriveTypeEnum::PICKUP, $repair->car_id);
                $inspection_job = RepairTrait::createInspectionJobs($repair_order->id, InspectionTypeEnum::MAINTENANCE_RC, TransferTypeEnum::IN, $repair->car_id, $repair->in_center_date);
            }
            if ((strcmp($repair->out_center, BOOL_FALSE) == 0) && (strcmp($repair->is_driver_out_center, BOOL_TRUE) == 0)) {
                $driving_job = RepairTrait::createDrivingJob($repair_order->id, SelfDriveTypeEnum::SEND, $repair->car_id);
                $inspection_job = RepairTrait::createInspectionJobs($repair_order->id, InspectionTypeEnum::MAINTENANCE_DC, TransferTypeEnum::OUT, $repair->car_id, $repair->out_center_date);
            }


            if (isset($request->replacements) && sizeof($request->replacements) > 0) {
                $user = Auth::user();
                foreach ($request->replacements as $key => $replacement) {
                    if (!$replacement['id']) {
                        $replacement_car = new ReplacementCar;
                        $replacement_car_count = DB::table('replacement_cars')->count() + 1;
                        $prefix = 'RC-';
                        $replacement_car->worksheet_no = generateRecordNumber($prefix, $replacement_car_count);
                        $replacement_car->replacement_type = $replacement['job_type_id'] ?? null;
                        $replacement_car->job_type = ReplacementJobTypeEnum::REPAIR;
                        $replacement_car->job_id = $repair->id;
                        $replacement_car->branch_id = $user ? $user->branch_id : null;
                        $replacement_car->main_car_id = $replacement['main_car_id'] ?? null;
                        $replacement_car->replacement_expect_date = $replacement['send_pickup_date'] ?? null;
                        $replacement_car->is_need_driver = $replacement['slide_id'] ? false : true;
                        $replacement_car->is_need_slide = $replacement['slide_id'] ? true : false;
                        $replacement_car->slide_id = $replacement['slide_id'] ?? null;
                        $replacement_car->is_cust_receive_replace = filter_var($replacement['is_at_tls'], FILTER_VALIDATE_BOOLEAN);
                        $replacement_car->replacement_expect_place = $replacement['send_pickup_place'] ?? null;
                        $replacement_car->customer_name = $request->contact;
                        $replacement_car->tel = $request->tel;
                        $replacement_car->save();

                        if ((!empty($request->replacement_files)) && (sizeof($request->replacement_files) > 0)) {
                            $all_replacement_files = $request->replacement_files;
                            if (isset($all_replacement_fixles[$key])) {
                                $replacement_car_files = $all_replacement_files[$key];
                                foreach ($replacement_car_files as $replacement_car_file) {
                                    if ($replacement_car_file) {
                                        $replacement_car->addMedia($replacement_car_file)->toMediaCollection('replacement_car_documents');
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (strcmp($repair_order->status, RepairStatusEnum::PENDING_REPAIR) == 0) {
            $repair_order_date_first = RepairOrderDate::where('repair_order_id', $repair_order->id)->where('status', STATUS_ACTIVE)->first();
            if (isset($repair_order_date_first)) {
                $repair_order_date = RepairOrderDate::find($repair_order_date_first->id);
            } else {
                $repair_order_date = new RepairOrderDate();
            }
            $repair_order_date->repair_order_id = $repair_order->id;
            $repair_order_date->center_date = $repair->in_center_date;
            $repair_order_date->save();

            if ($repair) {
                $date = Carbon::parse($repair->in_center_date);
                if ($date->isToday()) {
                    if ($repair_order) {
                        $repair_order->status = RepairStatusEnum::IN_PROCESS;
                        $repair_order->save();
                        $repair->status = RepairStatusEnum::IN_PROCESS;
                        $repair->save();
                    }
                }
            }
        }

        $step_repair_order = RepairOrder::find($request->id);
        if (!$step_repair_order) {
            $step_approve_management = new StepApproveManagement();
            $step_approve_management->createModelApproval(ConfigApproveTypeEnum::REPAIR_ORDER, RepairOrder::class, $repair_order->id);
        }

        if ($request->del_repair_order_line != null) {
            RepairOrderLine::whereIn('id', $request->del_repair_order_line)->delete();
        }
        if (!empty($request->repair_order_line)) {
            foreach ($request->repair_order_line as $request_repair_order_line) {
                if (isset($request_repair_order_line['id'])) {
                    $repair_order_line = RepairOrderLine::find($request_repair_order_line['id']);
                } else {
                    $repair_order_line = new RepairOrderLine();
                }
                if ($repair_order_line) {
                    $repair_order_line->repair_order_id = $repair_order->id;
                    $repair_order_line->repair_list_type = $request_repair_order_line['repair_type'];
                    $repair_order_line->repair_list_id = $request_repair_order_line['repair_list_id'];
                    $repair_order_line->check = $request_repair_order_line['check'];
                    $repair_order_line->price = $request_repair_order_line['price'];
                    $repair_order_line->amount = $request_repair_order_line['amount'];
                    $repair_order_line->discount = $request_repair_order_line['discount'];
                    $repair_order_line->vat = $request_repair_order_line['vat'];
                    $repair_order_line->total = $request_repair_order_line['total'];
                    $repair_order_line->remark = $request_repair_order_line['remark'];
                    $repair_order_line->save();
                }
            }
        }

        $user = Auth::user();
        if ($request->expense_files__pending_delete_ids) {
            $pending_delete_ids = $request->expense_files__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $repair_order->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('expense_files')) {
            foreach ($request->file('expense_files') as $file) {
                if ($file->isValid()) {
                    $repair_order->addMedia($file)
                        ->withCustomProperties(['created_by' => $user->id])
                        ->toMediaCollection('expense_files');
                }
            }
        }

        $quotation_count = Quotation::where('reference_type', RepairOrder::class)->where('reference_id', $repair_order->id)->count();
        if ($quotation_count <= 0) {
            $quotation_repiar = $this->createQuotationRepiar($repair_order->id);
        }

        $redirect_route = route($request->redirect_route);
        return $this->responseValidateSuccess($redirect_route);
    }

    public function createQuotationRepiar($repair_order_id)
    {
        $condition_group = ConditionQuotationTrait::getConditionGroup(ConditionGroupEnum::REPAIR_SERVICE);
        if (!$condition_group) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }

        $condition_repairs = ConditionQuotation::where('condition_group_id', $condition_group->id)
            ->orderBy('seq', 'asc')
            ->get()->map(function ($item) {
                $sub_query = ConditionQuotationChecklist::where('condition_quotations_id', $item->id)
                    ->orderBy('seq', 'asc')
                    ->get();
                $item->sub_condition_repairs = $sub_query;
                return $item;
            });
        if (sizeof($condition_repairs) > 0) {
            $quotation = new Quotation();
            $quotation->qt_type = 'DRAFT';
            $quotation->reference_type = RepairOrder::class;
            $quotation->reference_id = $repair_order_id;
            $quotation->save();

            foreach ($condition_repairs as $key => $item_condition_repair) {
                $quotation_form = new QuotationForm();
                $quotation_form->quotation_id = $quotation->id;
                $quotation_form->name = $item_condition_repair->name;
                $quotation_form->seq = $item_condition_repair->seq;
                $quotation_form->save();

                // save quotation checklist
                if (isset($item_condition_repair->sub_condition_repairs) && sizeof($item_condition_repair->sub_condition_repairs) > 0) {
                    foreach ($item_condition_repair->sub_condition_repairs as $index => $item_sub_condition) {
                        $quotation_form_checklist = new QuotationFormChecklist();
                        $quotation_form_checklist->quotation_form_id = $quotation_form->id;
                        $quotation_form_checklist->name = $item_sub_condition->name;
                        $quotation_form_checklist->seq = $item_sub_condition->seq;
                        $quotation_form_checklist->save();
                    }
                }
            }
        }
        return true;
    }

    public function updateStatus(Request $request)
    {
        if (strcmp($request->status, RepairStatusEnum::CANCEL) == 0) {
            $validator = Validator::make($request->all(), [
                'reason' => 'required_if:status,CANCEL'
            ], [
                'required_if' => 'กรุณากรอก :attribute'
            ], [
                'reason' => 'เหตุผลการยกเลิกใบสั่งซ่อม',
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }
        if (strcmp($request->status, RepairStatusEnum::EXPIRED) == 0) {
            // if (!$request->repair_date_new) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'กรุณากรอก' . __('repair_orders.repair_date_new'),
            //     ]);
            // }
            if (!$request->center_date_new) {
                return response()->json([
                    'success' => false,
                    'message' => 'กรุณากรอก' . __('repair_orders.center_date_new'),
                ]);
            }
        }

        $repair_order = RepairOrder::find($request->id);
        if (!$repair_order) {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found'),
            ]);
        }
        $repair = Repair::find($repair_order->repair_id);

        if (strcmp($request->status, RepairStatusEnum::CANCEL) == 0) {
            $repair_order->status = $request->status;
            $repair_order->reason = $request->reason;
            $repair_order->save();

            $repair->status = $repair_order->status;
            $repair->save();
        }

        if (strcmp($request->status, RepairStatusEnum::EXPIRED) == 0) {
            $repair_order_date_old = RepairOrderDate::where('repair_order_id', $repair_order->id)->where('status', STATUS_ACTIVE)->first();
            $repair_order_date_old->status = STATUS_DEFAULT;
            $repair_order_date_old->save();

            if ($request->center_date_new) {
                $repair_order_date = new RepairOrderDate();
                $repair_order_date->repair_order_id = $repair_order->id;
                $repair_order_date->center_date = $request->center_date_new;
                $repair_order_date->save();

                $repair_order->status = RepairStatusEnum::PENDING_REPAIR;
                $repair_order->save();

                $repair->repair_date = $request->repair_date_new;
                $repair->in_center_date = $repair_order_date->center_date;
                $repair->status = $repair_order->status;
                $repair->save();
            }
        }


        $redirect_route = route($request->redirect_route);
        return response()->json([
            'success' => 'ok',
            'message' => __('lang.delete_success'),
            'redirect' => $redirect_route
        ]);
    }

    public function getPdf($repair_order_id)
    {
        $repair_order = RepairOrder::find($repair_order_id);
        if (!$repair_order) {
            return abort(404);
        }

        $repair = Repair::find($repair_order->repair_id);
        if (!$repair) {
            return abort(404);
        }

        $car_id = $repair->car_id;
        if (!$car_id) {
            return abort(404);
        }
        $car = Car::find($car_id);
        if (!$car) {
            return abort(404);
        }
        $creditor = Creditor::find($repair_order->center_id);
        $expire_date = Carbon::parse($repair_order->created_at)->addDays(7);

        $data = [];
        $data['worksheet_name'] = __('repair_orders.page_title');
        $data['worksheet_no'] = $repair_order->worksheet_no;
        $data['datetime'] = $repair_order->created_at ? get_thai_date_format($repair_order->created_at, 'd/m/Y H:i') : '';
        $data['ref'] = $repair->worksheet_no;
        if ($creditor) {
            $data['center_name'] = ($creditor->name) ? $creditor->name : null;
            $data['center_address'] = ($creditor->address) ? $creditor->address : null;
            $data['center_tel'] = ($creditor->tel) ? $creditor->tel : null;
            $data['center_fax'] = ($creditor->fax) ? $creditor->fax : null;
        }
        $data['license_plate'] = $car->license_plate;
        $data['car_class'] = $car->carClass?->name;
        $data['mileage'] = $car->current_mileage;
        $data['car_color'] = $car->carColor?->name;
        $data['registered_date'] = $car->registered_date ? get_thai_date_format($car->registered_date, 'd/m/Y') : '';
        $data['chassis_no'] = $car->chassis_no;
        $data['engine_no'] = $car->engine_no;
        $data['expire_date'] = $expire_date ? get_thai_date_format($expire_date, 'd/m/Y') : '';
        $data['customer_name'] = $repair->contact;
        $data['tel'] = $repair->tel;
        $data['company_name'] = $repair->job?->customer_name;

        $repair_order_line = RepairOrderLine::where('repair_order_id', $repair_order->id)
            ->get()->map(function ($item) {
                $item->check_text = $item->check ? __('check_distances.type_text_' . $item->check) : null;
                $repair_list = RepairList::find($item->repair_list_id);
                $item->code_name = null;
                if ($repair_list) {
                    $item->code_name = $repair_list->code . ' ' . $repair_list->name;
                }
                return $item;
            });
        $data['repair_order_line'] = $repair_order_line;
        $data['insurance_company'] = $repair_order->remark;
        $condition_repair = [];
        $quotation = Quotation::where('reference_type', RepairOrder::class)->where('reference_id', $repair_order->id)->first();
        if ($quotation) {
            $condition_repair = QuotationForm::where('quotation_id', $quotation->id)->orderBy('seq', 'asc')
                ->get()->map(function ($item) {
                    $sub_query = QuotationFormChecklist::where('quotation_form_id', $item->id)
                        ->orderBy('seq', 'asc')
                        ->get();
                    $item->sub_condition_repair = $sub_query;
                    return $item;
                });
        }
        $data['condition_repair'] = $condition_repair;

        $pdf = PDF::loadView(
            'admin.repair-orders.pdf.section-1',
            [
                'data' => $data
            ]
        );
        return $pdf;
    }

    public function printPdf(Request $request)
    {
        $repair_order_id = $request->repair_order;
        $pdf = $this->getPdf($repair_order_id);
        return $pdf->stream();
    }

    public function sendMail(Request $request)
    {
        $repair_order_id = $request->repair_order_id;
        $repair_order = RepairOrder::find($repair_order_id);
        if (!$repair_order) {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found')
            ]);
        }
        $repair = Repair::find($repair_order->repair_id);

        $car_id = $repair->car_id;
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
        $creditor = Creditor::find($repair_order->center_id);

        $image = 'https://uat-smartcar.obs.ap-southeast-2.myhuaweicloud.com/logo-mail.png';
        if (App::environment('production')) {
            $image = 'https://production-smartcar.obs.ap-southeast-2.myhuaweicloud.com/logo-mail.png';
        }

        $mails = $request->tags;
        if (empty($mails)) {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found')
            ]);
        }

        $pdf = $this->getPdf($repair_order_id);

        $mail_data = [
            'license_plate' => ($car->license_plate) ? $car->license_plate : null,
            'center_name' => ($creditor->name) ? $creditor->name : null,
            'repair_type' => ($repair->repair_type) ? __('repairs.repair_type_' . $repair->repair_type) : null,
            'image' => $image,
        ];

        foreach ($mails as $mail) {
            Mail::to($mail)->send(new RepairOrderMail($mail_data, $pdf));
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function getDataRepair(Request $request)
    {
        $repair_id = $request->repair_id;
        $data = [];
        $repair_line = [];
        $car_data = [];
        $repair = Repair::find($repair_id);
        if ($repair) {
            $replacement_list = RepairTrait::getReplacementList($repair->id);
            $data['replacement_list'] = $replacement_list;
            $data['repair_type'] = ($repair->repair_type) ? __('repairs.repair_type_' . $repair->repair_type) : null;
            $data['repair_date'] = ($repair->repair_date) ? date('d/m/Y H:i', strtotime($repair->repair_date)) : null;
            $data['repair_create_by'] = ($repair->createdBy) ? $repair->createdBy->name : null;
            $data['mileage'] = ($repair->mileage) ? $repair->mileage : null;
            $data['place'] = ($repair->place) ? $repair->place : null;
            $data['remark'] = ($repair->remark) ? $repair->remark : null;
            $data['in_center'] = $repair->in_center;
            $data['in_center_date'] = ($repair->in_center_date) ? $repair->in_center_date : null;
            $data['is_driver_in_center'] = $repair->is_driver_in_center;
            $data['out_center'] = $repair->out_center;
            $data['out_center_date'] = ($repair->out_center_date) ? $repair->out_center_date : null;
            $data['is_driver_out_center'] = $repair->is_driver_out_center;
            $data['is_replacement'] = $repair->is_replacement;
            if (strcmp($repair->is_replacement, STATUS_ACTIVE) == 0) {
                $data['replacement_date'] = ($repair->replacement_date) ? $repair->replacement_date : null;
                $data['replacement_type'] = ($repair->replacement_type) ? $repair->replacement_type : null;
                $data['replacement_place'] = ($repair->replacement_place) ? $repair->replacement_place : null;
            }

            $repair_line = RepairLine::where('repair_id', $repair->id)
                ->get()->map(function ($item) {
                    $item->check_text = $item->check ? __('check_distances.type_text_' . $item->check) : null;
                    return $item;
                });

            $car_data = RepairTrait::getDataCar($repair->car_id);
            $status_class = ($car_data->status) ? __('cars.class_' . $car_data->status) : null;
            $status_text = ($car_data->status) ? __('cars.status_' . $car_data->status) : null;

            $repair_documents_medias = $repair->getMedia('repair_documents');
            $repair_documents_files = get_medias_detail($repair_documents_medias);
            $repair_documents_files = collect($repair_documents_files)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $data['repair_documents_files'] = $repair_documents_files;
        }

        return [
            'success' => true,
            'data' => $data,
            'repair_line' => $repair_line,
            'car_data' => $car_data,
            'status_class' => $status_class,
            'status_text' => $status_text,
        ];
    }

    public function getDataCenter(Request $request)
    {
        $center_id = $request->center_id;
        $data = [];
        $creditor = Creditor::find($center_id);
        if ($creditor) {
            $address = ($creditor->address) ? $creditor->address : null;
            $mobile = ($creditor->mobile) ? $creditor->mobile : null;
            $data['address'] = ($address) ? $address . '(' . $mobile . ')' : null;
        }

        return [
            'success' => true,
            'data' => $data,
        ];
    }

    public function selectRepair(Request $request)
    {
        $repair_list_ids = $request->parent_id;
        $data = [];
        $data = RepairList::select('id', 'code', 'name', 'price')
            ->when($repair_list_ids, function ($query) use ($repair_list_ids) {
                return $query->whereNotIn('id', $repair_list_ids);
            })
            ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('code', 'like', '%' . $request->s . '%');
                    $query->orWhere('name', 'like', '%' . $request->s . '%');
                }
            })
            ->limit(30)
            ->get()->map(function ($item) {
                $item->id = $item->id;
                $item->text = $item->code . '-' . $item->name;
                return $item;
            });
        return response()->json($data);
    }

    public function selectDistance(Request $request)
    {
        $car_id = $request->parent_id_2;
        $mileage = $request->parent_id;
        $car = Car::find($car_id);
        $data = [];
        if ($car && $car->car_class_id) {
            $data = CheckDistance::select('id', 'distance')
                ->where('car_class_id', $car->car_class_id)
                ->where('distance', '>=', $mileage)
                ->where(function ($query) use ($request) {
                    if (!empty($request->s)) {
                        $query->where('distance', 'like', '%' . $request->s . '%');
                    }
                })
                ->orderBy('distance')
                ->get()->map(function ($item) {
                    $item->id = $item->id;
                    $item->text = $item->distance;
                    return $item;
                });
        }
        return response()->json($data);
    }

    public function getDefaultDistanceLine(Request $request)
    {
        $distance_id = $request->distance_id;
        $distance_line = [];
        $distance_line = CheckDistanceLine::where('check_distance_id', $distance_id)
            ->get()->map(function ($item) {
                $item->check_text = $item->check ? __('check_distances.type_text_' . $item->check) : null;
                $item->date = ($item->created_at) ? date('Y-m-d', strtotime($item->created_at)) : null;
                $repair_list = RepairList::find($item->repair_list_id);
                $item->code_name = null;
                if ($repair_list) {
                    $item->code_name = $repair_list->code . ' ' . $repair_list->name;
                }
                return $item;
            });

        return [
            'success' => true,
            'distance_line' => $distance_line,
        ];
    }

    public function printSummaryPdf(Request $request)
    {
        $worksheet_no = $request->worksheet_no;
        $repair_type = $request->repair_type;
        $license_plate = $request->license_plate;
        $contact = $request->contact;
        $order_worksheet_no = $request->order_worksheet_no;
        $status = $request->status;
        $center = $request->center;
        $alert_date = $request->alert_date;
        $repair_order_date = $request->repair_order_date;
        $district_center = $request->district_center;
        $open_by = $request->open_by;

        $data = RepairOrder::leftJoin('repairs', 'repairs.id', '=', 'repair_orders.repair_id')
            ->leftJoin('cars', 'cars.id', '=', 'repairs.car_id')
            ->leftJoin('creditors', 'creditors.id', '=', 'repair_orders.center_id')
            ->leftJoin('provinces', 'provinces.id', '=', 'creditors.province_id')
            ->leftJoin('geographies', 'geographies.id', '=', 'provinces.geography_id')
            ->leftJoin('users', 'users.id', '=', 'repair_orders.created_by')
            ->select(
                'repair_orders.id',
                'repair_orders.created_at',
                'users.name as user_name',
                'creditors.name as center',
                'provinces.geography_id',
                'cars.license_plate',
                'repair_orders.status'
            )
            ->when($order_worksheet_no, function ($query) use ($order_worksheet_no) {
                $query->where('repair_orders.id', $order_worksheet_no);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('repair_orders.status', $status);
            })
            ->when($center, function ($query) use ($center) {
                $query->where('repair_orders.center_id', $center);
            })
            ->when($worksheet_no, function ($query) use ($worksheet_no) {
                $query->where('repair_orders.repair_id', $worksheet_no);
            })
            ->when($license_plate, function ($query) use ($license_plate) {
                $query->where('repairs.car_id', $license_plate);
            })
            ->when($repair_type, function ($query) use ($repair_type) {
                $query->where('repairs.repair_type', $repair_type);
            })
            ->when($contact, function ($query) use ($contact) {
                $query->where('repairs.contact', $contact);
            })
            ->when($alert_date, function ($query) use ($alert_date) {
                $query->whereDate('repairs.repair_date', $alert_date);
            })
            ->when($district_center, function ($query) use ($district_center) {
                $query->where('provinces.geography_id', $district_center);
            })
            ->when($repair_order_date, function ($query) use ($repair_order_date) {
                $query->whereDate('repair_orders.created_at', $repair_order_date);
            })
            ->where(function ($q) use ($open_by) {
                if (($open_by)) {
                    $q->where('repairs.open_by', RepairEnum::CALL_CENTER);
                }
            })
            ->orderBy('geographies.id')
            ->get();
        $data->map(function ($item) {
            $sub_line = RepairOrderLine::where('repair_order_id', $item->id)
                ->get()->map(function ($item_line) {
                    $repair_list = RepairList::find($item_line->repair_list_id);
                    $item_line->code_name = null;
                    if ($repair_list) {
                        $item_line->code_name = $repair_list->code . ' ' . $repair_list->name;
                    }
                    return $item_line;
                });
            $item->sub_line = $sub_line;
            return $item;
        });

        $pdf = PDF::loadView(
            'admin.repair-orders.pdf.summary.pdf',
            [
                'data' => $data
            ]
        );
        return $pdf->stream();
    }

    public function printDailySummaryPdf(Request $request)
    {
        $worksheet_no = $request->worksheet_no;
        $repair_type = $request->repair_type;
        $license_plate = $request->license_plate;
        $contact = $request->contact;
        $order_worksheet_no = $request->order_worksheet_no;
        $status = $request->status;
        $center = $request->center;
        $alert_date = $request->alert_date;
        $repair_order_date = $request->repair_order_date;
        $district_center = $request->district_center;
        $open_by = $request->open_by;

        $data = DB::table('geographies')->select('id', 'name')
            ->whereNotIn('id', ['7'])
            ->orderBy('id')->get();
        $data->map(function ($item) use (
            $worksheet_no,
            $repair_type,
            $license_plate,
            $contact,
            $order_worksheet_no,
            $status,
            $center,
            $alert_date,
            $repair_order_date,
            $district_center,
            $open_by
        ) {
            $sub_group_center = RepairOrder::leftJoin('repairs', 'repairs.id', '=', 'repair_orders.repair_id')
                ->leftJoin('cars', 'cars.id', '=', 'repairs.car_id')
                ->leftJoin('creditors', 'creditors.id', '=', 'repair_orders.center_id')
                ->leftJoin('provinces', 'provinces.id', '=', 'creditors.province_id')
                ->leftJoin('geographies', 'geographies.id', '=', 'provinces.geography_id')
                ->select(
                    'repair_orders.id',
                    'repair_orders.created_at',
                    'creditors.name as center',
                    'provinces.geography_id',
                    'cars.license_plate',
                    'repair_orders.status',
                    'repairs.open_by',
                )
                ->where('provinces.geography_id', $item->id)
                ->when($order_worksheet_no, function ($query) use ($order_worksheet_no) {
                    $query->where('repair_orders.id', $order_worksheet_no);
                })
                ->when($status, function ($query) use ($status) {
                    $query->where('repair_orders.status', $status);
                })
                ->when($center, function ($query) use ($center) {
                    $query->where('repair_orders.center_id', $center);
                })
                ->when($worksheet_no, function ($query) use ($worksheet_no) {
                    $query->where('repair_orders.repair_id', $worksheet_no);
                })
                ->when($license_plate, function ($query) use ($license_plate) {
                    $query->where('repairs.car_id', $license_plate);
                })
                ->when($repair_type, function ($query) use ($repair_type) {
                    $query->where('repairs.repair_type', $repair_type);
                })
                ->when($contact, function ($query) use ($contact) {
                    $query->where('repairs.contact', $contact);
                })
                ->when($alert_date, function ($query) use ($alert_date) {
                    $query->whereDate('repairs.repair_date', $alert_date);
                })
                ->when($district_center, function ($query) use ($district_center) {
                    $query->where('provinces.geography_id', $district_center);
                })
                ->when($repair_order_date, function ($query) use ($repair_order_date) {
                    $query->whereDate('repair_orders.created_at', $repair_order_date);
                })
                ->where(function ($q) use ($open_by) {
                    if (($open_by)) {
                        $q->where('repairs.open_by', RepairEnum::CALL_CENTER);
                    }
                })
                ->orderBy('repairs.created_at')
                ->get();
            $item->sum_by_tls = 0;
            $item->sum_by_call_center = 0;
            $sub_group_center->map(function ($sub_item) use ($item) {
                if (strcmp($sub_item->open_by, RepairEnum::REPAIR_DEPARTMENT) == 0) {
                    $item->sum_by_tls += 1;
                } elseif (strcmp($sub_item->open_by, RepairEnum::CALL_CENTER) == 0) {
                    $item->sum_by_call_center += 1;
                }
                return $item;
            });
            $item->sub_group = array();
            $key = 0;
            foreach ($sub_group_center as $index => $sub_item) {
                if ($sub_item->created_at->format('Y-m-d') == $sub_group_center[$index]['created_at']->format('Y-m-d')) {
                    $item->sub_group[$sub_item->created_at->format('Y-m-d')][$key] =
                        array(
                            "license_plate" => $sub_item->license_plate,
                            "center" => $sub_item->center,
                            "created_at" => $sub_item->created_at->format('Y-m-d'),
                            'id' => $sub_item->id,
                        );
                }
                $key++;
            }
            $item->sum_group = count($sub_group_center);

            return $item;
        });

        $pdf = PDF::loadView(
            'admin.repair-orders.pdf.daily-summary.pdf',
            [
                'data' => $data
            ]
        );
        return $pdf->stream();
    }

    public function getPriceRepair(Request $request)
    {
        $id = $request->id;
        $price = 0;
        $data = RepairList::select('price', 'id')
            ->where('id', $id)
            ->first();
        $price = $data->price;
        return [
            'success' => true,
            'price' => $price,
        ];
    }
}
