<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\InspectionTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\RepairEnum;
use App\Enums\SelfDriveTypeEnum;
use App\Models\Repair;
use App\Models\Car;
use App\Models\DrivingJob;
use App\Models\InspectionJob;
use App\Models\RepairLine;
use App\Models\RepairOrder;
use App\Models\User;
use App\Traits\RepairTrait;
use App\Traits\ReplacementCarTrait;
use DateTime;

class CallCenterRepairController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CallCenterRepair);
        $worksheet_no = $request->worksheet_no;
        $repair_type = $request->repair_type;
        $license_plate = $request->license_plate;
        $contact = $request->contact;
        $order_worksheet_no = $request->order_worksheet_no;
        $status = $request->status;
        $alert_date = $request->alert_date;
        $list = Repair::leftJoin('cars', 'cars.id', '=', 'repairs.car_id')
            ->leftJoin('repair_orders', 'repair_orders.repair_id', '=', 'repairs.id')
            ->select(
                'repairs.id',
                'repairs.status',
                'repairs.worksheet_no',
                'repairs.repair_type',
                'repairs.repair_date',
                'repairs.contact',
                'repairs.in_center_date',
                'cars.license_plate',
                'repair_orders.worksheet_no as order_worksheet_no',
                'repair_orders.expected_repair_date',
                'repair_orders.repair_date as completed_date',
            )
            ->where('repairs.open_by', RepairEnum::CALL_CENTER)
            ->search($request)
            ->when($license_plate, function ($query) use ($license_plate) {
                return $query->where('repairs.car_id', $license_plate);
            })
            ->when($order_worksheet_no, function ($query) use ($order_worksheet_no) {
                return $query->where('repair_orders.id', $order_worksheet_no);
            })
            ->orderBy('repairs.created_at', 'desc')
            ->paginate(PER_PAGE);

        $worksheet_no_list = Repair::select('id', 'worksheet_no as name')
            ->where('open_by', RepairEnum::CALL_CENTER)
            ->orderBy('worksheet_no')->get();
        $repair_type_list = RepairTrait::getRepairType();
        $license_plate_list = Car::select('id', 'license_plate as name')->get();
        $contact_list = Repair::select('contact as id', 'contact as name')
            ->where('open_by', RepairEnum::CALL_CENTER)
            ->orderBy('contact')->distinct()->get();
        $order_worksheet_no_list = RepairOrder::select('id', 'worksheet_no as name')->orderBy('worksheet_no')->get();
        $status_list = RepairTrait::getStatus();

        $create_uri = route('admin.call-center-repairs.create');
        $edit_uri = 'admin.call-center-repairs.edit';
        $view_uri = 'admin.call-center-repairs.show';
        $param = 'call_center_repair';
        $view_permission = Actions::View . '_' . Resources::CallCenterRepair;
        $manage_permission = Actions::Manage . '_' . Resources::CallCenterRepair;
        $page_title = __('repairs.page_title');
        return view('admin.repairs.index', [
            'list' => $list,
            'page_title' => $page_title,
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
            'status_list' => $status_list,
            'alert_date' => $alert_date,
            'create_uri' => $create_uri,
            'edit_uri' => $edit_uri,
            'view_uri' => $view_uri,
            'param' => $param,
            'view_permission' => $view_permission,
            'manage_permission' => $manage_permission,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::CallCenterRepair);
        $d = new Repair();
        $d->open_by = RepairEnum::CALL_CENTER;
        $d->in_center = BOOL_TRUE;
        $d->out_center = BOOL_TRUE;
        $date = new DateTime();
        $d->repair_date = $date->format('Y-m-d H:i:s');
        $replacement_type_list = ReplacementCarTrait::getReplacementTypeList();
        $check_list = RepairTrait::getCheckList();
        $repair_type_list = RepairTrait::getRepairType();
        $informer_type_list = RepairTrait::getInformer();
        $service_center_list = RepairTrait::getServiceCenter();
        $is_need_driver = RepairTrait::getIsNeedDriver();
        $yes_no_list = getYesNoList();
        $informers = User::select('id', 'name')->orderBy('name')->get();
        $car_license = null;
        $car_data = new Car;
        $rental = 0;
        $index_uri = 'admin.call-center-repairs.index';
        $page_title = __('lang.create') . __('repairs.page_title');
        return view('admin.repairs.form', [
            'd' => $d,
            'page_title' => $page_title,
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

    public function edit(Repair $call_center_repair)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CallCenterRepair);
        $replacement_type_list = ReplacementCarTrait::getReplacementTypeList();
        $check_list = RepairTrait::getCheckList();
        $repair_type_list = RepairTrait::getRepairType();
        $informer_type_list = RepairTrait::getInformer();
        $service_center_list = RepairTrait::getServiceCenter();
        $is_need_driver = RepairTrait::getIsNeedDriver();
        $informers = User::select('id', 'name')->orderBy('name')->get();
        $car = Car::find($call_center_repair->car_id);
        $car_license = null;
        $driving_job_in = null;
        $inspection_job_in = null;
        $driving_job_out = null;
        $inspection_job_out = null;
        if ($car) {
            if ($car->license_plate) {
                $car_license = $car->license_plate;
            } else if ($car->engine_no) {
                $car_license = __('inspection_cars.engine_no') . ' ' . $car->engine_no;
            } else if ($car->chassis_no) {
                $car_license = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
            }
        }
        $car_data = RepairTrait::getDataCar($call_center_repair->car_id);
        $rental = $car_data->rental;
        $check_repair_list = RepairLine::where('repair_id', $call_center_repair->id)
            ->get()->map(function ($item) {
                $item->check_text = __('check_distances.type_text_' . $item->check);
                return $item;
            });

        $repair_order_id = RepairOrder::where('repair_id', $call_center_repair->id)->first()?->id;
        if ($repair_order_id) {
            if ((strcmp($call_center_repair->in_center, BOOL_FALSE) == 0) && (strcmp($call_center_repair->is_driver_in_center, BOOL_TRUE) == 0)) {
                $driving_job_in = RepairTrait::getDrivingJob($repair_order_id, SelfDriveTypeEnum::PICKUP);
                $inspection_job_in = RepairTrait::getInspectionJob($repair_order_id, InspectionTypeEnum::MAINTENANCE_RC);
            }
            if ((strcmp($call_center_repair->out_center, BOOL_FALSE) == 0) && (strcmp($call_center_repair->is_driver_out_center, BOOL_TRUE) == 0)) {
                $driving_job_out = RepairTrait::getDrivingJob($repair_order_id, SelfDriveTypeEnum::SEND);
                $inspection_job_out = RepairTrait::getInspectionJob($repair_order_id, InspectionTypeEnum::MAINTENANCE_DC);
            }
        }


        $condotion_lt_rental =  RepairTrait::getConditionQuotation($call_center_repair);
        $repair_documents_files = $call_center_repair->getMedia('repair_documents');
        $repair_documents_files = get_medias_detail($repair_documents_files);
        $yes_no_list = getYesNoList();
        $replacement_list = RepairTrait::getReplacementList($call_center_repair->id);
        $index_uri = 'admin.call-center-repairs.index';
        $page_title =  __('lang.edit') . __('repairs.page_title');
        return view('admin.repairs.form', [
            'd' => $call_center_repair,
            'page_title' => $page_title,
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
            'check_repair_list' => $check_repair_list,
            'condotion_lt_rental' => $condotion_lt_rental,
            'index_uri' => $index_uri,
            'repair_documents_files' => $repair_documents_files,
            'yes_no_list' => $yes_no_list,
            'replacement_list' => $replacement_list,
            'driving_job_in' => $driving_job_in,
            'inspection_job_in' => $inspection_job_in,
            'driving_job_out' => $driving_job_out,
            'inspection_job_out' => $inspection_job_out,
            'mode' => MODE_UPDATE
        ]);
    }

    public function show(Repair $call_center_repair)
    {
        $this->authorize(Actions::View . '_' . Resources::CallCenterRepair);
        $replacement_type_list = ReplacementCarTrait::getReplacementTypeList();
        $check_list = RepairTrait::getCheckList();
        $repair_type_list = RepairTrait::getRepairType();
        $informer_type_list = RepairTrait::getInformer();
        $service_center_list = RepairTrait::getServiceCenter();
        $is_need_driver = RepairTrait::getIsNeedDriver();
        $informers = User::select('id', 'name')->orderBy('name')->get();
        $car = Car::find($call_center_repair->car_id);
        $car_license = null;
        $driving_job_in = null;
        $inspection_job_in = null;
        $driving_job_out = null;
        $inspection_job_out = null;
        if ($car) {
            if ($car->license_plate) {
                $car_license = $car->license_plate;
            } else if ($car->engine_no) {
                $car_license = __('inspection_cars.engine_no') . ' ' . $car->engine_no;
            } else if ($car->chassis_no) {
                $car_license = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
            }
        }
        $car_data = RepairTrait::getDataCar($call_center_repair->car_id);
        $rental = $car_data->rental;
        $check_repair_list = RepairLine::where('repair_id', $call_center_repair->id)
            ->get()->map(function ($item) {
                $item->check_text = __('check_distances.type_text_' . $item->check);
                return $item;
            });

        $repair_order_id = RepairOrder::where('repair_id', $call_center_repair->id)->first()?->id;
        if ($repair_order_id) {
            if ((strcmp($call_center_repair->in_center, BOOL_FALSE) == 0) && (strcmp($call_center_repair->is_driver_in_center, BOOL_TRUE) == 0)) {
                $driving_job_in = RepairTrait::getDrivingJob($repair_order_id, SelfDriveTypeEnum::PICKUP);
                $inspection_job_in = RepairTrait::getInspectionJob($repair_order_id, InspectionTypeEnum::MAINTENANCE_RC);
            }
            if ((strcmp($call_center_repair->out_center, BOOL_FALSE) == 0) && (strcmp($call_center_repair->is_driver_out_center, BOOL_TRUE) == 0)) {
                $driving_job_out = RepairTrait::getDrivingJob($repair_order_id, SelfDriveTypeEnum::SEND);
                $inspection_job_out = RepairTrait::getInspectionJob($repair_order_id, InspectionTypeEnum::MAINTENANCE_DC);
            }
        }

        $condotion_lt_rental =  RepairTrait::getConditionQuotation($call_center_repair);
        $repair_documents_files = $call_center_repair->getMedia('repair_documents');
        $repair_documents_files = get_medias_detail($repair_documents_files);
        $yes_no_list = getYesNoList();
        $replacement_list = RepairTrait::getReplacementList($call_center_repair->id);
        $index_uri = 'admin.call-center-repairs.index';
        $page_title =  __('lang.view') . __('repairs.page_title');
        return view('admin.repairs.form', [
            'd' => $call_center_repair,
            'page_title' => $page_title,
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
            'check_repair_list' => $check_repair_list,
            'condotion_lt_rental' => $condotion_lt_rental,
            'view' => true,
            'index_uri' => $index_uri,
            'repair_documents_files' => $repair_documents_files,
            'yes_no_list' => $yes_no_list,
            'replacement_list' => $replacement_list,
            'driving_job_in' => $driving_job_in,
            'inspection_job_in' => $inspection_job_in,
            'driving_job_out' => $driving_job_out,
            'inspection_job_out' => $inspection_job_out,
            'mode' => MODE_VIEW
        ]);
    }
}
