<?php

namespace App\Http\Controllers;

use App\Classes\Sap\SapProcess;
use App\Enums\PaymentGatewayEnum;
use App\Enums\RentalBillTypeEnum;
use App\Enums\RentalStatusEnum;
use App\Http\Controllers\Admin\ContractsController;
use App\Http\Controllers\Admin\ShortTermRentalSummaryController;
use App\Models\Quotation;
use App\Models\Rental;
use App\Models\SCBPaymentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Enums\ReceiptTypeEnum;
use Illuminate\Support\Facades\Log;
use App\Factories\ReceiptFactory;
use App\Enums\ReceiptLineNameEnum;

class SCBBillPaymentController extends Controller
{
    function verify(Request $request)
    {
        $type_request = 'verify';
        $payment_id = (string) Str::orderedUuid();
        $channel = ["ATM", "CDM", "PHON", "ENET", "TELE", "TELL", "PTNR"];

        // Check require data
        $validator = Validator::make($request->all(), [
            'request' => ['required'],
            'tranID' => ['required'],
            'tranDate' => ['required'],
            'channel' => ['required'],
            'account' => ['required'],
            'amount' => ['required'],
            'reference1' => ['required'],
            'reference2' => ['required'],
        ], [], []);

        if ($validator->stopOnFirstFailure()->fails()) {
            Log::channel('sentry')->debug('SCB Bill Payment (1)', $request->all());
            $response = $this->formatResponse("1000", "Invalid data", $request, $payment_id);
            $this->saveSCBPaymentLog($request, $response, $type_request);
            return $response;
        }

        if (!in_array($request->channel, $channel) || $request['request'] != "verify") {
            Log::channel('sentry')->debug('SCB Bill Payment (1)', $request->all());
            $response = $this->formatResponse("1000", "Invalid data", $request, $payment_id);
            $this->saveSCBPaymentLog($request, $response, $type_request);
            return $response;
        }

        $quotation = Quotation::where('ref_1', $request->reference1)
            ->where('ref_2', $request->reference2)
            ->where('total', $request->amount)
            ->where('reference_type', Rental::class)
            ->first();

        if (!$quotation) {
            $response = $this->formatResponse("1000", "Invalid data", $request, $payment_id);
            $this->saveSCBPaymentLog($request, $response, $type_request);
            return $response;
        }

        $rental = Rental::find($quotation->reference_id);
        if (!$rental) {
            $response = $this->formatResponse("1000", "Invalid data", $request, $payment_id);
            $this->saveSCBPaymentLog($request, $response, $type_request);
            return $response;
        }

        $tranDate = date('Y-m-d', strtotime($request->tranDate));
        $date = $quotation->updated_at;
        $days_to_add = 30; // simulate 30 days after updated_at quotation pdf
        $date = $date->addDays($days_to_add);
        $date = $date->format('Y-m-d');

        if ($quotation->qt_type == RentalBillTypeEnum::PRIMARY) {
            $date = date('Y-m-d', strtotime('-1 day', strtotime($rental->pickup_date)));
        }

        // Overdue case
        if ($tranDate > $date) {
            Log::channel('sentry')->debug('SCB Bill Payment (3)', $request->all());
            $response = $this->formatResponse("2002", "Over due", $request, $payment_id);
            $this->saveSCBPaymentLog($request, $response, $type_request);
            return $response;
        }

        if ($rental->status == RentalStatusEnum::PAID) {
            Log::channel('sentry')->debug('SCB Bill Payment (4)', $request->all());
            $response = $this->formatResponse("2001", "Duplicate transaction", $request, $payment_id);
            $this->saveSCBPaymentLog($request, $response, $type_request);
            return $response;
        }

        $response = $this->formatResponse("0000", "Success", $request, $payment_id);
        $this->saveSCBPaymentLog($request, $response, $type_request);
        return $response;
    }

    function confirm(Request $request)
    {
        $type_request = 'confirm';
        $data = $request->all();
        $payment_id = (string) Str::orderedUuid();
        $type = array(
            "confirm",
            "cancel"
        );

        $response = $this->formatResponse("0000", "Success", $request, $payment_id);
        if (!in_array($request['request'], $type)) {
            // Invalid type request
            Log::channel('sentry')->debug('SCB Bill Payment - confirm (1)', $request->all());
            $response = $this->formatResponse("1000", "Invalid data", $request, $payment_id);
            $this->saveSCBPaymentLog($request, $response, $type_request);
            return $response;
        }

        $quotation = Quotation::where('ref_1', $request->reference1)
            ->where('ref_2', $request->reference2)
            ->where('total', $request->amount)
            ->where('reference_type', Rental::class)
            ->first();
        if (!$quotation) {
            $response = $this->formatResponse("1000", "Invalid data", $request, $payment_id);
            $this->saveSCBPaymentLog($request, $response, $type_request);
            return $response;
        }

        $rental = Rental::find($quotation->reference_id);
        if (!$rental) {
            $response = $this->formatResponse("1000", "Invalid data", $request, $payment_id);
            $this->saveSCBPaymentLog($request, $response, $type_request);
            return $response;
        }

        // update rental bill
        if ($request['request'] == "confirm") {
            $rental->status = RentalStatusEnum::PAID;
            $rental->is_paid = true;
            $rental->payment_gateway = PaymentGatewayEnum::SCB_BILL_PAY;
            $rental->payment_date = date('Y-m-d H:i:s', strtotime($request->tranDate));
            $rental->payment_response_desc = $data;
            $rental->save();

            $receipt_type = $rental->getReceiptType();
            $rcf = new ReceiptFactory($receipt_type, $rental, $rental);
            if (strcmp($receipt_type, ReceiptTypeEnum::TAX_INVOICE) == 0) {
                $rcf->customer = $rcf->formatCustomerObjectFromBilling($rental);
            }

            // status rental bill equal PRIMARY update rental status
            if ($quotation->qt_type == RentalBillTypeEnum::PRIMARY) {
                // auto create DrivingJob, InspectionJob, CarParkTransfer
                ShortTermRentalSummaryController::createAutoModel($rental);
                ContractsController::createAutoContract($rental);  // auto create contract

                // gen receipt scb primary
                //ReceiptTrait::generateReceipt($rental->id, ReceiptTypeEnum::CAR_RENTAL, $rental_bill->id);
                $rcf->createWithLine($rental->id, Rental::class, ReceiptLineNameEnum::CAR_RENTAL);

                // save to sap interface table
                $sap = new SapProcess();
                $sap->afterPaymentBeforeService($rental->id, Rental::class);
            } else {
                // RentalBillTypeEnum::OTHER
                // gen receipt scb other
                //ReceiptTrait::generateReceipt($rental->id, ReceiptTypeEnum::OTHER, $rental_bill->id);
                $rcf->createWithLine($rental->id, Rental::class, ReceiptLineNameEnum::OTHER);

                $sap = new SapProcess();
                $sap->afterServicePaid($rental->id, Rental::class);
            }
        }
        $this->saveSCBPaymentLog($request, $response, $type_request);
        return $response;
    }

    public function saveSCBPaymentLog(Request $request, $response, $type)
    {
        $scb_bill_payment = new SCBPaymentLog();
        $scb_bill_payment->type_request = $type; // scb-billpayment-confirm
        $scb_bill_payment->request = $request['request'];
        $scb_bill_payment->user = $request->user;
        $scb_bill_payment->password = $request->password;
        $scb_bill_payment->tranID_request = $request->tranID;
        $scb_bill_payment->tranDate = date('Y-m-d H:i:s', strtotime($request->tranDate));
        $scb_bill_payment->channel = $request->channel;
        $scb_bill_payment->account = $request->account;
        $scb_bill_payment->amount_request = $request->amount;
        $scb_bill_payment->reference_1 = $request->reference1;
        $scb_bill_payment->reference_2_request = $request->reference2;
        $scb_bill_payment->reference_2_response = $request->reference2;
        $scb_bill_payment->reference_3 = $request->reference3;
        $scb_bill_payment->branchCode = $request->branchCode;
        $scb_bill_payment->terminalID = $request->terminalID;
        $scb_bill_payment->response = $request['request'];
        $scb_bill_payment->resCode = $response['resCode'];
        $scb_bill_payment->resMesg = $response['resMesg'];
        $scb_bill_payment->tranID_response = $request->tranID;
        $scb_bill_payment->paymentID = $response['paymentID'];
        $scb_bill_payment->amount_response = $request->amount;
        $scb_bill_payment->save();
    }

    public function formatResponse($res_code, $res_msg, $request, $payment_id)
    {
        return [
            "response" => $request['request'],
            "resCode" => $res_code,
            "resMesg" => $res_msg,
            "tranID" => $request->tranID,
            "reference2" => $request->reference2,
            "paymentID" => $payment_id,
        ];
    }
}
