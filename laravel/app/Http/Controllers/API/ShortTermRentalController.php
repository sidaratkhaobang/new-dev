<?php

namespace App\Http\Controllers\API;

use App\Classes\OrderManagement;
use App\Classes\ProductManagement;
use App\Classes\PromotionManagement;
use App\Classes\Sap\SapProcess;
use App\Enums\DiscountTypeEnum;
use App\Enums\OrderChannelEnum;
use App\Enums\OrderLineTypeEnum;
use App\Enums\QuotationStatusEnum;
use App\Enums\ReceiptTypeEnum;
use App\Enums\RentalBillTypeEnum;
use App\Enums\RentalStateEnum;
use App\Enums\RentalStatusEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\ServiceTypeEnum;
use App\Enums\TransferTypeEnum;
use App\Factories\QuotationFactory;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Customer;
use App\Models\DrivingJob;
use App\Models\Product;
use App\Models\ProductAdditional;
use App\Models\ProductAdditionalRelation;
use App\Models\Promotion;
use App\Models\PromotionCode;
use App\Models\Quotation;
use App\Models\QuotationLine;
use App\Models\Receipt;
use App\Models\Rental;
use App\Models\RentalBill;
use App\Models\RentalBillPromotionCode;
use App\Models\RentalCheckIn;
use App\Models\RentalDriver;
use App\Models\RentalLine;
use App\Models\RentalProductAdditional;
use App\Models\ServiceType;
use App\Traits\InspectionTrait;
use App\Traits\RentalTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ShortTermRentalController extends Controller
{
    use RentalTrait;
    public const BRANCH_NOT_VALID = 'BRANCH_NOT_VALID';
    public const CAR_IDS_NOT_EXIST = 'CAR_IDS_NOT_EXIST';
    public const NOT_ALLOW_MORE_THAN_1_CAR = 'NOT_ALLOW_MORE_THAN_1_CAR';
    public const CAR_NOT_AVAILABLE = 'CAR_NOT_AVAILABLE';
    public const COUPON_INVALID = 'COUPON_INVALID';
    public const CAR_ID_INVALID = 'CAR_ID_INVALID';
    public const PRODUCT_ADDITIONAL_NOT_ALLOW = 'PRODUCT_ADDITIONAL_NOT_ALLOW';
    public const PRODUCT_ADDITIONAL_INVALID = 'PRODUCT_ADDITIONAL_INVALID';
    public const DRIVER_NOT_ALLOW = 'DRIVER_NOT_ALLOW';

    public function index(Request $request)
    {
        $s = $request->s;
        $list = Rental::leftjoin('service_types', 'service_types.id', '=', 'rentals.service_type_id')
            ->leftjoin('products', 'products.id', '=', 'rentals.product_id')
            ->select(
                'rentals.id as id',
                'rentals.worksheet_no as worksheet_no',
                'rentals.service_type_id as service_type_id',
                'service_types.name as service_type_name',
                'rentals.product_id as product_id',
                'products.name as product_name'
            )
            ->when(!empty($s), function ($query) use ($s) {
                return $query->where(function ($q) use ($s) {
                    $q->where('rentals.worksheet_no', 'like', '%' . $s . '%');
                });
            })
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function booking(Request $request)
    {
        $service_type_id = $request->service_type_id;
        $branch_id = $request->branch_id;
        $pickup_date = $request->pickup_date;
        $return_date = $request->return_date;
        $product_id = $request->product_id;
        $origin_id = $request->origin_id;
        $origin_name = $request->origin_name;
        $origin_lat = $request->origin_lat;
        $origin_lng = $request->origin_lng;
        $origin_name_custom = $request->origin_name_custom;
        $origin_address = $request->origin_address;
        $destination_id = $request->destination_id;
        $destination_name = $request->destination_name;
        $destination_lat = $request->destination_lat;
        $destination_lng = $request->destination_lng;
        $destination_name_custom = $request->destination_name_custom;
        $destination_address = $request->destination_address;
        $avg_distance = $request->avg_distance;
        $customer_id = $request->customer_id;
        $promotion_id = $request->promotion_id;
        $promotion_code_ids = $request->promotion_code_ids;
        $is_required_tax_invoice = $request->is_required_tax_invoice;
        $payment_method = $request->payment_method;
        $payment_remark = $request->payment_remark;
        // $payment_gateway = $request->payment_gateway;
        $rental_car_ids = $request->rental_car_ids;
        $product_additionals = $request->product_additionals;
        $lines = $request->lines;
        $drivers = $request->drivers;

        $today = date("Y-m-d H:i");
        $validator = Validator::make($request->all(), [
            'service_type_id' => 'required',
            'branch_id' => 'required',
            'product_id' => 'required',
            'pickup_date' => 'required|date_format:Y-m-d H:i|after:' . $today,
            'return_date' => 'required|date_format:Y-m-d H:i|after_or_equal:pickup_date',
            'origin_id' => 'required',
            'destination_id' => 'required',
            'customer_id' => 'required',
        ], [], [
            'service_type_id' => __('short_term_rentals.service_type'),
            'branch_id' => __('short_term_rentals.branch'),
            'product_id' => __('short_term_rentals.package'),
            'origin_id' => __('short_term_rentals.origin'),
            'destination_id' => __('short_term_rentals.destination'),
            'pickup_date' => __('short_term_rentals.pickup_date'),
            'return_date' => __('short_term_rentals.return_date'),
            'customer_id' => __('short_term_rentals.customer_id'),
            // 'customer_name' => __('short_term_rentals.customer_name'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        if ($service_type_id) {
            $service_type = ServiceType::find($service_type_id);
            if (!$service_type) {
                return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
            }
        }

        // check if branch is in list
        $branch_list = RentalTrait::getBranchOfServiceType($service_type_id)->pluck('id')->toArray();
        if (!in_array($branch_id, $branch_list)) {
            return $this->responseWithCode(false, self::BRANCH_NOT_VALID, null, 422);
        }

        // validate the package
        $pm = new ProductManagement($service_type_id);
        $pm->setDates(date('Y-m-d H:i:s', strtotime($pickup_date)), date('Y-m-d H:i:s', strtotime($return_date)));
        $validated = $pm->validate($product_id);
        if (!$validated) {
            return $this->responseWithCode(false, $pm->error_message, null, 422);
        }

        if (!is_array($rental_car_ids) || sizeof($rental_car_ids) < 1) {
            return $this->responseWithCode(false, self::CAR_IDS_NOT_EXIST, null, 422);
        }

        $can_add_multiple_car = RentalTrait::canAddMutipleCar($service_type_id);
        if (!$can_add_multiple_car && sizeof($rental_car_ids) > 1) {
            return $this->responseWithCode(false, self::NOT_ALLOW_MORE_THAN_1_CAR, null, 422);
        }

        // check if car availble for booking
        $car_available_list = RentalTrait::getCarRentalTimeLine($request);
        $is_allowed_for_booking = RentalTrait::checkCarsAllowForBooking($car_available_list, $rental_car_ids);
        if (!$is_allowed_for_booking) {
            return $this->responseWithCode(false, self::CAR_NOT_AVAILABLE, null, 422);
        }

        $rental_count = Rental::all()->count() + 1;
        $prefix = 'SR-';
        $rental = new Rental();
        $rental->worksheet_no = generateRecordNumber($prefix, $rental_count);
        $rental->service_type_id = $service_type_id;
        $rental->rental_type = RentalTypeEnum::SHORT;
        $rental->order_channel = OrderChannelEnum::APPLICATION;
        $rental->branch_id = $branch_id;
        $rental->product_id = $product_id;
        $rental->pickup_date = $pickup_date;
        $fix_return_date = $pm->getReturnDate($product_id, $pickup_date, $return_date);
        $rental->return_date = $fix_return_date;
        $rental->origin_id = $origin_id;
        $rental->origin_name = $origin_name;
        $rental->origin_lat = $origin_lat;
        $rental->origin_lng = $origin_lng;
        $rental->origin_address = $origin_address;
        if (!empty($origin_name_custom)) {
            $rental->origin_name = $origin_name_custom;
        }
        $rental->destination_id = $destination_id;
        $rental->destination_name = $destination_name;
        $rental->destination_lat = $destination_lat;
        $rental->destination_lng = $destination_lng;
        $rental->destination_address = $destination_address;
        if (!empty($destination_name_custom)) {
            $rental->destination_name = $destination_name_custom;
        }
        $rental->avg_distance = $avg_distance;
        $rental->customer_id = $customer_id;
        $customer = Customer::find($customer_id);
        if ($customer) {
            $rental->customer_name = $customer->name;
            $rental->customer_address = $customer->address;
            $rental->customer_tel = $customer->tel;
            $rental->customer_email = $customer->email;
            $rental->customer_province_id = $customer->province_id;
        }
        $rental->is_required_tax_invoice = isset($is_required_tax_invoice) ? $is_required_tax_invoice : false;
        $rental->payment_method = $payment_method;
        $rental->payment_remark = $payment_remark;
        $rental->rental_state = RentalStateEnum::SUMMARY;
        $rental->status = RentalStatusEnum::PENDING;
        $rental->save();

        $rental_bill = RentalTrait::createRentalBill($rental->id);
        // save cars
        $bill_subtotal = 0;
        $bill_total = 0;
        $product_total = 0;
        $product_amount = 0;
        foreach ($rental_car_ids as $rental_car_id) {
            $rental_line = $this->saveCarRentalLine($rental, $rental_bill, $rental_car_id);
            // calculate price
            $pm = new ProductManagement($rental->service_type_id);
            $pm->setBranchId($rental->branch_id);
            $pm->setDates(date('Y-m-d H:i:s', strtotime($rental->pickup_date)), date('Y-m-d H:i:s', strtotime($rental->return_date)));
            $price = $pm->findPrice($rental_line->item_id, $rental->pickup_date, $rental->return_date, [
                'car_id' => $rental_line->car_id
            ]);
            $rental_line->subtotal = $price;
            $rental_line->total = $price;
            $rental_line->save();
            $bill_subtotal += $price;
            $bill_total += $price;

            // *** add product-additional
            $product_additional_relations = ProductAdditionalRelation::select('*')->where('product_id', $rental->product_id)->get();
            foreach ($product_additional_relations as $relation) {
                $product_additional = $relation->product_additional;
                if ($product_additional) {
                    $product_additional_name = $product_additional->name;
                    $product_additional_price = abs(floatval($product_additional->price));
                }
                $rental_pa = new RentalProductAdditional();
                $rental_pa->rental_id = $rental->id;
                $rental_pa->rental_bill_id = $rental_bill->id;
                $rental_pa->product_additional_id = $relation->product_addtional_id;
                $rental_pa->name = $product_additional_name;
                $rental_pa->car_id = $rental_car_id;
                $rental_pa->price = $product_additional_price;
                $rental_pa->amount = abs(intval($relation->amount));
                $rental_pa->is_free = $relation->is_free;
                $rental_pa->is_from_product = STATUS_ACTIVE;
                $rental_pa->save();
                $product_amount += abs(intval($relation->amount));
                $product_total += $product_additional_price * abs(intval($relation->amount));
            }
            $bill_subtotal += $product_total;
            $bill_total += $product_total;
        }

        if (sizeof($product_additional_relations) > 0) {
            $rental_line = new RentalLine();
            $rental_line->rental_id = $rental->id;
            $rental_line->rental_bill_id = $rental_bill->id;
            $rental_line->item_type = OrderLineTypeEnum::ADDITIONAL_PRODUCT_COST;
            $rental_line->item_id = (string) Str::orderedUuid();
            $rental_line->name = __('short_term_rentals.type_' . OrderLineTypeEnum::ADDITIONAL_PRODUCT_COST);
            $rental_line->description = '';
            $rental_line->total = $product_total;
            $rental_line->amount = $product_amount;
            $rental_line->pickup_date = $rental->pickup_date;
            $rental_line->return_date = $rental->return_date;
            $rental_line->save();
        }

        $rental_bill->bill_type = RentalBillTypeEnum::PRIMARY;
        $rental_bill->status = RentalStatusEnum::PENDING;
        $rental_bill->subtotal = $bill_subtotal;
        $rental_bill->total = $bill_total;
        $rental_bill->save();

        if (is_array($product_additionals) && sizeof($product_additionals) > 0) {
            $this->saveProductAdditionals($product_additionals, $rental->id, $rental_bill->id);
        }

        if ($rental->serviceType && strcmp($rental->serviceType->service_type, ServiceTypeEnum::SELF_DRIVE) === 0) {
            if (is_array($drivers) && sizeof($drivers) > 0) {
                $this->saveDrivers($drivers, $rental->id);
            }
        }

        if (is_array($lines) && sizeof($lines) > 0) {
            $this->saveRentalLines($lines, $rental->id, $rental_bill->id);
        }

        // find promotion
        if (!empty($promotion_id)) {
            $promotion = Promotion::find($promotion_id);
            if ($promotion) {
                $rental_bill->promotion_id = $promotion_id;
                $rental_bill->save();
                if (strcmp($promotion->discount_type, DiscountTypeEnum::FREE_ADDITIONAL_PRODUCT) == 0) {
                    $car_rental_lines = $this->getRentalCars($rental_bill->id);
                    foreach ($car_rental_lines as $key => $car_rental_line) {
                        $this->saveRentalFreeProducts($rental->id, $promotion->id, $car_rental_line->car_id);
                    }
                }
            }
        }

        //validate promotion code
        if (is_array($promotion_code_ids) && sizeof($promotion_code_ids) > 0) {
            $is_valid = $this->validatePromotionCode($promotion_code_ids, $rental, $rental_bill);
            if (!$is_valid) {
                return $this->responseWithCode(false, self::COUPON_INVALID, null, 422);
            }

            foreach ($promotion_code_ids as $voucher_id) {
                $this->saveRentalBillPromotionCode($rental_bill->id, $voucher_id);
                if (!empty($voucher_id)) {
                    $this->clearOldPromotionCode($voucher_id);
                    $this->saveNewPromotionCode($voucher_id, $rental->customer_id);
                }
            }
        }
        $this->saveRentalBillSummary($rental_bill, $promotion_code_ids);
        // TO DO check status

        // $quotation = $this->createRentalQuotation($rental, $rental_bill);
        // $this->saveRentalQuotationLines($rental_bill->id, $quotation->id);
        // TODO remove rental_bill
        /* $qtf = new QuotationFactory($rental_bill);
        $qtf->create(); */
        return $this->responseWithCode(true, DATA_SUCCESS, $rental->id, 200);
    }


    public function saveProductAdditionals($product_additionals, $rental_id, $rental_bill_id)
    {
        if ($product_additionals && sizeof($product_additionals) > 0) {
            $total = 0;
            $total_amount = 0;
            foreach ($product_additionals as $item) {
                if (isset($item['product_additional_id']) && isset($item['car_id'])) {
                    $product_additional = ProductAdditional::find($item['product_additional_id']);
                    if (isset($item['id'])) {
                        $rental_pa = RentalProductAdditional::find($item['id']);
                        if ($rental_pa) {
                            $rental_pa->update($item);
                        }
                    } else {
                        $rental_pa = new RentalProductAdditional();
                        $rental_pa->rental_id = $rental_id;
                        $rental_pa->rental_id = $rental_id;
                        $rental_pa->rental_bill_id = $rental_bill_id;
                        $rental_pa->product_additional_id = $item['product_additional_id'];
                        $rental_pa->car_id = $item['car_id'];
                        $rental_pa->name = isset($item['name']) ? $item['name'] : $product_additional->name;
                        $price = isset($item['price']) ? $item['price'] : $product_additional->price;
                        $amount = isset($item['amount']) ? intval($item['amount']) : $product_additional->price;
                        $rental_pa->price = $price;
                        $rental_pa->amount = $amount;
                        $rental_pa->is_free = isset($item['is_free']) ? filter_var($item['is_free'], FILTER_VALIDATE_BOOLEAN) : false;
                        $rental_pa->is_from_product = isset($item['is_from_product']) ? filter_var($item['is_from_product'], FILTER_VALIDATE_BOOLEAN) : false;
                        $rental_pa->is_from_promotion = isset($item['is_from_promotion']) ? filter_var($item['is_from_promotion'], FILTER_VALIDATE_BOOLEAN) : false;
                        $rental_pa->save();
                    }
                    if (!$rental_pa->is_free) {
                        $total += floatval($rental_pa->price * $rental_pa->amount);
                    }
                    $total_amount += intval($rental_pa->amount);
                }
            }
            if ($total > 0) {
                $rental_line = new RentalLine();
                $rental_line->rental_id = $rental_id;
                $rental_line->rental_bill_id = $rental_bill_id;
                $rental_line->item_type = OrderLineTypeEnum::ADDITIONAL_PRODUCT_COST;
                $rental_line->item_id = (string) Str::orderedUuid();
                $rental_line->name = __('short_term_rentals.type_' . OrderLineTypeEnum::ADDITIONAL_PRODUCT_COST);
                $rental_line->description = __('short_term_rentals.product_additional');
                $rental_line->subtotal = $total;
                $rental_line->total = $total;
                $rental_line->amount = $total_amount;
                $rental_line->save();
            }

            $rental_bill = RentalBill::find($rental_bill_id);
            $rental_bill->subtotal += $total;
            $rental_bill->total += $total;
            $rental_bill->save();
        }
        return true;
    }

    public function updateProductAdditionals($product_additionals, $rental_id, $rental_bill_id)
    {
        $total = 0;
        $total_amount = 0;
        foreach ($product_additionals as $item) {
            // $product_additional = ProductAdditional::find($item['product_additional_id']);
            if (!isset($item['id'])) {
                return $this->responseWithCode(false, self::PRODUCT_ADDITIONAL_INVALID, null, 422);
            }
            $rental_pa = RentalProductAdditional::find($item['id']);
            if (!$rental_pa) {
                return $this->responseWithCode(false, self::PRODUCT_ADDITIONAL_INVALID, null, 422);
            }
            $rental_pa->update($item);
            if (!$rental_pa->is_free) {
                $total += floatval($rental_pa->price * $rental_pa->amount);
            }
            $total_amount += intval($rental_pa->amount);
        }

        if ($total > 0) {
            $rental_line = RentalLine::firstOrNew([
                'rental_id' => $rental_id,
                'rental_bill_id' => $rental_bill_id,
                'item_type' => OrderLineTypeEnum::ADDITIONAL_PRODUCT_COST
            ]);
            $rental_line->item_id = (string) Str::orderedUuid();
            $rental_line->name = __('short_term_rentals.type_' . OrderLineTypeEnum::ADDITIONAL_PRODUCT_COST);
            $rental_line->description = __('short_term_rentals.product_additional');
            $rental_line->subtotal = $total;
            $rental_line->total = $total;
            $rental_line->amount = $total_amount;
            $rental_line->save();
        }

        $rental_bill = RentalBill::find($rental_bill_id);
        $rental_bill->subtotal += $total;
        $rental_bill->total += $total;
        $rental_bill->save();
        return true;
    }

    public function validatePromotionCode($promotion_code_ids, $rental, $rental_bill)
    {
        $validate = true;
        foreach ($promotion_code_ids as $promotion_code_id) {
            $voucher = PromotionCode::leftjoin('promotions', 'promotions.id', '=', 'promotion_codes.promotion_id')
                ->where('promotion_codes.id', $promotion_code_id)
                ->whereNull('promotion_codes.customer_id')
                ->where('promotion_codes.is_booking', STATUS_DEFAULT)
                ->where('promotion_codes.is_used', STATUS_DEFAULT)
                ->whereNull('promotion_codes.use_date')
                ->select(
                    'promotion_codes.id as id',
                    'promotion_codes.code as code',
                    'promotion_codes.code as name',
                    'promotion_codes.promotion_id as promotion_id',
                )
                ->first();
            if (!empty($voucher)) {
                $pm = new PromotionManagement($rental);
                $pm->setBranchId($rental->branch_id);
                $pm->setDates(date('Y-m-d H:i:s', strtotime($rental->pickup_date)), date('Y-m-d H:i:s', strtotime($rental->return_date)));
                $pm->setTotal($rental_bill->total);
                $valid = $pm->validate($voucher->promotion_id);
                if (!$valid) {
                    $validate = false;
                }
            }
            return $validate;
        }
    }

    public function saveRentalBillSummary($rental_bill, $promotion_code_ids)
    {
        // summary
        $om = new OrderManagement($rental_bill);
        $om->setPromotion($rental_bill->promotion_id, $promotion_code_ids);
        $om->isWithholdingTax($rental_bill->check_withholding_tax);
        $om->calculate();
        $summary = $om->getSummary();
        $rental_bill->subtotal = $summary['subtotal'];
        $rental_bill->discount = $summary['discount'];
        $rental_bill->coupon_discount = $summary['coupon_discount'];
        $rental_bill->vat = $summary['vat'];
        $rental_bill->withholding_tax = $summary['withholding_tax'];
        $rental_bill->total = $summary['total'];
        $rental_bill->save();
        return true;
    }

    public function saveCarRentalLine($rental, $rental_bill, $rental_car_id)
    {
        $rental_line = new RentalLine();
        $rental_line->car_id = $rental_car_id;
        $rental_line->rental_id = $rental->id;
        $rental_line->rental_bill_id = $rental_bill->id;
        $rental_line->item_type = Product::class;
        $rental_line->item_id = $rental->product_id;
        $rental_line->amount = 1;
        $rental_line->pickup_date = $rental->pickup_date;
        $rental_line->return_date = $rental->return_date;
        $rental_line->save();
        return $rental_line;
    }

    public function saveRentalLines($lines, $renta_id, $rental_bill_id)
    {
        $total = 0;
        foreach ($lines as $key => $item) {
            if (isset($item['amount']) && isset($item['subtotal'])) {
                $amount = intval($item['amount']);
                $subtotal = floatval($item['subtotal']);
                $item_total = $subtotal * $amount;
                $rental_line = new RentalLine();
                $rental_line->item_type = OrderLineTypeEnum::EXTRA;
                $rental_line->item_id = (string) Str::orderedUuid();
                $rental_line->rental_id = $renta_id;
                $rental_line->rental_bill_id = $rental_bill_id;
                $rental_line->name = isset($item['name']) ? $item['name'] : '';
                $rental_line->description = isset($item['description']) ? $item['description'] : '';
                $rental_line->amount = $amount;
                $rental_line->subtotal = $subtotal;
                $rental_line->total = $item_total;
                $rental_line->save();
                $total += $item_total;
            }
        }

        $rental_bill = RentalBill::find($rental_bill_id);
        $rental_bill->subtotal += $total;
        $rental_bill->total += $total;
        $rental_bill->save();
        return true;
    }

    public function saveDrivers($drivers, $rental_id)
    {
        foreach ($drivers as $item) {
            if (isset($item['id'])) {
                $rental_driver = RentalDriver::find($item['id']);
                if ($rental_driver) {
                    $rental_driver->update($item);
                }
            } else {
                $rental_driver = new RentalDriver();
                $rental_driver->rental_id = $rental_id;
                $rental_driver->customer_driver_id = $item['customer_driver_id'] ?? null;
                $rental_driver->name = $item['name'] ?? null;
                $rental_driver->tel = $item['tel'] ?? null;
                $rental_driver->email = $item['email'] ?? null;
                $rental_driver->citizen_id = $item['citizen_id'] ?? null;
                $rental_driver->save();
            }
        }
        return true;
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ], [], [
            'id' => __('short_term_rentals.rental_id'),
        ]);
        $id = $request->id;
        $branch_id = $request->branch_id;
        $pickup_date = $request->pickup_date;
        $return_date = $request->return_date;
        $product_id = $request->product_id;
        $rental_car_ids = $request->rental_car_ids;
        $promotion_id = $request->promotion_id;
        $promotion_code_ids = $request->promotion_code_ids;
        $product_additionals = $request->product_additionals;
        $drivers = $request->drivers;
        $rental = Rental::find($id);
        if (!$rental) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }
        if ($request->customer_id) {
            $customer = Customer::find($request->customer_id);
            if (!$customer) {
                return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
            }

            $rental->customer_name = $customer->name;
            $rental->customer_address = $customer->address;
            $rental->customer_tel = $customer->tel;
            $rental->customer_email = $customer->email;
            $rental->customer_province_id = $customer->province_id;
        }

        if ($request->service_type_id) {
            $service_type = ServiceType::find($request->service_type_id);
            if (!$service_type) {
                return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
            }
        }

        $rental->fill($request->all());

        $is_allowed_to_update = $this->isAllowedToUpdate($rental, $request);
        if (!$is_allowed_to_update) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }

        if ($rental->isDirty('branch_id')) {
            // check if branch is in list
            $branch_list = RentalTrait::getBranchOfServiceType($rental->service_type_id)->pluck('id')->toArray();
            if (!in_array($branch_id, $branch_list)) {
                return $this->responseWithCode(false, self::BRANCH_NOT_VALID, null, 422);
            }
        }

        if ($rental->isDirty(['product_id', 'pickup_date', 'return_date'])) {
            $pickup_date = $pickup_date ?? $rental->pickup_date;
            $return_date = $return_date ?? $rental->return_date;
            $product_id = $product_id ?? $rental->product_id;
            $pm = new ProductManagement($rental->service_type_id);
            $pm->setDates(date('Y-m-d H:i:s', strtotime($pickup_date)), date('Y-m-d H:i:s', strtotime($return_date)));
            $validated = $pm->validate($product_id);
            if (!$validated) {
                return $this->responseWithCode(false, $pm->error_message, null, 422);
            }
        }

        $rental->save();
        $rental_bill = RentalBill::where('rental_id', $rental->id)
            ->where('bill_type', RentalBillTypeEnum::PRIMARY)
            ->first();

        if (is_array($rental_car_ids) && sizeof($rental_car_ids) > 0) {
            $can_add_multiple_car = RentalTrait::canAddMutipleCar($rental->service_type_id);
            if (!$can_add_multiple_car && sizeof($rental_car_ids) > 1) {
                $rental->refresh();
                return $this->responseWithCode(false, self::NOT_ALLOW_MORE_THAN_1_CAR, null, 422);
            }

            // check if car availble for booking
            $car_available_list = RentalTrait::getCarRentalTimeLine($request);
            $is_allowed_for_booking = RentalTrait::checkCarsAllowForBooking($car_available_list, $rental_car_ids);
            if (!$is_allowed_for_booking) {
                $rental->refresh();
                return $this->responseWithCode(false, self::CAR_NOT_AVAILABLE, null, 422);
            }

            $pending_delete_lines = RentalLine::where('rental_id', $rental->id)->where('item_type', Product::class)
                ->whereNotNull('car_id')->pluck('id')->toArray();
            $pending_delete_product_adds = RentalProductAdditional::where('rental_id', $rental->id)->pluck('id')->toArray();
            $bill_subtotal = 0;
            $bill_total = 0;
            $product_total = 0;
            $product_amount = 0;
            foreach ($rental_car_ids as $rental_car_id) {
                $car = Car::find($rental_car_id);
                if (!$car) {
                    $rental->refresh();
                    return $this->responseWithCode(false, self::CAR_ID_INVALID, null, 422);
                }
                $rental_line = $this->saveCarRentalLine($rental, $rental_bill, $rental_car_id);
                $pm = new ProductManagement($rental->service_type_id);
                $pm->setBranchId($rental->branch_id);
                $pm->setDates(date('Y-m-d H:i:s', strtotime($rental->pickup_date)), date('Y-m-d H:i:s', strtotime($rental->return_date)));
                $price = $pm->findPrice($rental_line->item_id, $rental->pickup_date, $rental->return_date, [
                    'car_id' => $rental_line->car_id
                ]);
                $rental_line->subtotal = $price;
                $rental_line->total = $price;
                $rental_line->save();
                $bill_subtotal += $price;
                $bill_total += $price;

                // *** add product-additional
                $product_additional_relations = ProductAdditionalRelation::select('*')->where('product_id', $rental->product_id)->get();
                foreach ($product_additional_relations as $relation) {
                    $product_additional = $relation->product_additional;
                    if ($product_additional) {
                        $product_additional_name = $product_additional->name;
                        $product_additional_price = abs(floatval($product_additional->price));
                    }
                    $rental_pa = new RentalProductAdditional();
                    $rental_pa->rental_id = $rental->id;
                    $rental_pa->rental_bill_id = $rental_bill->id;
                    $rental_pa->product_additional_id = $relation->product_addtional_id;
                    $rental_pa->name = $product_additional_name;
                    $rental_pa->car_id = $rental_car_id;
                    $rental_pa->price = $product_additional_price;
                    $rental_pa->amount = abs(intval($relation->amount));
                    $rental_pa->is_free = $relation->is_free;
                    $rental_pa->is_from_product = STATUS_ACTIVE;
                    $rental_pa->save();
                    $product_amount += abs(intval($relation->amount));
                    $product_total += $product_additional_price * abs(intval($relation->amount));
                }
                $bill_subtotal += $product_total;
                $bill_total += $product_total;
            }

            if (sizeof($product_additional_relations) > 0) {
                $rental_line = RentalLine::firstOrNew([
                    'rental_id' => $rental->id,
                    'rental_bill_id' => $rental_bill->id,
                    'item_type' => OrderLineTypeEnum::ADDITIONAL_PRODUCT_COST
                ]);
                $rental_line->item_id = (string) Str::orderedUuid();
                $rental_line->name = __('short_term_rentals.type_' . OrderLineTypeEnum::ADDITIONAL_PRODUCT_COST);
                $rental_line->description = '';
                $rental_line->total = $product_total;
                $rental_line->amount = $product_amount;
                $rental_line->pickup_date = $rental->pickup_date;
                $rental_line->return_date = $rental->return_date;
                $rental_line->save();
            }
            $rental_bill->subtotal = $bill_subtotal;
            $rental_bill->total = $bill_total;
            $rental_bill->save();

            RentalLine::whereIn('id', $pending_delete_lines)->delete();
            RentalProductAdditional::whereIn('id', $pending_delete_product_adds)->delete();
        }

        if (is_array($product_additionals) && sizeof($product_additionals) > 0) {
            if (in_array($rental->status, [RentalStatusEnum::DRAFT, RentalStatusEnum::PENDING])) {
                $this->updateProductAdditionals($product_additionals, $rental->id, $rental_bill->id);
            } else {
                $rental->refresh();
                return $this->responseWithCode(false, self::PRODUCT_ADDITIONAL_NOT_ALLOW, null, 422);
            }
        }

        if (is_array($drivers) && sizeof($drivers) > 0) {
            if (
                $rental->serviceType && strcmp($rental->serviceType->service_type, ServiceTypeEnum::SELF_DRIVE) === 0
                && in_array($rental->status, [RentalStatusEnum::DRAFT, RentalStatusEnum::PENDING, RentalStatusEnum::PAID, RentalStatusEnum::CHANGE])
            ) {
                $this->saveDrivers($drivers, $rental->id);
            } else {
                $rental->refresh();
                return $this->responseWithCode(false, self::DRIVER_NOT_ALLOW, null, 422);
            }
        }

        if (in_array($rental->status, [RentalStatusEnum::DRAFT, RentalStatusEnum::PENDING])) {
            // find promotion
            $pm = new PromotionManagement($rental);
            $pm->setBranchId($rental->branch_id);
            $pm->setDates(date('Y-m-d H:i:s', strtotime($rental->pickup_date)), date('Y-m-d H:i:s', strtotime($rental->return_date)));
            $promotion = $pm->find($promotion_id);

            if (!empty($promotion_id)) {
                $rental_bill->promotion_id = $promotion_id;
            } else {
                $rental_bill->promotion_id = null;
            }

            RentalTrait::clearRentalBillPromotionCode($rental_bill->id, $rental_bill->rental_id);
            if (!empty($promotion_code_ids) && sizeof($promotion_code_ids) > 0) {
                // Check validate voucher first
                foreach ($promotion_code_ids as $voucher_id) {
                    $rental_bill_promo_code = new RentalBillPromotionCode();
                    $rental_bill_promo_code->rental_bill_id = $rental_bill->id;
                    $rental_bill_promo_code->promotion_code_id = $voucher_id;
                    $rental_bill_promo_code->save();
                    if (!empty($voucher_id)) {
                        $old_promotion_code_cleared = $this->clearOldPromotionCode($voucher_id);
                        $promotion_code_saved = $this->saveNewPromotionCode($voucher_id, $rental->customer_id);
                    } else {
                        // $rental_bill->promotion_code_id = null;
                    }
                }
            }
            $rental->save();
            $rental_bill->save();

            // clear + add gift from promotion
            if (!empty($promotion)) {
                // RentalLine::where('rental_id', $rental->id)->where('is_from_promotion', '1')->delete();
                RentalProductAdditional::where('rental_id', $rental->id)->where('is_from_promotion', '1')->delete();
                if (strcmp($promotion->discount_type, DiscountTypeEnum::FREE_ADDITIONAL_PRODUCT) == 0) {
                    $car_rental_lines = $this->getRentalCars($rental_bill->id);
                    foreach ($car_rental_lines as $key => $car_rental_line) {
                        RentalTrait::saveRentalFreeProducts($rental->id, $promotion->id, $car_rental_line->car_id);
                    }
                }
            }
            $checked_vouchers = RentalTrait::getSelectedVoucher($rental_bill->id);
            $om = new OrderManagement($rental_bill);
            $om->setPromotion($rental_bill->promotion_code, $checked_vouchers);
            $om->isWithholdingTax($rental_bill->check_withholding_tax);
            $om->calculate();
            $summary = $om->getSummary();

            $rental_bill->subtotal = $summary['subtotal'];
            $rental_bill->discount = $summary['discount'];
            $rental_bill->coupon_discount = $summary['coupon_discount'];
            $rental_bill->vat = $summary['vat'];
            $rental_bill->withholding_tax = $summary['withholding_tax'];
            $rental_bill->total = $summary['total'];
            $rental_bill->save();
        }
        $this->updateQuotation($rental);
        return $this->responseWithCode(true, DATA_SUCCESS, $rental->id, 200);
    }

    public function isAllowedToUpdate($model, $request)
    {
        if (in_array($model->status, [RentalStatusEnum::PREPARE, RentalStatusEnum::AWAIT_RECEIVE, RentalStatusEnum::ACTIVE, RentalStatusEnum::AWAIT_RETURN])) {
            if (
                $model->isDirty([
                    'pickup_date',
                    'product_id',
                    'branch_id',
                    'origin_id',
                    'origin_lat',
                    'origin_lng',
                    'origin_name_custom',
                    'origin_address',
                    'avg_distance',
                    'customer_id',
                    'promotion_id',
                    'payment_method',
                    'payment_remark',
                    'remark',
                    'is_required_tax_invoice'
                ])
            ) {
                return false;
            }

            if (is_array($request->rental_car_ids) && (sizeof($request->rental_car_ids) > 0)) {
                return false;
            }

            if (is_array($request->product_additionals) && (sizeof($request->product_additionals) > 0)) {
                return false;
            }
        }

        if (in_array($model->status, [RentalStatusEnum::PAID, RentalStatusEnum::CHANGE])) {
            if (
                $model->isDirty([
                    'product_id',
                    'branch_id',
                    'avg_distance',
                    'customer_id',
                    'promotion_id',
                    'payment_method',
                    'payment_remark',
                    'remark',
                    'is_required_tax_invoice'
                ])
            ) {
                return false;
            }
            if (is_array($request->rental_car_ids) && (sizeof($request->rental_car_ids) > 0)) {
                return false;
            }
            if (is_array($request->product_additionals) && (sizeof($request->product_additionals) > 0)) {
                return false;
            }
        }
        return true;
    }

    public function read(Request $request)
    {
        $data = Rental::select(
            'rentals.id',
            'rentals.worksheet_no',
            'rentals.service_type_id',
            'service_types.name as service_type_name',
            'rentals.branch_id',
            'branches.name as branch_name',
            'rentals.pickup_date',
            'rentals.return_date',
            'rentals.product_id',
            'products.name as product_name',
            'rentals.origin_id',
            'rentals.origin_name',
            'rentals.origin_lat',
            'rentals.origin_lng',
            'rentals.origin_address',
            'rentals.destination_id',
            'rentals.destination_name',
            'rentals.destination_lat',
            'rentals.destination_lng',
            'rentals.destination_address',
            'rentals.avg_distance',
            'rentals.customer_id',
            'rentals.customer_name',
            'rentals.remark as remark',
            'rentals.status as status',
            'rentals.promotion_id',
        )
            ->leftJoin('service_types', 'service_types.id', '=', 'rentals.service_type_id')
            ->leftJoin('branches', 'branches.id', '=', 'rentals.branch_id')
            ->leftJoin('products', 'products.id', '=', 'rentals.product_id')
            ->leftJoin('rental_bills', 'rental_bills.rental_id', '=', 'rentals.id')
            ->where('rentals.id', $request->id)
            ->addSelect(
                DB::raw('SUM(rental_bills.subtotal) as sum_subtotal'),
                DB::raw('SUM(rental_bills.discount) as sum_discount'),
                DB::raw('SUM(rental_bills.coupon_discount) as sum_coupon_discount'),
                DB::raw('SUM(rental_bills.vat) as sum_vat'),
                DB::raw('SUM(rental_bills.total) as sum_total'),
            )
            ->groupBy(
                'rentals.id',
                'rentals.worksheet_no',
                'rentals.service_type_id',
                'service_type_name',
                'rentals.branch_id',
                'branch_name',
                'rentals.pickup_date',
                'rentals.return_date',
                'rentals.product_id',
                'product_name',
                'rentals.origin_id',
                'rentals.origin_name',
                'rentals.origin_lat',
                'rentals.origin_lng',
                'rentals.origin_address',
                'rentals.destination_id',
                'rentals.destination_name',
                'rentals.destination_lat',
                'rentals.destination_lng',
                'rentals.destination_address',
                'rentals.avg_distance',
                'rentals.customer_id',
                'rentals.customer_name',
                'remark',
                'status',
                'rentals.promotion_id',
            )
            ->first();

        $data_bill = RentalBill::where('rental_id', $request->id)
            ->select(
                'id as id',
                'worksheet_no as worksheet_no',
                'bill_type',
                'subtotal',
                'discount',
                'coupon_discount',
                'vat',
                'total',
                'payment_method',
                'payment_remark',
                'status as status',
            )
            ->get()->toArray();
        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }

        $service_type = ($data->serviceType) ? $data->serviceType?->service_type : null;
        $allow_driver = RentalTrait::canAddDriver($service_type);
        if ($allow_driver) {
            $data_driver = RentalTrait::getRentalDriverList($request->id);
            $data_driver = $data_driver->makeHidden([
                'rental_id',
                'customer_driver_id',
                'is_check_dup',
                'pending_delete_license_files',
                'pending_delete_citizen_files',
                'media',
                'license_id',
                'citizen_id',
            ]);
        } else {
            $data_driver = [];
            $driver = new \stdClass();
            $driver->id = $data->customer_id;
            $driver->name = $data->customer_name;
            $driver->tel = $data->customer_tel;
            $driver->email = $data->customer_email;
            $data_driver[] = $driver;
        }

        $data_car = Car::leftJoin('rental_lines', 'rental_lines.car_id', '=', 'cars.id')
            ->leftJoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->where('rental_lines.rental_id', $request->id)
            ->select(
                'cars.id as car_id',
                'cars.car_class_id',
                'car_classes.full_name as car_class_name',
                'cars.license_plate'
            )
            ->get();
        foreach ($data_car as $car) {
            $car->stopovers = RentalTrait::getRentalCheckInList($request->id, $car->car_id);
            $car->product_adds = RentalTrait::getRentalLineProductAdditionalList($request->id, $car->car_id);
        }

        $data_promotion_code = RentalBillPromotionCode::leftJoin('rental_bills', 'rental_bills.id', '=', 'rental_bills_promotion_codes.rental_bill_id')
            ->where('rental_bills.rental_id', $request->id)
            ->where('bill_type', RentalBillTypeEnum::PRIMARY)
            ->select(
                'rental_bills_promotion_codes.promotion_code_id as promotion_code_id',
            )
            ->pluck('promotion_code_id');

        $product_transport_list = null;
        $product_transport_return_list = null;
        $allow_product_transport = RentalTrait::canAddProductTransport($service_type);
        if ($allow_product_transport) {
            $product_transport_list = RentalTrait::getRentalProductTransportList($request->id, TransferTypeEnum::OUT);
            $product_transport_return_list = RentalTrait::getRentalProductTransportReturnList($request->id, TransferTypeEnum::IN);

            $product_transport_list->map(function ($item) {
                $product_file_medias = $item->getMedia('product_file');
                $product_files = get_medias_detail($product_file_medias);
                $product_files = collect($product_files)->map(function ($item) {
                    $item['formated'] = true;
                    $item['saved'] = true;
                    $item['raw_file'] = null;
                    return $item;
                })->toArray();
                $item->product_files = $product_files;
                return $item;
            });

            $product_transport_return_list->map(function ($item) {
                $product_file_medias = $item->getMedia('product_file_return');
                $product_files_return = get_medias_detail($product_file_medias);
                $product_files_return = collect($product_files_return)->map(function ($item) {
                    $item['formated'] = true;
                    $item['saved'] = true;
                    $item['raw_file'] = null;
                    return $item;
                })->toArray();
                $item->product_files_return = $product_files_return;
                return $item;
            });
        }

        $data->product_transports = $product_transport_list;
        $data->product_transport_returns = $product_transport_return_list;

        $receipts = Receipt::where('reference_type', Rental::class)
            ->where('reference_id', $request->id)
            // ->where('receipt_type', ReceiptTypeEnum::CAR_RENTAL)
            ->get();
        $data_receipts = [];
        foreach ($receipts as $key => $item) {
            $ob = [
                'worksheet_no' => $item->worksheet_no,
                'link' => route('receipt-pdf', ['id' => $item->id])
            ];
            $data_receipts[] = $ob;
        }
        $data->receipts = $data_receipts;
        $data->status_name = __('short_term_rentals.status_' . $data->status);
        $data->promotion_code_ids = $data_promotion_code;
        $data->cars = $data_car;
        $data->driver = $data_driver;
        $data->rental_bills = $data_bill;
        $inspection = InspectionTrait::getInspectionJob(Rental::class, $request->id);
        $data->inspection_job_id = $inspection ? $inspection->id : null;
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }

    public function updateQuotation($rental)
    {
        $rental_bill = RentalBill::where('rental_id', $rental->id)->where('bill_type', RentalBillTypeEnum::OTHER)->get();
        if ($rental_bill) {
            $user = Auth::user();
            foreach ($rental_bill as $item_bill) {
                $quotation = Quotation::firstOrNew(['rental_bill_id' => $item_bill->id]);
                $quotation_count = Quotation::all()->count() + 1;
                $prefix = 'QT';
                if (!($quotation->exists)) {
                    $quotation->qt_no = generateRecordNumber($prefix, $quotation_count, false);
                }
                $quotation->qt_type = QuotationStatusEnum::DRAFT;
                $quotation->reference_type = Rental::class;
                $quotation->reference_id = $rental->id;
                $quotation->customer_id = $rental->customer_id;
                $quotation->customer_name = $rental->customer_name;
                $quotation->customer_address = $rental->customer_address;
                $quotation->customer_tel = $rental->customer_tel;
                $quotation->customer_email = $rental->customer_email;
                $quotation->customer_zipcode = $rental->customer_zipcode;
                $quotation->customer_province_id = $rental->customer_province_id;
                $quotation->subtotal = $item_bill->subtotal;
                $quotation->vat = $item_bill->vat;
                $quotation->total = $item_bill->total;
                $quotation->rental_bill_id = $item_bill->id;
                $quotation->save();

                $quotation->ref_1 = ($user && $user->branch) ? $user->branch->code : null;
                $quotation->ref_2 = $quotation->qt_no;
                $quotation->save();

                $rental->quotation_id = $quotation->id;
                $rental->save();
            }

            $rental_lines = RentalLine::whereIn('rental_bill_id', $rental_bill->pluck('id'))->get();
            if ($rental_lines) {
                foreach ($rental_lines as $item_line) {
                    $quotations = Quotation::where('rental_bill_id', $item_line->rental_bill_id)->first();
                    $quotation_line = new QuotationLine();
                    $quotation_line->quotation_id = $quotations->id;
                    $quotation_line->reference_id = $item_line->id;
                    $quotation_line->reference_type = RentalLine::class;
                    $quotation_line->amount = $item_line->amount;
                    $quotation_line->subtotal = $item_line->subtotal;
                    $quotation_line->save();
                }
            }
        }
        return true;
    }

    public function checkDriver(Request $request)
    {
        $id = $request->id;
        $is_valid = $request->is_valid;
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'exists:rentals,id'],
            'is_valid' => ['required', 'boolean'],
        ], [], [
            'id' => __('short_term_rentals.id'),
            'is_valid' => __('lang.is_valid'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $rental_drivers = RentalDriver::where('rental_id', $id)->get();
        if (sizeof($rental_drivers) == 0) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 422);
        }
        foreach ($rental_drivers as $key => $rental_driver) {
            $rental_driver->is_valid = $is_valid;
            $rental_driver->save();
        }
        return $this->responseWithCode(true, DATA_SUCCESS, $rental_drivers, 200);
    }

    public function rentalCheckIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rental_id' => ['required', 'exists:rentals,id'],
            'car_id' => ['required', 'exists:cars,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'location_name' => ['nullable', 'string', 'max:200'],
            'lat' => ['nullable', 'string', 'max:200'],
            'lng' => ['nullable', 'numeric', 'max:200'],
            'arrived_at' => ['nullable', 'date'],
            'departured_at' => ['nullable', 'date'],
        ], [], [
            'rental_id' => __('short_term_rentals.id'),
            'car_id' => 'car id',
            'location_id' => 'location id',
            'location_name' => 'location name',
            'lat' => 'lat',
            'lng' => 'lng',
            'arrived_at' => 'arrived at',
            'departured_at' => 'departured at',
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $rental_check_in = new RentalCheckIn;
        $rental_check_in->rental_id = $request->rental_id;
        $rental_check_in->car_id = $request->car_id;
        $rental_check_in->location_id = $request->location_id;
        $rental_check_in->location_name = $request->location_name;
        $rental_check_in->lat = $request->lat;
        $rental_check_in->lng = $request->lng;
        $rental_check_in->arrived_at = $request->arrived_at;
        $rental_check_in->departured_at = $request->departured_at;
        $rental_check_in->save();
        return $this->responseWithCode(true, DATA_SUCCESS, $rental_check_in, 200);
    }
}
