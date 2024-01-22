<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\PromotionTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PromotionCode;
use App\Models\Promotion;
use Illuminate\Support\Facades\Validator;
use App\Enums\PatternCodeEnum;
use Illuminate\Support\Str;
use App\Imports\PromotionCodeImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\OrderPromotionCode;
use App\Models\OrderPromotionCodeLine;
use App\Classes\Sap\SapProcess;
use App\Enums\PaymentGatewayEnum;
use App\Traits\ReceiptTrait;
use App\Enums\ReceiptTypeEnum;
use Illuminate\Validation\Rule;

class PromotionCodeController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Promotion);
        $promotion_id = $request->promotion_id;
        $page_title = __('promotions.page_title_code');
        $promotion = Promotion::find($promotion_id);
        if (empty($promotion)) {
            return redirect()->route('admin.promotions.index');
        }
        $list = PromotionCode::where('promotion_id', $promotion_id)->orderBy('code')->paginate(PER_PAGE);
        if (in_array($promotion->promotion_type, [PromotionTypeEnum::COUPON, PromotionTypeEnum::PARTNER])) {
            $page_title = __('promotions.page_title_code');
        } elseif (strcmp($promotion->promotion_type, PromotionTypeEnum::VOUCHER) == 0) {
            $page_title = __('promotions.voucher');
        }

        return view('admin.promotion-codes.index', [
            's' => $request->s,
            'list' => $list,
            'promotion' => $promotion,
            'page_title' => $page_title,
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Promotion);
        $promotion_id = $request->promotion_id;
        $promotion = Promotion::find($promotion_id);
        if (empty($promotion)) {
            return redirect()->route('admin.promotions.index');
        }
        $d = new PromotionCode();
        $promotion_id = $request->promotion_id;
        $pattern_list = $this->getPatternList();
        $reuse_list = $this->getReuseList();
        $yes_no_list = getYesNoList();
        $build = BOOL_FALSE;


        $page_title = __('lang.create') . __('promotions.page_title_code');
        return view('admin.promotion-codes.form', [
            'd' => $d,
            'page_title' => $page_title,
            'pattern_list' => $pattern_list,
            'reuse_list' => $reuse_list,
            'promotion_id' => $promotion_id,
            'yes_no_list' => $yes_no_list,
            'promotion' => $promotion,
            'build' => $build,
        ]);
    }

    public function edit(PromotionCode $promotion_code, Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Promotion);
        if (empty($promotion_code)) {
            return redirect()->route('admin.promotions.index');
        }
        $promotion_id = $promotion_code->promotion_id;
        $promotion = Promotion::find($promotion_id);
        $is_sold_list = $this->getIsSoldList();
        $is_used_list = $this->getIsusedList();
        $yes_no_list = getYesNoList();
        $build = BOOL_FALSE;
        $page_title = __('lang.edit') . __('promotions.page_title_code');
        return view('admin.promotion-codes.promotion-code-form', [
            'd' => $promotion_code,
            'page_title' => $page_title,
            'is_sold_list' => $is_sold_list,
            'is_used_list' => $is_used_list,
            'promotion_id' => $promotion_id,
            'promotion' => $promotion,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Promotion);
        $this->trimComma($request, ['code_digit', 'selling_price']);
        $custom_rules = [
            'start_sale_date' => ['nullable', 'date'],
            'end_sale_date' => ['nullable', 'date'],
        ];
        if (strcmp($request->promotion_type, PromotionTypeEnum::COUPON) == 0) {
            $custom_rules = [
                'can_reuse' => ['required'],
                'code' => [Rule::when($request->can_reuse == BOOL_TRUE, ['required'])],
                'quota' => [Rule::when($request->can_reuse == BOOL_TRUE, ['required'])],
                'pattern_code' => [Rule::when($request->can_reuse == BOOL_FALSE, ['required'])],
                'prefix_code' => [Rule::when($request->can_reuse == BOOL_FALSE, ['required'])],
                'code_digit' => [Rule::when($request->can_reuse == BOOL_FALSE, ['required'])],
                'amount_code' => [Rule::when($request->can_reuse == BOOL_FALSE, ['required'])]
            ];
        } else if (strcmp($request->promotion_type, PromotionTypeEnum::PARTNER) == 0) {
            $custom_rules = [
                'build_at' => ['required'],
                'can_reuse' => [Rule::when($request->build_at == BOOL_TRUE, ['required'])],
                'code_file' => [Rule::when($request->build_at == BOOL_FALSE, ['required'])],
                'code' => [Rule::when($request->can_reuse == BOOL_TRUE, ['required'])],
                'quota' => [Rule::when($request->can_reuse == BOOL_TRUE, ['required'])],
                'pattern_code' => [Rule::when($request->can_reuse == BOOL_FALSE, ['required'])],
                'prefix_code' => [Rule::when($request->can_reuse == BOOL_FALSE, ['required'])],
                'code_digit' => [Rule::when($request->can_reuse == BOOL_FALSE, ['required'])],
                'amount_code' => [Rule::when($request->can_reuse == BOOL_FALSE, ['required'])],
            ];
        } else if (strcmp($request->promotion_type, PromotionTypeEnum::VOUCHER) == 0) {
            $custom_rules = [
                'selling_price' => ['required'],
                'pattern_code' => ['required'],
                'prefix_code' => ['required'],
                'code_digit' => ['required'],
                'amount_code' => ['required'],
            ];
        }
        $validator = Validator::make($request->all(), $custom_rules, [], [
            'can_reuse' => __('promotions.can_reuse'),
            'build_at' => __('promotions.build'),
            'code' => __('promotions.coupon_code'),
            'code_file' => __('promotions.code_files'),
            'quota' => __('promotions.quota'),
            'pattern_code' => __('promotions.pattern_code'),
            'prefix_code' => __('promotions.prefix_code'),
            'code_digit' => __('promotions.code_digit'),
            'amount_code' => __('promotions.amount_code'),
            'selling_price' => __('promotions.selling_price'),
            'start_sale_date' => __('promotions.start_sale_date'),
            'end_sale_date' => __('promotions.end_sale_date'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $this->trimComma($request, ['amount_code']);
        $amount_code = intval($request->amount_code);
        $amount_code = ($amount_code <= 0 ? 1 : $amount_code);

        $code_digit = intval($request->code_digit);
        $code_digit = ($code_digit <= 0 ? 6 : $code_digit);

        $padding_count = strlen($amount_code);
        if ($code_digit > $padding_count) {
            $padding_count = $code_digit;
        }
        $str_padding = '';
        for ($i = 0; $i < $padding_count; $i++) {
            $str_padding .= '0';
        }
        $prefix = $request->prefix_code;
        $start_sale_date = $request->start_sale_date;
        $end_sale_date = $request->end_sale_date;
        $promotion = Promotion::where(['id' => $request->promotion_id])->first();

        if ($promotion) {
            $package_amount = intval($promotion->package_amount);
            $promotion_id = $promotion->id;
            if (strcmp($request->build_at, BOOL_FALSE) == 0) {
                if ($request->hasFile('code_file')) {
                    $path = $request->file('code_file');
                    Excel::import(new PromotionCodeImport($promotion_id, $start_sale_date, $end_sale_date), $path[0]);
                }
            }

            if (strcmp($request->can_reuse, BOOL_TRUE) == 0) {
                $promotion_code = new PromotionCode();
                $promotion_code->promotion_id = $promotion_id;
                $promotion_code->code = $request->code;
                $promotion_code->quota = intval($request->quota);
                $promotion_code->can_reuse = boolval($request->can_reuse);
                $promotion_code->start_sale_date = $start_sale_date;
                $promotion_code->end_sale_date = $end_sale_date;
                $promotion_code->save();
            } else {
                if (strcmp($request->pattern_code, PatternCodeEnum::PATTERN) == 0) {
                    $count = PromotionCode::where('promotion_id', $promotion_id)->count();
                    $start_number = ($count + 1);
                    for ($i = 1; $i <= $amount_code; $i++) {
                        $code = $prefix . str_pad($start_number, strlen($str_padding), $str_padding, STR_PAD_LEFT);
                        $check_duplicate = PromotionCode::where('code', $code)->exists();
                        if ($check_duplicate) {
                            $start_number++;
                            continue;
                        }
                        $promotion_code = new PromotionCode();
                        $promotion_code->promotion_id = $promotion_id;
                        $promotion_code->code = $code;
                        $selling_price = str_replace(',', '', $request->selling_price);
                        $promotion_code->selling_price = floatval($selling_price);
                        $promotion_code->can_reuse = boolval($request->can_reuse);
                        $promotion_code->start_sale_date = $start_sale_date;
                        $promotion_code->end_sale_date = $end_sale_date;
                        $promotion_code->save();
                        $promotion_code->group_id = $promotion_code->id;
                        $promotion_code->save();

                        if ($package_amount > 1) {
                            for ($j = 1; $j < $package_amount; $j++) {
                                $start_number++;
                                $code_free = $prefix . str_pad($start_number, strlen($str_padding), $str_padding, STR_PAD_LEFT);
                                $promotion_code_free = new PromotionCode();
                                $promotion_code_free->promotion_id = $promotion_id;
                                $promotion_code_free->code = $code_free;
                                $selling_price = str_replace(',', '', $request->selling_price);
                                $promotion_code_free->selling_price = floatval($selling_price);
                                $promotion_code_free->can_reuse = boolval($request->can_reuse);
                                $promotion_code_free->start_sale_date = $start_sale_date;
                                $promotion_code_free->end_sale_date = $end_sale_date;
                                $promotion_code_free->is_free = BOOL_TRUE;
                                $promotion_code_free->group_id = $promotion_code->id;
                                $promotion_code_free->save();
                            }
                        }
                        $start_number++;
                    }
                } else if (strcmp($request->pattern_code, PatternCodeEnum::RANDOM) == 0) {
                    for ($i = 1; $i <= $amount_code; $i++) {
                        $code = $prefix . Str::random($code_digit);
                        $promotion_code = new PromotionCode();
                        $promotion_code->promotion_id = $promotion_id;
                        $promotion_code->code = $code;
                        $selling_price = str_replace(',', '', $request->selling_price);
                        $promotion_code->selling_price = floatval($selling_price);
                        $promotion_code->can_reuse = boolval($request->can_reuse);
                        $promotion_code->start_sale_date = $start_sale_date;
                        $promotion_code->end_sale_date = $end_sale_date;
                        $promotion_code->save();
                        $promotion_code->group_id = $promotion_code->id;
                        $promotion_code->save();

                        if ($package_amount > 1) {
                            for ($j = 1; $j < $package_amount; $j++) {
                                $code_free = $prefix . Str::random($code_digit);
                                $promotion_code_free = new PromotionCode();
                                $promotion_code_free->promotion_id = $promotion_id;
                                $promotion_code_free->code = $code_free;
                                $selling_price = str_replace(',', '', $request->selling_price);
                                $promotion_code_free->selling_price = floatval($selling_price);
                                $promotion_code_free->can_reuse = boolval($request->can_reuse);
                                $promotion_code_free->start_sale_date = $start_sale_date;
                                $promotion_code_free->end_sale_date = $end_sale_date;
                                $promotion_code_free->is_free = BOOL_TRUE;
                                $promotion_code_free->group_id = $promotion_code->id;
                                $promotion_code_free->save();
                            }
                        }
                    }
                }
            }

            //$promotion->promotion_type = PromotionTypeEnum::COUPON;
            //$promotion->save();
        }

        $redirect_route = route('admin.promotion-codes.index', ['promotion_id' => $promotion->id]);
        return $this->responseValidateSuccess($redirect_route);
    }

    private function getPatternList()
    {
        return collect([
            [
                'id' => PatternCodeEnum::PATTERN,
                'value' => PatternCodeEnum::PATTERN,
                'name' => __('promotions.pattern_' . PatternCodeEnum::PATTERN),
            ],
            [
                'id' => PatternCodeEnum::RANDOM,
                'value' => PatternCodeEnum::RANDOM,
                'name' => __('promotions.pattern_' . PatternCodeEnum::RANDOM),
            ],
        ]);
    }

    private function getReuseList()
    {
        return collect([
            [
                'id' => BOOL_FALSE,
                'value' => BOOL_FALSE,
                'name' => __('promotions.can_reuse_' . BOOL_FALSE),
            ],
            [
                'id' => BOOL_TRUE,
                'value' => BOOL_TRUE,
                'name' => __('promotions.can_reuse_' . BOOL_TRUE),
            ],
        ]);
    }

    private function getIsUsedList()
    {
        return collect([
            [
                'id' => BOOL_TRUE,
                'value' => BOOL_TRUE,
                'name' => __('promotions.is_used_' . BOOL_TRUE),
            ],
            [
                'id' => BOOL_FALSE,
                'value' => BOOL_FALSE,
                'name' => __('promotions.is_used_' . BOOL_FALSE),
            ],
        ]);
    }

    private function getIsSoldList()
    {
        return collect([
            [
                'id' => BOOL_TRUE,
                'value' => BOOL_TRUE,
                'name' => __('promotions.is_sold_' . BOOL_TRUE),
            ],
            [
                'id' => BOOL_FALSE,
                'value' => BOOL_FALSE,
                'name' => __('promotions.is_sold_' . BOOL_FALSE),
            ],
        ]);
    }

    public function updatePromotionCode(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Promotion);
        $validator = Validator::make($request->all(), [
            'is_sold' => 'required',
            'sold_date' => ['required_if:is_sold,=,1'],
        ], [], [
            'is_sold' => __('promotions.is_sold'),
            'sold_date' => __('promotions.sold_date'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $promotion_code = PromotionCode::find($request->id);
        if (empty($promotion_code)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        $promotion_code->is_sold = $request->is_sold;
        $promotion_code->sold_date = $request->sold_date;
        $promotion_code->save();

        $vat = calculateVat($promotion_code->selling_price);

        $order_promotion_code_count = OrderPromotionCodeLine::where('promotion_code_id', $promotion_code->id)->count();
        if ($order_promotion_code_count <= 0) {
            if (strcmp($promotion_code->is_sold, BOOL_TRUE) == 0) {
                $order_promotion_code = new OrderPromotionCode();
                $order_promotion_code->amount = $promotion_code->selling_price;
                $order_promotion_code->vat = $vat;
                $order_promotion_code->save();

                $order_promotion_code_line = new OrderPromotionCodeLine();
                $order_promotion_code_line->order_promotion_code_id = $order_promotion_code->id;
                $order_promotion_code_line->promotion_code_id = $promotion_code->id;
                $order_promotion_code_line->total = $promotion_code->selling_price;
                $order_promotion_code_line->vat = $vat;
                $order_promotion_code_line->save();

                // gen receipt coupon
                ReceiptTrait::generateReceipt($order_promotion_code->id, ReceiptTypeEnum::VOUCHER_OF_CASH, null);

                $params = [
                    'payment_gateway' => PaymentGatewayEnum::SCB_BILL_PAY,
                    'payment_date' => date('Y-m-d'),
                    'promotion_code' => $promotion_code->code,
                    'promotion_id' => $promotion_code->promotion_id,
                ];
                $sap = new SapProcess();
                $sap->afterPaymentBeforeService($order_promotion_code->id, OrderPromotionCode::class, $params);
            }
        }

        $redirect_route = route('admin.promotion-codes.index', ['promotion_id' => $promotion_code->promotion_id]);
        return $this->responseValidateSuccess($redirect_route);
    }
}
