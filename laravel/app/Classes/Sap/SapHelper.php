<?php

namespace App\Classes\Sap;

use App\Enums\PaymentGatewayEnum;
use App\Models\Rental;
use App\Models\RentalBill;
use App\Models\RentalLine;
use App\Models\Product;
use App\Models\ProductGLAccount;
use App\Models\GLAccount;
use App\Models\CustomerGroupRelation;
use App\Models\GLAccountCustomerGroup;
use App\Models\OrderPromotionCode;
use App\Models\Quotation;
use App\Models\ReceiptLine;

trait SapHelper
{
    function getBankGLAccount($payment_gateway_type)
    {
        switch ($payment_gateway_type) {
            case PaymentGatewayEnum::BBL_EDC:
                return '102104080';
            case PaymentGatewayEnum::SCB_BILL_PAY:
                return '102114090';
            case PaymentGatewayEnum::APP_2C2P:
                return '102215040';
            case PaymentGatewayEnum::OMISE:
                return '110020010';
            case PaymentGatewayEnum::MC_PAYMENT:
                return '110020010';
            case PaymentGatewayEnum::CHEQUE:
                return '102215040';
        }
    }

    function getServiceGLAccount($rental_id, $branch_id, $customer_id)
    {
        $product_ids = RentalLine::where('rental_id', $rental_id)->where('item_type', Product::class)->pluck('item_id')->toArray();
        if (sizeof($product_ids) <= 0) {
            return null;
        }
        $product_id = $product_ids[0];
        $gl_account_ids = ProductGLAccount::where('product_id', $product_id)->pluck('gl_account_id')->toArray();

        $customer_group_ids = CustomerGroupRelation::where('customer_id', $customer_id)->pluck('customer_group_id')->toArray();
        $gl_account_ids_by_groups = GLAccountCustomerGroup::whereIn('customer_group_id', $customer_group_ids)->pluck('gl_account_id')->toArray();

        // 1. find by branch + group
        $gl_accounts = GLAccount::whereIn('id', $gl_account_ids)
            ->where('branch_id', $branch_id)
            ->whereIn('id', $gl_account_ids_by_groups)
            ->get();
        if (sizeof($gl_accounts) == 1) {
            $account = $gl_accounts[0]->account;
            return $account;
        }

        // 2. find by branch
        $gl_accounts = GLAccount::whereIn('id', $gl_account_ids)
            ->where('branch_id', $branch_id)
            ->get();
        if (sizeof($gl_accounts) == 1) {
            $account = $gl_accounts[0]->account;
            return $account;
        }

        // 3. find by group
        $gl_accounts = GLAccount::whereIn('id', $gl_account_ids)
            ->whereIn('id', $gl_account_ids_by_groups)
            ->get();
        if (sizeof($gl_accounts) == 1) {
            $account = $gl_accounts[0]->account;
            return $account;
        }

        // 4. find by product only
        $gl_accounts = GLAccount::whereIn('id', $gl_account_ids)
            ->get();
        if (sizeof($gl_accounts) == 1) {
            $account = $gl_accounts[0]->account;
            return $account;
        }

        // error // TODO
        return null;
    }

    function getRental($rental_id)
    {
        $rental = Rental::find($rental_id);
        return $rental;
    }

    function getRentalBill($rental_bill_id)
    {
        $rental_bill = RentalBill::with(['rental'])
            ->where('id', $rental_bill_id)->first();
        return $rental_bill;
    }

    function getReceiptWorksheetNo($reference_id, $reference_type)
    {
        $worksheet_no = null;
        $receipt_line = ReceiptLine::where('reference_type', $reference_type)->where('reference_id', $reference_id)->first();
        if ($receipt_line) {
            $receipt = $receipt_line->receipt;
            if ($receipt) {
                $worksheet_no = $receipt->worksheet_no;
            }
        }
        return $worksheet_no;
    }

    function roundNumber(float $wht_total)
    {
        return floatval(number_format($wht_total, 2, '.', ''));
    }

    function getOrderPromotion($order_promotion_code_id)
    {
        $order_promotion_code = OrderPromotionCode::where('id', $order_promotion_code_id)->first();
        return $order_promotion_code;
    }
}
