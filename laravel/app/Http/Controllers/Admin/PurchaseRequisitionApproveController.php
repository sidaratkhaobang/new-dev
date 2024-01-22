<?php

namespace App\Http\Controllers\Admin;

use App\Classes\NotificationManagement;
use App\Classes\StepApproveManagement;
use App\Enums\Actions;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\DepartmentEnum;
use App\Enums\InstallEquipmentPOStatusEnum;
use App\Enums\NotificationScopeEnum;
use App\Enums\POStatusEnum;
use App\Enums\PRStatusEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Approve;
use App\Models\ApproveLine;
use App\Models\ApproveLog;
use App\Models\ComparisonPrice;
use App\Models\ComparisonPriceLine;
use App\Models\Customer;
use App\Models\LongTermRental;
use App\Models\LongTermRentalLine;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionLine;
use App\Models\PurchaseRequisitionLineAccessory;
use App\Traits\HistoryTrait;
use App\Traits\NotificationTrait;
use App\Traits\PurchaseRequisitionTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PurchaseRequisitionApproveController extends Controller
{
    use PurchaseRequisitionTrait;

    public function index(Request $request)
    {
        // $this->authorize(Actions::Approve . '_' . Resources::Purchase_requisition);
        $this->authorize(Actions::View . '_' . Resources::PurchaseRequisitionApprove);
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

        $list = PurchaseRequisition::where(function ($q) use ($rental_type) {
            if (!is_null($rental_type)) {
                $q->where('rental_type', $rental_type);
            }
        })
            ->where(function ($q) use ($status) {
                if (!is_null($status)) {
                    $q->where('status', $status);
                }
            })
            ->whereNotIn('status', [PRStatusEnum::DRAFT])
            // ->branch()
            ->search($request->s, $request)
            ->sortable(['pr_no' => 'desc'])
            ->paginate(PER_PAGE);

        $pr_list = PurchaseRequisition::select('pr_no as name', 'id')->whereNotIn('status', [PRStatusEnum::DRAFT])
            ->orderBy('pr_no')->get();
        $rental_type_list = $this->getRentalType();
        $status_list = $this->getStatus();

        return view('admin.purchase-requisition-approve.index', [
            'list' => $list,
            's' => $request->s,
            'pr_list' => $pr_list,
            'pr_no' => $request->pr_no,
            'rental_type_list' => $rental_type_list,
            'rental_type' => $request->rental_type,
            'status_list' => $status_list,
            'status' => $request->status,
            'request_date' => $request->request_date,
            'require_date' => $request->require_date,
        ]);
    }

    public static function getRentalType()
    {
        $rental_type = collect([
            (object)[
                'id' => RentalTypeEnum::SHORT,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::SHORT),
                'value' => RentalTypeEnum::SHORT,
            ],
            (object)[
                'id' => RentalTypeEnum::LONG,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::LONG),
                'value' => RentalTypeEnum::LONG,
            ],
            (object)[
                'id' => RentalTypeEnum::REPLACEMENT,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::REPLACEMENT),
                'value' => RentalTypeEnum::REPLACEMENT,
            ],
            (object)[
                'id' => RentalTypeEnum::TRANSPORT,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::TRANSPORT),
                'value' => RentalTypeEnum::TRANSPORT,
            ],
            (object)[
                'id' => RentalTypeEnum::OTHER,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::OTHER),
                'value' => RentalTypeEnum::OTHER,
            ],
        ]);
        return $rental_type;
    }

    public function getStatus()
    {
        $status = collect([
            (object)[
                'id' => PRStatusEnum::PENDING_REVIEW,
                'name' => __('purchase_requisitions.status_' . PRStatusEnum::PENDING_REVIEW . '_text'),
                'value' => PRStatusEnum::PENDING_REVIEW,
            ],
            (object)[
                'id' => PRStatusEnum::CONFIRM,
                'name' => __('purchase_requisitions.status_' . PRStatusEnum::CONFIRM . '_text'),
                'value' => PRStatusEnum::CONFIRM,
            ],
            (object)[
                'id' => PRStatusEnum::REJECT,
                'name' => __('purchase_requisitions.status_' . PRStatusEnum::REJECT . '_text'),
                'value' => PRStatusEnum::REJECT,
            ],
            // (object) [
            //     'id' => PRStatusEnum::COMPLETE,
            //     'name' => __('purchase_requisitions.status_' . PRStatusEnum::COMPLETE . '_text'),
            //     'value' => PRStatusEnum::COMPLETE,
            // ],
        ]);
        return $status;
    }

    public function show(PurchaseRequisition $purchase_requisition_approve)
    {
        $this->authorize(Actions::View . '_' . Resources::PurchaseRequisitionApprove);
        $rental_type_list = $this->getRentalType();
        $rental_images_files = $purchase_requisition_approve->getMedia('rental_images');
        $rental_images_files = get_medias_detail($rental_images_files);
        $approve_images_files = $purchase_requisition_approve->getMedia('approve_images');
        $approve_images_files = get_medias_detail($approve_images_files);
        $refer_images_files = $purchase_requisition_approve->getMedia('refer_images');
        $refer_images_files = get_medias_detail($refer_images_files);
        $quotation_files = $purchase_requisition_approve->getMedia('quotation_files');
        $quotation_files = get_medias_detail($quotation_files);
        $replacement_approve_files = $purchase_requisition_approve->getMedia('replacement_approve_files');
        $replacement_approve_files = get_medias_detail($replacement_approve_files);
        $parent_list = PurchaseRequisition::where('parent_id', $purchase_requisition_approve->parent_id)->select('pr_no as name', 'id')->orderBy('pr_no')->get();

        $parent_no = null;
        if (!empty($purchase_requisition_approve->parent_id)) {
            $parent = PurchaseRequisition::find($purchase_requisition_approve->parent_id);
            if ($parent) {
                $parent_no = $parent->pr_no;
            }
        }

        $reference_name = ($purchase_requisition_approve->reference) ? $purchase_requisition_approve->reference->worksheet_no : null;

        $purchase_requisition_approve->customer_type = null;
        if ($purchase_requisition_approve->reference) {
            $customer = Customer::find($purchase_requisition_approve->reference->customer_id);
            if (!empty($customer)) {
                $purchase_requisition_approve->customer_type = $customer->customer_type;
            }
        }

        $pr_car_list = PurchaseRequisitionLine::where('purchase_requisition_id', $purchase_requisition_approve->id)->get();
        $pr_car_list->map(function ($item) {
            $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
            $item->car_color_text = ($item->color) ? $item->color->name : '';
            $item->amount_car = $item->amount;
            return $item;
        });

        $accessory_list = PurchaseRequisitionLineAccessory::whereIn('purchase_requisition_line_id', $pr_car_list->pluck('id'))->get();
        $car_accessory = [];
        $index = 0;
        foreach ($pr_car_list as $car_index => $car_item) {
            foreach ($accessory_list as $accessory_index => $accessory_item) {
                if (strcmp($car_item->id, $accessory_item->purchase_requisition_line_id) == 0) {
                    $car_accessory[$index]['accessory_id'] = $accessory_item->accessory_id;
                    $car_accessory[$index]['accessory_text'] = ($accessory_item->accessory) ? $accessory_item->accessory->name : '';
                    $car_accessory[$index]['accessory_version_text'] = ($accessory_item->accessory) ? $accessory_item->accessory->version : '';
                    $car_accessory[$index]['amount_accessory'] = $accessory_item->amount;
                    $car_accessory[$index]['remark_accessory'] = $accessory_item->remark;
                    $car_accessory[$index]['car_index'] = $car_index;
                    $index++;
                }
            }
        }

        // $approve_line_list = new StepApproveManagement();
        // $approve_return = $approve_line_list->logApprove(PurchaseRequisition::class, $purchase_requisition_approve->id);
        // $approve_line_list = $approve_return['approve_line_list'];
        // $approve = $approve_return['approve'];
        // if (!is_null($approve_line_list)) {
        //     // can approve or super user
        //     $approve_line_owner = new StepApproveManagement();
        //     $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
        // } else {
        //     $approve_line_owner = null;
        // }
        // $approve_line = HistoryTrait::getHistory(PurchaseRequisition::class, $purchase_requisition_approve->id);
        // $approve_line_list = $approve_line['approve_line_list'];
        // $approve = $approve_line['approve'];
        // $approve_line_logs = $approve_line['approve_line_logs'];
        // if (!is_null($approve_line_list)) {
        // can approve or super user
        $approve_line_owner = new StepApproveManagement();
        $approve_line_owner = $approve_line_owner->checkCanApprove(PurchaseRequisition::class, $purchase_requisition_approve->id, ConfigApproveTypeEnum::PURCHASE_REQUISITION);
        // } else {
        // $approve_line_owner = null;
        // }

        $purchase_requisition_cars = PurchaseRequisitionTrait::getPRCar($purchase_requisition_approve->id);
        $compare_price_list = PurchaseRequisitionTrait::getComparePriceList($purchase_requisition_approve->id);
        // $purchase_order_lines = PurchaseRequisitionTrait::getPRDealerLine($purchase_requisition_approve->id);
        $page_title = __('lang.view') . __('purchase_requisitions.page_title');
        return view('admin.purchase-requisition-approve.view', [
            'd' => $purchase_requisition_approve,
            'page_title' => $page_title,
            'rental_type_list' => $rental_type_list,
            'rental_images_files' => $rental_images_files,
            'refer_images_files' => $refer_images_files,
            'quotation_files' => $quotation_files,
            'replacement_approve_files' => $replacement_approve_files,
            'parent_list' => $parent_list,
            'parent_no' => $parent_no,
            'pr_car_list' => $pr_car_list,
            'car_accessory' => $car_accessory,
            'reference_name' => $reference_name,
            'purchase_requisition_cars' => $purchase_requisition_cars,
            'purchase_order_dealer_list' => $compare_price_list,
            'approve_images_files' => $approve_images_files,
            // 'purchase_order_lines' => $purchase_order_lines
            // 'approve_line_list' => $approve_line_list,
            // 'approve' => $approve,
            'approve_line_owner' => $approve_line_owner,
            // 'approve_line_logs' => $approve_line_logs,
        ]);
    }

    public function updatePurchaseRequisitionStatus(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::PurchaseRequisitionApprove);
        if (in_array($request->pr_status, [PRStatusEnum::CANCEL, PRStatusEnum::REJECT])) {
            $validator = Validator::make($request->all(), [
                'reason' => [
                    'required', 'max:255'
                ],
            ], [], [
                'reason' => __('lang.reason'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }
        $user = Auth::user();
        if (!empty($request->purchase_requisitions)) {
            foreach ($request->purchase_requisitions as $purchase_requisition_id) {
                $purchase_requisition = PurchaseRequisition::find($purchase_requisition_id);
                if (in_array($request->pr_status, [
                    PRStatusEnum::CONFIRM,
                    PRStatusEnum::CANCEL,
                    PRStatusEnum::REJECT,
                    PRStatusEnum::COMPLETE,
                ])) {
                    if (!in_array($request->pr_status, [PRStatusEnum::CANCEL, PRStatusEnum::COMPLETE])) {
                        // update approve step
                        $approve_update = new StepApproveManagement();
                        //fixed
                        $approve_update = $approve_update->updateApprove(PurchaseRequisition::class, $purchase_requisition->id, $request->pr_status, ConfigApproveTypeEnum::PURCHASE_REQUISITION, $request->reason);
                        // $approve_update = $approve_update->updateApprove($request, $purchase_requisition, $request->pr_status, PurchaseRequisition::class);
                    }

                    $purchase_requisition->status = isset($approve_update) ? $approve_update : $request->pr_status;
                    if (strcmp($request->pr_status, PRStatusEnum::CANCEL) == 0) {
                        $purchase_requisition->cancel_reason = $request->reason;
                    }
                    if (strcmp($request->pr_status, PRStatusEnum::REJECT) == 0) {

                        $purchase_requisition->reject_reason = $request->reason;
                        $this->sendNotificationPrReject($purchase_requisition, $purchase_requisition->pr_no);
                    }
                    if (strcmp($request->pr_status, PRStatusEnum::CONFIRM) == 0) {
                        //                         $this->genneratePurchaseOrder($purchase_requisition);
                        NotificationTrait::sendNotificationPrApprove($purchase_requisition->id, $purchase_requisition, $purchase_requisition->pr_no, 'success');
                    }
                    if (in_array($request->pr_status, [PRStatusEnum::CONFIRM, PRStatusEnum::REJECT])) {
                        $purchase_requisition->reviewed_by = $user->id;
                        $purchase_requisition->reviewed_at = date('Y-m-d H:i:s');
                    }
                    $purchase_requisition->save();
                    // if (
                    //     strcmp($request->pr_status, PRStatusEnum::CONFIRM) === 0
                    //     && $purchase_requisition->reference_type == LongTermRental::class
                    // ) {
                    //     $this->createPurchaseOrderOfLongTermRental($purchase_requisition, $purchase_requisition->reference_id);
                    // }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'ok',
                'redirect' => $request->redirect,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found'),
                'redirect' => $request->redirect,
            ]);
        }
    }

    public function updateApprove($data, $purchase_requisition)
    {
        $approve = Approve::where('job_type', PurchaseRequisition::class)->where('job_id', $purchase_requisition->id)->first();

        if ($data->approve_line_id) {
            $approve_line_not_check = ApproveLine::find($data->approve_line_id);
            $approve_line_not_check->is_pass = $data->pr_status === InstallEquipmentPOStatusEnum::CONFIRM ? STATUS_ACTIVE : STATUS_DEFAULT;
            $approve_line_not_check->save();
            // dd('2');
            if ($approve) {
                // dd('3');
                $approve_line = ApproveLine::where('approve_id', $approve->id)->where('is_pass', null)->whereNotIn('seq', [STATUS_DEFAULT])->orderBy('seq', 'asc')->first();

                $approve_log = new ApproveLog();
                $approve_log->approve_id = $approve_line_not_check->approve_id;
                $approve_log->approve_line_id = $data->approve_line_id;
                $approve_log_count = ApproveLog::where('approve_id', $approve->id)->count();
                if ($approve_log_count > 0) {
                    $approve_log->seq = $approve_log_count + 1;
                } else {
                    $approve_log->seq = 1;
                }
                $approve_log->user_id = Auth::user()->id;
                $approve_log->approved_date = Carbon::now();
                $approve_log->status = $data->pr_status;
                $approve_log->reason = $data->reason ? $data->reason : null;
                $approve_log->save();
            }
            if ($approve_line) {
                $approve->status_state = $approve_line->seq;
                $approve->save();
            }

            $super_user_approve = null;
            if ($approve_line_not_check->is_super_user == STATUS_ACTIVE && $approve_line_not_check->is_pass == STATUS_ACTIVE) {
                $approve->status = $data->pr_status;
                $approve->save();
                $super_user_approve = true;
            } else if ($approve_line_not_check->is_super_user == STATUS_ACTIVE && $approve_line_not_check->is_pass == STATUS_DEFAULT) {
                $approve->status = $data->pr_status;
                $approve->save();
                $super_user_approve = false;
            }
        }

        $status_install = InstallEquipmentPOStatusEnum::PENDING_REVIEW;
        $check_all_pass = ApproveLine::where('approve_id', $approve->id)->whereNotIn('seq', [STATUS_DEFAULT])->get();
        if (count($check_all_pass) == count($check_all_pass->where('is_pass', STATUS_ACTIVE)) || $super_user_approve === true) {
            $status_install = InstallEquipmentPOStatusEnum::CONFIRM;
        } else if ($data->pr_status === InstallEquipmentPOStatusEnum::REJECT || $super_user_approve === false) {
            $status_install = InstallEquipmentPOStatusEnum::REJECT;
        }

        return $status_install;
    }

    public function sendNotificationPrReject($modelPurchaseRequisition, $dataPrNo)
    {
        $dataDepartment = [
            DepartmentEnum::PCD_PURCHASE,
        ];
        $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
        $url = route('admin.purchase-requisition-approve.show', ['purchase_requisition_approve' => $modelPurchaseRequisition]);
        $notiTypeChange = new NotificationManagement('ไม่อนุมัติใบขอซื้อ', 'ใบขอซื้อ ' . $dataPrNo . ' ไม่ได้รับการอนุมัติ ', $url, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, [], 'danger');
        $notiTypeChange->send();
    }

    public function genneratePurchaseOrder($purchase_requisition)
    {
        $rental_type_list = $this->getRentalType();
        $to_day = date('Y-m-d');
        $purchase_order = new PurchaseOrder();
        $po_count = PurchaseOrder::all()->count() + 1;
        $prefix = 'PO';

        $purchase_order->po_no = generateRecordNumber($prefix, $po_count);
        $purchase_order->status = POStatusEnum::DRAFT;
        $purchase_order->request_date = date('Y-m-d');
        $purchase_order->pr_id = $purchase_requisition->id;
        $purchase_order->request_date = $to_day;
        $purchase_order->save();
        return true;
    }

    public function createPurchaseOrderOfLongTermRental($purchase_requisition, $long_term_rental_id)
    {
        $d = new PurchaseOrder();
        $po_count = PurchaseOrder::all()->count() + 1;
        $prefix = 'PO';
        $d->po_no = generateRecordNumber($prefix, $po_count);
        $d->status = POStatusEnum::DRAFT;
        $d->request_date = $purchase_requisition->request_date;
        $d->require_date = $purchase_requisition->require_date;
        $d->pr_id = $purchase_requisition->id;
        $d->save();
        $pr_lines = PurchaseRequisitionTrait::getPRCar($purchase_requisition->id);

        $prcheck = [];
        foreach ($pr_lines as $key => $pr_line) {
            $prcheck[$pr_line->id] = false;
        }

        $option = [];
        $option['item_type'] = LongTermRental::class;
        $lt_compare_price_list = PurchaseRequisitionTrait::getComparePriceList($long_term_rental_id, $option);
        foreach ($lt_compare_price_list as $key => $lt_compare_price) {
            $compare_price = new ComparisonPrice();
            $compare_price->item_type = PurchaseOrder::class;
            $compare_price->item_id = $d->id;
            $compare_price->creditor_id = $lt_compare_price->creditor_id;
            $compare_price->subtotal = $lt_compare_price->subtotal;
            $compare_price->discount = $lt_compare_price->discount;
            $compare_price->vat = $lt_compare_price->vat;
            $compare_price->total = $lt_compare_price->total;
            $compare_price->save();
            $medias = $lt_compare_price->getMedia('comparison_price');
            if ($medias) {
                foreach ($medias as $media) {
                    $copied_media = $media->copy($compare_price, 'comparison_price');
                }
            }
            foreach ($lt_compare_price->dealer_price_list as $key => $lt_compare_price_line) {
                $lt_line = LongTermRentalLine::find($lt_compare_price_line->item_id);
                $lt_car_class_id = $lt_line->car_class_id;
                $pr_line = $pr_lines->where('car_id', $lt_car_class_id)->first();
                if ($pr_line) {
                    if (isset($prcheck[$pr_line->id])) {
                        $prcheck[$pr_line->id] = true;
                    }
                    $compare_price_line = new ComparisonPriceLine();
                    $compare_price_line->comparison_price_id = $compare_price->id;
                    $compare_price_line->item_type = PurchaseRequisitionLine::class;
                    $compare_price_line->item_id = $pr_line->id;
                    $compare_price_line->name = $lt_compare_price_line->name;
                    $compare_price_line->amount = $lt_compare_price_line->amount;
                    $compare_price_line->subtotal = $lt_compare_price_line->subtotal;
                    $compare_price_line->discount = $lt_compare_price_line->discount;
                    $compare_price_line->vat = $lt_compare_price_line->vat;
                    $compare_price_line->total = $lt_compare_price_line->total;
                    $compare_price_line->save();
                }
            }
            foreach ($prcheck as $pr_line_id => $value) {
                if ($value == false) {
                    $pr_line = $pr_lines->where('id', $pr_line_id)->first();
                    if ($pr_line) {
                        $compare_price_line = new ComparisonPriceLine();
                        $compare_price_line->comparison_price_id = $compare_price->id;
                        $compare_price_line->item_type = PurchaseRequisitionLine::class;
                        $compare_price_line->item_id = $pr_line_id;
                        $compare_price_line->name = null;
                        $compare_price_line->amount = $pr_line->amount;
                        $compare_price_line->subtotal = 0;
                        $compare_price_line->discount = 0;
                        $compare_price_line->vat = 0;
                        $compare_price_line->total = 0;
                        $compare_price_line->save();
                    }
                }
            }
        }
        return true;
    }
}
