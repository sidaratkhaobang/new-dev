<?php

namespace App\Http\Controllers\Admin;

use App\Classes\NotificationManagement;
use App\Classes\StepApproveManagement;
use App\Enums\Actions;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\DepartmentEnum;
use App\Enums\ImportCarStatusEnum;
use App\Enums\NotificationScopeEnum;
use App\Enums\POStatusEnum;
use App\Enums\PRStatusEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\ComparisonPrice;
use App\Models\ComparisonPriceLine;
use App\Models\ImportCar;
use App\Models\ImportCarLine;
use App\Models\LongTermRental;
use App\Models\LongTermRentalLine;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionLine;
use App\Models\PurchaseRequisitionLineAccessory;
use App\Models\Rental;
use App\Traits\HistoryTrait;
use App\Traits\NotificationTrait;
use App\Traits\PurchaseRequisitionTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PurchaseOrderController extends Controller
{
    use PurchaseRequisitionTrait;

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::PurchaseOrder);
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
            ->whereIn('purchase_requisitions.status', [PRStatusEnum::CONFIRM, PRStatusEnum::COMPLETE])
            ->selectRaw(
                '
                purchase_orders.id,
                purchase_orders.po_no,
                purchase_orders.require_date,
                purchase_orders.status,
                purchase_requisitions.pr_no as pr_no,
                purchase_requisitions.rental_type as rental_type,
                rentals.worksheet_no as st_worksheet_no,
                lt_rentals.worksheet_no as lt_worksheet_no,
                SUM(purchase_requisition_lines.amount) as total_amount'
            )
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
            $item->rental_type = ($rental_type) ? $rental_type->name : '';
            return $item;
        });
        $page_title = __('purchase_orders.all_purchase_orders');
        return view('admin.purchase-orders.index', [
            's' => $s,
            'list' => $list,
            'page_title' => $page_title,
            'purchase_order_no' => $purchase_order_no,
            'purchase_requisition_no' => $purchase_requisition_no,
            'car_category_id' => $car_category_id,
            'status' => $status,
            'from_delivery_date' => $from_delivery_date,
            'to_delivery_date' => $to_delivery_date,
            'rental_type_list' => $rental_type_list,
            'rental_type' => $rental_type,
            'status_list' => $status_list
        ]);
    }

    public static function getPurchaseOrderStatus()
    {
        $status = collect([
            (object)[
                'id' => POStatusEnum::DRAFT,
                'name' => __('purchase_orders.status_' . POStatusEnum::DRAFT),
                'value' => POStatusEnum::DRAFT,
            ],
            (object)[
                'id' => POStatusEnum::PENDING_REVIEW,
                'name' => __('purchase_orders.status_' . POStatusEnum::PENDING_REVIEW),
                'value' => POStatusEnum::PENDING_REVIEW,
            ],
            (object)[
                'id' => POStatusEnum::CONFIRM,
                'name' => __('purchase_orders.status_' . POStatusEnum::CONFIRM),
                'value' => POStatusEnum::CONFIRM,
            ],
            (object)[
                'id' => POStatusEnum::REJECT,
                'name' => __('purchase_orders.status_' . POStatusEnum::REJECT),
                'value' => POStatusEnum::REJECT,
            ],
            (object)[
                'id' => POStatusEnum::CANCEL,
                'name' => __('purchase_orders.status_' . POStatusEnum::CANCEL),
                'value' => POStatusEnum::CANCEL,
            ],
            (object)[
                'id' => POStatusEnum::COMPLETE,
                'name' => __('purchase_orders.status_' . POStatusEnum::COMPLETE),
                'value' => POStatusEnum::COMPLETE,
            ],
        ]);
        return $status;
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::PurchaseOrder);
        abort(404);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::PurchaseOrder);
        $step_approve_management = new StepApproveManagement();
        $is_configured = $step_approve_management->isApproveConfigured(ConfigApproveTypeEnum::PURCHASE_ORDER);
        if (!$is_configured) {
            return $this->responseWithCode(false, __('lang.config_approve_warning') . __('purchase_orders.page_title'), null, 422);
        }

        $request->merge(['summary_price_total_float' => transform_float($request->summary_price_total)]);
        $custom_rules = [
            'ordered_creditor_id' => 'required',
            'pr_dealer' => 'required|array',
            'pr_dealer.*' => 'required',
            'pr_dealer.*.creditor_id' => 'required',
            'pr_dealer.*.dealer_price_list' => 'required|array',
            'summary_price_total_float' => 'required|numeric|min:1|max:99999999.99',
        ];
        $ordered_dealers = $request->selected_cars;
        $empty_car_price = true;
        foreach ($ordered_dealers as $key => $item) {
            if (!empty($item['price'])) {
                $empty_car_price = false;
            }
        }

        if ($empty_car_price) {
            $custom_rules['selected_cars_price'] = 'required';
        }

        $validator = Validator::make($request->all(), $custom_rules, [], [
            'ordered_creditor_id' => __('purchase_orders.dealer'),
            'selected_cars_price' => __('purchase_orders.price_required'),
            'selected_cars_price_value' => __('purchase_orders.min_price'),
            'summary_price_total_float' => __('purchase_orders.summary_price_total'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        if ($request->pr_id) {
            $pr_car_class = $this->savePRCar($request, $request->pr_id);
        }

        DB::transaction(function () use ($request, $ordered_dealers) {
            $purchase_order = PurchaseOrder::firstOrNew(['id' => $request->id]);
            $po_count = PurchaseOrder::all()->count() + 1;
            $prefix = 'PO';
            if (!($purchase_order->exists)) {
                $purchase_order->po_no = generateRecordNumber($prefix, $po_count);
                $purchase_order->status = POStatusEnum::DRAFT;
                $purchase_order->request_date = date('Y-m-d');
            }
            $purchase_order->pr_id = $request->pr_id;
            $purchase_order->require_date = $request->require_date;
            $purchase_order->remark = $request->remark;
            $purchase_order->time_of_delivery = $request->time_of_delivery;
            $purchase_order->payment_condition = $request->payment_condition;
            $purchase_order->creditor_id = $request->ordered_creditor_id;
            $purchase_order->total = $request->summary_price_total;
            $purchase_order->vat = $request->summary_vat_total;
            $purchase_order->discount = $request->summary_discount_total;
            $purchase_order->subtotal = (float)$request->summary_price_total - (float)$request->summary_vat_total;
            if ($request->status_updated) {
                $purchase_order->status = POStatusEnum::PENDING_REVIEW;
            }
            if ($request->quotation_files__pending_delete_ids) {
                $pending_delete_ids = $request->quotation_files__pending_delete_ids;
                if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                    foreach ($pending_delete_ids as $media_id) {
                        $purchase_order->deleteMedia($media_id);
                    }
                }
            }

            if ($request->hasFile('quotation_files')) {
                foreach ($request->file('quotation_files') as $file) {
                    if ($file->isValid()) {
                        $purchase_order->addMedia($file)->toMediaCollection('quotation_files');
                    }
                }
            }
            $purchase_order->save();
            $purchase_order_dealers = $request->pr_dealer;
            $cars = $request->cars;
            $comparison_price_ids = ComparisonPrice::where('item_type', PurchaseRequisition::class)
                ->where('item_id', $purchase_order->pr_id)->pluck('id')->toArray();

            ComparisonPriceLine::whereIn('comparison_price_id', $comparison_price_ids)->delete();
            $this->deleteDealerMedias($request);
            foreach ($purchase_order_dealers as $key => $purchase_order_dealer) {
                $compare_price = ComparisonPrice::firstOrNew(['id' => $purchase_order_dealer['id']]);
                $compare_price->item_id = $purchase_order->id;
                $compare_price->item_type = PurchaseOrder::class;
                $compare_price->creditor_id = $purchase_order_dealer['creditor_id'];
                $dealer_price_list = $purchase_order_dealer['dealer_price_list'];

                $sum_dealer_price_subtotal = 0;
                $sum_dealer_price_vat = 0;
                $sum_dealer_price_discount = 0;
                $sum_dealer_price_total = 0;
                $compare_price->save();

                if ((!empty($request->dealer_files)) && (sizeof($request->dealer_files) > 0)) {
                    $all_dealer_files = $request->dealer_files;
                    if (isset($all_dealer_files[$key])) {
                        $compare_price_dealer_files = $all_dealer_files[$key];
                        foreach ($compare_price_dealer_files as $compare_price_dealer_file) {
                            if ($compare_price_dealer_file) {
                                $compare_price->addMedia($compare_price_dealer_file)->toMediaCollection('comparison_price');
                            }
                        }
                    }
                }

                foreach ($dealer_price_list as $key => $dealer_price) {
                    $car_amount = $cars['amount'][$dealer_price['car_id']];
                    $compare_price_line = new ComparisonPriceLine();
                    $compare_price_line->comparison_price_id = $compare_price->id;
                    $compare_price_line->item_id = $dealer_price['car_id'];
                    $compare_price_line->item_type = PurchaseRequisitionLine::class;
                    $compare_price_line->amount = $car_amount;
                    $compare_price_line->subtotal = $dealer_price['vat_exclude'];
                    $compare_price_line->vat = $dealer_price['vat'];
                    $compare_price_line->discount = $dealer_price['discount'];
                    $compare_price_line->total = $dealer_price['car_price'];
                    $compare_price_line->save();
                    $sum_dealer_price_subtotal += $dealer_price['vat_exclude'] * $car_amount;
                    $sum_dealer_price_vat += $dealer_price['vat'] * $car_amount;
                    $sum_dealer_price_discount += $dealer_price['discount'] * $car_amount;
                    $sum_dealer_price_total += $dealer_price['car_price'] * $car_amount;
                }
                $compare_price = ComparisonPrice::find($compare_price->id);
                $compare_price->subtotal = $sum_dealer_price_subtotal;
                $compare_price->vat = $sum_dealer_price_vat;
                $compare_price->discount = $sum_dealer_price_discount;
                $compare_price->total = $sum_dealer_price_total;
                $compare_price->save();
            }

            PurchaseOrderLine::where('purchase_order_id', $purchase_order->id)->delete();
            foreach ($ordered_dealers as $key => $ordered_dealer) {
                $car_amount = isset($ordered_dealer['car_amount']) ? $ordered_dealer['car_amount'] : null;
                if ($car_amount && $car_amount > 0) {
                    $purchase_order_line = new PurchaseOrderLine();
                    $purchase_order_line->purchase_order_id = $purchase_order->id;
                    $purchase_order_line->pr_line_id = $key;
                    $purchase_order_line->name = $ordered_dealer['name'];
                    $purchase_order_line->amount = $car_amount;
                    $purchase_order_line->vat = $ordered_dealer['vat'];
                    $purchase_order_line->total = $ordered_dealer['price'];
                    $purchase_order_line->discount = $ordered_dealer['discount'];
                    $purchase_order_line->subtotal = $ordered_dealer['price'] - $ordered_dealer['vat'];
                    $purchase_order_line->save();
                }
            }

            $po_check = PurchaseOrder::find($request->id);
            if (!$po_check) {
                $config_type_enum = ConfigApproveTypeEnum::PURCHASE_ORDER;
                $model_type = PurchaseOrder::class;
                $model_id = $purchase_order->id;
                $step_approve_management = new StepApproveManagement();
                $step_approve_management->createModelApproval($config_type_enum, $model_type, $model_id);

            }
            if (empty($request->id)) {
                NotificationTrait::sendNotificationPrOpenOrder($purchase_order->id, $purchase_order, $purchase_order->purchaseRequisiton->pr_no);
            }
        });

        $redirect_route = route('admin.purchase-orders.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    private function savePRCar($request, $purchase_requisition_id)
    {
        // dd($purchase_requisition_id);
        $pr_line = PurchaseRequisitionLine::where('purchase_requisition_id', $purchase_requisition_id)->get();
        // dd($pr_line);
        if (!empty($pr_line)) {
            foreach ($pr_line as $car_index => $pr_car_line) {
                // $pr_car = new PurchaseRequisitionLine();
                // $pr_car->purchase_requisition_id = $purchase_requisition_id;
                // $pr_car->car_class_id = $request_pr_car['car_class_id'];
                // $pr_car->car_color_id = $request_pr_car['car_color_id'];
                // $pr_car->amount = $request_pr_car['amount_car'];
                // $pr_car->remark = $request_pr_car['remark_car'];
                // $pr_car->save();

                if (!empty($request->accessories)) {
                    $pr_car_accessory = $this->savePRAccessory($request, $pr_car_line, $car_index);
                }
            }
        }
    }

    private function savePRAccessory($request, $pr_car, $car_index)
    {
        PurchaseRequisitionLineAccessory::where('purchase_requisition_line_id', $pr_car->id)->delete();
        if (!empty($request->accessories)) {
            foreach ($request->accessories as $accessory_index => $accessories) {
                if (strcmp($accessories['car_index'], $car_index) == 0) {
                    $pr_car_accessory = new PurchaseRequisitionLineAccessory();
                    $pr_car_accessory->purchase_requisition_line_id = $pr_car->id;
                    $pr_car_accessory->accessory_id = $accessories['accessory_id'];
                    $pr_car_accessory->amount = intval($accessories['accessory_amount']);
                    $pr_car_accessory->remark = $accessories['remark_accessory'];
                    $pr_car_accessory->type_accessories = $accessories['type_accessories'];
                    $pr_car_accessory->save();
                }
            }
        }

        return true;
    }

    private function deleteDealerMedias($request)
    {
        $delete_dealer_ids = $request->delete_dealer_ids;
        if ((!empty($delete_dealer_ids)) && (is_array($delete_dealer_ids))) {
            foreach ($delete_dealer_ids as $delete_id) {
                $campare_price_delete = ComparisonPrice::find($delete_id);
                $campare_price_medias = $campare_price_delete->getMedia('comparison_price');
                foreach ($campare_price_medias as $campare_price_media) {
                    $campare_price_media->delete();
                }
                $campare_price_delete->delete();
            }
        }

        $pending_delete_dealer_files = $request->pending_delete_dealer_files;
        if ((!empty($pending_delete_dealer_files)) && (sizeof($pending_delete_dealer_files) > 0)) {
            foreach ($pending_delete_dealer_files as $dealer_media_id) {
                $dealer_media = Media::find($dealer_media_id);
                if ($dealer_media && $dealer_media->model_id) {
                    $comparison_price = ComparisonPrice::find($dealer_media->model_id);
                    $comparison_price->deleteMedia($dealer_media->id);
                }
            }
        }
        return true;
    }

    public function edit(PurchaseOrder $purchase_order)
    {
        $this->authorize(Actions::Manage . '_' . Resources::PurchaseOrder);
        $rental_type_list = PurchaseOrderOpenController::getRentalType();
        $purchase_requisition = PurchaseRequisition::find($purchase_order->pr_id);
        $rental_images_files = $purchase_requisition->getMedia('rental_images');
        $rental_images_files = get_medias_detail($rental_images_files);
        $refer_images_files = $purchase_requisition->getMedia('refer_images');
        $refer_images_files = get_medias_detail($refer_images_files);
        $quotation_files = $purchase_order->getMedia('quotation_files');
        $quotation_files = get_medias_detail($quotation_files);

        $purchase_requisition_cars = PurchaseRequisitionTrait::getPRCar($purchase_order->pr_id);
        $option = [];
        $option['item_type'] = PurchaseOrder::class;
        $compare_price_list = PurchaseRequisitionTrait::getComparePriceList($purchase_order->id, $option);
        foreach ($compare_price_list as $key => $compare_price) {
            if (sizeof($compare_price->dealer_price_list) > 0) {
                foreach ($compare_price->dealer_price_list as $key => $dealer_price) {
                    $lt_line = LongTermRentalLine::find($dealer_price->pr_line_id);
                    if ($lt_line) {
                        $pr_car = $purchase_requisition_cars->where('car_id', $lt_line->car_class_id)
                            ->where('color_id', $lt_line->car_color_id)->first();
                        if ($pr_car) {
                            $dealer_price->car_id = $pr_car->id;
                        }
                    }
                }
            }
        }
        $purchase_order_lines = $this->getPurchaseOrderLineByID($purchase_order->id);
        $payment_condition_list = PurchaseRequisitionTrait::getPaymentConditionList();
        $page_title = __('lang.edit') . __('purchase_orders.po');
        $redirect_route = route('admin.purchase-orders.index');

        $pr_car_list = PurchaseRequisitionLine::where('purchase_requisition_id', $purchase_requisition->id)->get();
        $pr_car_list->map(function ($item) {
            $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
            $item->car_color_text = ($item->color) ? $item->color->name : '';
            $item->amount_car = $item->amount;
            $item->remark_car = $item->remark;
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

        // approve log
        // $approve_line_list = new StepApproveManagement();
        // $approve_return = $approve_line_list->logApprove(PurchaseOrder::class, $purchase_order->id);
        // $approve_line_list = $approve_return['approve_line_list'];
        // $approve = $approve_return['approve'];
        // if (!is_null($approve_line_list)) {
        //     // can approve or super user
        //     $approve_line_owner = new StepApproveManagement();
        //     $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
        // } else {
        //     $approve_line_owner = null;
        // }
        $approve_line = HistoryTrait::getHistory(PurchaseOrder::class, $purchase_order->id);
        $approve_line_list = $approve_line['approve_line_list'];
        $approve = $approve_line['approve'];
        $approve_line_logs = $approve_line['approve_line_logs'];
        if (!is_null($approve_line_list)) {
            // can approve or super user
            $approve_line_owner = new StepApproveManagement();
            // $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
            $approve_line_owner = $approve_line_owner->checkCanApprove(PurchaseOrder::class, $purchase_order->id,ConfigApproveTypeEnum::PURCHASE_ORDER);
        } else {
            $approve_line_owner = null;
        }

        return view('admin.purchase-orders.form', [
            'd' => $purchase_order,
            'page_title' => $page_title,
            'rental_type_list' => $rental_type_list,
            'rental_images_files' => $rental_images_files,
            'refer_images_files' => $refer_images_files,
            'quotation_files' => $quotation_files,
            'purchase_requisition_cars' => $purchase_requisition_cars,
            'purchase_requisition_car_list' => $purchase_requisition_cars,
            'purchase_order_dealer_list' => $compare_price_list,
            'purchase_order_lines' => $purchase_order_lines,
            'purchase_requisition' => $purchase_requisition,
            'payment_condition_list' => $payment_condition_list,
            'redirect_route' => $redirect_route,
            'pr_car_list' => $pr_car_list,
            'car_accessory' => $car_accessory,
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
            'approve_line_owner' => $approve_line_owner,
            'approve_line_logs' => $approve_line_logs,
        ]);
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

    public function updatePurchaseOrderStatus(Request $request)
    {
        // TO DO
        if (in_array($request->purchase_order_status, [
            POStatusEnum::REJECT,
            // POStatusEnum::CANCEL,
        ])) {
            $validator = Validator::make($request->all(), [
                'reject_reason' => ['required', 'max:255'],
            ], [], [
                'reject_reason' => __('lang.reason')
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        if ($request->purchase_order_id) {
            // DB::transaction(function () use ($request) {
            $purchase_order = PurchaseOrder::find($request->purchase_order_id);

            if (in_array($request->purchase_order_status, [POStatusEnum::CANCEL])) {
                $purchase_order->status = $request->purchase_order_status;
                $purchase_order->reason = $request->reject_reason;
            } else {
                // update approve step
                $approve_update = new StepApproveManagement();
                // $approve_update = $approve_update->updateApprove($request, $purchase_order, $request->purchase_order_status, PurchaseOrder::class);
                //fixed
                $approve_update = $approve_update->updateApprove(PurchaseOrder::class, $purchase_order->id, $request->purchase_order_status,ConfigApproveTypeEnum::PURCHASE_ORDER,$request->reject_reason);

                $purchase_order->status = $approve_update;
                $purchase_order->reason = $request->reject_reason;
            }

            if (in_array($request->purchase_order_status, [POStatusEnum::CONFIRM, POStatusEnum::REJECT])) {
                $user = Auth::user();
                $purchase_order->reviewed_by = $user->id;
                $purchase_order->reviewed_at = date('Y-m-d H:i:s');
            }
            $purchase_order->save();

            $import_car = ImportCar::where('import_cars.po_id', $request->purchase_order_id)->first();
            if ($import_car) {
                if (in_array($request->purchase_order_status, [POStatusEnum::CANCEL])) {
                    $import_car->status = ImportCarStatusEnum::CANCEL;
                    $import_car->save();
                }
            }

            if (in_array($request->purchase_order_status, [POStatusEnum::CONFIRM])) {
                $this->saveDefaultImportCar($purchase_order);
                NotificationTrait::sendNotificationPrOpenOrder($purchase_order->id, $purchase_order, $purchase_order->purchaseRequisiton->pr_no, 'success');
            }
            if (in_array($request->purchase_order_status, [POStatusEnum::REJECT])) {
                $this->sendNotificationPrOpenOrderReject($purchase_order, $purchase_order->purchaseRequisiton->pr_no);
            }

            return response()->json([
                'success' => true,
                'message' => 'ok',
                'redirect' => ($request->redirect_route) ? $request->redirect_route : route('admin.purchase-orders.index')
            ]);
            // });
        } else {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found'),
                'redirect' => ($request->redirect_route) ? $request->redirect_route : route('admin.purchase-orders.index')
            ]);
        }
    }

    public function saveDefaultImportCar($model)
    {
        $import_car = new ImportCar();
        $import_car->po_id = $model->id;
        $import_car->created_at = $model->created_at;
        $import_car->save();

        $po_lines = PurchaseOrderLine::where('purchase_order_id', $model->id)->get();

        foreach ($po_lines as $key => $po_line) {
            for ($i = 0; $i < $po_line->amount; $i++) {
                $import_car_line = new ImportCarLine();
                $import_car_line->import_car_id = $import_car->id;
                $import_car_line->po_line_id = $po_line->id;
                $import_car_line->save();
            }
        }
        return true;
    }

    public function sendNotificationPrOpenOrderReject($modelPurchaseOrder, $dataPrOrderNo)
    {
        $dataDepartment = [
            DepartmentEnum::PCD_PURCHASE,
        ];
        $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
                $url = route('admin.purchase-order-approve.show', ['purchase_order_approve' => $modelPurchaseOrder]);
        $notiTypeChange = new NotificationManagement('ไม่อนุมัติใบสั่งซื้อ', 'ใบขอซื้อ ' . $dataPrOrderNo . ' ไม่ได้รับการอนุมัติ ', $url, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, [], 'danger');
        $notiTypeChange->send();
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::PurchaseOrder);
        $purchase_order = PurchaseOrder::find($id);
        $purchase_order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
            'redirect' => route('admin.purchase-orders.index')
        ]);
    }

    public function show(PurchaseOrder $purchase_order)
    {
        $this->authorize(Actions::View . '_' . Resources::PurchaseOrder);
        $rental_type_list = PurchaseOrderOpenController::getRentalType();
        $purchase_requisition = PurchaseRequisition::find($purchase_order->pr_id);
        $rental_images_files = $purchase_requisition->getMedia('rental_images');
        $rental_images_files = get_medias_detail($rental_images_files);
        $refer_images_files = $purchase_requisition->getMedia('refer_images');
        $refer_images_files = get_medias_detail($refer_images_files);
        $quotation_files = $purchase_order->getMedia('quotation_files');
        $quotation_files = get_medias_detail($quotation_files);

        $purchase_requisition_cars = PurchaseRequisitionTrait::getPRCar($purchase_order->pr_id);
        $option = [];
        $option['item_type'] = PurchaseOrder::class;
        $compare_price_list = PurchaseRequisitionTrait::getComparePriceList($purchase_order->id, $option);
        $purchase_order_lines = $this->getPurchaseOrderLineByID($purchase_order->id);
        $payment_condition_list = PurchaseRequisitionTrait::getPaymentConditionList();
        $page_title = __('lang.view') . __('purchase_orders.po');

        $pr_car_list = PurchaseRequisitionLine::where('purchase_requisition_id', $purchase_requisition->id)->get();
        $pr_car_list->map(function ($item) {
            $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
            $item->car_color_text = ($item->color) ? $item->color->name : '';
            $item->amount_car = $item->amount;
            $item->remark_car = $item->remark;
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

        // approve log
        // $approve_line_list = new StepApproveManagement();
        // $approve_return = $approve_line_list->logApprove(PurchaseOrder::class, $purchase_order->id);
        // $approve_line_list = $approve_return['approve_line_list'];
        // $approve = $approve_return['approve'];
        $approve_line = HistoryTrait::getHistory(PurchaseOrder::class, $purchase_order->id);
        $approve_line_list = $approve_line['approve_line_list'];
        $approve = $approve_line['approve'];
        $approve_line_logs = $approve_line['approve_line_logs'];
        if (!is_null($approve_line_list)) {
            // can approve or super user
            $approve_line_owner = new StepApproveManagement();
            $approve_line_owner = $approve_line_owner->checkCanApprove(PurchaseOrder::class, $purchase_order->id,ConfigApproveTypeEnum::PURCHASE_ORDER);

        } else {
            $approve_line_owner = null;
        }

        return view('admin.purchase-orders.view', [
            'd' => $purchase_order,
            'page_title' => $page_title,
            'view_detail' => true,
            'rental_type_list' => $rental_type_list,
            'rental_images_files' => $rental_images_files,
            'refer_images_files' => $refer_images_files,
            'quotation_files' => $quotation_files,
            'purchase_requisition' => $purchase_requisition,
            'purchase_requisition_cars' => $purchase_requisition_cars,
            'purchase_order_dealer_list' => $compare_price_list,
            'purchase_order_lines' => $purchase_order_lines,
            'payment_condition_list' => $payment_condition_list,
            'pr_car_list' => $pr_car_list,
            'car_accessory' => $car_accessory,
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
            'approve_line_logs' => $approve_line_logs,
        ]);
    }

    public function printPdf(Request $request)
    {
        $purchase_order_id = $request->purchase_order_id;
        $purchase_order = PurchaseOrder::find($request->purchase_order_id);
        $purchase_requisition = PurchaseRequisition::find($purchase_order->pr_id);
        $purchase_order_lines = $this->getPurchaseOrderLineByID($purchase_order_id);

        $rental_images_files = $purchase_requisition->getMedia('rental_images');
        $rental_images_files = get_medias_detail($rental_images_files);
        $refer_images_files = $purchase_requisition->getMedia('refer_images');
        $refer_images_files = get_medias_detail($refer_images_files);
        $quotation_files = $purchase_order->getMedia('quotation_files');
        $quotation_files = get_medias_detail($quotation_files);
        $purchase_requisition_cars = PurchaseRequisitionTrait::getPRCar($purchase_requisition->id);
        $total_car_amount = 0;
        foreach ($purchase_requisition_cars as $key => $cars) {
            $total_car_amount += $cars->amount;
        }
        $option = [];
        $option['creditor_id'] = $purchase_order->creditor_id;
        $option['item_type'] = Purchaseorder::class;
        $compare_price_list = PurchaseRequisitionTrait::getComparePriceList($purchase_order->id, $option);
        $compare_price = $compare_price_list->first();
        $sum_subtotal = 0;
        foreach ($purchase_order_lines as $key => $po_line) {
            $compare_price_data = $compare_price->dealer_price_list->where('car_id', $po_line->item_id)->first();
            $price_per_unit = ($compare_price_data->total) ? $compare_price_data->total : 0;
            $po_line->price_per_unit = $price_per_unit;
            $accessory_list = PurchaseRequisitionLineAccessory::where('purchase_requisition_line_id', $po_line->pr_line_id)->get();
            $po_line->accessories = $accessory_list;
            $sum_subtotal += ($price_per_unit * $po_line->amount);
        }

        $total_dealer_car_amount = 0;
        foreach ($purchase_order_lines as $key => $dealer_lines) {
            $total_dealer_car_amount += $dealer_lines->amount;
        }
        $rental_data = [];
        $request->rental_type = $purchase_requisition->rental_type;
        $request->rental_id = $purchase_requisition->reference_id;
        $rental_json = $this->getRentalTypeData($request);
        if (isset($rental_json['data']) && sizeof($rental_json['data']) > 0) {
            $rental_data = ($rental_json['data'][0]) ? $rental_json['data'][0]->toArray() : [];
        }

        $page_title = $purchase_order->po_no;
        $pdf = PDF::loadView(
            'admin.purchase-orders.component-pdf.pdf',
            [
                'purchase_order' => $purchase_order,
                'purchase_order_lines' => $purchase_order_lines,
                'purchase_requisition' => $purchase_requisition,
                'page_title' => $page_title,
                'rental_images_files' => $rental_images_files,
                'refer_images_files' => $refer_images_files,
                'quotation_files' => $quotation_files,
                'total_dealer_car_amount' => $total_dealer_car_amount,
                'purchase_order_dealer_list' => $compare_price_list,
                'purchase_requisition_cars' => $purchase_requisition_cars,
                'total_car_amount' => $total_car_amount,
                'rental_data' => $rental_data,
                'sum_subtotal' => $sum_subtotal
            ]
        );
        return $pdf->stream();
    }

    public function getRentalTypeData(Request $request)
    {
        $rental_type = $request->rental_type;
        $rental_id = $request->rental_id;
        $data = collect([]);
        if (strcmp($rental_type, RentalTypeEnum::SHORT) == 0) {
            $data = Rental::leftJoin('customers', 'customers.id', '=', 'rentals.customer_id')
                ->select(
                    'customers.customer_type',
                    'rentals.id',
                    'rentals.customer_name',
                    'rentals.worksheet_no',
                    'rentals.created_at as request_date',
                )
                ->where('rentals.id', $rental_id)
                ->get();
            $data->map(function ($item) {
                $item->customer_type = ($item->customer_type) ? __('customers.type_' . $item->customer_type) : null;
                return $item;
            });
        }

        if (strcmp($rental_type, RentalTypeEnum::LONG) == 0) {
            $data = LongTermRental::leftJoin('customers', 'customers.id', '=', 'lt_rentals.customer_id')
                ->select(
                    'customers.customer_type',
                    'lt_rentals.id',
                    'lt_rentals.customer_name',
                    'lt_rentals.job_type',
                    'lt_rentals.rental_duration',
                    'lt_rentals.worksheet_no',
                    'lt_rentals.created_at as request_date',
                )
                ->where('lt_rentals.id', $rental_id)
                ->get();
            $data->map(function ($item) {
                $item->customer_type = ($item->customer_type) ? __('customers.type_' . $item->customer_type) : null;
                $item->job_type = ($item->job_type) ? __('long_term_rentals.job_type_' . $item->job_type) : null;
                return $item;
            });
        }

        return [
            'success' => true,
            'rental_type' => $rental_type,
            'data' => $data
        ];
    }
}
