<?php

namespace App\Traits;

use App\Models\Rental;
use App\Models\RentalBill;
use App\Models\OrderPromotionCode;
use App\Models\Customer;
use App\Models\CustomerBillingAddress;
use App\Models\Receipt;
use App\Enums\ReceiptTypeEnum;
use App\Enums\RentalBillTypeEnum;
use Illuminate\Http\Request;

trait ReceiptTrait
{
    public static function generateReceipt($reference_id, $receipt_type, $rental_bill_id = null)
    {
        $receipt_count = Receipt::all()->count() + 1;
        $prefix = '';
        $receipt = new Receipt();
        $receipt->worksheet_no = generateRecordNumber($prefix, $receipt_count, false);

        if (in_array($receipt_type, [ReceiptTypeEnum::CAR_RENTAL, ReceiptTypeEnum::OTHER])) {
            $rental_bill = RentalBill::find($rental_bill_id);
            if ($rental_bill) {
                $receipt->rental_bill_id = $rental_bill_id;
                $receipt->reference_id = $reference_id;
                $receipt->reference_type = Rental::class;
                $receipt->receipt_type = $receipt_type;

                $rental = Rental::find($reference_id);
                $customer = Customer::find($rental->customer_id);
                if ((strcmp($rental_bill->check_customer_address, BOOL_FALSE) == 0)) {
                    // if don't check address false data billind address customer in rental bill
                    $customer_billing_address = CustomerBillingAddress::find($rental_bill->customer_billing_address_id);
                    if ($customer_billing_address) {
                        $receipt->customer_id = ($customer_billing_address->customer_id) ? $customer_billing_address->customer_id : null;
                        $receipt->customer_name = ($customer_billing_address->name) ? $customer_billing_address->name : null;
                        $receipt->customer_address = ($customer_billing_address->address) ? $customer_billing_address->address : null;
                        $receipt->customer_tel = ($customer_billing_address->tel) ? $customer_billing_address->tel : null;
                        $receipt->customer_email = ($customer_billing_address->email) ? $customer_billing_address->email : null;
                        $receipt->customer_code = ($customer->customer_code) ? $customer->customer_code : null;
                        $receipt->customer_tax_no = ($customer_billing_address->tax_no) ? $customer_billing_address->tax_no : null;
                    }
                } else {
                    // if check address true data customer dup rental or customer billing address null
                    if ($customer) {
                        $receipt->customer_id = ($rental->customer_id) ? $rental->customer_id : null;
                        $receipt->customer_name = ($rental->customer_name) ? $rental->customer_name : null;
                        $receipt->customer_address = ($rental->customer_address) ? $rental->customer_address : null;
                        $receipt->customer_tel = ($rental->customer_tel) ? $rental->customer_tel : null;
                        $receipt->customer_email = ($rental->customer_email) ? $rental->customer_email : null;
                        $receipt->customer_code = ($customer->customer_code) ? $customer->customer_code : null;
                        $receipt->customer_tax_no = ($customer->tax_no) ? $customer->tax_no : null;
                    }
                }

                $receipt->subtotal = $rental_bill->subtotal;
                $receipt->vat = $rental_bill->vat;
                $receipt->withholding_tax = $rental_bill->withholding_tax;
                $receipt->total = $rental_bill->total;
                $receipt->save();
                if ((strcmp($receipt_type, ReceiptTypeEnum::CAR_RENTAL) == 0) && strcmp($rental_bill->bill_type, RentalBillTypeEnum::PRIMARY) == 0) {
                    $rental->receipt_no = $receipt->worksheet_no;
                    $rental->save();
                }
                $rental_bill->receipt_id = $receipt->id;
                $rental_bill->save();
            }
        } else if (strcmp($receipt_type, ReceiptTypeEnum::VOUCHER_OF_CASH) == 0) {
            $order_promotion_code = OrderPromotionCode::find($reference_id);
            if ($order_promotion_code) {
                $customer = Customer::find($order_promotion_code->customer_id);
                $receipt->reference_id = $reference_id;
                $receipt->reference_type = OrderPromotionCode::class;
                $receipt->receipt_type = $receipt_type;

                if ((strcmp($order_promotion_code->check_customer_address, BOOL_FALSE) == 0)) {
                    // if don't check address false data billind address customer in order promotion code
                    $customer_billing_address = CustomerBillingAddress::find($order_promotion_code->customer_billing_address_id);
                    if ($customer_billing_address) {
                        $receipt->customer_id = ($customer_billing_address->customer_id) ? $customer_billing_address->customer_id : null;
                        $receipt->customer_name = ($customer_billing_address->name) ? $customer_billing_address->name : null;
                        $receipt->customer_address = ($customer_billing_address->address) ? $customer_billing_address->address : null;
                        $receipt->customer_tel = ($customer_billing_address->tel) ? $customer_billing_address->tel : null;
                        $receipt->customer_email = ($customer_billing_address->email) ? $customer_billing_address->email : null;
                        $receipt->customer_code = ($customer->customer_code) ? $customer->customer_code : null;
                        $receipt->customer_tax_no = ($customer_billing_address->tax_no) ? $customer_billing_address->tax_no : null;
                    }
                } else {
                    // if check address true data customer dup order promotion code or customer billing address null
                    if ($customer) {
                        $receipt->customer_id = ($order_promotion_code->customer_id) ? $order_promotion_code->customer_id : null;
                        $receipt->customer_name = ($customer->name) ? $customer->name : null;
                        $receipt->customer_address = ($customer->address) ? $customer->address : null;
                        $receipt->customer_tel = ($customer->tel) ? $customer->tel : null;
                        $receipt->customer_email = ($customer->email) ? $customer->email : null;
                        $receipt->customer_code = ($customer->customer_code) ? $customer->customer_code : null;
                        $receipt->customer_tax_no = ($customer->tax_no) ? $customer->tax_no : null;
                    }
                }

                $receipt->subtotal = ($order_promotion_code->amount - $order_promotion_code->vat);
                $receipt->vat = $order_promotion_code->vat;
                $receipt->total = $order_promotion_code->amount;
                $receipt->save();

                $order_promotion_code->receipt_id = $receipt->id;
                $order_promotion_code->save();
            }
        } else {
            //
        }
    }
}
