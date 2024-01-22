<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\RepairEnum;
use App\Enums\CheckDistanceTypeEnum;
use App\Enums\InspectionTypeEnum;
use App\Enums\RepairStatusEnum;
use App\Enums\SelfDriveTypeEnum;
use App\Models\RepairOrder;
use App\Models\Repair;
use App\Models\Car;
use App\Models\RepairLine;
use App\Models\Creditor;
use App\Models\DrivingJob;
use App\Models\InspectionJob;
use App\Models\RepairList;
use App\Models\RepairOrderLine;
use App\Models\User;
use App\Traits\RepairTrait;
use App\Traits\ReplacementCarTrait;


class CallCenterRepairOrderController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CallCenterRepairOrder);
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
            ->where('repairs.open_by', RepairEnum::CALL_CENTER)
            ->orderBy('repair_orders.created_at', 'desc')
            ->paginate(PER_PAGE);

        $worksheet_no_list = RepairOrder::select('repairs.id', 'repairs.worksheet_no as name')
            ->leftJoin('repairs', 'repairs.id', '=', 'repair_orders.repair_id')
            ->where('repairs.open_by', RepairEnum::CALL_CENTER)
            ->orderBy('repairs.worksheet_no')->get();
        $repair_type_list = RepairTrait::getRepairType();
        $license_plate_list = Car::select('id', 'license_plate as name')->get();
        $contact_list = RepairOrder::select('repairs.contact as id', 'repairs.contact as name')
            ->leftJoin('repairs', 'repairs.id', '=', 'repair_orders.repair_id')
            ->where('repairs.open_by', RepairEnum::CALL_CENTER)
            ->orderBy('repairs.contact')->distinct()->get();
        $order_worksheet_no_list = RepairOrder::select('repair_orders.id', 'repair_orders.worksheet_no as name')
            ->leftJoin('repairs', 'repairs.id', '=', 'repair_orders.repair_id')
            ->where('repairs.open_by', RepairEnum::CALL_CENTER)
            ->orderBy('repair_orders.worksheet_no')->get();
        $status_list = RepairTrait::getStatus();
        $center_list = RepairOrder::leftJoin('creditors', 'creditors.id', '=', 'repair_orders.center_id')
            ->leftJoin('repairs', 'repairs.id', '=', 'repair_orders.repair_id')
            ->select('creditors.id', 'creditors.name')
            ->where('repairs.open_by', RepairEnum::CALL_CENTER)
            ->orderBy('creditors.name')->distinct()->get();
        $district_center_list = RepairTrait::getDistrict();

        $create_uri = route('admin.call-center-repair-orders.create');
        $edit_uri = 'admin.call-center-repair-orders.edit';
        $view_uri = 'admin.call-center-repair-orders.show';
        $param = 'call_center_repair_order';
        $view_permission = Actions::View . '_' . Resources::CallCenterRepairOrder;
        $manage_permission = Actions::Manage . '_' . Resources::CallCenterRepairOrder;
        $open_by = true;
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
        $this->authorize(Actions::Manage . '_' . Resources::CallCenterRepairOrder);
        $d = new RepairOrder();
        $d->status = RepairStatusEnum::WAIT_OPEN_REPAIR_ORDER;
        $replacement_type_list = ReplacementCarTrait::getReplacementTypeList();
        $yes_no_list = getYesNoList();
        $check_list = $this->getCheckList();
        $repair_type_list = RepairTrait::getRepairType();
        $service_center_list = RepairTrait::getServiceCenter();
        $is_need_driver = RepairTrait::getIsNeedDriver();
        $repair_ids = RepairOrder::pluck('repair_id')->toArray();
        $repair_no_list = Repair::select('id', 'worksheet_no as name')
            ->where('open_by', RepairEnum::CALL_CENTER)
            ->whereNotIn('id', $repair_ids)->get();
        $center_list = RepairTrait::getCenterList();
        $repair_lists = RepairTrait::getRepairListId();
        $car_data = new Car;
        $rental = 0;
        $index_uri = 'admin.call-center-repair-orders.index';
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
            'yes_no_list' => $yes_no_list,
            'replacement_list' => [],
            'driving_job_in' => null,
            'inspection_job_in' => null,
            'driving_job_out' => null,
            'inspection_job_out' => null,
            'mode' => MODE_CREATE
        ]);
    }

    public function edit(RepairOrder $call_center_repair_order)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CallCenterRepairOrder);
        $replacement_type_list = ReplacementCarTrait::getReplacementTypeList();
        $yes_no_list = getYesNoList();
        $check_list = $this->getCheckList();
        $repair_type_list = RepairTrait::getRepairType();
        $service_center_list = RepairTrait::getServiceCenter();
        $is_need_driver = RepairTrait::getIsNeedDriver();
        $repair_no_list = Repair::select('id', 'worksheet_no as name')->where('open_by', RepairEnum::CALL_CENTER)->get();
        $center_list = RepairTrait::getCenterList();
        $repair_lists = RepairTrait::getRepairListId();
        $have_expenses_list = RepairTrait::getHaveExpensesList();
        $car_data = new Car;
        $rental = 0;
        $call_center_repair_order->car_license = null;
        $repair = Repair::find($call_center_repair_order->repair_id);
        $replacement_list = RepairTrait::getReplacementList($repair?->id);
        $condotion_lt_rental = [];
        $driving_job_in = null;
        $inspection_job_in = null;
        $driving_job_out = null;
        $inspection_job_out = null;

        if ($repair) {
            $call_center_repair_order->car_id = $repair->car_id;
            $call_center_repair_order->contact = $repair->contact;
            $call_center_repair_order->tel = $repair->tel;
            $condotion_lt_rental =  RepairTrait::getConditionQuotation($repair);
            $car_data = RepairTrait::getDataCar($repair->car_id);
            if ($car_data) {
                $rental = $car_data->rental;
                if ($car_data->license_plate) {
                    $call_center_repair_order->car_license = $car_data->license_plate;
                } else if ($car_data->engine_no) {
                    $call_center_repair_order->car_license = __('inspection_cars.engine_no') . ' ' . $car_data->engine_no;
                } else if ($car_data->chassis_no) {
                    $call_center_repair_order->car_license = __('inspection_cars.chassis_no') . ' ' . $car_data->chassis_no;
                }
            }
            $call_center_repair_order->is_replacement = $repair->is_replacement;
            $call_center_repair_order->replacement_date = $repair->replacement_date;
            $call_center_repair_order->replacement_type = $repair->replacement_type;
            $call_center_repair_order->replacement_place = $repair->replacement_place;
            $call_center_repair_order->in_center = $repair->in_center;
            $call_center_repair_order->in_center_date = $repair->in_center_date;
            $call_center_repair_order->is_driver_in_center = $repair->is_driver_in_center;
            $call_center_repair_order->out_center = $repair->out_center;
            $call_center_repair_order->out_center_date = $repair->out_center_date;
            $call_center_repair_order->is_driver_out_center = $repair->is_driver_out_center;

            $repair_documents_files = $repair->getMedia('repair_documents');
            $repair_documents_files = get_medias_detail($repair_documents_files);

            if ((strcmp($repair->in_center, BOOL_FALSE) == 0) && (strcmp($repair->is_driver_in_center, BOOL_TRUE) == 0)) {
                $driving_job_in = RepairTrait::getDrivingJob($call_center_repair_order->id, SelfDriveTypeEnum::PICKUP);
                $inspection_job_in = RepairTrait::getInspectionJob($call_center_repair_order->id, InspectionTypeEnum::MAINTENANCE_RC);
            }
            if ((strcmp($repair->out_center, BOOL_FALSE) == 0) && (strcmp($repair->is_driver_out_center, BOOL_TRUE) == 0)) {
                $driving_job_out = RepairTrait::getDrivingJob($call_center_repair_order->id, SelfDriveTypeEnum::SEND);
                $inspection_job_out = RepairTrait::getInspectionJob($call_center_repair_order->id, InspectionTypeEnum::MAINTENANCE_DC);
            }
        }

        $creditor = Creditor::find($call_center_repair_order->center_id);
        $call_center_repair_order->center_address = null;
        if ($creditor) {
            $address =  ($creditor->address) ? $creditor->address : null;
            $mobile =  ($creditor->mobile) ? $creditor->mobile : null;
            $call_center_repair_order->center_address = ($address) ? $address . '(' . $mobile . ')' : null;
        }

        $repair_line = RepairLine::where('repair_id', $call_center_repair_order->repair_id)
            ->get()->map(function ($item) {
                $item->check_text = $item->check ? __('check_distances.type_text_' . $item->check) : null;
                return $item;
            });

        $repair_order_line = RepairOrderLine::where('repair_order_id', $call_center_repair_order->id)
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
        $expense_file_media = $call_center_repair_order->getMedia('expense_files');
        $expense_files = get_medias_detail($expense_file_media);
        if (isset($expense_file_media[0]->custom_properties['created_by'])) {
            $user_id = $expense_file_media[0]->custom_properties['created_by'];
            $user = User::find($user_id);
            if ($user) {
                $user_file = $user->name;
            }
        }

        $route_group = [
            'tab_repair_order' => route('admin.call-center-repair-orders.edit', ['call_center_repair_order' => $call_center_repair_order]),
            'tab_condition' => route('admin.repair-order-conditions.edit', ['repair_order_condition' => $call_center_repair_order]),
        ];
        $index_uri = 'admin.call-center-repair-orders.index';
        $page_title =  __('lang.edit') . __('repair_orders.page_title');
        return view('admin.repair-orders.form', [
            'd' => $call_center_repair_order,
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

    public function show(RepairOrder $call_center_repair_order)
    {
        $this->authorize(Actions::View . '_' . Resources::CallCenterRepairOrder);
        $replacement_type_list = ReplacementCarTrait::getReplacementTypeList();
        $check_list = $this->getCheckList();
        $repair_type_list = RepairTrait::getRepairType();
        $service_center_list = RepairTrait::getServiceCenter();
        $is_need_driver = RepairTrait::getIsNeedDriver();
        $repair_no_list = Repair::select('id', 'worksheet_no as name')->where('open_by', RepairEnum::CALL_CENTER)->get();
        $center_list = RepairTrait::getCenterList();
        $repair_lists = RepairTrait::getRepairListId();
        $have_expenses_list = RepairTrait::getHaveExpensesList();
        $car_data = new Car;
        $rental = 0;
        $call_center_repair_order->car_license = null;
        $repair = Repair::find($call_center_repair_order->repair_id);
        $condotion_lt_rental = [];
        $yes_no_list = getYesNoList();
        $driving_job_in = null;
        $inspection_job_in = null;
        $driving_job_out = null;
        $inspection_job_out = null;

        $replacement_list = RepairTrait::getReplacementList($repair?->id);
        if ($repair) {
            $call_center_repair_order->car_id = $repair->car_id;
            $call_center_repair_order->contact = $repair->contact;
            $call_center_repair_order->tel = $repair->tel;
            $condotion_lt_rental =  RepairTrait::getConditionQuotation($repair);
            $car_data = RepairTrait::getDataCar($repair->car_id);
            if ($car_data) {
                $rental = $car_data->rental;
                if ($car_data->license_plate) {
                    $call_center_repair_order->car_license = $car_data->license_plate;
                } else if ($car_data->engine_no) {
                    $call_center_repair_order->car_license = __('inspection_cars.engine_no') . ' ' . $car_data->engine_no;
                } else if ($car_data->chassis_no) {
                    $call_center_repair_order->car_license = __('inspection_cars.chassis_no') . ' ' . $car_data->chassis_no;
                }
            }
            $call_center_repair_order->is_replacement = $repair->is_replacement;
            $call_center_repair_order->replacement_date = $repair->replacement_date;
            $call_center_repair_order->replacement_type = $repair->replacement_type;
            $call_center_repair_order->replacement_place = $repair->replacement_place;
            $call_center_repair_order->in_center = $repair->in_center;
            $call_center_repair_order->in_center_date = $repair->in_center_date;
            $call_center_repair_order->is_driver_in_center = $repair->is_driver_in_center;
            $call_center_repair_order->out_center = $repair->out_center;
            $call_center_repair_order->out_center_date = $repair->out_center_date;
            $call_center_repair_order->is_driver_out_center = $repair->is_driver_out_center;

            $repair_documents_files = $repair->getMedia('repair_documents');
            $repair_documents_files = get_medias_detail($repair_documents_files);

            if ((strcmp($call_center_repair_order->in_center, BOOL_FALSE) == 0) && (strcmp($call_center_repair_order->is_driver_in_center, BOOL_TRUE) == 0)) {
                $driving_job_in = RepairTrait::getDrivingJob($call_center_repair_order->id, SelfDriveTypeEnum::PICKUP);
                $inspection_job_in = RepairTrait::getInspectionJob($call_center_repair_order->id, InspectionTypeEnum::MAINTENANCE_RC);
            }
            if ((strcmp($call_center_repair_order->out_center, BOOL_FALSE) == 0) && (strcmp($call_center_repair_order->is_driver_out_center, BOOL_TRUE) == 0)) {
                $driving_job_out = RepairTrait::getDrivingJob($call_center_repair_order->id, SelfDriveTypeEnum::SEND);
                $inspection_job_out = RepairTrait::getInspectionJob($call_center_repair_order->id, InspectionTypeEnum::MAINTENANCE_DC);
            }
        }

        $creditor = Creditor::find($call_center_repair_order->center_id);
        $call_center_repair_order->center_address = null;
        if ($creditor) {
            $address =  ($creditor->address) ? $creditor->address : null;
            $mobile =  ($creditor->mobile) ? $creditor->mobile : null;
            $call_center_repair_order->center_address = ($address) ? $address . '(' . $mobile . ')' : null;
        }

        $repair_line = RepairLine::where('repair_id', $call_center_repair_order->repair_id)
            ->get()->map(function ($item) {
                $item->check_text = $item->check ? __('check_distances.type_text_' . $item->check) : null;
                return $item;
            });

        $repair_order_line = RepairOrderLine::where('repair_order_id', $call_center_repair_order->id)
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
        $expense_file_media = $call_center_repair_order->getMedia('expense_files');
        $expense_files = get_medias_detail($expense_file_media);
        if (isset($expense_file_media[0]->custom_properties['created_by'])) {
            $user_id = $expense_file_media[0]->custom_properties['created_by'];
            $user = User::find($user_id);
            if ($user) {
                $user_file = $user->name;
            }
        }

        $route_group = [
            'tab_repair_order' => route('admin.call-center-repair-orders.show', ['call_center_repair_order' => $call_center_repair_order]),
            'tab_condition' => route('admin.repair-order-conditions.show', ['repair_order_condition' => $call_center_repair_order]),
        ];
        $index_uri = 'admin.call-center-repair-orders.index';
        $page_title =  __('lang.view') . __('repair_orders.page_title');
        return view('admin.repair-orders.form', [
            'd' => $call_center_repair_order,
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
            'user_file' => $user_file,
            'expense_files' => $expense_files,
            'yes_no_list' => $yes_no_list,
            'replacement_list' => $replacement_list,
            'driving_job_in' => $driving_job_in,
            'inspection_job_in' => $inspection_job_in,
            'driving_job_out' => $driving_job_out,
            'inspection_job_out' => $inspection_job_out,
            'mode' => MODE_VIEW
        ]);
    }

    public function getCheckList()
    {
        return collect([
            (object) [
                'id' => CheckDistanceTypeEnum::CHECK,
                'name' => __('check_distances.type_text_' . CheckDistanceTypeEnum::CHECK),
                'value' => CheckDistanceTypeEnum::CHECK,
            ],
        ]);
    }
}
