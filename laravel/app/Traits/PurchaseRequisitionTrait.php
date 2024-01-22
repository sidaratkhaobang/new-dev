<?php

namespace App\Traits;

use App\Enums\CustomerTypeEnum;
use App\Enums\PaymentOptionEnum;
use App\Models\ComparisonPrice;
use App\Models\ComparisonPriceLine;
use App\Models\PurchaseRequisition;
// use App\Models\PurchaseRequisitionDealerLine;
use App\Models\PurchaseRequisitionLine;
use Illuminate\Support\Facades\DB;

trait PurchaseRequisitionTrait
{

    static public function getPRCar($purchase_requisition_id)
    {
        $purchase_requisition_cars = PurchaseRequisitionLine::leftJoin('car_classes', 'car_classes.id', '=', 'purchase_requisition_lines.car_class_id')
            ->leftJoin('car_colors', 'car_colors.id', '=', 'purchase_requisition_lines.car_color_id')
            ->where('purchase_requisition_id', $purchase_requisition_id)
            ->select(
                'purchase_requisition_lines.id as id',
                'car_classes.name as model',
                'car_classes.id as car_id',
                'car_classes.full_name as model_full_name',
                'car_colors.id as color_id',
                'car_colors.name as color',
                'purchase_requisition_lines.amount as amount',
            )
            ->get();
        return $purchase_requisition_cars;
    }

    static public function getComparePriceList($purchase_requisition_id, $optional = [])
    {
        $compare_price_list = ComparisonPrice::where('item_id', $purchase_requisition_id)
            ->when(sizeof($optional) > 0, function ($query) use ($optional) {
                if (isset($optional['creditor_id'])) {
                    $query->where('creditor_id', $optional['creditor_id']);
                }
            })
            ->where(function ($query) use ($optional) {
                if (isset($optional['item_type'])) {
                    $query->where('item_type', $optional['item_type']);
                } else {
                    $query->where('item_type', PurchaseRequisition::class);
                }
            })
            ->get();

        $compare_price_list->map(function ($item) {
            $item->creditor_text = ($item->creditor) ? $item->creditor->name : '';
            $compare_price_line_list = ComparisonPriceLine::where('comparison_price_id', $item->id)->get();
            $compare_price_line_list->map(function ($compare_price_line) {
                $compare_price_line->car_id = $compare_price_line->item_id;
                $compare_price_line->pr_line_id = $compare_price_line->item_id;
                $compare_price_line->car_price = $compare_price_line->total;
                $compare_price_line->vat_exclude = $compare_price_line->subtotal;
                $compare_price_line->car_discount = $compare_price_line->discount;
            });

            $item->dealer_price_list = $compare_price_line_list;
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

    static public function getPaymentConditionList()
    {
        return collect([
            (object)[
                'id' => PaymentOptionEnum::CASH,
                'value' => PaymentOptionEnum::CASH,
                'name' => __('purchase_orders.payment_' . PaymentOptionEnum::CASH)
            ],
            (object)[
                'id' => PaymentOptionEnum::BILL,
                'value' => PaymentOptionEnum::BILL,
                'name' => __('purchase_orders.payment_' . PaymentOptionEnum::BILL)
            ],
        ]);
    }
}
