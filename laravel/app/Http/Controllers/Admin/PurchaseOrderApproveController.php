<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Enums\Actions;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\POStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Approve;
use App\Models\ApproveLog;
use App\Models\ApproveLine;
use App\Models\ComparisonPrice;
use App\Models\ComparisonPriceLine;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionLine;
use App\Models\Rental;
use App\Models\Role;
use App\Models\User;
use App\Models\Department;
use App\Traits\PurchaseRequisitionTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\HistoryTrait;

class PurchaseOrderApproveController extends Controller
{
    use PurchaseRequisitionTrait;
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::PurchaseOrderApprove);
        $s = $request->s;
        $purchase_order_no = $request->purchase_order_no;
        $purchase_requisition_no = $request->purchase_requisition_no;
        $car_category_id = $request->car_category_id;
        $status = $request->status;
        $from_delivery_date = $request->from_delivery_date;
        $to_delivery_date = $request->to_delivery_date;
        $rental_type = null;
        if (strcmp($request->rental_type, "0") == 0) {
            $rental_type = 0;
        } else {
            $rental_type = $request->rental_type;
        }

        $status = null;
        if (strcmp($request->status, "0") == 0) {
            $status = 0;
        } else {
            $status = $request->status;
        }
        $rental_type_list = PurchaseOrderOpenController::getRentalType();
        $status_list = $this->getPurchaseOrderStatus();

        $list = PurchaseOrder::sortable(['po_no' => 'desc'])
            ->leftjoin('purchase_requisitions', 'purchase_orders.pr_id', '=', 'purchase_requisitions.id')
            ->leftJoin('purchase_requisition_lines', 'purchase_requisition_lines.purchase_requisition_id', '=', 'purchase_requisitions.id')
            ->leftJoin('rentals', function ($join) {
                $join->on('rentals.id', '=', 'purchase_requisitions.reference_id')
                    ->where('purchase_requisitions.reference_type', '=', Rental::class);
            })
            ->leftJoin('lt_rentals', function ($join) {
                $join->on('lt_rentals.id', '=', 'purchase_requisitions.reference_id')
                    ->where('purchase_requisitions.reference_type', '=', LongTermRental::class);
            })
            ->selectRaw('
            purchase_orders.id,
            purchase_orders.po_no,
            purchase_orders.require_date,
            purchase_orders.status,
            purchase_requisitions.pr_no as pr_no, 
            purchase_requisitions.rental_type as rental_type, 
            rentals.worksheet_no as st_worksheet_no,
            lt_rentals.worksheet_no as lt_worksheet_no,
            SUM(purchase_requisition_lines.amount) as total_amount')
            ->groupBy(
                'purchase_orders.id',
                'purchase_orders.po_no',
                'purchase_orders.require_date',
                'purchase_orders.status',
                'pr_no',
                'rental_type',
                'st_worksheet_no',
                'lt_worksheet_no',
            )
            ->whereNotIn('purchase_orders.status', [POStatusEnum::DRAFT, POStatusEnum::COMPLETE])
            ->where(function ($q) use ($rental_type, $status, $purchase_requisition_no) {
                if (!is_null($rental_type)) {
                    $q->where('purchase_requisitions.rental_type', $rental_type);
                }
                if (!is_null($status)) {
                    $q->where('purchase_orders.status', $status);
                }
                if (!empty($purchase_requisition_no)) {
                    $q->where('purchase_requisitions.pr_no', 'like', '%' . $purchase_requisition_no . '%');
                }
            })
            ->branch()
            ->search($s, $request)
            ->paginate(PER_PAGE);

        $list->map(function ($item) use ($rental_type_list) {
            $rental_type = findObjectById($rental_type_list, $item->rental_type);
            $item->rental_type  = ($rental_type) ? $rental_type->name : '';
            return $item;
        });
        $page_title = __('purchase_orders.confirm_purchase_orders');
        return view('admin.purchase-orders.index', [
            's' => $s,
            'page_title' => $page_title,
            'list' => $list,
            'purchase_order_no' => $purchase_order_no,
            'purchase_requisition_no' => $purchase_requisition_no,
            'car_category_id' => $car_category_id,
            'status' => $status,
            'from_delivery_date' => $from_delivery_date,
            'to_delivery_date' => $to_delivery_date,
            'rental_type_list' => $rental_type_list,
            'rental_type' => $rental_type,
            'status_list' => $status_list,
            'view_only' => true
        ]);
    }

    public function show(PurchaseOrder $purchase_order_approve)
    {
        $this->authorize(Actions::View . '_' . Resources::PurchaseOrderApprove);
        $rental_type_list = PurchaseOrderOpenController::getRentalType();
        $purchase_requisition = PurchaseRequisition::find($purchase_order_approve->pr_id);
        $rental_images_files = $purchase_requisition->getMedia('rental_images');
        $rental_images_files = get_medias_detail($rental_images_files);
        $refer_images_files = $purchase_requisition->getMedia('refer_images');
        $refer_images_files = get_medias_detail($refer_images_files);
        $quotation_files = $purchase_order_approve->getMedia('quotation_files');
        $quotation_files = get_medias_detail($quotation_files);
        $purchase_requisition_cars = PurchaseRequisitionTrait::getPRCar($purchase_order_approve->pr_id);
        $option = [];
        $option['item_type'] = PurchaseOrder::class;
        $compare_price_list = PurchaseRequisitionTrait::getComparePriceList($purchase_order_approve->id, $option);
        $purchase_order_lines = $this->getPurchaseOrderLineByID($purchase_order_approve->id);
        $payment_condition_list = PurchaseRequisitionTrait::getPaymentConditionList();
        $page_title = __('lang.view') . __('purchase_orders.po');

        // $approve_line_list = new StepApproveManagement();
        // $approve_return = $approve_line_list->logApprove(PurchaseOrder::class, $purchase_order_approve->id);
        // $approve_line_list = $approve_return['approve_line_list'];
        // $approve = $approve_return['approve'];
        // if (!is_null($approve_line_list)) {
        //     // can approve or super user
        //     $approve_line_owner = new StepApproveManagement();
        //     $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
        // } else {
        //     $approve_line_owner = null;
        // }
        $approve_line = HistoryTrait::getHistory(PurchaseOrder::class, $purchase_order_approve->id);
        $approve_line_list = $approve_line['approve_line_list'];
        $approve = $approve_line['approve'];
        $approve_line_logs = $approve_line['approve_line_logs'];
        if (!is_null($approve_line_list)) {
            // can approve or super user
            $approve_line_owner = new StepApproveManagement();
            $approve_line_owner = $approve_line_owner->checkCanApprove(PurchaseOrder::class, $purchase_order_approve->id,ConfigApproveTypeEnum::PURCHASE_ORDER);

        } else {
            $approve_line_owner = null;
        }
        return view('admin.purchase-order-approve.view', [
            'd' => $purchase_order_approve,
            'page_title' => $page_title,
            'rental_type_list' => $rental_type_list,
            'rental_images_files' => $rental_images_files,
            'refer_images_files' => $refer_images_files,
            'quotation_files' => $quotation_files,
            'purchase_requisition_cars' => $purchase_requisition_cars,
            'purchase_order_dealer_list' => $compare_price_list,
            'purchase_order_lines' => $purchase_order_lines,
            'purchase_requisition' => $purchase_requisition,
            'payment_condition_list' => $payment_condition_list,
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
            'approve_line_owner' => $approve_line_owner,
            'approve_line_logs' => $approve_line_logs,
        ]);
    }

    // public function checkCanApprove($user, $approve, $is_super_user_check)
    // {
    //     $approve_check_status = Approve::find($approve->id);
    //     $approve_line_owner = null;
    //     $is_super_user = null;
    //     if (is_null($approve_check_status->status)) {
    //         $is_super_user = $is_super_user_check;
    //         if (!$is_super_user) {
    //             // department, role, user
    //             $approve_line_owner = ApproveLine::where('approve_id', $approve->id)->where('is_pass', null)->where('seq', $approve->status_state)->where('department_id', $user->user_department_id)
    //                 ->where('role_id', $user->role_id)->where('user_id', $user->id)->first();

    //             if (!$approve_line_owner) { // department, role
    //                 $approve_line_owner = ApproveLine::where('approve_id', $approve->id)->where('is_pass', null)->where('seq', $approve->status_state)->where('department_id', $user->user_department_id)
    //                     ->where('role_id', $user->role_id)->where('user_id', null)->first();
    //             }
    //             if (!$approve_line_owner) { // department
    //                 // dd($approve_line_owner);
    //                 $approve_line_owner = ApproveLine::where('approve_id', $approve->id)->where('is_pass', null)->where('seq', $approve->status_state)->where('department_id', $user->user_department_id)
    //                     ->where('role_id', null)->where('user_id', null)->first();
    //             }
    //         } else {
    //             $approve_line_owner = $is_super_user_check;
    //         }
    //     }
    //     // dd($approve_line_owner);
    //     return $approve_line_owner;
    // }

    // public function logApprove($approve)
    // {
    //     $approve_line_all = ApproveLine::where('approve_id', $approve->id)->whereNotIn('seq', [STATUS_DEFAULT])->orderBy('seq', 'asc')->count();
    //     $approve_line_is_pass_null = ApproveLine::where('approve_id', $approve->id)->where('is_pass', null)->whereNotIn('seq', [STATUS_DEFAULT])->orderBy('seq', 'asc')->count();
    //     $approve_line_is_super_user = ApproveLine::where('approve_id', $approve->id)->whereNotNull('is_pass')->whereIn('seq', [STATUS_DEFAULT])->orderBy('seq', 'asc')->count();
    //     if ($approve_line_all == $approve_line_is_pass_null && $approve_line_is_super_user == 0) {
    //         $approve_line_list = ApproveLine::where('approve_id', $approve->id)->whereNotIn('seq', [STATUS_DEFAULT])->orderBy('seq', 'asc')->get();
    //         $approve_line_list->map(function ($item) {
    //             if ($item->user_id) {
    //                 $user = User::find($item->user_id);
    //                 $item->user_name = ($user->name) ? $user->name : '';
    //             } else {
    //                 $item->user_name =  '';
    //             }
    //             if ($item->role_id) {
    //                 $role = Role::find($item->role_id);
    //                 $item->role_name = ($role->name) ? $role->name : '';
    //             } else {
    //                 $item->role_name =  '';
    //             }

    //             if ($item->department_id) {
    //                 $department = Department::find($item->department_id);
    //                 $item->department_name = ($department->name) ? $department->name : '';
    //             } else {
    //                 $item->department_name =  '';
    //             }
    //             return $item;
    //         });
    //         $approve_line_list = $approve_line_list->toArray();
    //     } else { // approve log
    //         $approve_log_id = ApproveLog::where('approve_id', $approve->id)->orderBy('seq', 'asc')->pluck('approve_line_id');
    //         $log_approve_arr = [];
    //         foreach ($approve_log_id as $app_log) {
    //             $log_arr = [];
    //             $approve_line_id = ApproveLine::find($app_log);
    //             $approve_log_data = ApproveLog::where('approve_line_id', $app_log)->first();
    //             $log_arr['seq'] = $approve_line_id->seq;
    //             $log_arr['is_pass'] = $approve_line_id->is_pass;

    //             $log_arr['reason'] = ($approve_log_data->reason) ? $approve_log_data->reason : '';
    //             if ($approve_log_data->user_id) {
    //                 $user = User::find($approve_log_data->user_id);
    //                 $log_arr['user_name'] = ($user->name) ? $user->name : '';
    //             } else {
    //                 $log_arr['user_name'] =  '';
    //             }

    //             if ($user->role_id) {
    //                 $role = Role::find($user->role_id);
    //                 $log_arr['role_name'] = ($role->name) ? $role->name : '';
    //             } else {
    //                 $log_arr['role_name'] =  '';
    //             }

    //             if ($user->user_department_id) {
    //                 $department = Department::find($user->user_department_id);
    //                 $log_arr['department_name'] = ($department->name) ? $department->name : '';
    //             } else {
    //                 $log_arr['department_name'] =  '';
    //             }
    //             if ($log_arr) {
    //                 $log_approve_arr[] = $log_arr;
    //             }
    //             if ($approve_line_id->seq == STATUS_DEFAULT || $approve_line_id->is_pass == STATUS_DEFAULT) {
    //                 $is_break = true;
    //                 break;
    //             }
    //         }
    //         $approve_line_list = $log_approve_arr;

    //         if (!isset($is_break)) {
    //             // approve line waiting
    //             $approve_line = ApproveLine::where('approve_id', $approve->id)->where('is_pass', null)->whereNotIn('seq', [STATUS_DEFAULT])->orderBy('seq', 'asc')->get();
    //             foreach ($approve_line as $app_line) {
    //                 $line_arr = [];
    //                 $line_arr['seq'] = $app_line->seq;
    //                 $line_arr['is_pass'] = $app_line->is_pass;
    //                 if ($app_line->user_id) {
    //                     $user = User::find($app_line->user_id);
    //                     $line_arr['user_name'] = ($user->name) ? $user->name : '';
    //                 } else {
    //                     $line_arr['user_name'] =  '';
    //                 }
    //                 if ($app_line->role_id) {
    //                     $role = Role::find($app_line->role_id);
    //                     $line_arr['role_name'] = ($role->name) ? $role->name : '';
    //                 } else {
    //                     $line_arr['role_name'] =  '';
    //                 }

    //                 if ($app_line->department_id) {
    //                     $department = Department::find($app_line->department_id);
    //                     $line_arr['department_name'] = ($department->name) ? $department->name : '';
    //                 } else {
    //                     $line_arr['department_name'] =  '';
    //                 }
    //                 if ($line_arr) {
    //                     array_push($approve_line_list, $line_arr);
    //                 }
    //             }
    //         }
    //     }
    //     return $approve_line_list;
    // }

    public function getComparePriceList($purchase_requisition_id)
    {
        $compare_price_list = ComparisonPrice::where('item_id', $purchase_requisition_id)
            ->where('item_type', PurchaseRequisition::class)->get();

        $compare_price_list->map(function ($item) {
            $item->creditor_text = ($item->creditor) ? $item->creditor->name : '';
            $compare_price_line_list = ComparisonPriceLine::where('comparison_price_id', $item->id)->get();
            $compare_price_line_list->map(function ($compare_price_line) {
                $compare_price_line->car_id = $compare_price_line->item_id;
                $compare_price_line->car_price = $compare_price_line->total;
                $compare_price_line->vat_exclude = $compare_price_line->subtotal;
            });
            $item->dealer_price_list =  $compare_price_line_list;
            $medias = $item->getMedia('comparison_price');
            $dealer_files = get_medias_detail($medias);
            $dealer_files = collect($dealer_files)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $item->dealer_files = $dealer_files;
            return $item;
        });
        return $compare_price_list;
    }

    public function getPurchaseOrderLineByID($purchase_order_id)
    {
        $purchase_order_lines = PurchaseOrderLine::leftjoin('purchase_requisition_lines', 'purchase_requisition_lines.id', '=', 'purchase_order_lines.pr_line_id')
            ->leftJoin('car_classes', 'car_classes.id', '=', 'purchase_requisition_lines.car_class_id')
            ->leftJoin('car_colors', 'car_colors.id', '=', 'purchase_requisition_lines.car_color_id')
            ->where('purchase_order_lines.purchase_order_id', $purchase_order_id)
            ->select(
                'purchase_order_lines.*',
                'purchase_requisition_lines.id as item_id',
                'purchase_requisition_lines.amount as exac_amount',
                'car_colors.name as color'
            )->get();
        return $purchase_order_lines;
    }

    public static function getPurchaseOrderStatus()
    {
        $status = collect([
            (object) [
                'id' => POStatusEnum::CONFIRM,
                'name' => __('purchase_orders.status_' . POStatusEnum::CONFIRM),
                'value' => POStatusEnum::CONFIRM,
            ],
            (object) [
                'id' => POStatusEnum::REJECT,
                'name' => __('purchase_orders.status_' . POStatusEnum::REJECT),
                'value' => POStatusEnum::REJECT,
            ],
            (object) [
                'id' => POStatusEnum::CANCEL,
                'name' => __('purchase_orders.status_' . POStatusEnum::CANCEL),
                'value' => POStatusEnum::CANCEL,
            ]
        ]);
        return $status;
    }
}
