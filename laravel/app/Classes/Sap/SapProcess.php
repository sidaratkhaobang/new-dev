<?php

namespace App\Classes\Sap;

use App\Classes\Sap\SapModel;
use App\Enums\SAPAccountTypeEnum;
use App\Enums\SAPStatusEnum;
use App\Enums\SAPTransferSubTypeEnum;
use App\Enums\SAPTransferTypeEnum;
use App\Models\Branch;
use App\Models\Customer;
use App\Enums\CustomerTypeEnum;
use App\Enums\GLAccountEnum;
use App\Models\OrderPromotionCodeLine;
use App\Models\ProductAdditional;
use App\Models\PromotionCode;
use App\Models\Rental;
use App\Models\DrivingJob;
use App\Enums\SAPInterfaceLineTypeEnum;
use App\Models\Car;
use App\Models\Quotation;
use App\Models\RentalBill;
use App\Traits\RentalTrait;
use Carbon\Carbon;
use App\Models\OrderPromotionCode;
use App\Models\Promotion;
use App\Models\Receipt;
use App\Enums\ReceiptStatusEnum;
use App\Models\RentalLine;
use Exception;
use Illuminate\Support\Facades\Log;

class SapProcess extends SapTransaction
{
    use SapHelper, RentalTrait;

    function afterPaymentBeforeService($data_id, $class_name, $params = [])
    {
        // check data type
        $param1_text = '';
        $param3_text = '';
        $require_check_withholding_tax = false;
        $withholding_tax = false;
        if (strcmp($class_name, Rental::class) == 0) {
            $rental = $this->getRental($data_id);
            $branch_id = $rental->branch_id;
            $payment_gateway_type = $rental->payment_gateway;
            $customer_id = $rental->customer_id;
            $vat = $this->roundNumber(floatval($rental->vat));
            $total = $this->roundNumber(floatval($rental->total));
            $posting_date = date('Y-m-d');
            $document_date = date('Y-m-d', strtotime($rental->payment_date));
            $sap_tranfer_type = SAPTransferTypeEnum::CASH_SALE_S_RENTAL;
            $param1_text = RentalTrait::getPaymentGateWayDetailForSAP($rental);
            $param3_text = RentalTrait::getLicensePlateRentalCars($rental->id);
            $require_check_withholding_tax = true;
            $withholding_tax = boolval($rental->is_withholding_tax);
            $reference_document = $this->getReceiptWorksheetNo($rental->id, Rental::class);
        } else if (strcmp($class_name, OrderPromotionCode::class) == 0) {
            $order_promotion_code = $this->getOrderPromotion($data_id);
            $branch_id = null;
            $payment_gateway_type = $params['payment_gateway'];
            $customer_id = $order_promotion_code->customer_id;
            $vat = $this->roundNumber(floatval($order_promotion_code->vat));
            $total = $this->roundNumber(floatval($order_promotion_code->amount));
            $posting_date = date('Y-m-d', strtotime($params['payment_date']));
            $document_date = date('Y-m-d');
            $sap_tranfer_type = SAPTransferTypeEnum::CASH_SALE_COUPON;
            $reference_document = $this->getReceiptWorksheetNo($order_promotion_code->id, OrderPromotionCode::class);
            $param1_text = "2c2p";
        } else {
            throw new Exception('Invalid object', 0);
        }

        // default params
        $document_type = 'DN';
        $branch_number = '0500';
        if ($branch_id) {
            $branch_number = Branch::where('id', $branch_id)->first()->code;
        }
        $header_text = 'REV.RENTAL CAR';
        $reference_document = ($reference_document) ? $reference_document : 'เลขที่ใบเสร็จ';

        // get GL
        $bank_gl_account = $this->getBankGLAccount($payment_gateway_type);

        // check WHT
        $is_wht = false;
        $customer = Customer::where('id', $customer_id)->first();
        $customer_type = ($customer) ?  $customer->customer_type : null;
        $customer_name = ($customer) ? $customer->name : null;
        if (strcmp($class_name, Rental::class) == 0) {
            $rental = $this->getRental($data_id);
            $customer_name = $rental->customer_name;
        }
        if (in_array($customer_type, [CustomerTypeEnum::CORPORATION])) {
            if ($require_check_withholding_tax) {
                $is_wht = $withholding_tax;
            }
        }

        $total_excl_vat = $this->roundNumber($total - $vat);
        $wht_total = 0;
        if ($is_wht) {
            $wht_total = $this->roundNumber(($total_excl_vat * 5 / 100));
        }
        $total_without_wht = $this->roundNumber($total - $wht_total);

        // $total_exclude_vat = $temp_total - $this->vat;
        // if ($this->is_withholding_tax) {;
        //     $this->withholding_tax = (5 / 100) * $total_exclude_vat;
        // } else {
        //     $this->withholding_tax = 0;
        // }
        // $this->total = $temp_total - $this->withholding_tax;

        $param1 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
        $param1->account_no = $bank_gl_account;
        $param1->amount_in_document = $total;
        $param1->assignment = $customer_name;
        $param1->text = $param1_text;
        $this->addDrLine($param1);

        if ($is_wht) {
            $param2 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
            $param2->account_no = GLAccountEnum::WHT;
            $param2->amount_in_document = $wht_total;
            $param2->assignment = $customer_name;
            $param2->text = $customer_name;
            $this->addDrLine($param2);
        }

        if ((strcmp($class_name, OrderPromotionCode::class) == 0)) {
            if (is_array($params['promotion_code'])) {
                // api
                foreach ($params['promotion_code'] as $promotion_code) {
                    $branch_number = Promotion::leftJoin('branches', 'branches.id', '=', 'promotions.branch_id')->where('promotions.id', $promotion_code['id'])->first()->code;
                    $param3 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
                    $param3->account_no = GLAccountEnum::DEFERRED_INCOME;
                    $param3->amount_in_document = $total_excl_vat / $params['count_promotion_code'];
                    $param3->assignment = $customer_name;
                    $param3->text = $promotion_code['code'];
                    $this->addCrLine($param3);
                }
            } else {
                // manual
                $branch_number = Promotion::leftJoin('branches', 'branches.id', '=', 'promotions.branch_id')->where('promotions.id', $params['promotion_id'])->first()->code;
                $param3 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
                $param3->account_no = GLAccountEnum::DEFERRED_INCOME;
                $param3->amount_in_document = $total_excl_vat;
                $param3->assignment = $customer_name;
                $param3->text = $params['promotion_code'];
                $this->addCrLine($param3);
            }
        }

        if (strcmp($class_name, Rental::class) == 0) {
            $rental = $this->getRental($data_id);
            $rental_cars = RentalTrait::getRentalLineCars($rental->id);
            $car_amount = count($rental_cars);
            $base_amount = 0;
            foreach ($rental_cars as $key => $rental_car) {
                $param3 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
                $param3->account_no = GLAccountEnum::DEFERRED_INCOME;
                $amount_in_document = RentalTrait::calculatePricePerEachRentalCar($rental, $rental_car);
                $param3->amount_in_document = $amount_in_document;
                $param3->assignment = $customer_name;
                $param3->text = $rental_car->car->license_plate . '/'
                    . get_date_time_by_format($rental->pickup_date, 'dmY') . '-'
                    . get_date_time_by_format($rental->return_date, 'dmY');
                $this->addCrLine($param3);
                $base_amount += $amount_in_document;
            }
            $total = $base_amount;
        }

        $param4 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
        $param4->account_no = GLAccountEnum::OUTPUT_TAX;
        $param4->amount_in_document = $vat;
        $param4->base_amount = $total;
        $param4->tax_code = 'O7';
        $param4->assignment = $reference_document;
        $param4->text = $customer_name;
        $this->addCrLine($param4, SAPInterfaceLineTypeEnum::OUTPUT_TAX);
        $this->generateSAPTransactions(SAPAccountTypeEnum::AR, $sap_tranfer_type, SAPTransferSubTypeEnum::AFTER_PAYMENT, $document_type, SAPStatusEnum::PENDING);
    }

    function startService($rental_id, $driving_job_id)
    {
        $rental = $this->getRental($rental_id);
        $rental_bill = RentalTrait::getRentalBillPrimaryByRentalId($rental_id);
        $branch_id = $rental->branch_id;
        $payment_gateway_type = $rental->payment_gateway;
        $customer_id = $rental->customer_id;
        $vat = $this->roundNumber(floatval($rental_bill->vat));
        $total = $this->roundNumber(floatval($rental_bill->total));

        $driving_job = DrivingJob::select('actual_rented_date')->where('id', $driving_job_id)->first();
        $posting_date = date('Y-m-d');
        $document_date = date('Y-m-d', strtotime($driving_job->actual_rented_date));
        // default params
        $document_type = 'DM';
        $branch = Branch::where('id', $branch_id)->first();
        $branch_number = $branch->code;
        $cost_center = $branch->cost_center;
        $header_text = 'REV.RENTAL CAR';
        $customer = Customer::where('id', $customer_id)->first();
        $customer_name = $rental->customer_name;

        $total_excl_vat = $this->roundNumber($total - $vat);
        $service_gl_account = $this->getServiceGLAccount($rental_id, $branch_id, $customer_id);

        $rental_car_lines = RentalTrait::getRentalLineCars($rental_id);
        $rental_car_license_plates = RentalTrait::getLicensePlateRentalCars($rental_id);
        $total_discount = $rental_bill->discount + $rental_bill->coupon_discount;
        $total_discount_per_each_car = $total_discount / count($rental_car_lines);
        if ($total > 0) {
            $reference_document = $this->getReceiptWorksheetNo($rental->id, Rental::class);
            foreach ($rental_car_lines as $key => $rental_car_line) {
                $param1 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
                $param1->account_no = GLAccountEnum::DEFERRED_INCOME;
                $total_price_per_each_car = RentalTrait::calculatePricePerEachRentalCar($rental, $rental_car_line);
                $param1->amount_in_document = $this->roundNumber($total_price_per_each_car - $total_discount_per_each_car);
                $param1->cost_center = '';
                $param1->assignment = $customer_name;
                $param1->text = $rental_car_line->car->license_plate . '/'
                    . get_date_time_by_format($rental->pickup_date, 'dmY') . '-'
                    . get_date_time_by_format($rental->return_date, 'dmY');
                $this->addDrLine($param1);

                $param2 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
                $param2->account_no = GLAccountEnum::INCOME;
                $param2->amount_in_document = $this->roundNumber($total_price_per_each_car - $total_discount_per_each_car);
                $param2->assignment = 'เลขสัญญา';
                $param2->cost_center = $cost_center;
                $param2->text = $rental_car_line->car->license_plate . '/'
                    . get_date_time_by_format($rental->pickup_date, 'dmY') . '-'
                    . get_date_time_by_format($rental->return_date, 'dmY');
                $this->addCrLine($param2, SAPInterfaceLineTypeEnum::INCOME);
            }
        }

        $rental_vouchers = RentalTrait::getSelectedVoucher($rental_bill->id);
        foreach ($rental_vouchers as $key => $rental_voucher) {
            $order_promotion_code = RentalTrait::getOrderPromotionCode($rental->customer_id, $rental_voucher);
            $reference_document = $this->getReceiptWorksheetNo($order_promotion_code->order_promotion_code_id, OrderPromotionCode::class);
            $param1 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
            $param1->account_no = GLAccountEnum::DEFERRED_INCOME;
            $param1->amount_in_document = $this->roundNumber(getPriceExcludeVat($order_promotion_code->total));
            $param1->cost_center = '';
            $param1->assignment = $customer_name;
            $promotion_code = PromotionCode::find($order_promotion_code->promotion_code_id);
            $param1->text = $promotion_code->code;
            $this->addDrLine($param1);

            $param2 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
            $param2->account_no = GLAccountEnum::INCOME;
            $param2->amount_in_document = $this->roundNumber(getPriceExcludeVat($order_promotion_code->total));
            $param2->assignment = 'เลขสัญญา';
            $param2->cost_center = $cost_center;
            $param2->text = $promotion_code->code;
            $this->addCrLine($param2, SAPInterfaceLineTypeEnum::INCOME);
        }
        $this->generateSAPTransactions(SAPAccountTypeEnum::AR, SAPTransferTypeEnum::CASH_SALE_S_RENTAL, SAPTransferSubTypeEnum::START_SERVICE, $document_type, SAPStatusEnum::PENDING);
    }

    function startServiceCouponsCommerce($rental_bill_id)
    {
        $rental_bill = $this->getRentalBill($rental_bill_id);
        $rental = Rental::find($rental_bill->rental_id);
        $branch_id = $rental_bill->rental->branch_id;
        $customer_id = $rental_bill->rental->customer_id;
        $posting_date = date('Y-m-d');
        $document_date = date('Y-m-d', strtotime($rental_bill->payment_date));
        $sap_tranfer_type = SAPTransferTypeEnum::CASH_SALE_COUPON;
        $rental_id = $rental_bill->rental_id;
        // default params
        $document_type = 'DM';
        $branch = Branch::where('id', $branch_id)->first();
        $branch_number = $branch->code;
        $cost_center = $branch->cost_center;
        $header_text = 'REV.RENTAL CAR';
        $reference_document = 'เลขที่ใบเสร็จ';
        $customer_name = $rental->customer_name;

        $promotion_code_arr = RentalTrait::getSelectedVoucher($rental_bill_id);
        $promo_code = [];
        foreach ($promotion_code_arr as $key => $promotion_code) {
            $promotion_code = PromotionCode::find($promotion_code);
            $promo_code[] = $promotion_code->code;
        }
        $text = implode(", ", $promo_code);
        $sum_promotion_code_total = OrderPromotionCodeLine::whereIn('promotion_code_id', $promotion_code_arr)->sum('total');

        $service_gl_account = $this->getServiceGLAccount($rental_id, $branch_id, $customer_id);

        $param1 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
        $param1->account_no = GLAccountEnum::DEFERRED_INCOME;
        $param1->cost_center = $cost_center;
        $param1->amount_in_document = $sum_promotion_code_total;
        $param1->assignment = $customer_name;
        $param1->text = $text;

        $param2 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
        $param2->account_no = GLAccountEnum::INCOME;
        $param2->amount_in_document = $sum_promotion_code_total;
        $param2->cost_center = $cost_center;
        $param2->assignment = 'เลขสัญญา';
        $param2->text = 'ทะเบียนรถ';

        $this->addDrLine($param1);
        $this->addCrLine($param2);
        $this->generateSAPTransactions(SAPAccountTypeEnum::AR, $sap_tranfer_type, SAPTransferSubTypeEnum::START_SERVICE, $document_type, SAPStatusEnum::PENDING);
        return true;
    }

    function afterServiceInform($rental)
    {
        if (empty($rental)) {
            throw new Exception('Empty rental', 0);
        }

        if (!is_a($rental, Rental::class)) {
            throw new Exception('Invalid object', 0);
        }

        $branch_id = $rental->branch_id;
        $customer_id = $rental->customer_id;
        $total = $this->roundNumber(floatval($rental->total));
        $vat = $this->roundNumber(floatval($rental->vat));
        $posting_date = date('Y-m-d');
        if ($rental->updated_at) {
            $document_date = Carbon::parse($rental->updated_at)->format('Y-m-d');
        } else {
            $document_date = Carbon::now()->format('Y-m-d');
        }

        // default params
        $document_type = 'D1';
        $branch_number = Branch::where('id', $branch_id)->first()->code;
        $branch_cost_center = Branch::where('id', $branch_id)->first()->cost_center;
        $header_text = 'REV.RENTAL CAR';
        $reference_document = 'เลขที่ Invoice';

        $customer = Customer::where('id', $customer_id)->first();
        $customer_name = $customer->name;
        $service_gl_account = $this->getServiceGLAccount($rental->id, $branch_id, $customer_id);

        $param1 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
        $param1->account_no = GLAccountEnum::TRADE_RECEIVABLE;
        $param1->amount_in_document = $total;
        $param1->assignment = 'เลขที่ inv';
        $param1->text = $customer_name;
        $this->addDrLine($param1);

        $rental_car_lines = RentalTrait::getRentalLineCars($rental->id);
        foreach ($rental_car_lines as $key => $rental_car_line) {
            $param2 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
            $param2->account_no = $service_gl_account;
            $total_price_per_each_car = RentalTrait::calculatePricePerEachRentalCar($rental, $rental_car_line);
            $param1->amount_in_document = $this->roundNumber($total_price_per_each_car);
            $param2->amount_in_document = $total - $vat;
            $param2->cost_center = $branch_cost_center;
            $param2->assignment = 'เลขสัญญา';
            $param2->text = $rental_car_line->car->license_plate . '/'
                . get_date_time_by_format($rental->pickup_date, 'dmY') . '-'
                . get_date_time_by_format($rental->return_date, 'dmY');
            $this->addCrLine($param2);
        }

        $param3 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
        $param3->account_no = GLAccountEnum::DEFERRED_OUTPUT_TAX;
        $param3->tax_code = 'R7';
        $param3->amount_in_document = $vat;
        $param3->base_amount = $total - $vat;
        $param3->text = $customer_name;

        $this->addCrLine($param3, SAPInterfaceLineTypeEnum::DEFERRED_OUTPUT_TAX);
        $this->generateSAPTransactions(SAPAccountTypeEnum::AR, SAPTransferTypeEnum::CASH_SALE_S_RENTAL, SAPTransferSubTypeEnum::AFTER_SERVICE_INFORM, $document_type, SAPStatusEnum::PENDING);
    }


    function afterServicePaid($data_id, $class_name, $params = [])
    {
        // check data type
        if (strcmp($class_name, Rental::class) == 0) {
            $rental = $this->getRental($data_id);
            $branch_id = $rental->branch_id;
            $payment_gateway_type = $rental->payment_gateway;
            $customer_id = $rental->customer_id;
            $vat = $this->roundNumber(floatval($rental->vat));
            $total = $this->roundNumber(floatval($rental->total));
            $posting_date = date('Y-m-d');
            $document_date = date('Y-m-d', strtotime($rental->payment_date));
            $reference_document = $this->getReceiptWorksheetNo($rental->id, Rental::class);
        } else {
            throw new Exception('Invalid object', 0);
        }

        // default params
        $document_type = 'DN';
        $branch_number = Branch::where('id', $branch_id)->first()->code;
        $header_text = 'REV.RENTAL CAR';
        $reference_document = ($reference_document) ? $reference_document : 'เลขที่ใบเสร็จ';

        // get GL
        $bank_gl_account = $this->getBankGLAccount($payment_gateway_type);

        // check WHT
        $is_wht = false;
        $customer = Customer::where('id', $customer_id)->first();
        $customer_type = $customer->customer_type;
        $customer_name = $customer->name;
        if (in_array($customer_type, [CustomerTypeEnum::CORPORATION]) && (boolval($rental->is_withholding_tax))) {
            $is_wht = true;
        }

        $total_excl_vat = $this->roundNumber($total - $vat);
        $wht_total = 0;
        if ($is_wht) {
            $wht_total = $this->roundNumber(($total_excl_vat * 5 / 100));
        }
        $total_without_wht = $this->roundNumber($total - $wht_total);

        // Dr. บัญชีธนาคาร -S/A SCB
        $param1 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
        $param1->account_no = $bank_gl_account;
        $param1->amount_in_document = $total_without_wht;
        $param1->assignment = $customer_name;
        $param1->text = RentalTrait::getPaymentGateWayDetailForSAP($rental);

        if ($is_wht) {
            // Dr.ภาษีถูกหัก ณ.ที่จ่าย(WHT)
            $param2 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
            $param2->account_no = GLAccountEnum::WHT;
            $param2->amount_in_document = $wht_total;
            $param2->assignment = $customer_name;
            $param2->text = $customer_name;
        }

        // Cr. ลูกหนี้
        $param3 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
        $param3->account_no = GLAccountEnum::RECEIVABLE;
        $param3->amount_in_document = $total;
        $param3->assignment = 'เลขที่ inv';
        $param3->text = $customer_name;

        // Dr. Deferred ภาษีขาย
        $param4 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
        $param4->account_no = GLAccountEnum::DEFERRED_OUTPUT_TAX;
        $param4->amount_in_document = $vat;
        $param4->tax_code = 'R7';
        $param4->base_amount = $total_excl_vat;
        $param4->text = $customer_name;

        // Cr. ภาษีขาย
        $param5 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
        $param5->account_no = GLAccountEnum::OUTPUT_TAX;
        $param5->amount_in_document = $vat;
        $param5->base_amount = $total_excl_vat;
        $param5->tax_code = 'O7';
        $param5->assignment = $reference_document;
        $param5->text = $customer_name;

        $this->addDrLine($param1);
        if ($is_wht) {
            $this->addDrLine($param2);
        }
        $this->addCrLine($param3);
        $this->addDrLine($param4);
        $this->addCrLine($param5);
        $this->generateSAPTransactions(SAPAccountTypeEnum::AR, SAPTransferTypeEnum::CASH_SALE_S_RENTAL, SAPTransferSubTypeEnum::AFTER_SERVICE_PAID, $document_type, SAPStatusEnum::PENDING);
    }

    function afterExpiredCoupon($promotion_code_arr)
    {
        // default params
        $posting_date = date('Y-m-d'); // now
        $document_date = date('Y-m-d', strtotime("-1 days")); //date('Y-m-d', strtotime($rental_bill->payment_date)); วันที่สิ้นสุดการใช้งาน
        $document_type = 'DM';
        $branch_number = '0500';
        $header_text = 'REV.RENTAL CAR';
        $reference_document = 'เลขที่ใบเสร็จ';

        // promotion code text
        $promo_code = [];
        foreach ($promotion_code_arr as $key => $promotion_code) {
            $promotion_code = PromotionCode::find($promotion_code);
            // $promo_code[] = $promotion_code->code;

            // $text = implode(", ", $promo_code);

            // sum total order promotion code
            $order_promotion_code_line = OrderPromotionCodeLine::where('promotion_code_id', $promotion_code->id)->first();
            $branch_expire = Promotion::leftJoin('branches', 'branches.id', '=', 'promotions.branch_expired_id')->where('promotions.id', $promotion_code->promotion_id)->select('branches.cost_center')->first();
            $branch_number = Promotion::leftJoin('branches', 'branches.id', '=', 'promotions.branch_id')->where('promotions.id', $promotion_code->promotion_id)->first()->code;
            $customer_text = '';
            $order_promotion_code = OrderPromotionCode::find($order_promotion_code_line->order_promotion_code_id);
            if ($order_promotion_code && $order_promotion_code->customer_id) {
                $customer = Customer::find($order_promotion_code->customer_id);
                $customer_text = $customer->name;
            }
            //Dr. รายได้รับล่วงหน้า
            $param1 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
            $param1->account_no = GLAccountEnum::DEFERRED_INCOME;
            $param1->amount_in_document = $order_promotion_code_line->total - $order_promotion_code_line->vat;
            $param1->assignment = $customer_text;
            $param1->text = $promotion_code->code;

            //Cr. รายได้
            $param2 = new SapModel($posting_date, $document_date, $document_type, $branch_number, $reference_document, $header_text);
            $param2->account_no = GLAccountEnum::INCOME;
            $param2->amount_in_document = $order_promotion_code_line->total - $order_promotion_code_line->vat;
            $param2->cost_center = $branch_expire->cost_center;
            $param2->assignment = 'เลขสัญญา';
            $param2->text = $promotion_code->code . ' คูปองหมดอายุ';

            $this->addDrLine($param1);
            $this->addCrLine($param2);
        }
        $this->generateSAPTransactions(SAPAccountTypeEnum::AR, SAPTransferTypeEnum::CASH_SALE_COUPON, SAPTransferSubTypeEnum::EXPIRED_COUPON, $document_type, SAPStatusEnum::PENDING);
    }
}
