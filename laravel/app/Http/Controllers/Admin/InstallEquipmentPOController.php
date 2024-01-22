<?php

namespace App\Http\Controllers\Admin;

use App\Classes\InstallEquipmentManagement;
use App\Classes\StepApproveManagement;
use App\Enums\Resources;
use App\Enums\Actions;
use App\Http\Controllers\Controller;
use App\Models\Approve;
use App\Models\ApproveLine;
use App\Models\ApproveLog;
use App\Models\Creditor;
use App\Models\InstallEquipmentLine;
use App\Models\InstallEquipmentPOLine;
use App\Models\InstallEquipmentPurchaseOrder;
use App\Traits\InstallEquipmentTrait;
use App\Models\Role;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\HistoryTrait;

class InstallEquipmentPOController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::InstallEquipmentPO);
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

        return view('admin.install-equipment-purchase-orders.index', [
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

    public function edit(InstallEquipmentPurchaseOrder $install_equipment_purchase_order)
    {
        $this->authorize(Actions::Manage . '_' . Resources::InstallEquipmentPO);
        $install_equipment_po_line_list = InstallEquipmentPOLine::where('install_equipment_po_id', $install_equipment_purchase_order->id)->get();
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
        $install_equip_management = new InstallEquipmentManagement();
        $install_equip_management->calculateInstallEquipmentPO($install_equipment_purchase_order->install_equipment_id);
        $summary = $install_equip_management->getSummary();
        $summary = (object) $summary;
        $store_uri = route('admin.install-equipment-purchase-orders.store');
        $page_title = __('lang.edit') . __('install_equipment_pos.page_title');

        //logApprove
        // $approve_line_logs = [];
        // $approve_line_management = new StepApproveManagement();
        // $approve_return = $approve_line_management->logApprove(InstallEquipmentPurchaseOrder::class, $install_equipment_purchase_order->id);
        // $approve_line_list = $approve_return['approve_line_list'];
        // $approve = $approve_return['approve'];
        // $approve_line_logs = $approve_line_management->getHistoryLogs();
        $approve_line = HistoryTrait::getHistory(InstallEquipmentPurchaseOrder::class, $install_equipment_purchase_order->id);
        $approve_line_list = $approve_line['approve_line_list'];
        $approve = $approve_line['approve'];
        $approve_line_logs = $approve_line['approve_line_logs'];

        return view('admin.install-equipment-purchase-orders.form', [
            'd' => $install_equipment_purchase_order,
            'install_equipment_po_line_list' => $install_equipment_po_line_list,
            'page_title' => $page_title,
            'store_uri' => $store_uri,
            'summary' => $summary,
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
            'approve_line_logs' => $approve_line_logs
        ]);
    }

    public function logApprove($approve)
    {
        $approve_line_all = ApproveLine::where('approve_id', $approve->id)->whereNotIn('seq', [STATUS_DEFAULT])->orderBy('seq', 'asc')->count();
        $approve_line_is_pass_null = ApproveLine::where('approve_id', $approve->id)->where('is_pass', null)->whereNotIn('seq', [STATUS_DEFAULT])->orderBy('seq', 'asc')->count();
        $approve_line_is_super_user = ApproveLine::where('approve_id', $approve->id)->whereNotNull('is_pass')->whereIn('seq', [STATUS_DEFAULT])->orderBy('seq', 'asc')->count();
        if ($approve_line_all == $approve_line_is_pass_null && $approve_line_is_super_user == 0) {
            $approve_line_list = ApproveLine::where('approve_id', $approve->id)->whereNotIn('seq', [STATUS_DEFAULT])->orderBy('seq', 'asc')->get();
            $approve_line_list->map(function ($item) {
                if ($item->user_id) {
                    $user = User::find($item->user_id);
                    $item->user_name = ($user->name) ? $user->name : '';
                } else {
                    $item->user_name =  '';
                }
                if ($item->role_id) {
                    $role = Role::find($item->role_id);
                    $item->role_name = ($role->name) ? $role->name : '';
                } else {
                    $item->role_name =  '';
                }

                if ($item->department_id) {
                    $department = Department::find($item->department_id);
                    $item->department_name = ($department->name) ? $department->name : '';
                } else {
                    $item->department_name =  '';
                }
                return $item;
            });
            $approve_line_list = $approve_line_list->toArray();
        } else { // approve log
            $approve_log_id = ApproveLog::where('approve_id', $approve->id)->orderBy('seq', 'asc')->pluck('approve_line_id');
            $log_approve_arr = [];
            foreach ($approve_log_id as $app_log) {
                $log_arr = [];
                $approve_line_id = ApproveLine::find($app_log);
                $approve_log_data = ApproveLog::where('approve_line_id', $app_log)->first();
                $log_arr['seq'] = $approve_line_id->seq;
                $log_arr['is_pass'] = $approve_line_id->is_pass;

                $log_arr['reason'] = ($approve_log_data->reason) ? $approve_log_data->reason : '';
                if ($approve_log_data->user_id) {
                    $user = User::find($approve_log_data->user_id);
                    $log_arr['user_name'] = ($user->name) ? $user->name : '';
                } else {
                    $log_arr['user_name'] =  '';
                }

                if ($user->role_id) {
                    $role = Role::find($user->role_id);
                    $log_arr['role_name'] = ($role->name) ? $role->name : '';
                } else {
                    $log_arr['role_name'] =  '';
                }

                if ($user->user_department_id) {
                    $department = Department::find($user->user_department_id);
                    $log_arr['department_name'] = ($department->name) ? $department->name : '';
                } else {
                    $log_arr['department_name'] =  '';
                }
                if ($log_arr) {
                    $log_approve_arr[] = $log_arr;
                }
                if ($approve_line_id->seq == STATUS_DEFAULT || $approve_line_id->is_pass == STATUS_DEFAULT) {
                    $is_break = true;
                    break;
                }
            }
            $approve_line_list = $log_approve_arr;

            if (!isset($is_break)) {
                // approve line waiting
                $approve_line = ApproveLine::where('approve_id', $approve->id)->where('is_pass', null)->whereNotIn('seq', [STATUS_DEFAULT])->orderBy('seq', 'asc')->get();
                foreach ($approve_line as $app_line) {
                    $line_arr = [];
                    $line_arr['seq'] = $app_line->seq;
                    $line_arr['is_pass'] = $app_line->is_pass;
                    if ($app_line->user_id) {
                        $user = User::find($app_line->user_id);
                        $line_arr['user_name'] = ($user->name) ? $user->name : '';
                    } else {
                        $line_arr['user_name'] =  '';
                    }
                    if ($app_line->role_id) {
                        $role = Role::find($app_line->role_id);
                        $line_arr['role_name'] = ($role->name) ? $role->name : '';
                    } else {
                        $line_arr['role_name'] =  '';
                    }

                    if ($app_line->department_id) {
                        $department = Department::find($app_line->department_id);
                        $line_arr['department_name'] = ($department->name) ? $department->name : '';
                    } else {
                        $line_arr['department_name'] =  '';
                    }
                    if ($line_arr) {
                        array_push($approve_line_list, $line_arr);
                    }
                }
            }
        }
        return $approve_line_list;
    }

    public function show(InstallEquipmentPurchaseOrder $install_equipment_purchase_order)
    {
        $this->authorize(Actions::View . '_' . Resources::InstallEquipmentPO);
        $install_equipment_po_line_list = InstallEquipmentPOLine::where('install_equipment_po_id', $install_equipment_purchase_order->id)->get();
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
        $install_equip_management = new InstallEquipmentManagement();
        $install_equip_management->calculateInstallEquipmentPO($install_equipment_purchase_order->install_equipment_id);
        $summary = $install_equip_management->getSummary();
        $summary = (object) $summary;
        $store_uri = route('admin.install-equipment-purchase-orders.store');
        $page_title = __('lang.view') . __('install_equipment_pos.page_title');

        //logApprove
        $approve_line_logs = [];
        $approve_line_management = new StepApproveManagement();
        $approve_return = $approve_line_management->logApprove(InstallEquipmentPurchaseOrder::class, $install_equipment_purchase_order->id);
        $approve_line_list = $approve_return['approve_line_list'];
        $approve = $approve_return['approve'];
        $approve_line_logs = $approve_line_management->getHistoryLogs();

        return view('admin.install-equipment-purchase-orders.form', [
            'd' => $install_equipment_purchase_order,
            'install_equipment_po_line_list' => $install_equipment_po_line_list,
            'page_title' => $page_title,
            'store_uri' => $store_uri,
            'summary' => $summary,
            'view_only' => true,
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
            'approve_line_logs' => $approve_line_logs
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::InstallEquipmentPO);
        $install_equipment_po = InstallEquipmentPurchaseOrder::findOrFail($request->id);
        $install_equipment_po->time_of_delivery = $request->time_of_delivery;
        $install_equipment_po->payment_term = $request->payment_term;
        $install_equipment_po->contact = $request->contact;
        $install_equipment_po->car_user = $request->car_user;
        $install_equipment_po->remark = $request->remark;
        $install_equipment_po->quotation_remark = $request->quotation_remark;

        $install_equipment_po_lines = $request->install_equipment_po_lines;
        if ($install_equipment_po_lines) {
            $sum_subtotal = 0;
            $sum_total = 0;
            $sum_vat = 0;
            $sum_discount = 0;
            $sum_amount = 0;
            foreach ($install_equipment_po_lines as $key => $item) {
                if (!isset($item['id'])) {
                    continue;
                }

                $install_equipment_po_line = InstallEquipmentPOLine::find($item['id']);
                if (!$install_equipment_po_line) {
                    continue;
                }
                $install_equipment_po_line->install_equipment_po_id = $install_equipment_po->id;
                $install_equipment_po_line->accessory_id = $item['accessory_id'];
                $total = $item['total'] ? floatval($item['total']) : 0;
                $discount = $item['discount'] ? floatval($item['discount']) : 0;
                $amount = $item['amount'] ? intval($item['amount']) : 0;
                $vat = floatval($total * 7 / 107);
                $subtotal = $total - $vat;
                $sum_subtotal += $subtotal;
                $sum_total += $total;
                $sum_vat += $vat;
                $sum_discount += $discount;
                $sum_amount += $amount;

                // $install_equipment_po_line->amount = $amount;
                $install_equipment_po_line->subtotal = $subtotal;
                $install_equipment_po_line->discount = $discount;
                $install_equipment_po_line->vat = $vat;
                $install_equipment_po_line->total = $total;
                $install_equipment_po_line->save();
            }

            $install_equipment_po->subtotal = $sum_subtotal;
            $install_equipment_po->discount = $sum_discount;
            $install_equipment_po->vat = $sum_vat;
            $install_equipment_po->total = $sum_total;
            $install_equipment_po->amount = $sum_amount;
            $install_equipment_po->save();

            $ie_management = new InstallEquipmentManagement();
            $ie_management->updateInstallEquipmentLines($install_equipment_po->id);
        }
        $redirect_route = route('admin.install-equipment-purchase-orders.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function printInstallEquipmentPOPdf(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::InstallEquipmentPO);
        $install_equipment_po_id = $request->install_equipment_po_id;
        if (!$install_equipment_po_id) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        $install_equipment_po = InstallEquipmentPurchaseOrder::find($install_equipment_po_id);
        if (!$install_equipment_po) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        $install_equip_management = new InstallEquipmentManagement();
        $install_equipment_po_lines = $install_equip_management->findInstallEquipmentPOLines($install_equipment_po_id);
        $install_equip_management->calculateInstallEquipmentPO($install_equipment_po->install_equipment_id);
        $summary = $install_equip_management->getSummary();
        $summary = (object) $summary;
        $summary->total_discount_include = $summary->total - $summary->discount;
        $pdf = PDF::loadView(
            'admin.install-equipments.pdfs.purchase-order.pdf',
            [
                'd' => $install_equipment_po,
                'install_equipment_po_lines' => $install_equipment_po_lines,
                'summary' => $summary,
            ]
        );
        return $pdf->stream();
    }
}
