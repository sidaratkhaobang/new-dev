<?php

namespace App\Http\Controllers\Admin;

use App\Classes\InstallEquipmentManagement;
use App\Classes\StepApproveManagement;
use App\Enums\DrivingJobTypeStatusEnum;
use App\Enums\InspectionFormEnum;
use App\Enums\InspectionTypeEnum;
use App\Enums\InstallEquipmentPOStatusEnum;
use App\Enums\InstallEquipmentStatusEnum;
use App\Enums\Resources;
use App\Enums\Actions;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\SelfDriveTypeEnum;
use App\Enums\TransferTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Approve;
use App\Models\ApproveLine;
use App\Models\ApproveLog;
use App\Models\CarParkTransfer;
use App\Models\DrivingJob;
use App\Models\InspectionFlow;
use App\Models\InspectionFormSection;
use App\Models\InspectionJob;
use App\Models\InspectionJobChecklist;
use App\Models\InspectionJobStep;
use App\Models\InspectionStep;
use App\Models\InstallEquipment;
use App\Models\InstallEquipmentLine;
use App\Models\InstallEquipmentPOLine;
use App\Models\InstallEquipmentPurchaseOrder;
use App\Models\Role;
use App\Models\User;
use App\Models\Department;
use App\Traits\InstallEquipmentTrait;
use Carbon\Carbon;
use DateTime;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class InstallEquipmentPOApproveController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::InstallEquipmentPOApprove);
        $install_equipment_po_no = $request->install_equipment_po_no;
        $install_equipment_no = $request->install_equipment_no;
        $supplier_id = $request->supplier_id;
        $status_id = $request->status_id;
        $chassis_no = $request->chassis_no;
        $license_plate = $request->license_plate;

        $list = InstallEquipmentPurchaseOrder::leftjoin('install_equipments', 'install_equipments.id', '=', 'install_equipment_purchase_orders.install_equipment_id')
            ->leftJoin('creditors', 'creditors.id', '=', 'install_equipment_purchase_orders.supplier_id')
            ->leftJoin('cars', 'cars.id', '=', 'install_equipment_purchase_orders.car_id')
            ->select(
                'install_equipment_purchase_orders.*',
                'install_equipments.worksheet_no as ie_worksheet_no',
                'creditors.name as supplier_name',
                'cars.license_plate as license_plate',
                'cars.chassis_no as chassis_no',
            )
            ->search($request)
            ->sortable()
            ->orderBy('install_equipment_purchase_orders.created_at', 'desc')
            ->whereNotIn('install_equipments.status', [STATUS_INACTIVE])
            ->paginate(PER_PAGE);

        $supplier_list = InstallEquipmentPurchaseOrder::select(['creditors.id', 'creditors.name'])
            ->leftJoin('creditors', 'creditors.id', '=', 'install_equipment_purchase_orders.supplier_id')
            ->groupBy('creditors.id', 'creditors.name')
            ->get(); // Supplier

        $install_equipment_po_no_list = InstallEquipmentPurchaseOrder::select(['install_equipment_purchase_orders.id', 'install_equipment_purchase_orders.worksheet_no as name'])->get();   // เลขที่ใบสั่งซื้อ

        $install_equipment_no_list = InstallEquipmentPurchaseOrder::select(['install_equipments.id', 'install_equipments.worksheet_no as name'])
            ->leftJoin('install_equipments', 'install_equipments.id', '=', 'install_equipment_purchase_orders.install_equipment_id')
            ->get();    // เลขที่ใบขอติดตั้งอุปกรณ์

        $car = InstallEquipmentPurchaseOrder::leftJoin('cars', 'cars.id', '=', 'install_equipment_purchase_orders.car_id');
        $license_plate_list = $car->select(['cars.id', 'cars.license_plate as name'])->whereNotNull('cars.license_plate')->groupBy('cars.id', 'cars.license_plate')->get(); // หมายเลขตัวถัง
        $chassis_no_list = $car->select(['cars.id', 'cars.chassis_no as name'])->whereNotNull('cars.chassis_no')->groupBy('cars.id', 'cars.chassis_no')->get();  // ทะเบียนรถ
        $status_list = InstallEquipmentTrait::getTempIntallEquipmentPOStatusList();


        return view('admin.install-equipment-purchase-order-approves.index', [
            'list' => $list,
            's' => $request->s,
            'status_id' => $status_id,
            'status_list' => $status_list,
            'supplier_id' => $supplier_id,
            'supplier_list' => $supplier_list,
            'license_plate' => $license_plate,
            'license_plate_list' => $license_plate_list,
            'chassis_no' => $chassis_no,
            'chassis_no_list' => $chassis_no_list,
            'install_equipment_po_no' => $install_equipment_po_no,
            'install_equipment_po_no_list' => $install_equipment_po_no_list,
            'install_equipment_no' => $install_equipment_no,
            'install_equipment_no_list' => $install_equipment_no_list,
        ]);
    }

    public function updateStatus(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::InstallEquipmentPOApprove);
        $install_equipment_po = InstallEquipmentPurchaseOrder::find($request->id);
        if (!$install_equipment_po) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }

        if (strcmp($install_equipment_po->status, InstallEquipmentPOStatusEnum::PENDING_REVIEW) !== 0) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }

        // update approve step
        $approve_update = new StepApproveManagement();
        // $approve_update = $approve_update->updateApprove($request, $install_equipment_po, $request->status_update, InstallEquipmentPurchaseOrder::class);
        $approve_update = $approve_update->updateApprove(InstallEquipmentPurchaseOrder::class, $install_equipment_po->id, $request->status_update,null,$request->reject_reason);

        $install_equipment_po->status = $approve_update;
        $install_equipment_po->reject_reason = $request->reject_reason ?? '';
        $install_equipment_po->save();

        if (strcmp($install_equipment_po->status, InstallEquipmentPOStatusEnum::CONFIRM) === 0) {
            $this->updateInstallEquipmentStatus($install_equipment_po->install_equipment_id, InstallEquipmentStatusEnum::CONFIRM);
            // $this->createDrivingJob($install_equipment_po->install_equipment_id, $install_equipment_po->car_id);
            // $this->createInspectionJobs($install_equipment_po->id, $install_equipment_po->car_id);
        }

        if (strcmp($install_equipment_po->status, InstallEquipmentPOStatusEnum::REJECT) === 0) {
            $this->updateInstallEquipmentStatus($install_equipment_po->install_equipment_id, InstallEquipmentStatusEnum::REJECT);
        }

        $redirect_route = route('admin.install-equipment-po-approves.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(InstallEquipmentPurchaseOrder $install_equipment_po_approve)
    {
        $this->authorize(Actions::View . '_' . Resources::InstallEquipmentPOApprove);
        $install_equipment_po_line_list = InstallEquipmentPOLine::where('install_equipment_po_id', $install_equipment_po_approve->id)->get();
        $install_equipment_po_line_list->map(function ($item) use ($install_equipment_po_line_list) {
            $item->accessory_class = ($item->accessory) ? $item->accessory->version : '';
            $item->overall_subtotal = round(floatval($item->subtotal * $item->amount), 2);
            $item->overall_vat = round(floatval($item->vat * $item->amount), 2);
            $overall_total = ($item->total * $item->amount) - ($item->discount);
            $item->overall_total = number_format($overall_total, 2, '.', '');
            $item->accessory_text = '';
            if ($item->accessory) {
                $item->accessory_text = $item->accessory->code . ' - ' . $item->accessory->name;
            }
        });

        //logApprove
        $approve_line_logs = [];
        $approve_line_management = new StepApproveManagement();
        $approve_return = $approve_line_management->logApprove(InstallEquipmentPurchaseOrder::class, $install_equipment_po_approve->id);
        $approve_line_list = $approve_return['approve_line_list'];
        $approve = $approve_return['approve'];
        $approve_line_logs = $approve_line_management->getHistoryLogs();

        // can approve or super user
        $approve_line_owner = new StepApproveManagement();
        // $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
        // ConfigApproveTypeEnum::EQUIPMENT_ORDER
        $approve_line_owner = $approve_line_owner->checkCanApprove(InstallEquipmentPurchaseOrder::class, $install_equipment_po_approve->id);


        $install_equip_management = new InstallEquipmentManagement();
        $install_equip_management->calculateInstallEquipmentPO($install_equipment_po_approve->install_equipment_id);
        $summary = $install_equip_management->getSummary();
        $summary = (object) $summary;
        $store_uri = route('admin.install-equipment-po-approves.update-status');
        $page_title = __('lang.view') . __('install_equipment_pos.page_title');
        return view('admin.install-equipment-purchase-order-approves.form', [
            'd' => $install_equipment_po_approve,
            'install_equipment_po_line_list' => $install_equipment_po_line_list,
            'page_title' => $page_title,
            'store_uri' => $store_uri,
            'summary' => $summary,
            'view_only' => true,
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
            'approve_line_owner' => $approve_line_owner,
            'approve_line_logs' => $approve_line_logs
        ]);
    }

    public function updateInstallEquipmentStatus($id, $status)
    {
        $install_equipment = InstallEquipment::find($id);
        if (!$install_equipment) {
            return false;
        }

        $install_equipment->status = $status;
        $install_equipment->save();
        return true;
    }

    public function create()
    {
        abort(404);
    }

    public function edit()
    {
        abort(404);
    }

    public function update()
    {
        abort(404);
    }
}
