<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PromotionCode;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;
use App\Models\OrderPromotionCode;
use App\Models\OrderPromotionCodeLine;
use App\Classes\Sap\SapProcess;
use App\Enums\PaymentGatewayEnum;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Traits\ReceiptTrait;
use App\Enums\ReceiptTypeEnum;
use App\Models\CustomerBillingAddress;

class PromotionCodeController extends Controller
{
    public function index(Request $request)
    {
        $list = PromotionCode::select('promotion_codes.id', 'promotion_codes.code', 'is_sold', 'sold_date', 'is_free', 'promotion_codes.start_sale_date', 'promotion_codes.end_sale_date')
            ->leftJoin('promotions', 'promotions.id', '=', 'promotion_codes.promotion_id')
            ->where('promotions.id', $request->promotion_id)
            ->where('is_offline', BOOL_FALSE)
            ->where('is_free', BOOL_FALSE)
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read(Request $request)
    {
        $data = PromotionCode::select('promotion_codes.id', 'promotion_codes.code', 'is_offline', 'is_free', 'selling_price', 'is_sold', 'sold_date', 'can_reuse', 'quota')
            ->addSelect('customer_id', 'is_booking', 'is_used', 'use_date')
            ->leftJoin('promotions', 'promotions.id', '=', 'promotion_codes.promotion_id')
            ->where('promotion_codes.id', $request->id)
            ->where('promotions.id', $request->promotion_id)
            ->first();
        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }

    public function buy(Request $request)
    {
        // dd($request->id);
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'array', 'min:1'],
            'customer_id' => ['required', 'string', 'max:36', 'exists:customers,id'],
            'amount' => ['required'],
            'check_customer_address' => ['required'],
            'customer_billing_address_id' => ['required_if:check_customer_address,=,0'],
        ], [], [
            'id' => __('promotions.id'),
            'customer_id' => __('promotions.customer_id'),
            'amount' => __('promotions.amount'),
            'check_customer_address' => __('promotions.check_customer_address'),
            'customer_billing_address_id' => __('promotions.customer_billing_address_id'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        // check id is valid
        $ids = $request->id;
        if (!is_array($ids)) {
            return $this->responseWithCode(false, DATA_UNPROCESSABLE, null, 422);
        }
        if (sizeof($ids) <= 0) {
            return $this->responseWithCode(false, DATA_UNPROCESSABLE, null, 422);
        }
        $countcheck = PromotionCode::whereIn('group_id', $ids)
            ->where('is_offline', '0')
            ->where('is_sold', '0')
            ->whereNull('sold_date')
            ->whereNull('buyer_id')
            ->whereNull('customer_id')
            ->where('is_booking', '0')
            ->where('is_used', '0')
            ->whereNull('use_date')
            ->count();

        $promotion = PromotionCode::select('promotions.package_amount')
            ->leftJoin('promotions', 'promotions.id', '=', 'promotion_codes.promotion_id')
            ->whereIn('promotion_codes.id', $ids)
            ->first();
        $total_package = intval($promotion->package_amount) * sizeof($ids);

        if (!($countcheck == $total_package)) {
            return $this->responseWithCode(false, DATA_UNPROCESSABLE, null, 422);
        }

        //customer
        $customer = Customer::find($request->customer_id);
        if (empty($customer)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        if (strcmp($request->check_customer_address, BOOL_FALSE) == 0) {
            $customer_billing_address = CustomerBillingAddress::where('id', $request->customer_billing_address_id)->where('customer_id', $customer->id)->first();
            if (empty($customer_billing_address)) {
                return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
            }
        }

        // booking code
        try {
            DB::transaction(function () use ($request, $ids, $countcheck, $customer) {

                PromotionCode::whereIn('group_id', $ids)->limit($countcheck)->update([
                    'is_sold' => '1',
                    'sold_date' => date('Y-m-d H:i:s'),
                    'payment_description' => ((string)$request->payment_description),
                    'buyer_id' => $customer->id,
                    'customer_id' => $customer->id,
                ]);


                $vat = calculateVat($request->amount);
                $order_promotion_code = new OrderPromotionCode();
                $order_promotion_code->amount = $request->amount;
                $order_promotion_code->vat = floatval($vat);
                $order_promotion_code->customer_id = $customer->id;
                $order_promotion_code->payment_description = ((string)$request->payment_description);
                $order_promotion_code->check_customer_address = $request->check_customer_address;
                $order_promotion_code->customer_billing_address_id = $request->customer_billing_address_id;
                $order_promotion_code->save();

                $arr_code = [];
                $total_amount = ($request->amount / sizeof($ids));
                $vat_line = calculateVat($total_amount);
                foreach ($ids as $key => $item_id) {
                    $promotion_code = PromotionCode::find($item_id);
                    $order_promotion_code_line = new OrderPromotionCodeLine();
                    $order_promotion_code_line->order_promotion_code_id = $order_promotion_code->id;
                    $order_promotion_code_line->promotion_code_id = $item_id;
                    $order_promotion_code_line->total = floatval($total_amount);
                    $order_promotion_code_line->vat = floatval($vat_line);
                    $order_promotion_code_line->save();
                    array_push($arr_code, ['code' => $promotion_code->code, 'id' => $promotion_code->promotion_id]);
                }

                // gen receipt coupon
                ReceiptTrait::generateReceipt($order_promotion_code->id, ReceiptTypeEnum::VOUCHER_OF_CASH, null);

                $params = [
                    'payment_gateway' => PaymentGatewayEnum::APP_2C2P,
                    'payment_date' => date('Y-m-d'),
                    'promotion_code' => $arr_code,
                    'count_promotion_code' => count($ids),
                ];
                $sap = new SapProcess();

                $sap->afterPaymentBeforeService($order_promotion_code->id, OrderPromotionCode::class, $params);
            });
        } catch (Exception $e) {
            return $this->responseWithCode(false, DATABASE_ERROR, null, 500);
        }
        return $this->responseWithCode(true, DATA_SUCCESS, null, 200);
    }

    public function transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'string', 'max:36', 'exists:promotion_codes,id'],
            'customer_sender_id' => ['required', 'string', 'max:36', 'exists:customers,id'],
            'customer_receiver_id' => ['required', 'string', 'max:36', 'exists:customers,id'],
        ], [], [
            'id' => __('promotions.id'),
            'customer_sender_id' => __('promotions.customer_sender_id'),
            'customer_receiver_id' => __('promotions.customer_receiver_id'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        if (strcmp($request->customer_sender_id, $request->customer_receiver_id) == 0) {
            return $this->responseWithCode(false, 'The sender and receiver cannot be the same.', null, 422);
        }

        $code = PromotionCode::find($request->id);
        if (boolval($code->is_used) || (!empty($code->use_date))) {
            return $this->responseWithCode(false, 'The code has already been used.', null, 422);
        }

        if (strcmp($request->customer_sender_id, $code->buyer_id) != 0) {
            return $this->responseWithCode(false, 'Customer does not own this code(1).', null, 422);
        }

        if (strcmp($request->customer_sender_id, $code->customer_id) != 0) {
            return $this->responseWithCode(false, 'Customer does not own this code(2).', null, 422);
        }

        $code->customer_id = $request->customer_receiver_id;
        $code->transfer_date = date('Y-m-d H:i:s');
        $code->save();

        return $this->responseWithCode(true, DATA_SUCCESS, $code->id, 200);
    }
}
