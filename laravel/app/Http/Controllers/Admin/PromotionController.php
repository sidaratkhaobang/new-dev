<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Promotion;
use App\Models\PromotionProduct;
use App\Models\PromotionEffectiveProduct;
use App\Models\PromotionSale;
use App\Models\PromotionCarClass;
use App\Models\PromotionCustomerGroup;
use App\Models\ProductAdditional;
use App\Models\PromotionFreeProduct;
use App\Models\PromotionFreeProductAdditional;
use App\Models\PromotionFreeCarClass;
use App\Models\Branch;
use App\Models\CarClass;
use App\Models\CustomerGroup;
use App\Models\User;
use App\Enums\DiscountTypeEnum;
use App\Enums\DiscountModeEnum;
use App\Enums\PromotionTypeEnum;
use App\Models\Product;
use App\Models\PromotionCode;
use App\Models\PromotionEffectiveCarClass;
use App\Models\PromotionIncompatible;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Promotion);
        $name = $request->name;
        $default_name = null;
        if ($name) {
            $promotion = Promotion::find($name);
            $default_name = $promotion->name;
        }
        $type_id = $request->type_id;
        $branch_id = $request->branch_id;
        $branch_list = Branch::select('id', 'name')->get();
        $type = $this->getPromotionAllTypeList();
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $list = Promotion::leftJoin('branches', 'branches.id', '=', 'promotions.branch_id')
            ->sortable('code')
            ->select(
                'promotions.id',
                'promotions.name',
                'promotions.code',
                'promotions.priority',
                'promotions.start_date',
                'promotions.end_date',
                'promotions.status',
                'promotions.promotion_type',
                'branches.name as branch_name',
            )
            ->groupBy(
                'promotions.id',
                'promotions.name',
                'promotions.code',
                'promotions.priority',
                'promotions.start_date',
                'promotions.end_date',
                'promotions.status',
                'promotions.promotion_type',
                'branch_name'
            )
            ->search($request->s, $request)->paginate(PER_PAGE);

        return view('admin.promotions.index', [
            's' => $request->s,
            'list' => $list,
            'name' => $name,
            'default_name' => $default_name,
            'branch_id' => $branch_id,
            'branch_list' => $branch_list,
            'type' => $type,
            'type_id' => $type_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
    }

    public function selectType()
    {
        $promotion_types = $this->getPromotionTypeList();
        $page_title = __('lang.create') . __('promotions.promotion_type');
        return view('admin.promotions.form-type', [
            'page_title' => $page_title,
            'promotion_types' => $promotion_types,
        ]);
    }

    public function createPromotion(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Promotion);

        if (empty($request->promotion_type_id)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกประเภทโปรโมชัน',
            ], 422);
        }
        $promotion_type = $request->promotion_type_id;
        $redirect_route = route('admin.promotions.create', ['promotion_type' => $promotion_type]);
        return $this->responseValidateSuccess($redirect_route);
    }

    public function create(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Promotion);
        $d = new Promotion();
        $d->status = STATUS_ACTIVE;
        $d->discount_type = DiscountTypeEnum::PERCENT;
        $d->discount_mode = DiscountModeEnum::ALL;
        $d->is_check_min_total = BOOL_FALSE;
        $d->is_check_min_hours = BOOL_FALSE;
        $d->is_check_min_days = BOOL_FALSE;
        $d->is_check_min_distance = BOOL_FALSE;
        $voucher_type_id = BOOL_FALSE;

        $branch_list = $this->getBranchList();
        $status_list = $this->getStatusList();
        $discount_type = $this->getDiscountTypeList();
        $discount_mode = $this->getDiscountModeList();
        $product_additional_list = $this->getProductAdditionalList();
        $check_list = $this->getCheckList();
        $customer_group_list = $this->getCustomerGroupList();
        $car_class_list = $this->getCarClassList();
        $sale_list = $this->getSaleList();
        $product_list = $this->getProductSkuList();
        $incompatible_list = $this->getInCompatiblePromotionList();
        $promotion_type = $request->promotion_type;
        $coupon_list = $this->getCouponList();
        $coupon_types = $this->getCouponTypeList();
        $voucher_type = $this->getVoucherTypeList();

        $free_product = [];
        $free_car_class = [];
        $free_product_additional = [];
        $promotion_effective = [];
        $car_class = [];
        $customer_group = [];
        $product = [];
        $sale = [];
        $incompatible = [];
        $quota = null;
        $page_title = __('lang.create');
        $label_name = null;
        $label_code = null;
        $label_btn = null;

        if (strcmp($promotion_type, PromotionTypeEnum::PROMOTION) == 0) {
            $coupon_id = PromotionTypeEnum::PROMOTION;
            $page_title = __('lang.create') . __('promotions.promotion_coupon');
            $label_name = __('promotions.name_coupon');
            $label_code = __('promotions.code_coupon');
            $label_btn = __('promotions.save_code_coupon');
        } elseif (strcmp($promotion_type, PromotionTypeEnum::VOUCHER) == 0) {
            $coupon_id = null;
            $page_title = __('lang.create') . ' ' . __('promotions.voucher');
            $label_name = __('promotions.name_voucher');
            $label_code = __('promotions.code_voucher');
            $label_btn = __('promotions.save_code_voucher');
        }

        return view('admin.promotions.form-info', [
            'd' => $d,
            'page_title' => $page_title,
            'branch_list' => $branch_list,
            'status_list' => $status_list,
            'discount_type' => $discount_type,
            'check_list' => $check_list,
            'customer_group_list' => $customer_group_list,
            'car_class_list' => $car_class_list,
            'sale_list' => $sale_list,
            'product_additional_list' => $product_additional_list,
            'discount_mode' => $discount_mode,
            'product_list' => $product_list,
            'free_product' => $free_product,
            'free_car_class' => $free_car_class,
            'free_product_additional' => $free_product_additional,
            'promotion_effective' => $promotion_effective,
            'car_class' => $car_class,
            'customer_group' => $customer_group,
            'product' => $product,
            'sale' => $sale,
            'create' => true,
            'incompatible_list' => $incompatible_list,
            'incompatible' => $incompatible,
            'coupon_list' => $coupon_list,
            'promotion_type' => $promotion_type,
            'coupon_types' => $coupon_types,
            'voucher_type' => $voucher_type,
            'voucher_type_id' => $voucher_type_id,
            'label_name' => $label_name,
            'label_code' => $label_code,
            'label_btn' => $label_btn,
            'coupon_id' => $coupon_id,
            'quota' => $quota,
        ]);
    }

    public function edit(Promotion $promotion)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Promotion);
        $branch_list = $this->getBranchList();
        $status_list = $this->getStatusList();
        $discount_type = $this->getDiscountTypeList();
        $discount_mode = $this->getDiscountModeList();
        $product_additional_list = $this->getProductAdditionalList();
        $check_list = $this->getCheckList();
        $customer_group_list = $this->getCustomerGroupList();
        $car_class_list = $this->getCarClassList();
        $sale_list = $this->getSaleList();
        $product_list = $this->getProductSkuList();
        $incompatible_list = $this->getInCompatiblePromotionList();
        $coupon_list = $this->getCouponList();
        $coupon_types = $this->getCouponTypeList();
        $voucher_type = $this->getVoucherTypeList();

        $free_product = $this->getPromotionFreeProductArray($promotion->id);
        $free_product_additional = $this->getPromotionFreeProductAdditionalArray($promotion->id);
        $free_car_class = $this->getPromotionFreeCarClassArray($promotion->id);
        $promotion_effective = $this->getPromotionEffectiveCarClassArray($promotion->id);
        $car_class = $this->getPromotionCarClassArray($promotion->id);
        $customer_group = $this->getPromotionCustomerGroupArray($promotion->id);
        $product = $this->getPromotionProductArray($promotion->id);
        $sale = $this->getPromotionSaleArray($promotion->id);
        $incompatible = $this->getPromotionInCompatibleArray($promotion->id);

        $promotion_type = $promotion->promotion_type;
        $voucher_type_id = null;
        if (in_array($promotion_type, [PromotionTypeEnum::VOUCHER]) && $promotion->package_amount > 1) {
            $voucher_type_id = BOOL_TRUE;
        } else {
            $voucher_type_id = BOOL_FALSE;
        }

        $promotion->discount_day = 0;
        if (in_array($promotion->discount_type, [DiscountTypeEnum::FREE_CAR_CLASS])) {
            $promotion->discount_day = intval($promotion->discount_amount);
        }

        $coupon_id = null;
        $quota = null;
        if (in_array($promotion_type, [PromotionTypeEnum::COUPON, PromotionTypeEnum::PARTNER])) {
            $coupon_id = PromotionTypeEnum::COUPON;
        } else {
            $coupon_id = PromotionTypeEnum::PROMOTION;
            $quota = PromotionCode::where('promotion_id', $promotion->id)->select('quota')->first();
            if ($quota) {
                $quota = $quota->quota;
            }
        }

        if (in_array($promotion->promotion_type, [PromotionTypeEnum::PROMOTION, PromotionTypeEnum::COUPON, PromotionTypeEnum::PARTNER])) {
            $page_title = __('lang.edit') . __('promotions.promotion_coupon');
            $label_name = __('promotions.name_coupon');
            $label_code = __('promotions.code_coupon');
            $label_btn = __('promotions.save_code_coupon');
        } elseif (strcmp($promotion->promotion_type, PromotionTypeEnum::VOUCHER) == 0) {
            $page_title = __('lang.edit')  . ' ' . __('promotions.voucher');
            $label_name = __('promotions.name_voucher');
            $label_code = __('promotions.code_voucher');
            $label_btn = __('promotions.save_code_voucher');
        }
        return view('admin.promotions.form-info', [
            'd' => $promotion,
            'page_title' => $page_title,
            'branch_list' => $branch_list,
            'status_list' => $status_list,
            'discount_type' => $discount_type,
            'check_list' => $check_list,
            'customer_group_list' => $customer_group_list,
            'car_class_list' => $car_class_list,
            'sale_list' => $sale_list,
            'product_additional_list' => $product_additional_list,
            'discount_mode' => $discount_mode,
            'product_list' => $product_list,
            'free_product' => $free_product,
            'free_product_additional' => $free_product_additional,
            'free_car_class' => $free_car_class,
            'promotion_effective' => $promotion_effective,
            'car_class' => $car_class,
            'customer_group' => $customer_group,
            'product' => $product,
            'sale' => $sale,
            'incompatible_list' => $incompatible_list,
            'incompatible' => $incompatible,
            'coupon_list' => $coupon_list,
            'label_name' => $label_name,
            'label_code' => $label_code,
            'label_btn' => $label_btn,
            'promotion_type' => $promotion_type,
            'coupon_types' => $coupon_types,
            'voucher_type' => $voucher_type,
            'voucher_type_id' => $voucher_type_id,
            'coupon_id' => $coupon_id,
            'quota' => $quota,
        ]);
    }

    public function show(Promotion $promotion)
    {
        $this->authorize(Actions::View . '_' . Resources::Promotion);
        $branch_list = $this->getBranchList();
        $status_list = $this->getStatusList();
        $discount_type = $this->getDiscountTypeList();
        $discount_mode = $this->getDiscountModeList();
        $product_additional_list = $this->getProductAdditionalList();
        $check_list = $this->getCheckList();
        $customer_group_list = $this->getCustomerGroupList();
        $car_class_list = $this->getCarClassList();
        $sale_list = $this->getSaleList();
        $product_list = $this->getProductSkuList();
        $incompatible_list = $this->getInCompatiblePromotionList();
        $coupon_list = $this->getCouponList();
        $coupon_types = $this->getCouponTypeList();
        $voucher_type = $this->getVoucherTypeList();

        $free_product = $this->getPromotionFreeProductArray($promotion->id);
        $free_product_additional = $this->getPromotionFreeProductAdditionalArray($promotion->id);
        $free_car_class = $this->getPromotionFreeCarClassArray($promotion->id);
        $promotion_effective = $this->getPromotionEffectiveCarClassArray($promotion->id);
        $car_class = $this->getPromotionCarClassArray($promotion->id);
        $customer_group = $this->getPromotionCustomerGroupArray($promotion->id);
        $product = $this->getPromotionProductArray($promotion->id);
        $sale = $this->getPromotionSaleArray($promotion->id);
        $incompatible = $this->getPromotionInCompatibleArray($promotion->id);

        $promotion_type = $promotion->promotion_type;
        $voucher_type_id = null;
        if (in_array($promotion_type, [PromotionTypeEnum::VOUCHER]) && $promotion->package_amount > 1) {
            $voucher_type_id = BOOL_TRUE;
        } else {
            $voucher_type_id = BOOL_FALSE;
        }

        $promotion->discount_day = 0;
        if (in_array($promotion->discount_type, [DiscountTypeEnum::FREE_CAR_CLASS])) {
            $promotion->discount_day = intval($promotion->discount_amount);
        }

        $coupon_id = null;
        $quota = null;
        if (in_array($promotion_type, [PromotionTypeEnum::COUPON, PromotionTypeEnum::PARTNER])) {
            $coupon_id = PromotionTypeEnum::COUPON;
        } else {
            $coupon_id = PromotionTypeEnum::PROMOTION;
            $quota = PromotionCode::where('promotion_id', $promotion->id)->select('quota')->first();
            if ($quota) {
                $quota = $quota->quota;
            }
        }

        if (in_array($promotion->promotion_type, [PromotionTypeEnum::PROMOTION, PromotionTypeEnum::COUPON, PromotionTypeEnum::PARTNER])) {
            $page_title = __('lang.view') . __('promotions.promotion_coupon');
            $label_name = __('promotions.name_coupon');
            $label_code = __('promotions.code_coupon');
            $label_btn = __('promotions.save_code_coupon');
        } elseif (strcmp($promotion->promotion_type, PromotionTypeEnum::VOUCHER) == 0) {
            $page_title = __('lang.view') . ' ' . __('promotions.voucher');
            $label_name = __('promotions.name_voucher');
            $label_code = __('promotions.code_voucher');
            $label_btn = __('promotions.save_code_voucher');
        }

        return view('admin.promotions.form-info', [
            'd' => $promotion,
            'page_title' => $page_title,
            'branch_list' => $branch_list,
            'status_list' => $status_list,
            'discount_type' => $discount_type,
            'check_list' => $check_list,
            'customer_group_list' => $customer_group_list,
            'car_class_list' => $car_class_list,
            'sale_list' => $sale_list,
            'product_additional_list' => $product_additional_list,
            'discount_mode' => $discount_mode,
            'product_list' => $product_list,
            'free_product' => $free_product,
            'free_car_class' => $free_car_class,
            'free_product_additional' => $free_product_additional,
            'promotion_effective' => $promotion_effective,
            'car_class' => $car_class,
            'customer_group' => $customer_group,
            'product' => $product,
            'sale' => $sale,
            'view' => true,
            'incompatible_list' => $incompatible_list,
            'incompatible' => $incompatible,
            'coupon_list' => $coupon_list,
            'label_name' => $label_name,
            'label_code' => $label_code,
            'label_btn' => $label_btn,
            'promotion_type' => $promotion_type,
            'coupon_types' => $coupon_types,
            'voucher_type' => $voucher_type,
            'voucher_type_id' => $voucher_type_id,
            'coupon_id' => $coupon_id,
            'quota' => $quota,
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Promotion);
        $promotion = Promotion::find($id);
        $promotion->delete();

        return $this->responseComplete();
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Promotion);
        $this->trimComma($request, ['quota', 'package_amount']);
        $custom_rules = [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('promotions', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            // 'code' => [
            //     'required', 'string',
            // ],
            'min_total' => [
                'nullable', 'string', 'max:10',
            ],
            'min_hours' => [
                'nullable', 'string', 'max:8',
            ],
            'min_days' => [
                'nullable', 'string', 'max:8',
            ],
            'min_distance' => [
                'nullable', 'string', 'max:10',
            ],
            'quota' => [Rule::when($request->promotion_type == PromotionTypeEnum::PROMOTION, ['required'])],
            // 'branch_id' => ['required'],
            // 'priority' => ['required'],
            'start_date' => ['required'],
            'end_date' => ['required'],
            'branch_expired_id' => [Rule::when($request->promotion_type == PromotionTypeEnum::VOUCHER, ['required'])],
        ];
        if (strcmp($request->voucher_type, BOOL_TRUE) == 0) {
            $custom_rules = [
                'package_amount' => ['required', 'integer', 'min:2'],
            ];
        }

        $validator = Validator::make($request->all(), $custom_rules, [], [
            'name' => __('promotions.name'),
            'code' => __('promotions.code'),
            'quota' => __('promotions.quota'),
            'min_total' => __('promotions.min_total'),
            'min_hours' => __('promotions.min_hours'),
            'min_days' => __('promotions.min_day'),
            'min_distance' => __('promotions.min_distance'),
            // 'branch_id' => __('promotions.branch'),
            // 'priority' => __('promotions.priority'),
            'start_date' => __('promotions.start_date'),
            'end_date' => __('promotions.end_date'),
            'branch_expired_id' => __('promotions.branch_expired'),
            'package_amount' => __('promotions.package_amount'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $branch = Branch::where('code', '0500')->where('is_main', STATUS_ACTIVE)->first();

        $promotion = Promotion::firstOrNew(['id' => $request->id]);
        $promotion->name = $request->name;
        $promotion->code = strval($request->code);
        $promotion->branch_id = ($branch) ? $branch->id : null;
        $promotion->discount_type = $request->discount_type;
        $promotion->discount_mode = $request->discount_mode;
        $discount_amount = str_replace(',', '', $request->discount_amount);
        $promotion->discount_amount = ($discount_amount) ? $discount_amount : 0;
        if (in_array($request->discount_type, [DiscountTypeEnum::FREE_CAR_CLASS])) {
            $promotion->discount_amount = intval($request->discount_day);
        }
        $promotion->priority = intval($request->priority);
        $promotion->is_check_min_total = $request->is_check_min_total;
        $min_total = str_replace(',', '', $request->min_total);
        $promotion->min_total = ($min_total) ? $min_total : 0;
        $promotion->is_check_min_hours = $request->is_check_min_hours;
        $min_hours = str_replace(',', '', $request->min_hours);
        $promotion->min_hours = ($min_hours) ? $min_hours : 0;
        $promotion->is_check_min_days = $request->is_check_min_days;
        $min_days = str_replace(',', '', $request->min_days);
        $promotion->min_days = ($min_days) ? $min_days : 0;
        $promotion->is_check_min_distance = $request->is_check_min_distance;
        $min_distance = str_replace(',', '', $request->min_distance);
        $promotion->min_distance = ($min_distance) ? $min_distance : 0;
        $promotion->start_date = $request->start_date;
        $promotion->end_date = $request->end_date;
        $promotion->start_sale_date = (!empty($request->start_sale_date) ? date('Y-m-d', strtotime($request->start_sale_date)) : null);
        $promotion->end_sale_date = (!empty($request->end_sale_date) ? date('Y-m-d', strtotime($request->end_sale_date)) : null);
        $promotion->branch_expired_id = ($request->branch_expired_id) ? $request->branch_expired_id : null;
        $promotion->status = $request->status;
        $promotion->promotion_type = $request->promotion_type;
        if ($request->voucher_type) {
            $promotion->package_amount = ($request->voucher_type === BOOL_FALSE) ? 1 : $request->package_amount;
        }
        $promotion->save();

        if ($promotion->id) {
            $promotion_free_product = $this->savePromotionFreeProduct($request, $promotion->id);
            $promotion_free_product_additional = $this->savePromotionFreeeProductAdditional($request, $promotion->id);
            $promotion_free_car_class = $this->savePromotionFreeCarClass($request, $promotion->id);
            $promotion_effective_car_class = $this->savePromotionEffectiveCarClass($request, $promotion->id);
            $promotion_car_class = $this->savePromotionCarClass($request, $promotion->id);
            $promotion_customer_group = $this->savePromotionCustomerGroup($request, $promotion->id);
            $promotion_product = $this->savePromotionProduct($request, $promotion->id);
            $promotion_sale = $this->savePromotionSale($request, $promotion->id);
            $promotion_incompatible = $this->savePromotionIncompatible($request, $promotion->id);

            if (strcmp($promotion->promotion_type, PromotionTypeEnum::PROMOTION) === 0) {
                $promotion_code = $this->savePromotionCode($request, $promotion->id);
            }
        }

        $redirect_route = route('admin.promotions.index');
        if ($request->set_promotion_code) {
            $redirect_route = route('admin.promotion-codes.index', ['promotion_id' => $promotion->id]);
        }
        return $this->responseValidateSuccess($redirect_route);
    }

    private function savePromotionFreeProduct($request, $promotion_id)
    {
        PromotionFreeProduct::where('promotion_id', $promotion_id)->delete();
        if (!empty($request->free_product)) {
            foreach ($request->free_product as $free_product_id) {
                $free_product = new PromotionFreeProduct();
                $free_product->promotion_id = $promotion_id;
                $free_product->product_id = $free_product_id;
                $free_product->save();
            }
        }
        return true;
    }

    private function getPromotionFreeProductArray($promotion_id)
    {
        return PromotionFreeProduct::join('promotions', 'promotions.id', '=', 'promotions_free_products.promotion_id')
            ->join('products', 'products.id', '=', 'promotions_free_products.product_id')
            ->select('products.id as id', 'products.name as name')
            ->where('promotions_free_products.promotion_id', $promotion_id)
            ->pluck('products.id')
            ->toArray();
    }

    private function savePromotionFreeeProductAdditional($request, $promotion_id)
    {
        PromotionFreeProductAdditional::where('promotion_id', $promotion_id)->delete();
        if (!empty($request->free_product_additional)) {
            foreach ($request->free_product_additional as $free_product_additional_id) {
                $free_product_additional = new PromotionFreeProductAdditional();
                $free_product_additional->promotion_id = $promotion_id;
                $free_product_additional->product_additional_id = $free_product_additional_id;
                $free_product_additional->save();
            }
        }
        return true;
    }

    private function getPromotionFreeProductAdditionalArray($promotion_id)
    {
        return PromotionFreeProductAdditional::join('promotions', 'promotions.id', '=', 'promotions_free_product_additionals.promotion_id')
            ->join('product_additionals', 'product_additionals.id', '=', 'promotions_free_product_additionals.product_additional_id')
            ->select('product_additionals.id as id', 'product_additionals.name as name')
            ->where('promotions_free_product_additionals.promotion_id', $promotion_id)
            ->pluck('product_additionals.id')
            ->toArray();
    }

    private function savePromotionFreeCarClass($request, $promotion_id)
    {
        PromotionFreeCarClass::where('promotion_id', $promotion_id)->delete();
        if (!empty($request->free_car_class)) {
            foreach ($request->free_car_class as $free_car_class_id) {
                $free_car_class = new PromotionFreeCarClass();
                $free_car_class->promotion_id = $promotion_id;
                $free_car_class->car_class_id = $free_car_class_id;
                $free_car_class->save();
            }
        }
        return true;
    }

    private function getPromotionFreeCarClassArray($promotion_id)
    {
        return PromotionFreeCarClass::join('promotions', 'promotions.id', '=', 'promotions_free_car_classes.promotion_id')
            ->join('car_classes', 'car_classes.id', '=', 'promotions_free_car_classes.car_class_id')
            ->select('car_classes.id as id', 'car_classes.name as name')
            ->where('promotions_free_car_classes.promotion_id', $promotion_id)
            ->pluck('products.id')
            ->toArray();
    }

    private function savePromotionEffectiveCarClass($request, $promotion_id)
    {
        PromotionEffectiveCarClass::where('promotion_id', $promotion_id)->delete();
        if (!empty($request->promotion_effective)) {
            foreach ($request->promotion_effective as $promotion_effective_id) {
                $promotion_effective = new PromotionEffectiveCarClass();
                $promotion_effective->promotion_id = $promotion_id;
                $promotion_effective->car_class_id = $promotion_effective_id;
                $promotion_effective->save();
            }
        }
        return true;
    }

    private function getPromotionEffectiveCarClassArray($promotion_id)
    {
        return PromotionEffectiveCarClass::join('promotions', 'promotions.id', '=', 'promotions_effective_car_classes.promotion_id')
            ->join('car_classes', 'car_classes.id', '=', 'promotions_effective_car_classes.car_class_id')
            ->select('car_classes.id as id', 'car_classes.full_name as name')
            ->where('promotions_effective_car_classes.promotion_id', $promotion_id)
            ->pluck('car_classes.id')
            ->toArray();
    }

    private function savePromotionCarClass($request, $promotion_id)
    {
        PromotionCarClass::where('promotion_id', $promotion_id)->delete();

        if (!empty($request->car_class)) {
            foreach ($request->car_class as $car_class_id) {
                $car_class = new PromotionCarClass();
                $car_class->promotion_id = $promotion_id;
                $car_class->car_class_id = $car_class_id;
                $car_class->save();
            }
        }
        return true;
    }

    private function getPromotionCarClassArray($promotion_id)
    {
        return PromotionCarClass::join('promotions', 'promotions.id', '=', 'promotions_car_classes.promotion_id')
            ->join('car_classes', 'car_classes.id', '=', 'promotions_car_classes.car_class_id')
            ->select('car_classes.id as id', 'car_classes.name as name')
            ->where('promotions_car_classes.promotion_id', $promotion_id)
            ->pluck('car_classes.id')
            ->toArray();
    }

    private function savePromotionCustomerGroup($request, $promotion_id)
    {
        PromotionCustomerGroup::where('promotion_id', $promotion_id)->delete();
        if (!empty($request->customer_group)) {
            foreach ($request->customer_group as $customer_group_id) {
                $customer_group = new PromotionCustomerGroup();
                $customer_group->promotion_id = $promotion_id;
                $customer_group->customer_group_id = $customer_group_id;
                $customer_group->save();
            }
        }
        return true;
    }

    private function getPromotionCustomerGroupArray($promotion_id)
    {
        return PromotionCustomerGroup::join('promotions', 'promotions.id', '=', 'promotions_customer_groups.promotion_id')
            ->join('customer_groups', 'customer_groups.id', '=', 'promotions_customer_groups.customer_group_id')
            ->select('customer_groups.id as id', 'customer_groups.name as name')
            ->where('promotions_customer_groups.promotion_id', $promotion_id)
            ->pluck('customer_groups.id')
            ->toArray();
    }

    private function savePromotionSale($request, $promotion_id)
    {
        PromotionSale::where('promotion_id', $promotion_id)->delete();
        if (!empty($request->sale)) {
            foreach ($request->sale as $sale_id) {
                $sale = new PromotionSale();
                $sale->promotion_id = $promotion_id;
                $sale->user_id = $sale_id;
                $sale->save();
            }
        }
        return true;
    }

    private function savePromotionIncompatible($request, $promotion_id)
    {
        PromotionIncompatible::where('promotion_id', $promotion_id)->delete();
        if (!empty($request->incompatible)) {
            foreach ($request->incompatible as $incompatible_id) {
                $sale = new PromotionIncompatible();
                $sale->promotion_id = $promotion_id;
                $sale->promotion_incompatible_id = $incompatible_id;
                $sale->save();
            }
        }
        return true;
    }

    private function getPromotionSaleArray($promotion_id)
    {
        return PromotionSale::join('promotions', 'promotions.id', '=', 'promotions_sales.promotion_id')
            ->join('users', 'users.id', '=', 'promotions_sales.user_id')
            ->select('users.id as id', 'users.name as name')
            ->where('promotions_sales.promotion_id', $promotion_id)
            ->pluck('users.id')
            ->toArray();
    }

    private function getPromotionInCompatibleArray($promotion_id)
    {
        return PromotionIncompatible::join('promotions', 'promotions.id', '=', 'promotions_incompatible.promotion_incompatible_id')
            ->select('promotions_incompatible.promotion_incompatible_id as id', 'promotions.code as name')
            ->where('promotions_incompatible.promotion_id', $promotion_id)
            ->pluck('id')
            ->toArray();
    }

    private function savePromotionProduct($request, $promotion_id)
    {
        PromotionProduct::where('promotion_id', $promotion_id)->delete();
        if (!empty($request->product)) {
            foreach ($request->product as $product_id) {
                $product = new PromotionProduct();
                $product->promotion_id = $promotion_id;
                $product->product_id = $product_id;
                $product->save();
            }
        }
        return true;
    }


    private function savePromotionCode($request, $promotion_id)
    {
        $promotion_code = PromotionCode::where('promotion_id', $promotion_id)->first();
        $promotion_code = PromotionCode::firstOrNew(['promotion_id' => $promotion_id]);
        $promotion_code->promotion_id = $promotion_id;
        $promotion_code->code = $request->code;
        $promotion_code->quota = intval($request->quota);
        $promotion_code->can_reuse = STATUS_ACTIVE;
        $promotion_code->start_sale_date = $request->start_sale_date;
        $promotion_code->end_sale_date = $request->end_sale_date;
        $promotion_code->save();
        return true;
    }

    private function getPromotionProductArray($promotion_id)
    {
        return PromotionProduct::join('promotions', 'promotions.id', '=', 'promotions_products.promotion_id')
            ->join('products', 'products.id', '=', 'promotions_products.product_id')
            ->select('products.id as id', 'products.name as name')
            ->where('promotions_products.promotion_id', $promotion_id)
            ->pluck('products.id')
            ->toArray();
    }

    private function getStatusList()
    {
        return collect([
            [
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => __('promotions.status_' . STATUS_ACTIVE),
            ],
            [
                'id' => STATUS_INACTIVE,
                'value' => STATUS_INACTIVE,
                'name' => __('promotions.status_' . STATUS_INACTIVE),
            ],
        ]);
    }

    private function getDiscountTypeList()
    {
        return collect([
            [
                'id' => DiscountTypeEnum::PERCENT,
                'value' => DiscountTypeEnum::PERCENT,
                'name' => __('promotions.discount_type_' . DiscountTypeEnum::PERCENT),
            ],
            [
                'id' => DiscountTypeEnum::AMOUNT,
                'value' => DiscountTypeEnum::AMOUNT,
                'name' => __('promotions.discount_type_' . DiscountTypeEnum::AMOUNT),
            ],
            [
                'id' => DiscountTypeEnum::FIXED_PRICE,
                'value' => DiscountTypeEnum::FIXED_PRICE,
                'name' => __('promotions.discount_type_' . DiscountTypeEnum::FIXED_PRICE),
            ],
            /* [
                'id' => DiscountTypeEnum::FREE_PRODUCT,
                'value' => DiscountTypeEnum::FREE_PRODUCT,
                'name' => __('promotions.discount_type_' . DiscountTypeEnum::FREE_PRODUCT),
            ], */
            [
                'id' => DiscountTypeEnum::FREE_CAR_CLASS,
                'value' => DiscountTypeEnum::FREE_CAR_CLASS,
                'name' => __('promotions.discount_type_' . DiscountTypeEnum::FREE_CAR_CLASS),
            ],
            [
                'id' => DiscountTypeEnum::FREE_ADDITIONAL_PRODUCT,
                'value' => DiscountTypeEnum::FREE_ADDITIONAL_PRODUCT,
                'name' => __('promotions.discount_type_' . DiscountTypeEnum::FREE_ADDITIONAL_PRODUCT),
            ],

        ]);
    }

    private function getDiscountModeList()
    {
        return collect([
            [
                'id' => DiscountModeEnum::ALL,
                'value' => DiscountModeEnum::ALL,
                'name' => __('promotions.discount_mode_' . DiscountModeEnum::ALL),
            ],
            [
                'id' => DiscountModeEnum::TRANSACTION,
                'value' => DiscountModeEnum::TRANSACTION,
                'name' => __('promotions.discount_mode_' . DiscountModeEnum::TRANSACTION),
            ],

        ]);
    }

    private function getCheckList()
    {
        return collect([
            [
                'id' => BOOL_FALSE,
                'value' => BOOL_FALSE,
                'name' => __('promotions.no_check'),
            ],
            [
                'id' => BOOL_TRUE,
                'value' => BOOL_TRUE,
                'name' => __('promotions.check'),
            ],
        ]);
    }

    private function getPromotionTypeList()
    {
        return collect([
            [
                'id' => PromotionTypeEnum::PROMOTION,
                'value' => PromotionTypeEnum::PROMOTION,
                'name' => 'โปรโมชัน/คูปอง',
            ],
            [
                'id' => PromotionTypeEnum::VOUCHER,
                'value' => PromotionTypeEnum::VOUCHER,
                'name' => 'Voucher',
            ],

        ]);
    }

    private function getPromotionAllTypeList()
    {
        return collect([
            (object)[
                'id' => PromotionTypeEnum::PROMOTION,
                'value' => PromotionTypeEnum::PROMOTION,
                'name' => __('promotions.promotion_type_' . PromotionTypeEnum::PROMOTION),
            ],
            (object)[
                'id' => PromotionTypeEnum::COUPON,
                'value' => PromotionTypeEnum::COUPON,
                'name' => __('promotions.promotion_type_' . PromotionTypeEnum::COUPON),
            ],
            (object)[
                'id' => PromotionTypeEnum::VOUCHER,
                'value' => PromotionTypeEnum::VOUCHER,
                'name' => __('promotions.promotion_type_' . PromotionTypeEnum::VOUCHER),
            ],
            (object)[
                'id' => PromotionTypeEnum::PARTNER,
                'value' => PromotionTypeEnum::PARTNER,
                'name' => __('promotions.promotion_type_' . PromotionTypeEnum::PARTNER),
            ],

        ]);
    }

    private function getCouponList()
    {
        return collect([
            [
                'id' => PromotionTypeEnum::COUPON,
                'value' => PromotionTypeEnum::COUPON,
                'name' => 'มี',
            ],
            [
                'id' => PromotionTypeEnum::PROMOTION,
                'value' => PromotionTypeEnum::PROMOTION,
                'name' => 'ไม่มี',
            ],

        ]);
    }

    private function getCouponTypeList()
    {
        return collect([
            [
                'id' => PromotionTypeEnum::COUPON,
                'value' => PromotionTypeEnum::COUPON,
                'name' => 'คูปองจาก True-Leasing',
            ],
            [
                'id' => PromotionTypeEnum::PARTNER,
                'value' => PromotionTypeEnum::PARTNER,
                'name' => 'คูปองจาก Partner',
            ],

        ]);
    }

    private function getVoucherTypeList()
    {
        return collect([
            [
                'id' => BOOL_FALSE,
                'value' => BOOL_FALSE,
                'name' => __('promotions.voucher_type_' . BOOL_FALSE),
            ],
            [
                'id' => BOOL_TRUE,
                'value' => BOOL_TRUE,
                'name' => __('promotions.voucher_type_' . BOOL_TRUE),
            ],

        ]);
    }

    public function getCustomerGroupList()
    {
        $list = CustomerGroup::select('id', 'name')->get();
        return $list;
    }

    public function getCarClassList()
    {
        $list = CarClass::select('id', 'full_name as name')->get();
        return $list;
    }

    public function getBranchList()
    {
        $list = Branch::select('id', 'name')->get();
        return $list;
    }

    public function getProductAdditionalList()
    {
        $list = ProductAdditional::select('id', 'name')->get();
        return $list;
    }

    public function getSaleList()
    {
        $list = User::select('id', 'name')->get();
        return $list;
    }

    public function getProductList()
    {
        $list = Product::select('id', 'name')->get();
        return $list;
    }

    public function getProductSkuList()
    {
        $list = Product::select('id', 'sku as name')->get();
        return $list;
    }

    public function getInCompatiblePromotionList()
    {
        $list = Promotion::select('id', 'name')->get();
        return $list;
    }
}
