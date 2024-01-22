<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ComparisonPriceStatusEnum;
use App\Enums\LongTermRentalStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Creditor;
use App\Models\LongTermRental;
use App\Traits\RentalTrait;
use App\Traits\PurchaseRequisitionTrait;
use Illuminate\Http\Request;

class LongTermRentalComparePriceApproveController extends LongTermRentalComparePriceController
{
    use RentalTrait, PurchaseRequisitionTrait;
    public function index(Request $request)
    {
        $s = $request->s;
        $worksheet_id = $request->worksheet_id;
        $worksheet_name = null;
        if ($worksheet_id) {
            $lt = LongTermRental::find($worksheet_id);
            $worksheet_name = $lt->worksheet_no;
        }
        $status = $request->status;
        $compare_price_status_list = RentalTrait::getComparePriceStatusList();
        $compare_price_status_list->shift();
        $list = $this->getLongTermRentalForApprove($request);

        return view('admin.long-term-rental-compare-price-approve.index', [
            'list' => $list,
            's' => $s,
            'status' => $status,
            'worksheet_id' => $worksheet_id,
            'worksheet_name' => $worksheet_name,
            'compare_price_status_list' => $compare_price_status_list
        ]);
    }

    public function show(LongTermRental $rental)
    {
        $cars = $this->getCarSpecAndEquipments($rental->id);
        $option['item_type'] = LongTermRental::class;
        $compare_price_list = PurchaseRequisitionTrait::getComparePriceList($rental->id, $option);
        $selected_dealer = Creditor::where('id', $rental->creditor_id)
            ->select('id', 'name')->first();
        if ($selected_dealer) {
            $selected_dealer = $selected_dealer->toArray();
        }
        $page_title = __('long_term_rentals.view_compare_price');
        return view('admin.long-term-rental-compare-price-approve.view', [
            'd' => $rental,
            'page_title' => $page_title,
            'purchase_requisition_cars' => $cars,
            'purchase_order_dealer_list' => $compare_price_list,
            'selected_dealer' => $selected_dealer
        ]);
    }

    public function getLongTermRentalForApprove($request)
    {
        $status = $request->status;
        $worksheet_id = $request->worksheet_id;
        $s = $request->s;

        $list = LongTermRental::select('id', 'worksheet_no', 'comparison_price_status')
            ->where('status', LongTermRentalStatusEnum::COMPARISON_PRICE)
            ->whereNotIn('comparison_price_status', [ComparisonPriceStatusEnum::DRAFT])
            ->when($status, function ($query) use ($status) {
                $query->where('comparison_price_status', $status);
            })
            ->when($s, function ($query) use ($s) {
                $query->where('worksheet_no', 'like', '%' . $s . '%');
            })
            ->when($worksheet_id, function ($query) use ($worksheet_id) {
                $query->where('id', $worksheet_id);
            })
            ->paginate(PER_PAGE);
        return $list;
    }

    public function updateComparePriceStatus(Request $request)
    {
        $rental = LongTermRental::find($request->rental_id);
        $rental->comparison_price_status = $request->compare_price_status;
        if ($request->compare_price_status == ComparisonPriceStatusEnum::CONFIRM) {
           $rental->status = LongTermRentalStatusEnum::QUOTATION;
        }
        $rental->save();
        return response()->json([
            'success' => 'ok',
            'message' => __('lang.store_success_message'),
            'redirect' => $request->redirect_route
        ]);
    }

}
