<?php

namespace App\Http\Controllers;

use App\Classes\QuickPay;
use App\Classes\Sap\SapProcess;
use App\Enums\PaymentGatewayEnum;
use App\Enums\QuotationStatusEnum;
use App\Enums\RentalBillTypeEnum;
use App\Enums\RentalStatusEnum;
use App\Http\Controllers\Admin\ContractsController;
use App\Http\Controllers\Admin\ShortTermRentalSummaryController;
use App\Models\Rental;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use PHPUnit\Runner\Exception;
use App\Enums\ReceiptTypeEnum;
use App\Enums\ReceiptLineNameEnum;
use App\Models\Quotation;
use App\Traits\DayTrait;
use App\Factories\ReceiptFactory;

class QuickPayCallbackController extends Controller
{
    use DayTrait;

    function frontend(Request $request)
    {
        $paymentResponse = $request->paymentResponse;
        $response = base64_decode($paymentResponse);
        $response = json_decode($response, true);
        if (App::environment('local')) {
            $qp = new QuickPay();
            $qp->invoice_no = $response['invoiceNo'];
            $response = $qp->inquiry();
            if ($response['respCode'] === "0000") {
                $this->updatePayment($response);
            }
        }
        return view('payment-success');
    }

    function backend(Request $request)
    {
        $payload = new QuickPay();
        if ($request->payload) {
            $response = $payload->processPayload($request->payload);
            if (App::environment(['local', 'dev', 'uat'])) {
                Log::channel('sentry')->alert('QuickPay backend:response', $response);
            }
            if ($response['respCode'] === "0000") {
                $this->updatePayment($response);
            }
        } else {
            Log::channel('sentry')->alert('QuickPay backend:response', ['not found payload request']);
        }
    }

    function updatePayment($data)
    {
        $quotation_id = $data['userDefined1'];
        $model_class = $data['userDefined2'];
        $rental_id = $data['userDefined3'];
        if (strcmp($model_class, Quotation::class) === 0) {
            $quotation = $this->updateRentall($rental_id, $quotation_id, $data);
            if (!$quotation) {
                throw new Exception(DATA_NOT_FOUND, 0);
            }
            $rental = Rental::find($rental_id);
            if (!$rental) {
                throw new Exception(DATA_NOT_FOUND, 0);
            }

            $receipt_type = $rental->getReceiptType();
            $rcf = new ReceiptFactory($receipt_type, $rental, $rental);
            if (strcmp($receipt_type, ReceiptTypeEnum::TAX_INVOICE) == 0) {
                $rcf->customer = $rcf->formatCustomerObjectFromBilling($rental);
            }

            if ($quotation->qt_type == RentalBillTypeEnum::PRIMARY) {
                // gen receipt 2c2p primary
                ShortTermRentalSummaryController::createAutoModel($rental);
                ContractsController::createAutoContract($rental);  // auto create contract
                //ReceiptTrait::generateReceipt($rental_bill->rental_id, ReceiptTypeEnum::CAR_RENTAL, $rental_bill->id);
                $rcf->createWithLine($rental->id, Rental::class, ReceiptLineNameEnum::CAR_RENTAL);

                $sap = new SapProcess();
                $sap->afterPaymentBeforeService($rental->id, Rental::class);
            } else {
                // RentalBillTypeEnum::OTHER
                // gen receipt 2c2p other
                //ReceiptTrait::generateReceipt($rental_bill->rental_id, ReceiptTypeEnum::OTHER, $rental_bill->id);
                $rcf->createWithLine($rental->id, Rental::class, ReceiptLineNameEnum::OTHER);

                $sap = new SapProcess();
                $sap->afterServiceInform($rental);
                $sap = new SapProcess();
                $sap->afterServicePaid($rental->id, Rental::class);
            }
        }
    }

    function updateRentall($rental_id, $quotation_id, $data)
    {
        $date_time = strtotime($data['transactionDateTime']);
        $date_time = date('Y-m-d', $date_time);
        $rental = Rental::find($rental_id);
        if (!$rental || !in_array($rental->status, [RentalStatusEnum::PENDING])) {
            Log::channel('sentry')->error('updateRentall fail : rental', [
                'rental_id' => $rental_id,
                'data' => $data
            ]);
            return false;
        }

        $quotation = Quotation::find($quotation_id);
        if (!$quotation || !in_array($quotation->status, [QuotationStatusEnum::DRAFT, QuotationStatusEnum::PENDING_REVIEW, QuotationStatusEnum::CONFIRM])) {
            __log('updateRentall fail : quotation', [
                'quotation_id' => $quotation_id,
                'data' => $data
            ], 'error');
            return false;
        }
        if (boolval($quotation->is_paid)) {
            __log('updateRentall fail : quotation already paid', [
                'quotation_id' => $quotation_id,
                'data' => $data
            ], 'error');
            return false;
        }
        if (floatval($data['amount']) != floatval($quotation->total)) {
            __log('updateRentall fail : amount', [
                'quotation_id' => $quotation_id,
                'data' => $data
            ], 'error');
            return false;
        }
        if (!$this->isDateLessThan(date('Y-m-d'), $quotation->payment_expire_date)) {
            __log('updateRentall fail : payment_expire_date', [
                'quotation_id' => $quotation_id,
                'data' => $data
            ], 'error');
            return false;
        }

        $quotation->is_paid = true;
        $quotation->payment_date = $date_time;
        $quotation->payment_response_desc = serialize($data);
        $quotation->payment_gateway = PaymentGatewayEnum::APP_2C2P;
        $quotation->save();
        if (strcmp($quotation->qt_type, RentalBillTypeEnum::PRIMARY) === 0) {
            $rental->is_paid = true;
            $rental->payment_date = $date_time;
            $rental->quotation_id = $quotation_id;
            $rental->payment_response_desc = serialize($data);
            $rental->payment_gateway = PaymentGatewayEnum::APP_2C2P;
            $rental->status = RentalStatusEnum::PAID;
            $rental->save();
        }
        return $quotation;
    }
}
