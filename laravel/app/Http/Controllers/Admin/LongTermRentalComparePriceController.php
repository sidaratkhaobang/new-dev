<?php

namespace App\Http\Controllers\Admin;

use App\Classes\NotificationManagement;
use App\Enums\ComparisonPriceStatusEnum;
use App\Enums\DepartmentEnum;
use App\Enums\LongTermRentalPriceStatusEnum;
use App\Enums\LongTermRentalStatusEnum;
use App\Enums\NotificationScopeEnum;
use App\Enums\SpecStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\CarClass;
use App\Models\ComparisonPrice;
use App\Models\ComparisonPriceLine;
use App\Models\Customer;
use App\Models\LongTermRental;
use App\Models\LongTermRentalLine;
use App\Models\LongTermRentalLineAccessory;
use App\Models\LongTermRentalLineMonth;
use App\Models\LongTermRentalMonth;
use App\Models\LongTermRentalType;
use App\Models\PurchaseOrderLine;
use App\Traits\CustomerTrait;
use App\Traits\InsuranceTrait;
use App\Traits\LongTermRentalTrait;
use App\Traits\NotificationTrait;
use App\Traits\PurchaseRequisitionTrait;
use App\Traits\RentalTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class LongTermRentalComparePriceController extends Controller
{
    use LongTermRentalTrait;
    use RentalTrait, PurchaseRequisitionTrait, CustomerTrait;

    public function index(Request $request)
    {
        $customer_id = $request->customer;
        $worksheet_id = $request->worksheet_no;
        $spec_status_id = $request->spec_status;
        $lt_rental_type = $request->lt_rental_type;
        $status = $request->status;
        $compare_price_status_list = RentalTrait::getComparePriceStatusList();
        $list = $this->getLongTermRental($request);
        $customer_list = LongTermRental::select('id', 'customer_name as name')
            ->byComparePriceStatus()->get();

        $worksheet_list = LongTermRental::select('id', 'worksheet_no as name')
            ->byComparePriceStatus()->get();
        $lt_rental_type_list = LongTermRentalType::withTrashed()->get();
        return view('admin.long-term-rental-compare-price.index', [
            'list' => $list,
            'status' => $status,
            'worksheet_id' => $worksheet_id,
            'worksheet_list' => $worksheet_list,
            'compare_price_status_list' => $compare_price_status_list,
            's' => $request->s,
            'customer_id' => $customer_id,
            'customer_list' => $customer_list,
            'spec_status_id' => $spec_status_id,
            'lt_rental_type' => $lt_rental_type,
            'lt_rental_type_list' => $lt_rental_type_list,
            'from_offer_date' => $request->from_offer_date,
            'to_offer_date' => $request->to_offer_date,
        ]);
    }

    public function getLongTermRental($request)
    {
        $status = $request->status;
        $worksheet_id = $request->worksheet_id;
        $s = $request->s;
        $lt_rental_type = $request->lt_rental_type;

        $list = LongTermRental::sortable(['created_at' => 'desc'])
            ->select(
                'lt_rentals.id',
                'lt_rentals.worksheet_no',
                'lt_rentals.spec_status',
                'lt_rentals.comparison_price_status',
                'lt_rentals.customer_name',
                'lt_rentals.offer_date',
                'lt_rentals.status',
                'lt_rental_types.name as rental_type'
            )
            ->leftJoin('lt_rental_types', 'lt_rental_types.id', '=', 'lt_rentals.lt_rental_type_id')
            ->where('lt_rentals.spec_status', SpecStatusEnum::CONFIRM)
            // ->where('status', LongTermRentalStatusEnum::COMPARISON_PRICE)
            ->whereIn('lt_rentals.comparison_price_status', [ComparisonPriceStatusEnum::DRAFT, ComparisonPriceStatusEnum::CONFIRM])
            ->when($status, function ($query) use ($status) {
                $query->where('lt_rentals.comparison_price_status', $status);
            })
            ->when($lt_rental_type, function ($query) use ($lt_rental_type) {
                $query->where('lt_rentals.lt_rental_type_id', $lt_rental_type);
            })
            ->when($s, function ($query) use ($s) {
                $query->where('lt_rentals.worksheet_no', 'like', '%' . $s . '%');
            })
            ->when($worksheet_id, function ($query) use ($worksheet_id) {
                $query->where('lt_rentals.id', $worksheet_id);
            })
            ->orderBy('lt_rentals.worksheet_no')
            ->paginate(PER_PAGE);
        return $list;
    }

    public function show(LongTermRental $rental)
    {
        $allow_edit_status = [ComparisonPriceStatusEnum::DRAFT, ComparisonPriceStatusEnum::REJECT, ComparisonPriceStatusEnum::CONFIRM];
        if (!in_array($rental->comparison_price_status, $allow_edit_status)) {
            return redirect()->route('admin.long-term-rental.compare-price.index');
        }
        $cars = $this->getCarSpecAndEquipments($rental->id);
        $option = [];
        $option['item_type'] = LongTermRental::class;
        $compare_price_list = PurchaseRequisitionTrait::getComparePriceList($rental->id, $option);
        $compare_price_list->map(function ($item) {
            $item->car_price = $item->total;
            return $item;
        });
        $with_trash = true;
        $lt_rental_type_list = LongTermRentalTrait::getRentalJobTypeList($with_trash);
        $customer_type_list = CustomerTrait::getCustomerType();
        $customer_code = null;
        if ($rental->customer_id) {
            $customer = Customer::find($rental->customer_id);
            $customer_code = $customer->customer_code;
            $rental->customer_type = $customer->customer_type;
        }

        $long_term_rental_id = $rental->id;
        $long_term_rental = LongTermRental::find($long_term_rental_id);
        if (empty($long_term_rental)) {
            return redirect()->route('admin.long-term-rentals.index');
        }
        $tor_files = $long_term_rental->getMedia('tor_file');
        $tor_files = get_medias_detail($tor_files);
        $car_list = LongTermRentalLine::leftjoin('lt_rental_tor_lines', 'lt_rental_tor_lines.id', '=', 'lt_rental_lines.lt_rental_tor_line_id')
            ->leftjoin('lt_rental_tors', 'lt_rental_tors.id', '=', 'lt_rental_tor_lines.lt_rental_tor_id')
            ->where('lt_rental_lines.lt_rental_id', $long_term_rental->id)
            ->select(
                'lt_rental_lines.*',
                'lt_rental_tors.id as tor_id',
                'lt_rental_tors.remark_tor',
            )
            // ->orderBy('tor_id')
            ->get();
        $car_list->map(function ($item) {
            $long_term_remtal_line_accessories = LongTermRentalLineAccessory::where('lt_rental_line_id', $item->id)->get();
            $long_term_remtal_line_accessories = count($long_term_remtal_line_accessories);
            $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
            $item->car_color_text = ($item->color) ? $item->color->name : '';
            $item->amount_car = $item->amount;
            $item->remark_tor = $item->remark_tor;
            $accessory_list = $this->getAccessoriesByTorLineId($item->id);
            $item->accessory_list = $accessory_list;
            return $item;
        });

        $accessory_list = LongTermRentalLineAccessory::whereIn('lt_rental_line_id', $car_list->pluck('id'))->get();
        $car_accessory = [];
        $index = 0;
        foreach ($car_list as $car_index => $car_item) {
            foreach ($accessory_list as $accessory_item) {
                if (strcmp($car_item->id, $accessory_item->lt_rental_line_id) == 0) {
                    $car_accessory[$index]['accessory_id'] = $accessory_item->accessory_id;
                    $car_accessory[$index]['accessory_text'] = ($accessory_item->accessory) ? $accessory_item->accessory->name : '';
                    $car_accessory[$index]['amount_accessory'] = $accessory_item->amount;
                    $car_accessory[$index]['amount_per_car_accessory'] = $accessory_item->amount_per_car;
                    $car_accessory[$index]['tor_section'] = $accessory_item->tor_section;
                    $car_accessory[$index]['remark'] = $accessory_item->remark;
                    $car_accessory[$index]['car_index'] = $car_index;
                    $index++;
                }
            }
        }

        $page_title = __('long_term_rentals.compare_price');
        return view('admin.long-term-rental-compare-price.form', [
            'd' => $rental,
            'page_title' => $page_title,
            'purchase_requisition_cars' => $cars,
            'purchase_order_dealer_list' => $compare_price_list,
            'purchase_requisition_car_list' => $cars,
            'lt_rental_type_list' => $lt_rental_type_list,
            'customer_type_list' => $customer_type_list,
            'customer_code' => $customer_code,

            'tor_files' => $tor_files,
            'page_title' => $page_title,
            'car_list' => $car_list,
            'car_accessory' => $car_accessory,
            'view' => true,
        ]);
    }

    public function getCarSpecAndEquipments($lt_rental_id)
    {
        $cars = LongTermRentalLine::leftJoin('car_classes', 'car_classes.id', '=', 'lt_rental_lines.car_class_id')
            ->leftJoin('car_colors', 'car_colors.id', '=', 'lt_rental_lines.car_color_id')
            ->where('lt_rental_id', $lt_rental_id)
            ->select(
                'lt_rental_lines.id as id',
                'car_classes.name as model',
                'car_classes.full_name as model_full_name',
                'car_colors.id as color_id',
                'car_colors.name as color',
                'lt_rental_lines.amount as amount',
                'lt_rental_lines.showroom_price as showroom_price',
            )
            ->get();
        return $cars;
    }

    public function edit(LongTermRental $rental)
    {
        $allow_edit_status = [ComparisonPriceStatusEnum::DRAFT, ComparisonPriceStatusEnum::REJECT, ComparisonPriceStatusEnum::CONFIRM];
        if (!in_array($rental->comparison_price_status, $allow_edit_status)) {
            return redirect()->route('admin.long-term-rental.compare-price.index');
        }

        $cannot_add = null;
        if (strcmp($rental->comparison_price_status, ComparisonPriceStatusEnum::CONFIRM) == 0) {
            $cannot_add = true;
        }

        $cars = $this->getCarSpecAndEquipments($rental->id);
        $option = [];
        $option['item_type'] = LongTermRental::class;
        $compare_price_list = PurchaseRequisitionTrait::getComparePriceList($rental->id, $option);
        $compare_price_list->map(function ($item) {
            $item->car_price = $item->total;
            return $item;
        });
        $with_trash = true;
        $lt_rental_type_list = LongTermRentalTrait::getRentalJobTypeList($with_trash);
        $customer_type_list = CustomerTrait::getCustomerType();
        $customer_code = null;
        if ($rental->customer_id) {
            $customer = Customer::find($rental->customer_id);
            $customer_code = $customer->customer_code;
            $rental->customer_type = $customer->customer_type;
        }

        $long_term_rental_id = $rental->id;
        $long_term_rental = LongTermRental::find($long_term_rental_id);
        if (empty($long_term_rental)) {
            return redirect()->route('admin.long-term-rentals.index');
        }
        $tor_files = $long_term_rental->getMedia('tor_file');
        $tor_files = get_medias_detail($tor_files);
        $car_list = LongTermRentalLine::leftjoin('lt_rental_tor_lines', 'lt_rental_tor_lines.id', '=', 'lt_rental_lines.lt_rental_tor_line_id')
            ->leftjoin('lt_rental_tors', 'lt_rental_tors.id', '=', 'lt_rental_tor_lines.lt_rental_tor_id')
            ->where('lt_rental_lines.lt_rental_id', $long_term_rental->id)
            ->select(
                'lt_rental_lines.*',
                'lt_rental_tors.id as tor_id',
                'lt_rental_tors.remark_tor',
            )
            ->get();
        $car_list->map(function ($item) {
            $long_term_remtal_line_accessories = LongTermRentalLineAccessory::where('lt_rental_line_id', $item->id)->get();
            $long_term_remtal_line_accessories = count($long_term_remtal_line_accessories);
            $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
            $item->car_color_text = ($item->color) ? $item->color->name : '';
            $item->amount_car = $item->amount;
            $item->remark_tor = $item->remark_tor;
            $accessory_list = $this->getAccessoriesByTorLineId($item->id);
            $item->accessory_list = $accessory_list;
            return $item;
        });

        $accessory_list = LongTermRentalLineAccessory::whereIn('lt_rental_line_id', $car_list->pluck('id'))->get();
        $car_accessory = [];
        $index = 0;
        foreach ($car_list as $car_index => $car_item) {
            foreach ($accessory_list as $accessory_item) {
                if (strcmp($car_item->id, $accessory_item->lt_rental_line_id) == 0) {
                    $car_accessory[$index]['accessory_id'] = $accessory_item->accessory_id;
                    $car_accessory[$index]['accessory_text'] = ($accessory_item->accessory) ? $accessory_item->accessory->name : '';
                    $car_accessory[$index]['amount_per_car_accessory'] = $accessory_item->amount_per_car;
                    $car_accessory[$index]['amount_accessory'] = $accessory_item->amount;
                    $car_accessory[$index]['tor_section'] = $accessory_item->tor_section;
                    $car_accessory[$index]['remark'] = $accessory_item->remark;
                    $car_accessory[$index]['type_accessories'] = $accessory_item->type_accessories;
                    $car_accessory[$index]['car_index'] = $car_index;
                    $index++;
                }
            }
        }

        $page_title = __('long_term_rentals.compare_price');
        return view('admin.long-term-rental-compare-price.form', [
            'd' => $rental,
            'page_title' => $page_title,
            'purchase_requisition_cars' => $cars,
            'purchase_order_dealer_list' => $compare_price_list,
            'purchase_requisition_car_list' => $cars,
            'lt_rental_type_list' => $lt_rental_type_list,
            'customer_type_list' => $customer_type_list,
            'customer_code' => $customer_code,
            'tor_files' => $tor_files,
            'page_title' => $page_title,
            'car_list' => $car_list,
            'car_accessory' => $car_accessory,
            'cannot_add' => $cannot_add,
        ]);
    }

    public function getSelectedDealer($purchase_order)
    {
        $purchase_order_lines = PurchaseOrderLine::leftjoin('lt_rental_lines', 'lt_rental_lines.id', '=', 'purchase_order_lines.pr_line_id')
            ->leftJoin('car_classes', 'car_classes.id', '=', 'lt_rental_lines.car_class_id')
            ->leftJoin('car_colors', 'car_colors.id', '=', 'lt_rental_lines.car_color_id')
            ->where('purchase_order_lines.purchase_order_id', $purchase_order->id)
            ->select(
                'purchase_order_lines.*',
                'lt_rental_lines.id as item_id',
                'lt_rental_lines.amount as exac_amount',
                'car_colors.name as color'
            )->get();
        return $purchase_order_lines;
    }

    public function store(Request $request)
    {
        $lt_rental_data = LongTermRental::find($request->id);
        $compares = ComparisonPrice::where('item_id', $lt_rental_data->id)->get();
        if ($request->add_on) {
            if ($request->id) {

                if (isset($request->pending_delete_car_ids)) {
                    LongTermRentalLine::whereIn('id', $request->pending_delete_car_ids)->delete();
                }

                $long_term_car_accessory = $this->saveCarAccessory($request, $request->id);
            }
            foreach ($compares as $compare) {

                foreach ($request->data as $key => $dealer_price) {
                    $compare_price_line = new ComparisonPriceLine();
                    $compare_price_line->comparison_price_id = $compare->id;
                    $compare_price_line->item_id = $dealer_price['car_class_id'];
                    $compare_price_line->item_type = CarClass::class;
                    $compare_price_line->amount = $dealer_price['amount_car'];
                    $compare_price_line->subtotal = 0;
                    $compare_price_line->vat = 0;
                    $compare_price_line->total = 0;
                    $compare_price_line->save();
                    // $sum_dealer_price_subtotal += $dealer_price['vat_exclude'] * $car_amount;
                    // $sum_dealer_price_vat += $dealer_price['vat'] * $car_amount;
                    // $sum_dealer_price_total += $dealer_price['car_price'] * $car_amount;
                }
            }
            $redirect_route = route('admin.long-term-rental.compare-price.edit', ['rental' => $lt_rental_data->id]);
            return $this->responseValidateSuccess($redirect_route);
        }

        $custom_rules = [
            // 'ordered_creditor_id' => 'required',
            'pr_dealer' => 'required|array',
            'pr_dealer.*' => 'required',
            'pr_dealer.*.creditor_id' => 'required',
            'pr_dealer.*.dealer_price_list' => 'required|array',
        ];
        $validator = Validator::make($request->all(), $custom_rules, [], [
            'ordered_creditor_id' => __('purchase_orders.dealer'),
            'selected_cars_price' => __('purchase_orders.price_required'),
            'selected_cars_price_value' => __('purchase_orders.min_price'),
            'summary_price_total_float' => __('purchase_orders.summary_price_total'),
            'pr_dealer' => __('purchase_orders.dealer'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        DB::transaction(function () use ($request) {
            $lt_rental = LongTermRental::find($request->id);
            $lt_rental->creditor_id = $request->ordered_creditor_id;

            if ($lt_rental->comparison_price_status === ComparisonPriceStatusEnum::REJECT) {
                $lt_rental->comparison_price_status = ComparisonPriceStatusEnum::DRAFT;
            }

            if ($request->status_pending_review) {

                $lt_rental->comparison_price_status = ComparisonPriceStatusEnum::CONFIRM;
                $lt_rental->rental_price_status = LongTermRentalPriceStatusEnum::DRAFT;
                $lt_rental->status = LongTermRentalStatusEnum::RENTAL_PRICE;
                $this->SendNotificationRentalPrice($lt_rental, $lt_rental->worksheet_no);
            }
            $lt_rental->save();

            if (isset($request->showroom_prices)) {
                $showroom_prices = $request->showroom_prices;
                foreach ($showroom_prices as $key => $showroom_price) {
                    $lt_line = LongTermRentalLine::find($key);
                    if ($lt_line) {
                        $lt_line->showroom_price = floatval($showroom_price);
                        $lt_line->save();
                    }
                }
            }

            $offer_price_dealers = $request->pr_dealer;
            $cars = $request->cars;
            $comparison_price_ids = ComparisonPrice::where('item_type', LongTermRental::class)
                ->where('item_id', $lt_rental->id)
                ->pluck('id')->toArray();

            ComparisonPriceLine::whereIn('comparison_price_id', $comparison_price_ids)->delete();
            $this->deleteDealerMedias($request);
            foreach ($offer_price_dealers as $key => $offer_price) {
                $compare_price = ComparisonPrice::firstOrNew(['id' => $offer_price['id']]);
                $compare_price->item_id = $lt_rental->id;
                $compare_price->item_type = LongTermRental::class;
                $compare_price->creditor_id = $offer_price['creditor_id'];
                $dealer_price_list = $offer_price['dealer_price_list'];

                $sum_dealer_price_subtotal = 0;
                $sum_dealer_price_vat = 0;
                $sum_dealer_price_total = 0;
                $sum_dealer_price_discount = 0;
                $compare_price->save();

                if ((!empty($request->dealer_files)) && (sizeof($request->dealer_files) > 0)) {
                    $all_dealer_files = $request->dealer_files;
                    if (isset($all_dealer_files[$key])) {
                        $compare_price_dealer_files = $all_dealer_files[$key];
                        foreach ($compare_price_dealer_files as $compare_price_dealer_file) {
                            if ($compare_price_dealer_file) {
                                $compare_price->addMedia($compare_price_dealer_file)->toMediaCollection('comparison_price');
                            }
                        }
                    }
                }
                foreach ($dealer_price_list as $key => $dealer_price) {
                    $car_amount = $request->data[$key]['amount_car'];
                    // $car_amount = $dealer_price['amount_car'];
                    $compare_price_line = new ComparisonPriceLine();
                    $compare_price_line->comparison_price_id = $compare_price->id;
                    $compare_price_line->item_id = $dealer_price['car_id'];
                    $compare_price_line->item_type = CarClass::class;
                    $compare_price_line->amount = $car_amount;
                    $compare_price_line->subtotal = $dealer_price['vat_exclude'];
                    $compare_price_line->vat = $dealer_price['vat'];
                    $compare_price_line->total = $dealer_price['car_price'];
                    $compare_price_line->discount = floatval($dealer_price['car_discount']);
                    $compare_price_line->save();
                    $sum_dealer_price_subtotal += $dealer_price['vat_exclude'] * $car_amount;
                    $sum_dealer_price_vat += $dealer_price['vat'] * $car_amount;
                    $sum_dealer_price_total += ($dealer_price['car_price'] - $dealer_price['car_discount']) * $car_amount;
                    $sum_dealer_price_discount += $dealer_price['car_discount'] * $car_amount;
                }
                $compare_price = ComparisonPrice::find($compare_price->id);
                $compare_price->subtotal = $sum_dealer_price_subtotal;
                $compare_price->vat = $sum_dealer_price_vat;
                $compare_price->total = $sum_dealer_price_total;
                $compare_price->discount = $sum_dealer_price_discount;
                $compare_price->save();
            }
        });

        if ($request->id) {
            if (isset($request->pending_delete_car_ids)) {
                LongTermRentalLine::whereIn('id', $request->pending_delete_car_ids)->delete();
            }
            // $long_term_car_accessory = $this->saveCarAccessory($request, $request->id);
        }
        $redirect_route = route('admin.long-term-rental.compare-price.index');

        $status_arr = [
            ComparisonPriceStatusEnum::CONFIRM,
            ComparisonPriceStatusEnum::REJECT,
        ];
        if (!in_array($lt_rental_data->comparison_price_status, $status_arr)) {
            //        Create Request Premium
            InsuranceTrait::createRequestPremium($lt_rental_data?->id);
        }

        return $this->responseValidateSuccess($redirect_route);
    }

    private function saveCarAccessory($request, $lt_rental_id)
    {
        if (!empty($request->data)) {
            foreach ($request->data as $car_index => $request_car) {
                if (isset($request_car['id'])) {
                    $lt_rental_line = LongTermRentalLine::find($request_car['id']);
                } else {
                    $lt_rental_line = new LongTermRentalLine();
                    $new_lt_rental_line = true;
                }
                $lt_rental_line->lt_rental_id = $lt_rental_id;
                $lt_rental_line->car_class_id = $request_car['car_class_id'];
                $lt_rental_line->car_color_id = $request_car['car_color_id'];
                $lt_rental_line->amount = intval($request_car['amount_car']);
                $lt_rental_line->remark = $request_car['remark'];
                $lt_rental_line->have_accessories = $request_car['have_accessories'];
                $lt_rental_line->save();

                // save accessory
                LongTermRentalLineAccessory::where('lt_rental_line_id', $request_car['id'])->delete();
                if (isset($request_car['accessory']) && sizeof($request_car['accessory']) > 0) {
                    foreach ($request_car['accessory'] as $accessory_key => $accessory) {
                        $long_term_accessory = new LongTermRentalLineAccessory();
                        $long_term_accessory->lt_rental_line_id = $lt_rental_line->id;
                        $long_term_accessory->accessory_id = $accessory['id'];
                        $long_term_accessory->amount = intval($accessory['amount']);
                        $long_term_accessory->amount_per_car = intval($accessory['amount_per_car']);
                        $long_term_accessory->tor_section = $accessory['tor_section'];
                        $long_term_accessory->remark = $accessory['remark'];
                        $long_term_accessory->type_accessories = $accessory['type_accessories'];
                        $long_term_accessory->save();
                    }
                }
            }
            if (isset($new_lt_rental_line)) {
                $this->createRentalLines($lt_rental_id, $lt_rental_line);
            }
        }
        return true;
    }

    public function createRentalLines($lt_rental_id, $lt_rental_line)
    {
        $lt_month_list = LongTermRentalMonth::where('lt_rental_id', $lt_rental_id)->get();
        foreach ($lt_month_list as $key => $lt_month) {
            $lt_line_month = new LongTermRentalLineMonth();
            $lt_line_month->lt_rental_line_id = $lt_rental_line->id;
            $lt_line_month->lt_rental_month_id = $lt_month->id;
            $lt_line_month->save();
        }
        return true;
    }

    public function SendNotificationRentalPrice($modelLongtermRental, $dataWorkSheetNo)
    {
        $dataDepartment = [
            DepartmentEnum::PCD_PURCHASE,
        ];
        $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
        $notiUserId = NotificationTrait::getUserId($notiDepartmentId);
        $url = route('admin.long-term-rental.quotations.edit', ['rental' => $modelLongtermRental]);
        $notiTypeChange = new NotificationManagement('จัดทำราคาเช่า', 'ใบขอเช่ายาว ' . $dataWorkSheetNo . ' กรุณาจัดทำราคาค่าเช่า', $url, NotificationScopeEnum::USER, $notiUserId, []);
        $notiTypeChange->send();
    }

    private function deleteDealerMedias($request)
    {
        $delete_dealer_ids = $request->delete_dealer_ids;
        if ((!empty($delete_dealer_ids)) && (is_array($delete_dealer_ids))) {
            foreach ($delete_dealer_ids as $delete_id) {
                $campare_price_delete = ComparisonPrice::find($delete_id);
                $campare_price_medias = $campare_price_delete->getMedia('comparison_price');
                foreach ($campare_price_medias as $campare_price_media) {
                    $campare_price_media->delete();
                }
                $campare_price_delete->delete();
            }
        }

        $pending_delete_dealer_files = $request->pending_delete_dealer_files;
        if ((!empty($pending_delete_dealer_files)) && (sizeof($pending_delete_dealer_files) > 0)) {
            foreach ($pending_delete_dealer_files as $dealer_media_id) {
                $dealer_media = Media::find($dealer_media_id);
                if ($dealer_media && $dealer_media->model_id) {
                    $comparison_price = ComparisonPrice::find($dealer_media->model_id);
                    $comparison_price->deleteMedia($dealer_media->id);
                }
            }
        }
        return true;
    }
}
