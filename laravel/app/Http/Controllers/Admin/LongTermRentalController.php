<?php

namespace App\Http\Controllers\Admin;

use App\Classes\NotificationManagement;
use App\Enums\Actions;
use App\Enums\AuctionResultEnum;
use App\Enums\AuctionStatusEnum;
use App\Enums\DepartmentEnum;
use App\Enums\ImportCarLineStatusEnum;
use App\Enums\ImportCarStatusEnum;
use App\Enums\InstallEquipmentStatusEnum;
use App\Enums\LongTermRentalJobType;
use App\Enums\LongTermRentalProgressStatusEnum;
use App\Enums\LongTermRentalStatusEnum;
use App\Enums\LongTermRentalTypeAccessoryEnum;
use App\Enums\NotificationScopeEnum;
use App\Enums\QuotationStatusEnum;
use App\Enums\Resources;
use App\Enums\SpecStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\AuctionRejectReason;
use App\Models\Customer;
use App\Models\ImportCarLine;
use App\Models\InstallEquipment;
use App\Models\LongTermRental;
use App\Models\LongTermRentalLine;
use App\Models\LongTermRentalLineAccessory;
use App\Models\LongTermRentalLineMonth;
use App\Models\LongTermRentalMonth;
use App\Models\LongTermRentalPRLine;
use App\Models\LongTermRentalTor;
use App\Models\LongTermRentalTorLine;
use App\Models\LongTermRentalTorLineAccessory;
use App\Models\LongTermRentalType;
use App\Models\Province;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionLine;
use App\Models\PurchaseRequisitionLineAccessory;
use App\Models\Quotation;
use App\Traits\CustomerTrait;
use App\Traits\LongTermRentalTrait;
use App\Traits\NotificationTrait;
use App\Traits\RentalTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LongTermRentalController extends Controller
{
    use CustomerTrait, RentalTrait, LongTermRentalTrait;

    protected $YesNoList;

    public function __construct()
    {
        $this->YesNoList = collect([
            [
                'id' => BOOL_TRUE,
                'value' => BOOL_TRUE,
                'name' => 'ตีเสปครถโดยฝ่ายการตลาด',
            ],
            [
                'id' => BOOL_FALSE,
                'value' => BOOL_FALSE,
                'name' => 'ตีเสปครถโดยฝ่าย QA',
            ],
        ]);
    }

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::LongTermRental);
        $worksheet_id = $request->worksheet_no;
        $customer_id = $request->customer;
        $status_id = $request->status;
        $spec_status_id = $request->spec_status;
        $won_auction_status_id = $request->won_auction;
        $quotation_id = $request->quotation_no;
        // $lt_rental_type = $request->lt_rental_type;
        $lists = LongTermRental::sortable(['created_at' => 'desc'])
            ->select(
                'lt_rentals.id',
                'lt_rentals.worksheet_no',
                'lt_rentals.customer_name',
                'lt_rentals.won_auction',
                'lt_rentals.status',
                'lt_rentals.spec_status',
                'lt_rentals.comparison_price_status',
                'lt_rentals.rental_price_status',
                'quotations.qt_no',
                'lt_rentals.quotation_id',
                'quotations.status as quotation_status',
                'lt_rental_types.name as lt_rental_type_name'
            )
            ->leftJoin('quotations', 'quotations.id', '=', 'lt_rentals.quotation_id')
            ->leftJoin('lt_rental_types', 'lt_rental_types.id', '=', 'lt_rentals.lt_rental_type_id')
            ->when($spec_status_id, function ($query) use ($spec_status_id) {
                return $query->where('lt_rentals.spec_status', $spec_status_id);
            })
            ->when($status_id, function ($query) use ($status_id) {

                if ($status_id == LongTermRentalStatusEnum::QUOTATION_CONFIRM) {
                    return $query->where('quotations.status', "CONFIRM");
                } else {
                    return $query->where('lt_rentals.status', $status_id);
                }
            })
            ->when($won_auction_status_id, function ($query) use ($won_auction_status_id) {
                return $query->where('lt_rentals.won_auction', $won_auction_status_id);
            })
            ->when($quotation_id, function ($query) use ($quotation_id) {
                return $query->where('lt_rentals.quotation_id', $quotation_id);
            })
            ->search($request->s, $request)->paginate(PER_PAGE);

        $lists->map(function ($item) {
            $pr = PurchaseRequisition::where('reference_type', LongTermRental::class)
                ->where('reference_id', $item->id)
                ->pluck('id')
                ->toArray();

            if ($pr) {
                $item->order_status = LongTermRentalProgressStatusEnum::COMPLETE;
                $po = PurchaseOrder::whereIn('pr_id', $pr)->pluck('id')->toArray();
                $pr_line = PurchaseRequisitionLine::whereIn('purchase_requisition_id', $pr)->pluck('id')->toArray();
                $po_line_amount = PurchaseOrderLine::whereIn('purchase_order_id', $po)->sum('amount');
                $lt_rental_pr_line_amount = LongTermRentalPRLine::where('lt_rental_id', $item->id)->sum('amount');

                //สถานะซื้อรถ
                $item->order_status = ($po_line_amount == $lt_rental_pr_line_amount) ? LongTermRentalProgressStatusEnum::SUCCESS_ORDER : (($po_line_amount < $lt_rental_pr_line_amount && $po_line_amount > 0) ? LongTermRentalProgressStatusEnum::PROCESSING : LongTermRentalProgressStatusEnum::COMPLETE);
                if ($item->order_status == LongTermRentalProgressStatusEnum::PROCESSING) {
                    $item->delivery_car_status = ImportCarStatusEnum::WAITING_DELIVERY;
                }

                $item->amount = $po_line_amount;
                $item->total_amount = $lt_rental_pr_line_amount;

                //สถานะส่งมอบรถ, สถานะติดตั้งอุปกรณ์
                $po_line = PurchaseOrderLine::whereIn('purchase_order_id', $po)->pluck('id')->toArray();
                $import_car_line_count = ImportCarLine::whereIn('po_line_id', $po_line)
                    ->whereIn('status_delivery', [ImportCarLineStatusEnum::PENDING_DELIVERY, ImportCarLineStatusEnum::SUCCESS_DELIVERY])
                    ->count();

                $install_equipment = InstallEquipment::whereIn('po_id', $po)->get();
                $pr_id_additional = PurchaseRequisitionLineAccessory::where('type_accessories', LongTermRentalTypeAccessoryEnum::ADDITIONAL)->whereIn('purchase_requisition_line_id', $pr_line)->pluck('purchase_requisition_line_id')->toArray();
                $pr_line_amount = PurchaseRequisitionLine::whereIn('id', $pr_id_additional)->sum('amount');

                if ($import_car_line_count == $lt_rental_pr_line_amount) {
                    $item->delivery_car_status = ImportCarStatusEnum::DELIVERY_COMPLETE;
                    if ($install_equipment) {
                        $waiting_count = $install_equipment->where('status', InstallEquipmentStatusEnum::WAITING)->count();
                        $in_process_count = $install_equipment->where('status', InstallEquipmentStatusEnum::INSTALL_IN_PROCESS)->count();
                        $complete_count = $install_equipment->where('status', InstallEquipmentStatusEnum::COMPLETE)->count();

                        if ($waiting_count > 0) {
                            $item->install_equipment_status = InstallEquipmentStatusEnum::WAITING;
                        }

                        if ($in_process_count > 0) {
                            $item->install_equipment_status = InstallEquipmentStatusEnum::INSTALL_IN_PROCESS;
                            $item->install_equipment_amount = $in_process_count;
                            $item->install_equipment_total_amount = $pr_line_amount;
                        }

                        if ($complete_count == $pr_line_amount && $complete_count > 0) {
                            $item->install_equipment_status = InstallEquipmentStatusEnum::COMPLETE;
                        }

                        if ($complete_count < $pr_line_amount) {
                            $item->install_equipment_status = InstallEquipmentStatusEnum::INSTALL_IN_PROCESS;
                            $item->install_equipment_amount = $complete_count;
                            $item->install_equipment_total_amount = $pr_line_amount;
                        }
                    }
                } else if ($import_car_line_count < $lt_rental_pr_line_amount && $import_car_line_count > 0) {
                    $item->delivery_car_status = LongTermRentalProgressStatusEnum::DELIVERING;
                    $item->delivery_car_amount = $import_car_line_count;
                    if ($install_equipment) {
                        $waiting_count = $install_equipment->where('status', InstallEquipmentStatusEnum::WAITING)->count();
                        $in_process_count = $install_equipment->where('status', InstallEquipmentStatusEnum::INSTALL_IN_PROCESS)->count();
                        $complete_count = $install_equipment->where('status', InstallEquipmentStatusEnum::COMPLETE)->count();

                        if ($waiting_count > 0) {
                            $item->install_equipment_status = InstallEquipmentStatusEnum::WAITING;
                        }

                        if ($in_process_count > 0) {
                            $item->install_equipment_status = InstallEquipmentStatusEnum::INSTALL_IN_PROCESS;
                            $item->install_equipment_amount = $in_process_count;
                            $item->install_equipment_total_amount = $pr_line_amount;
                        }

                        if ($complete_count == $pr_line_amount && $complete_count > 0) {
                            $item->install_equipment_status = InstallEquipmentStatusEnum::COMPLETE;
                        }

                        if ($complete_count < $pr_line_amount) {
                            $item->install_equipment_status = InstallEquipmentStatusEnum::INSTALL_IN_PROCESS;
                            $item->install_equipment_amount = $complete_count;
                            $item->install_equipment_total_amount = $pr_line_amount;
                        }
                    }
                }
            }
            return $item;
        });

        $worksheet_list = LongTermRental::select('id', 'worksheet_no as name')->get();
        $customer_list = LongTermRental::select('id', 'customer_name as name')->whereNotNull('customer_name')->get();
        $quotation_list = Quotation::select('id', 'qt_no as name')->where('reference_type', LongTermRental::class)->get();
        $status_list = RentalTrait::getStatusRentalList();
        $spec_status_list = RentalTrait::getSpecStatusList();
        // $rental_price_status_list = RentalTrait::getRentalPriceStatusList();
        $won_auction_list = $this->getRentalAuctionSearch();
        // $lt_rental_type_list = LongTermRentalType::all();
        //        Check Quotation Confirm Status
        $lists->map(function ($item) {
            if ($item->quotation->status == LongTermRentalStatusEnum::CONFIRM && $item->status == LongTermRentalStatusEnum::QUOTATION) {
                return $item->status = LongTermRentalStatusEnum::QUOTATION_CONFIRM;
            }
        });
        return view('admin.long-term-rentals.index', [
            's' => $request->s,
            'lists' => $lists,
            'worksheet_list' => $worksheet_list,
            'customer_list' => $customer_list,
            'worksheet_id' => $worksheet_id,
            'customer_id' => $customer_id,
            'status_id' => $status_id,
            'spec_status_id' => $spec_status_id,
            'status_list' => $status_list,
            'spec_status_list' => $spec_status_list,
            'won_auction_list' => $won_auction_list,
            'won_auction_status_id' => $won_auction_status_id,
            'quotation_list' => $quotation_list,
            'quotation_id' => $quotation_id,
            // 'lt_rental_type' => $lt_rental_type,
            // 'lt_rental_type_list' => $lt_rental_type_list,
            // 'from_offer_date' => $request->from_offer_date,
            // 'to_offer_date' => $request->to_offer_date,
        ]);
    }

    private function getRentalAuctionSearch()
    {
        return collect([
            (object)[
                'id' => AuctionResultEnum::WAITING,
                'value' => AuctionResultEnum::WAITING,
                'name' => __('long_term_rentals.won_auction_' . AuctionResultEnum::WAITING),
            ],
            (object)[
                'id' => AuctionResultEnum::WON,
                'value' => AuctionResultEnum::WON,
                'name' => __('long_term_rentals.won_auction_' . AuctionResultEnum::WON),
            ],
            (object)[
                'id' => AuctionResultEnum::LOSE,
                'value' => AuctionResultEnum::LOSE,
                'name' => __('long_term_rentals.won_auction_' . AuctionResultEnum::LOSE),
            ],
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::LongTermRental);
        $d = new LongTermRental();
        $d->need_pay_auction = BOOL_FALSE;
        $d->need_actual_delivery_date = BOOL_TRUE;
        $d->won_auction = AuctionResultEnum::WAITING;
        $d->customer_type = null;
        $d->check_spec = BOOL_FALSE;
        $customer_code = null;
        $customer_type_list = CustomerTrait::getCustomerType();
        $lt_rental_type_list = $this->getRentalJobTypeList();
        $approval_type_list = LongTermRentalTrait::getRentalApprovalList();
        $check_spec_list = $this->getCheckSpecList();
        $need_pay_auction_list = $this->getRentalPayAuctionList();
        $won_auction_list = $this->getRentalAuctionResultList();
        $approval_status_list = LongTermRentalTrait::getLongTermRentalApproveStatusList();
        $auction_reject_list = AuctionRejectReason::select('id', 'name')->get();
        $province_list = Province::select('id', 'name_th as name')->get();
        $spec_bom_list = LongTermRental::select('id', 'worksheet_no as name')->get();
        $page_title = __('lang.create') . __('long_term_rentals.page_title');
        $count_files = 0;
        $month = null;
        $allow_confirm = $this->isAllowConfirmCreatePR($d);
        $status_list = $this->getStatusList();
        $yes_no_list = $this->YesNoList;

        return view('admin.long-term-rentals.form', [
            'd' => $d,
            'page_title' => $page_title,
            'customer_type_list' => $customer_type_list,
            'province_list' => $province_list,
            'customer_code' => $customer_code,
            'lt_rental_type_list' => $lt_rental_type_list,
            'need_pay_auction_list' => $need_pay_auction_list,
            'won_auction_list' => $won_auction_list,
            'auction_reject_list' => $auction_reject_list,
            'count_files' => $count_files,
            'check_spec_list' => $check_spec_list,
            'rental_type' => null,
            'spec_bom_list' => $spec_bom_list,
            'month' => $month,
            'approval_type_list' => $approval_type_list,
            'approval_status_list' => $approval_status_list,
            'allow_confirm' => $allow_confirm,
            'status_list' => $status_list,
            'route_group' => [
                'route_lt_rental' => '',
                'route_pr_line' => '',
            ],
            'yes_no_list' => $yes_no_list,
            'create' => true,
        ]);
    }

    public function getCheckSpecList()
    {
        return collect([
            [
                'id' => BOOL_FALSE,
                'value' => BOOL_FALSE,
                'name' => __('long_term_rentals.select_spec_' . BOOL_FALSE),
            ],
            [
                'id' => BOOL_TRUE,
                'value' => BOOL_TRUE,
                'name' => __('long_term_rentals.select_spec_' . BOOL_TRUE),
            ],
        ]);
    }

    public function getRentalPayAuctionList()
    {
        return collect([
            [
                'id' => BOOL_FALSE,
                'value' => BOOL_FALSE,
                'name' => __('long_term_rentals.need_pay_auction_' . BOOL_FALSE),
            ],
            [
                'id' => BOOL_TRUE,
                'value' => BOOL_TRUE,
                'name' => __('long_term_rentals.need_pay_auction_' . BOOL_TRUE),
            ],
        ]);
    }

    //    GetCarListData

    public function getRentalAuctionResultList()
    {
        return collect([
            [
                'id' => AuctionResultEnum::WAITING,
                'value' => AuctionResultEnum::WAITING,
                'name' => __('long_term_rentals.won_auction_' . AuctionResultEnum::WAITING),
            ],
            [
                'id' => AuctionResultEnum::WON,
                'value' => AuctionResultEnum::WON,
                'name' => __('long_term_rentals.won_auction_' . AuctionResultEnum::WON),
            ],
            [
                'id' => AuctionResultEnum::LOSE,
                'value' => AuctionResultEnum::LOSE,
                'name' => __('long_term_rentals.won_auction_' . AuctionResultEnum::LOSE),
            ],
        ]);
    }

    //    Create LongTermRentalTor Data Function

    public function isAllowConfirmCreatePR($lt_rental)
    {
        $confirmable = false;
        if (
            in_array($lt_rental->status, [
                LongTermRentalStatusEnum::QUOTATION,
                LongTermRentalStatusEnum::COMPLETE,
                LongTermRentalStatusEnum::CANCEL
            ])
            && $lt_rental->quotation
            && in_array($lt_rental->quotation->status, [QuotationStatusEnum::CONFIRM])
        ) {
            $confirmable = true;
        }
        return $confirmable;
    }

    public static function getStatusList()
    {
        return collect([
            [
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => __('accident_informs.status_' . STATUS_ACTIVE),
            ],
            [
                'id' => STATUS_DEFAULT,
                'value' => STATUS_DEFAULT,
                'name' => __('accident_informs.status_' . STATUS_DEFAULT),
            ],
        ]);
    }

    public function edit(LongTermRental $long_term_rental)
    {
        $this->authorize(Actions::Manage . '_' . Resources::LongTermRental);
        $lt_status_is_completed = (strcmp($long_term_rental->status, LongTermRentalStatusEnum::COMPLETE) === 0) ? true : false;
        if ($lt_status_is_completed) {
            return redirect()->action(
                [LongTermRentalController::class, 'show'],
                ['long_term_rental' => $long_term_rental]
            );
        }

        $customer_code = null;
        if ($long_term_rental->customer_id) {
            $customer = Customer::find($long_term_rental->customer_id);
            $customer_code = $customer->customer_code;
            $long_term_rental->customer_type = $customer->customer_type;
        }
        $customer_type_list = CustomerTrait::getCustomerType();
        $lt_rental_type_list = $this->getRentalJobTypeList();
        $need_pay_auction_list = $this->getRentalPayAuctionList();
        $won_auction_list = $this->getRentalAuctionResultList();
        $approval_type_list = LongTermRentalTrait::getRentalApprovalList();
        $approval_status_list = LongTermRentalTrait::getLongTermRentalApproveStatusList();
        $check_spec_list = $this->getCheckSpecList();
        $auction_reject_list = AuctionRejectReason::select('id', 'name')->get();
        $province_list = Province::select('id', 'name_th as name')->get();
        $spec_bom_list = LongTermRental::select('id', 'worksheet_no as name')->get();
        $status_list = $this->getStatusList();

        $tor_files = $long_term_rental->getMedia('tor_file');
        $tor_files = get_medias_detail($tor_files);
        $count_files = count($tor_files);

        $rental_files = $long_term_rental->getMedia('rental_file');
        $rental_files = get_medias_detail($rental_files);

        $approved_rental_files = $long_term_rental->getMedia('approved_rental_file');
        $approved_rental_files = get_medias_detail($approved_rental_files);

        $payment_forms = $long_term_rental->getMedia('payment_form');
        $payment_forms = get_medias_detail($payment_forms);

        $rental_type = ($long_term_rental->rentalType) ? $long_term_rental->rentalType->type : null;

        $month_arr = LongTermRentalMonth::where('lt_rental_id', $long_term_rental->id)->get()->toArray();
        $month = implode(',', array_column($month_arr, 'month'));
        $lt_rental_month = LongTermRentalMonth::where('lt_rental_id', $long_term_rental->id)->where('month', $long_term_rental->rental_duration)->count();
        $allow_confirm = $this->isAllowConfirmCreatePR($long_term_rental);
        $route_group = [
            'route_lt_rental' => route('admin.long-term-rentals.edit', ['long_term_rental' => $long_term_rental]),
            'route_pr_line' => route('admin.long-term-rentals.pr-lines.edit', ['long_term_rental' => $long_term_rental]),
            //            'route_car_contract' => route('admin.long-term-rentals.car-info-and-deliver.edit', ['long_term_rental' => $long_term_rental])
        ];
        $page_title = __('lang.edit') . __('long_term_rentals.page_title');
        $QuationStatus = $long_term_rental->quotation->status;
        $yes_no_list = $this->YesNoList;

        if (!empty($long_term_rental->id)) {
            $car_list = $this->GetCarListData($long_term_rental->id);
        } else {
            $car_list = [];
        }

        return view('admin.long-term-rentals.form', [
            'd' => $long_term_rental,
            'page_title' => $page_title,
            'customer_type_list' => $customer_type_list,
            'province_list' => $province_list,
            'lt_rental_type_list' => $lt_rental_type_list,
            'need_pay_auction_list' => $need_pay_auction_list,
            'won_auction_list' => $won_auction_list,
            'customer_code' => $customer_code,
            'tor_files' => $tor_files,
            'auction_reject_list' => $auction_reject_list,
            'count_files' => $count_files,
            'check_spec_list' => $check_spec_list,
            'rental_type' => $rental_type,
            'rental_files' => $rental_files,
            'payment_forms' => $payment_forms,
            'spec_bom_list' => $spec_bom_list,
            'month' => $month,
            'approval_type_list' => $approval_type_list,
            'approval_status_list' => $approval_status_list,
            'approved_rental_files' => $approved_rental_files,
            'allow_confirm' => $allow_confirm,
            'status_list' => $status_list,
            'route_group' => $route_group,
            'quotation_status' => $QuationStatus,
            'yes_no_list' => $yes_no_list,
            'car_list' => $car_list,
        ]);
    }

    public function GetCarListData($lt_rental_id = null)
    {

        if (!empty($lt_rental_id)) {


            $tor_line_list = LongTermRentalTorLine::leftjoin('lt_rental_tors', 'lt_rental_tors.id', '=', 'lt_rental_tor_lines.lt_rental_tor_id')
                ->leftjoin('lt_rentals', 'lt_rentals.id', '=', 'lt_rental_tors.lt_rental_id')
                ->select(
                    'lt_rental_tor_lines.*',
                    'lt_rental_tors.id as tor_id',
                    'lt_rental_tors.remark_tor',
                    'lt_rentals.actual_delivery_date',
                )
                ->where('lt_rentals.id', $lt_rental_id)
                ->orderBy('tor_id')
                ->get();

            $tor_line_list->map(function ($item) {
                $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
                $item->car_color_text = ($item->color) ? $item->color->name : '';
                $item->amount_car = $item->amount;
                $accessory_list = $this->getAccessoriesByTorLineId($item->id);
                $item->accessory_list = $accessory_list;
                return $item;
            });

            // TODO
            $accessory_list = LongTermRentalTorLineAccessory::whereIn('lt_rental_tor_line_id', $tor_line_list->pluck('id')->toArray())->get();
            $car_accessory = [];
            $index = 0;
            foreach ($tor_line_list as $car_index => $car_item) {
                foreach ($accessory_list as $accessory_item) {
                    if (strcmp($car_item->id, $accessory_item->lt_rental_tor_line_id) == 0) {
                        $car_accessory[$index]['accessory_id'] = $accessory_item->accessory_id;
                        $car_accessory[$index]['accessory_text'] = ($accessory_item->accessory) ? $accessory_item->accessory->name : '';
                        $car_accessory[$index]['amount_accessory'] = $accessory_item->amount;
                        $car_accessory[$index]['amount_per_car_accessory'] = $accessory_item->amount_per_car;
                        $car_accessory[$index]['tor_section'] = $accessory_item->tor_section;
                        $car_accessory[$index]['remark'] = $accessory_item->remark;
                        $car_accessory[$index]['type_accessories'] = $accessory_item->type_accessories;
                        $car_accessory[$index]['car_index'] = $car_index;
                        $index++;
                    }
                }
            }
            if (empty($tor_line_list)) {
                $tor_line_list = [];
            }
        }
        return $tor_line_list;
    }

    public function show(LongTermRental $long_term_rental)
    {
        $this->authorize(Actions::View . '_' . Resources::LongTermRental);
        $customer_code = null;
        if ($long_term_rental->customer_id) {
            $customer = Customer::find($long_term_rental->customer_id);
            $customer_code = $customer->customer_code;
            $long_term_rental->customer_type = $customer->customer_type;
        }
        $with_trash = true;
        $customer_type_list = CustomerTrait::getCustomerType();
        $lt_rental_type_list = LongTermRentalTrait::getRentalJobTypeList($with_trash);
        $need_pay_auction_list = $this->getRentalPayAuctionList();
        $won_auction_list = $this->getRentalAuctionResultList();
        $check_spec_list = $this->getCheckSpecList();
        $approval_type_list = LongTermRentalTrait::getRentalApprovalList();
        $approval_status_list = LongTermRentalTrait::getLongTermRentalApproveStatusList();
        $auction_reject_list = AuctionRejectReason::select('id', 'name')->withTrashed()->get();
        $province_list = Province::select('id', 'name_th as name')->get();
        $spec_bom_list = LongTermRental::select('id', 'worksheet_no as name')->withTrashed()->get();
        $status_list = $this->getStatusList();

        $tor_files = $long_term_rental->getMedia('tor_file');
        $tor_files = get_medias_detail($tor_files);
        $count_files = count($tor_files);

        $rental_files = $long_term_rental->getMedia('rental_file');
        $rental_files = get_medias_detail($rental_files);

        $approved_rental_files = $long_term_rental->getMedia('approved_rental_file');
        $approved_rental_files = get_medias_detail($approved_rental_files);

        $payment_forms = $long_term_rental->getMedia('payment_form');
        $payment_forms = get_medias_detail($payment_forms);

        $rental_type = ($long_term_rental->rentalType) ? $long_term_rental->rentalType->type : null;

        $month_arr = LongTermRentalMonth::where('lt_rental_id', $long_term_rental->id)->get()->toArray();
        $month = implode(',', array_column($month_arr, 'month'));
        $allow_confirm = $this->isAllowConfirmCreatePR($long_term_rental);
        $route_group = [
            'route_lt_rental' => route('admin.long-term-rentals.show', ['long_term_rental' => $long_term_rental]),
            'route_pr_line' => route('admin.long-term-rentals.pr-lines.show', ['long_term_rental' => $long_term_rental]),
            'route_car_contract' => route('admin.long-term-rentals.car-info-and-deliver.show', ['long_term_rental' => $long_term_rental])
        ];
        $page_title = __('lang.view') . __('long_term_rentals.page_title');
        $QuationStatus = $long_term_rental->quotation->status;
        $yes_no_list = $this->YesNoList;
        if (!empty($long_term_rental->id)) {
            $car_list = $this->GetCarListData($long_term_rental->id);
        } else {
            $car_list = [];
        }
        return view('admin.long-term-rentals.form', [
            'd' => $long_term_rental,
            'page_title' => $page_title,
            'customer_type_list' => $customer_type_list,
            'province_list' => $province_list,
            'lt_rental_type_list' => $lt_rental_type_list,
            'need_pay_auction_list' => $need_pay_auction_list,
            'won_auction_list' => $won_auction_list,
            'auction_reject_list' => $auction_reject_list,
            'customer_code' => $customer_code,
            'tor_files' => $tor_files,
            'count_files' => $count_files,
            'view' => true,
            'check_spec_list' => $check_spec_list,
            'rental_type' => $rental_type,
            'rental_files' => $rental_files,
            'payment_forms' => $payment_forms,
            'spec_bom_list' => $spec_bom_list,
            'month' => $month,
            'approval_type_list' => $approval_type_list,
            'approval_status_list' => $approval_status_list,
            'approved_rental_files' => $approved_rental_files,
            'allow_confirm' => $allow_confirm,
            'status_list' => $status_list,
            'route_group' => $route_group,
            'quotation_status' => $QuationStatus,
            'yes_no_list' => $yes_no_list,
            'car_list' => $car_list
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $this->authorize(Actions::Manage . '_' . Resources::LongTermRental);
        $lt_rental_type = LongTermRentalType::find($request->lt_rental_type_id);
        $validate_data = [
            'lt_rental_type_id' => ['required'],
            'won_auction' => [
                Rule::when($lt_rental_type && ($lt_rental_type->job_type == LongTermRentalJobType::EBIDDING || $lt_rental_type->job_type == LongTermRentalJobType::AUCTION), ['required']),
            ],
            'approval_type' => ['required'],
            //            'rental_duration' => ['required'],
            // 'require_date' => ['required'],
            'customer_id' => ['required'],
            'customer_tel' => ['nullable', 'max:20'],
            'month' => ['required'],
            'is_spec' => ['required'],
        ];

        $validator = Validator::make($request->all(), $validate_data, [], [
            'lt_rental_type_id' => __('long_term_rentals.job_type'),
            'won_auction' => __('long_term_rentals.won_auction'),
            'approval_type' => __('long_term_rentals.type'),
            //            'rental_duration' => __('long_term_rentals.rental_duration'),
            // 'require_date' => __('purchase_requisitions.require_date'),
            'customer_id' => __('long_term_rentals.customer_code'),
            'customer_tel' => __('long_term_rentals.tel'),
            'month' => __('long_term_rentals.rental_months'),
            'is_spec' => __('long_term_rentals.rental_spec'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        //        Check if is_spec = 0 Need Minimum 1 Car
        // $validator = Validator::make($request->all(), [
        //     'cars' => ['required_if:is_spec,0']
        // ]);
        // if ($validator->fails()) {
        //     return $this->responseWithCode(false, __("long_term_rentals.cars_require"), null, 404);
        // }

        if (strcmp($request->is_spec, BOOL_TRUE) == 0) {
            if ($request->count_files <= 0) {
                $validator = Validator::make($request->all(), [
                    'tor_file' => [
                        'required'
                    ],
                ], [], [
                    'tor_file' => __('long_term_rentals.tor_file'),
                ]);
                if ($validator->stopOnFirstFailure()->fails()) {
                    return $this->responseValidateFailed($validator);
                }
            }
        }

        if (strcmp($request->rental_type, AuctionStatusEnum::AUCTION) == 0) {
            $validator = Validator::make($request->all(), [
                'actual_delivery_date_auction' => [Rule::when($request->need_actual_delivery_date_auction == STATUS_ACTIVE, ['required'])],
                'delivery_date_remark_auction' => [Rule::when($request->need_actual_delivery_date_auction == STATUS_DEFAULT, ['required'])],
            ], [], [
                'actual_delivery_date_auction' => __('long_term_rentals.actual_delivery_date'),
                'delivery_date_remark_auction' => __('long_term_rentals.delivery_date_range'),
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        if (strcmp($request->rental_type, AuctionStatusEnum::NO_AUCTION) == 0) {
            $validator = Validator::make($request->all(), [
                'actual_delivery_date_no_auction' => [Rule::when($request->need_actual_delivery_date_no_auction == STATUS_ACTIVE, ['required'])],
                'delivery_date_remark_no_auction' => [Rule::when($request->need_actual_delivery_date_no_auction == STATUS_DEFAULT, ['required'])],
            ], [], [
                'actual_delivery_date_no_auction' => __('long_term_rentals.actual_delivery_date'),
                'delivery_date_remark_no_auction' => __('long_term_rentals.delivery_date_range'),
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        $month_arr = explode(",", $request->month);
        foreach ($month_arr as $month) {
            if (!is_numeric($month)) {
                return response()->json([
                    'success' => false,
                    'message' => __('long_term_rentals.month_type_invalid')
                ], 422);
            }
        }

        //        if (!in_array($request->rental_duration, $month_arr)) {
        //            return response()->json([
        //                'success' => false,
        //                'message' => __('long_term_rentals.rental_duration_invalid')
        //            ], 422);
        //        }
        $actual_delivery_date = null;
        $contract_start_date = null;
        $contract_end_date = null;
        $delivery_date_remark = null;
        $need_actual_delivery_date = null;
        if (strcmp($request->rental_type, AuctionStatusEnum::AUCTION) == 0) {
            $actual_delivery_date = $request->actual_delivery_date_auction;
            $contract_start_date = $request->contract_start_date_auction;
            $contract_end_date = $request->contract_end_date_auction;

            $need_actual_delivery_date = $request->need_actual_delivery_date_auction;
            $delivery_date_remark = $request->delivery_date_remark_auction;
        } elseif (strcmp($request->rental_type, AuctionStatusEnum::NO_AUCTION) == 0) {
            $actual_delivery_date = $request->actual_delivery_date_no_auction;
            $contract_start_date = $request->contract_start_date_no_auction;
            $contract_end_date = $request->contract_end_date_no_auction;

            $need_actual_delivery_date = $request->need_actual_delivery_date_no_auction;
            $delivery_date_remark = $request->delivery_date_remark_no_auction;
        }

        $long_term_rental = LongTermRental::firstOrNew(['id' => $request->id]);
        $long_term_rental_count = LongTermRental::all()->count() + 1;
        $prefix = 'LR-';
        if (!($long_term_rental->exists)) {
            $long_term_rental->worksheet_no = generateRecordNumber($prefix, $long_term_rental_count);
        }
        $long_term_rental->lt_rental_type_id = $request->lt_rental_type_id;
        $long_term_rental->approval_type = $request->approval_type;
        //        $long_term_rental->rental_duration = $request->rental_duration;
        $long_term_rental->auction_submit_date = $request->auction_submit_date;
        $long_term_rental->auction_winning_date = $request->auction_winning_date;
        $long_term_rental->offer_date = $request->offer_date;
        // $long_term_rental->require_date = $request->require_date;

        if (strcmp($request->rental_type, AuctionStatusEnum::AUCTION) == 0) {
            $long_term_rental->won_auction = $request->won_auction;
            $long_term_rental->need_pay_auction = boolval($request->need_pay_auction);
        } else {
            $long_term_rental->won_auction = AuctionResultEnum::OTHER;
            $long_term_rental->need_pay_auction = BOOL_FALSE;
        }
        $long_term_rental->bidder_price = floatval($request->bidder_price);
        $long_term_rental->bidder_name = $request->bidder_name;
        $long_term_rental->actual_delivery_date = $actual_delivery_date;
        $long_term_rental->contract_start_date = $contract_start_date;
        $long_term_rental->contract_end_date = $contract_end_date;
        $long_term_rental->reject_reason_id = $request->reject_reason_id;
        $long_term_rental->reject_reason_description = $request->reject_reason_description;
        $long_term_rental->remark = $request->remark;
        $long_term_rental->customer_id = $request->customer_id;
        $long_term_rental->customer_name = $request->customer_name;
        $long_term_rental->customer_address = $request->customer_address;
        $long_term_rental->customer_tel = $request->customer_tel;
        $long_term_rental->customer_email = $request->customer_email;
        $long_term_rental->customer_zipcode = $request->customer_zipcode;
        $long_term_rental->customer_province_id = $request->customer_province_id;
        $long_term_rental->is_spec = $request->is_spec;
        $long_term_rental->check_spec = boolval($request->check_spec);
        $long_term_rental->need_actual_delivery_date = boolval($need_actual_delivery_date);
        $long_term_rental->delivery_date_remark = $delivery_date_remark;
        if ($request->create) {
            $lt_status = LongTermRentalStatusEnum::SPECIFICATION;
        } else {
            if ($request->status) {
                $lt_status = $request->status;
            } else {
                $lt_status = LongTermRentalStatusEnum::NEW;
            }
        }
        $long_term_rental->status = $lt_status;

        $long_term_rental->bom_id = $request->bom_id;
        $long_term_rental->contact_name = $request->contact_name;
        $long_term_rental->contact_email = $request->contact_email;
        $long_term_rental->contact_tel = $request->contact_tel;
        $long_term_rental->contact_remark = $request->contact_remark;
        $long_term_rental->save();


        //        CreateDataTorLine
        if ($request->tor_file__pending_delete_ids) {
            $pending_delete_ids = $request->tor_file__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $long_term_rental->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('tor_file')) {
            foreach ($request->file('tor_file') as $image) {
                if ($image->isValid()) {
                    $long_term_rental->addMedia($image)->toMediaCollection('tor_file');
                }
            }
        }

        if ($request->rental_file__pending_delete_ids) {
            $pending_delete_ids = $request->rental_file__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $long_term_rental->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('rental_file')) {
            foreach ($request->file('rental_file') as $image) {
                if ($image->isValid()) {
                    $long_term_rental->addMedia($image)->toMediaCollection('rental_file');
                }
            }
        }

        if ($request->approved_rental_file__pending_delete_ids) {
            $pending_delete_ids = $request->approved_rental_file__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $long_term_rental->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('approved_rental_file')) {
            foreach ($request->file('approved_rental_file') as $image) {
                if ($image->isValid()) {
                    $long_term_rental->addMedia($image)->toMediaCollection('approved_rental_file');
                }
            }
        }

        if ($request->payment_form__pending_delete_ids) {
            $pending_delete_ids = $request->payment_form__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $long_term_rental->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('payment_form')) {
            foreach ($request->file('payment_form') as $image) {
                if ($image->isValid()) {
                    $long_term_rental->addMedia($image)->toMediaCollection('payment_form');
                }
            }
        }

        // if (
        //     strcmp($long_term_rental->status, LongTermRentalStatusEnum::COMPLETE) == 0
        //     && strcmp($quotation_status, QuotationStatusEnum::CONFIRM) == 0
        // ) {
        //     $this->createPurchaseRequisition($long_term_rental);
        // }

        $save_months = $this->saveLongTermRentalMonths($request->month, $long_term_rental);


        // if ($request->set_specs) {
        //     if (!empty($long_term_rental->bom_id)) {
        //         $this->saveLongTermRentalTorBom($long_term_rental);
        //     }
        //     // $redirect_route = route('admin.long-term-rental.specs.edit', ['rental' => $long_term_rental->id, 'create' => $request->create]);
        // }


        //        Check If is_spec = 0 and Check If Car Array Not Empty
        $long_term_rental_id = $long_term_rental->id;
        $DeleteCarData = $request->pending_delete_car_ids;
        $CarData = $request->cars;
        if (empty($request->is_spec)) {
            if (!empty($request->cars) || !empty($request->pending_delete_car_ids)) {
                $this->CreateDataTorLine($CarData, $DeleteCarData, $long_term_rental_id);
                $long_term_rental->spec_status = SpecStatusEnum::PENDING_CHECK;
                $long_term_rental->save();
            }
        } else {
            $long_term_rental->spec_status = SpecStatusEnum::DRAFT;
            $long_term_rental->save();
            if (!empty($DeleteCarData)) {
                $this->CreateDataTorLine(null, $DeleteCarData, null);
            }
        }
        //   Send Notifiaction LongTermRental
        $this->sendNotificationLongTermRental($request, $long_term_rental->worksheet_no, $long_term_rental);
        if (strcmp($request->is_spec, BOOL_TRUE) == 0) {
            $redirect_route = route('admin.long-term-rental.specs.edit', ['rental' => $long_term_rental->id]);
        } elseif (strcmp($request->is_spec, BOOL_FALSE) == 0) {
            $redirect_route = route('admin.long-term-rentals.index');
        }

        return $this->responseValidateSuccess($redirect_route);
    }

    public function saveLongTermRentalMonths($months, $long_term_rental)
    {
        $month_arr = explode(",", $months);
        $this->deleteRentalMonths($long_term_rental->id, $month_arr);
        if (strlen($month_arr[0]) != 0) {
            foreach (array_unique($month_arr) as $item) {
                $lt_rental_month = LongTermRentalMonth::where('lt_rental_id', $long_term_rental->id)
                    ->where('month', $item)->first();
                if (!$lt_rental_month) {
                    $lt_rental_month = new LongTermRentalMonth();
                    $lt_rental_month->lt_rental_id = $long_term_rental->id;
                    $lt_rental_month->month = $item;
                    $lt_rental_month->save();
                    if (strcmp($long_term_rental->spec_status, SpecStatusEnum::CONFIRM) === 0) {
                        $lt_rental_lines = LongTermRentalTrait::getRentalLinesFromRentalId($long_term_rental->id);
                        foreach ($lt_rental_lines as $key => $lt_rental_line) {
                            $this->saveRentalLineMonths($lt_rental_line->id, $lt_rental_month->id);
                        }
                    }
                }
            }
        }
        return true;
    }

    public function deleteRentalMonths($lt_rental_id, $months)
    {
        return LongTermRentalMonth::where('lt_rental_id', $lt_rental_id)
            ->whereNotIn('month', array_unique($months))
            ->delete();
    }

    private function saveLongTermRentalTorBom($long_term_rental)
    {
        $long_term_tor = LongTermRentalTor::where('lt_rental_id', $long_term_rental->bom_id)->get();
        if (!empty($long_term_tor)) {
            foreach ($long_term_tor as $item_tor) {
                $lt_rental_tor = $item_tor->replicate();
                $lt_rental_tor->lt_rental_id = $long_term_rental->id;
                $lt_rental_tor->save();

                $long_term_tor_line = LongTermRentalTorLine::where('lt_rental_tor_id', $item_tor->id)->get();
                if (!empty($long_term_tor_line)) {
                    foreach ($long_term_tor_line as $item_tor_line) {
                        $lt_rental_tor_line = $item_tor_line->replicate();
                        $lt_rental_tor_line->lt_rental_tor_id = $lt_rental_tor->id;
                        $lt_rental_tor_line->save();

                        $long_term_tor_line_accessory = LongTermRentalTorLineAccessory::where('lt_rental_tor_line_id', $item_tor_line->id)->get();
                        if (!empty($long_term_tor_line_accessory)) {
                            foreach ($long_term_tor_line_accessory as $item_tor_line_accessory) {
                                $lt_rental_tor_line_accessory = $item_tor_line_accessory->replicate();
                                $lt_rental_tor_line_accessory->lt_rental_tor_line_id = $lt_rental_tor_line->id;
                                $lt_rental_tor_line_accessory->save();
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    public function CreateDataTorLine($CarData = null, $CarDeleteData = null, $lt_rental_id = null)
    {
        if (!empty($CarDeleteData)) {
            LongTermRentalTorLine::whereIn('id', $CarDeleteData)->delete();
        }
        if (!empty($lt_rental_id)) {
            $lt_rental_id = $lt_rental_id;
            $lt_rental_tor_id = null;
            $lt_rental_tor = LongTermRentalTor::firstOrNew(['id' => $lt_rental_tor_id]);
            $lt_rental_tor->lt_rental_id = $lt_rental_id;
            $lt_rental_tor->save();
            if ($lt_rental_tor->id) {
                if (!empty($CarData)) {
                    $this->saveCarAccessory($CarData, $lt_rental_tor->id);
                }
            }
        }
    }

    private function saveCarAccessory($CarData, $lt_rental_tor_id)
    {
        if (!empty($CarData)) {
            foreach ($CarData as $car_index => $request_car) {
                if (isset($request_car['id'])) {
                    $lt_rental_tor_line = LongTermRentalTorLine::find($request_car['id']);
                } else {
                    $lt_rental_tor_line = new LongTermRentalTorLine();
                }
                $lt_rental_tor_line->lt_rental_tor_id = $lt_rental_tor_id;
                $lt_rental_tor_line->car_class_id = $request_car['car_class_id'];
                $lt_rental_tor_line->car_color_id = $request_car['car_color_id'];
                $lt_rental_tor_line->amount = intval($request_car['amount_car']);
                $lt_rental_tor_line->remark = $request_car['remark'];
                $lt_rental_tor_line->have_accessories = $request_car['have_accessories'];
                $lt_rental_tor_line->save();

                // save accessory
                LongTermRentalTorLineAccessory::where('lt_rental_tor_line_id', $lt_rental_tor_line->id)->delete();
                if (isset($request_car['accessory']) && sizeof($request_car['accessory']) > 0) {
                    foreach ($request_car['accessory'] as $accessory_key => $accessory) {
                        $long_term_accessory = new LongTermRentalTorLineAccessory();
                        $long_term_accessory->lt_rental_tor_line_id = $lt_rental_tor_line->id;
                        $long_term_accessory->accessory_id = $accessory['id'];
                        $long_term_accessory->amount = intval($accessory['amount']);
                        $long_term_accessory->amount_per_car = intval($accessory['amount_per_car']);
                        $long_term_accessory->tor_section = isset($accessory['tor_section']) ? $accessory['tor_section'] : null;
                        $long_term_accessory->remark = $accessory['remark'];
                        $long_term_accessory->type_accessories = $accessory['type_accessories'];
                        $long_term_accessory->save();
                    }
                }
            }
        }
        return true;
    }

    public function sendNotificationLongTermRental($dataRequest, $dataWorkSheetNo = null, $modelLongtermRental)
    {

        $requestIsSpec = $dataRequest?->is_spec;
        $requestId = $dataRequest?->id;

        if (!empty($requestIsSpec) && empty($requestId)) {
            $dataDepartment = [
                DepartmentEnum::QMD_QUALITY_ASSURANCE,
            ];
            $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
            $url = route('admin.long-term-rental.specs.edit', ['rental' => $modelLongtermRental]);
            $notiTypeChange = new NotificationManagement('ตีสเปครถ', 'ใบขอเช่ายาว ' . $dataWorkSheetNo . ' กรุณาตีสเปครถ', $url, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, []);
            $notiTypeChange->send();
        } else
        if (empty($requestIsSpec) && empty($requestId)) {
            $dataDepartment = [
                DepartmentEnum::PCD_PURCHASE,
            ];
            $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
            $url = route('admin.long-term-rental.spec-check-cars.edit', ['rental' => $modelLongtermRental]);
            $notiTypeChange = new NotificationManagement('เช็กรถ', 'ใบขอเช่ายาว ' . $dataWorkSheetNo . ' กรุณาเช็กรถ', $url, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, []);
            $notiTypeChange->send();
        }
    }

    public function generateLastDateLongTerm(Request $request)
    {
        $myDate = $request->start_date;
        $start_date = Carbon::createFromFormat('Y-m-d', $myDate);
        $end_date = $start_date->addMonth($request->duration);
        $end_date = $end_date->format('Y-m-d');
        return [
            'success' => true,
            'end_date' => $end_date
        ];
    }

    function checkRentalType(Request $request)
    {
        $lt_rental_type_id = $request->lt_rental_type_id;
        $rental_type = null;
        $lt_rental_type = LongTermRentalType::find($lt_rental_type_id);
        $rental_type = $lt_rental_type->type;

        return [
            'success' => true,
            'lt_rental_type_id' => $lt_rental_type_id,
            'rental_type' => $rental_type
        ];
    }

    public function printRentalRequisitionPdf(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::LongTermRental);
        $long_term_rental_id = $request->long_term_rental;
        $lt_rental = LongTermRental::find($long_term_rental_id);

        $quotation = Quotation::find($lt_rental->quotation_id);
        $today = Carbon::now()->isoFormat('DD MMMM YYYY');
        $lt_rental->customer_fax = $lt_rental->customer ? $lt_rental->customer->fax : null;
        $lt_rental->quotation_no = $quotation->qt_no;

        $lt_rental_line = LongTermRentalLine::find($request->lt_rental_line_id);
        $lt_rental_line_month_price = LongTermRentalLineMonth::where('lt_rental_line_id', $lt_rental_line->id)
            ->where('lt_rental_month_id', $request->lt_month_id)
            ->value('subtotal_price');
        $lt_rental_month = LongTermRentalMonth::find($request->lt_month_id);

        $lt_rental_line->lt_rental_line_month_price = $lt_rental_line_month_price;
        $lt_rental_line->lt_rental_month = $lt_rental_month->month;
        $lt_rental_line->amount = intval($request->lt_rental_car_class_amount);
        $lt_rental_line->car_class_text = ($lt_rental_line->carClass) ? $lt_rental_line->carClass->full_name . ' - ' . $lt_rental_line->carClass->name : '';
        $lt_rental_line->car_color_text = ($lt_rental_line->color) ? $lt_rental_line->color->name : '';

        $lt_rental_line_accessory = LongTermRentalLineAccessory::where('lt_rental_line_id', $lt_rental_line->id)->get();
        $lt_rental_accessory = [];
        $index = 0;
        foreach ($lt_rental_line_accessory as $accessory_index => $accessory_item) {
            $lt_rental_accessory[$index]['accessory_text'] = ($accessory_item->accessory) ? $accessory_item->accessory->name : '';
            $index++;
        }

        if ($quotation->edit_count && intval($quotation->edit_count) > 0) {
            $lt_rental->quotation_no .= 'Rev.' . strval(sprintf('%02d', $quotation->edit_count));
        }

        $lt_rental_line->lt_rental_accessory = $lt_rental_accessory;
        $page_title = $lt_rental->worksheet_no;

        $pdf = PDF::loadView(
            'admin.long-term-rentals.requisition-form-pdf.pdf',
            [
                'today' => $today,
                'lt_rental' => $lt_rental,
                'lt_rental_line' => $lt_rental_line,
                'page_title' => $page_title,
            ]
        );
        return $pdf->stream();
    }

    public function printRentalPDFbyConfig(Request $request)
    {
        $long_term_rental_id = $request->long_term_rental;
        $lt_rental_line_id = $request->lt_rental_line_id;
        $lt_month_id = $request->lt_month_id;
        $lt_rental_car_class_amount = $request->lt_rental_car_class_amount;

        $validate_data = [
            'lt_rental_line_id' => 'required',
            'lt_month_id' => 'required',
            'lt_rental_car_class_amount' => 'required | integer'
        ];

        $validator = Validator::make($request->all(), $validate_data, [], [
            'lt_rental_line_id' => __('long_term_rentals.car_class'),
            'lt_rental_car_class_amount' => __('long_term_rentals.car_amount'),
            'lt_month_id' => __('long_term_rentals.rental_duration'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        return response()->json([
            'success' => true,
            'message' => 'ok',
            'redirect' => route('admin.long-term-rentals.rental-requisition-pdf', [
                'long_term_rental' => $long_term_rental_id,
                'lt_rental_line_id' => $lt_rental_line_id,
                'lt_month_id' => $lt_month_id,
                'lt_rental_car_class_amount' => $lt_rental_car_class_amount
            ]),
        ]);
    }

    public function checkCountMonth(Request $request)
    {
        $long_term_rental_id = $request->long_term_rental;
        $lt_rental = LongTermRental::find($long_term_rental_id);
        $lt_rental_month = LongTermRentalMonth::where('lt_rental_id', $lt_rental->id)->where('month', $lt_rental->rental_duration)->count();
        if (strcmp($lt_rental_month, '0') == 0) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาระบุระยะเวลาเช่า (เดือน) ให้ถูกต้อง'
            ]);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'ok',
                'redirect' => route('admin.long-term-rentals.rental-requisition-pdf', ['long_term_rental' => $request->long_term_rental]),
            ]);
        }
    }
}
