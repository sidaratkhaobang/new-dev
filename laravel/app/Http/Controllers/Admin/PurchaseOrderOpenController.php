<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\POStatusEnum;
use App\Enums\PRStatusEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\LongTermRental;
use App\Models\LongTermRentalLine;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionLine;
use App\Models\PurchaseRequisitionLineAccessory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ComparisonPrice;
use App\Models\ComparisonPriceLine;
use App\Models\Customer;
use App\Traits\PurchaseRequisitionTrait;

class PurchaseOrderOpenController extends Controller
{
    use PurchaseRequisitionTrait;

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::OpenPurchaseOrder);
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

        $sub_po = PurchaseOrder::select('pr_id', DB::raw(' sum(purchase_order_lines.amount) as po_amount '))
            ->join('purchase_order_lines', 'purchase_order_lines.purchase_order_id', '=', 'purchase_orders.id')
            ->whereNotIn('status', [POStatusEnum::REJECT, POStatusEnum::CANCEL])
            ->groupBy('pr_id');

        $sub_po2 = PurchaseOrder::select('pr_id', DB::raw(' count(purchase_orders.id) as po_count '))
            ->groupBy('pr_id');

        $list = PurchaseRequisition::sortable(['pr_no' => 'desc'])
            ->leftJoin('purchase_requisition_lines', 'purchase_requisition_lines.purchase_requisition_id', '=', 'purchase_requisitions.id')
            ->leftJoinSub($sub_po, 'sub_po', function ($join) {
                $join->on('purchase_requisitions.id', '=', 'sub_po.pr_id');
            })
            ->leftJoinSub($sub_po2, 'sub_po2', function ($join) {
                $join->on('purchase_requisitions.id', '=', 'sub_po2.pr_id');
            })
            ->where(function ($q) use ($rental_type, $status) {
                if ($rental_type) {
                    $q->where('purchase_requisitions.rental_type', $rental_type);
                }
                if ($status) {
                    $q->where('purchase_requisitions.status', $status);
                }
            })
            ->selectRaw('
                purchase_requisitions.id,
                purchase_requisitions.pr_no,
                purchase_requisitions.rental_type,
                purchase_requisitions.status,
                purchase_requisitions.require_date,
                SUM(purchase_requisition_lines.amount) as total_amount,
                sub_po.po_amount,
                sub_po2.po_count')
            ->groupBy(
                'purchase_requisitions.id',
                'purchase_requisitions.pr_no',
                'purchase_requisitions.rental_type',
                'purchase_requisitions.status',
                'purchase_requisitions.require_date',
                'sub_po.po_amount',
                'sub_po2.po_count'
            )
            ->whereIn('purchase_requisitions.status', [PRStatusEnum::CONFIRM, PRStatusEnum::COMPLETE])
            ->branch()
            ->search($request->s, $request)
            ->paginate(PER_PAGE);

        $pr_list = PurchaseRequisition::select('pr_no as name', 'id')->orderBy('pr_no')->get();
        $rental_type_list = $this->getRentalType();
        $status_list = $this->getStatus();

        return view('admin.purchase-order-open.index', [
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

    public function show(PurchaseRequisition $purchase_order_open)
    {
        $this->authorize(Actions::View . '_' . Resources::OpenPurchaseOrder);
        $request_date = date($purchase_order_open->request_date);
        $rental_type_list = $this->getRentalType();
        $rental_images_files = $purchase_order_open->getMedia('rental_images');
        $rental_images_files = get_medias_detail($rental_images_files);
        $refer_images_files = $purchase_order_open->getMedia('refer_images');
        $refer_images_files = get_medias_detail($refer_images_files);
        $approve_images_files = $purchase_order_open->getMedia('approve_images');
        $approve_images_files = get_medias_detail($approve_images_files);
        $replacement_approve_files = $purchase_order_open->getMedia('replacement_approve_files');
        $replacement_approve_files = get_medias_detail($replacement_approve_files);
        $parent_list = PurchaseRequisition::where('parent_id', $purchase_order_open->parent_id)->select('pr_no as name', 'id')->orderBy('pr_no')->get();

        $parent_no = null;
        if (!empty($purchase_order_open->parent_id)) {
            $parent = PurchaseRequisition::find($purchase_order_open->parent_id);
            if ($parent) {
                $parent_no = $parent->pr_no;
            }
        }

        $reference_name = ($purchase_order_open->reference) ? $purchase_order_open->reference->worksheet_no : null;

        $purchase_order_open->customer_type = null;
        if ($purchase_order_open->reference) {
            $customer = Customer::find($purchase_order_open->reference->customer_id);
            if (!empty($customer)) {
                $purchase_order_open->customer_type = $customer->customer_type;
            }
        }

        $pr_car_class_list = PurchaseRequisitionLine::where('purchase_requisition_id', $purchase_order_open->id)->get();
        $pr_car_class_list->map(function ($item) {
            $item->car_class_text = ($item->carClass) ? $item->carClass->name : '';
            $item->car_color_text = ($item->color) ? $item->color->name : '';
            $item->amount_car = $item->amount;
            return $item;
        });

        $pr_car_list = PurchaseRequisitionLine::where('purchase_requisition_id', $purchase_order_open->id)->get();
        $pr_car_list->map(function ($item) {
            $item->car_class_text = ($item->carClass) ? $item->carClass->name : '';
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

        $pr_car_list = PurchaseRequisitionLine::where('purchase_requisition_id', $purchase_order_open->id)->get();
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

        $purchase_order_list  = PurchaseOrder::with('purchaseOrderLines')
            ->where('purchase_orders.pr_id', $purchase_order_open->id)
            ->get();
        $page_title = __('purchase_requisitions.view');
        return view('admin.purchase-order-open.view',  [
            'd' => $purchase_order_open,
            'page_title' => $page_title,
            'request_date' => $request_date,
            'rental_type_list' => $rental_type_list,
            'rental_images_files' => $rental_images_files,
            'refer_images_files' => $refer_images_files,
            'approve_images_files' => $approve_images_files,
            'replacement_approve_files' => $replacement_approve_files,
            'parent_list' => $parent_list,
            'parent_no' => $parent_no,
            'pr_car_class_list' => $pr_car_class_list,
            'pr_car_list' => $pr_car_list,
            'purchase_order_list' => $purchase_order_list,
            'car_accessory' => $car_accessory,
            'reference_name' => $reference_name,
            'pr_car_list' => $pr_car_list,
        ]);
    }

    public static function getRentalType()
    {
        $rental_type = collect([
            (object) [
                'id' => RentalTypeEnum::SHORT,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::SHORT),
                'value' => RentalTypeEnum::SHORT,
            ],
            (object) [
                'id' => RentalTypeEnum::LONG,
                'name' => __('purchase_requisitions.rental_type_' . RentalTypeEnum::LONG),
                'value' => RentalTypeEnum::LONG,
            ],
            (object) [
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
            (object) [
                'id' => PRStatusEnum::CONFIRM,
                'name' => __('purchase_requisitions.status_pending_text'),
                'value' => PRStatusEnum::CONFIRM,
            ],
            (object) [
                'id' => PRStatusEnum::COMPLETE,
                'name' => __('purchase_requisitions.status_complete_text'),
                'value' => PRStatusEnum::COMPLETE,
            ],
        ]);
        return $status;
    }

    public function create(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::OpenPurchaseOrder);
        $rental_type_list = $this->getRentalType();
        $to_day = date('Y-m-d');

        $purchase_requisition_id = $request->purchase_requisition_id;
        $purchase_requisition = PurchaseRequisition::find($purchase_requisition_id);
        if (empty($purchase_requisition)) {
            return redirect()->route('admin.purchase-order-open.index');
        }

        $d = new PurchaseOrder();
        $d->pr_id = $purchase_requisition_id;
        $d->purchase_requisition_no = $purchase_requisition->pr_no;
        $d->purchase_requissition_date = $purchase_requisition->require_date;
        $d->request_date = $to_day;
        $d->delivery_date = $purchase_requisition->request_date;
        // recheck
        $d->request_date = $purchase_requisition->request_date;
        $d->require_date = $purchase_requisition->require_date;
        $d->purchase_requisition_remark = $purchase_requisition->remark;
        $rental_type = findObjectById($rental_type_list, $purchase_requisition->rental_type);
        $d->rental_type = ($rental_type) ? $rental_type->name : '';
        $purchase_requisition_cars = PurchaseRequisitionTrait::getPRCar($purchase_requisition_id);
        $page_title = __('purchase_orders.page_title');

        // load comparison price from Long Term
        $compare_price_list = [];
        if ($purchase_requisition->reference_type === LongTermRental::class) {
            $option = [];
            $option['item_type'] = LongTermRental::class;
            $compare_price_list = PurchaseRequisitionTrait::getComparePriceList($purchase_requisition->reference_id, $option);
        }

        // load lastest comparison price from PO
        $lastest_po = PurchaseOrder::where('pr_id', $purchase_requisition_id)->orderBy('created_at', 'DESC')->first();
        if ($lastest_po) {
            $option = [];
            $option['item_type'] = PurchaseOrder::class;
            $compare_price_list = PurchaseRequisitionTrait::getComparePriceList($lastest_po->id, $option);
        }

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

        $payment_condition_list = PurchaseRequisitionTrait::getPaymentConditionList();
        $rental_images_files = $purchase_requisition->getMedia('rental_images');
        $rental_images_files = get_medias_detail($rental_images_files);
        $refer_images_files = $purchase_requisition->getMedia('refer_images');
        $refer_images_files = get_medias_detail($refer_images_files);
        $quotation_files = $d->getMedia('quotation_files');
        $quotation_files = get_medias_detail($quotation_files);
        $redirect_route = route('admin.purchase-order-open.index');

        $pr_car_list = PurchaseRequisitionLine::where('purchase_requisition_id', $purchase_requisition_id)->get();
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
                    $car_accessory[$index]['type_accessories'] = $accessory_item->type_accessories;
                    $car_accessory[$index]['car_index'] = $car_index;
                    $index++;
                }
            }
        }
        return view('admin.purchase-orders.form', [
            'd' => $d,
            'page_title' => $page_title,
            'purchase_requisition_car_list' => $purchase_requisition_cars,
            'purchase_requisition_cars' => $purchase_requisition_cars,
            'purchase_order_dealer_list' => $compare_price_list,
            'purchase_order_lines' => [],
            'rental_type_list' => $rental_type_list,
            'purchase_requisition' => $purchase_requisition,
            'payment_condition_list' => $payment_condition_list,
            'rental_images_files' => $rental_images_files,
            'refer_images_files' => $refer_images_files,
            'quotation_files' => $quotation_files,
            'redirect_route' => $redirect_route,
            'pr_car_list' => $pr_car_list,
            'car_accessory' => $car_accessory,
            'mode' => MODE_CREATE
        ]);
    }
}
