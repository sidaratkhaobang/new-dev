<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Models\LongTermRentalMonth;
use App\Models\LongTermRentalType;
use App\Traits\CustomerTrait;
use App\Traits\LongTermRentalTrait;
use App\Traits\NotificationTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LongTermRental;
use App\Models\ComparisonPrice;
use App\Models\Quotation;
use App\Models\QuotationForm;
use App\Models\QuotationFormChecklist;
use App\Models\ConditionQuotation;
use App\Models\ConditionQuotationChecklist;
use App\Models\ComparisonPriceLine;
use App\Enums\QuotationStatusEnum;
use App\Enums\LongTermRentalStatusEnum;
use App\Enums\SpecStatusEnum;
use App\Enums\ComparisonPriceStatusEnum;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\LongTermRentalApprovalTypeEnum;
use App\Enums\LongTermRentalPriceStatusEnum;
use App\Models\Approve;
use App\Models\ApproveLine;
use App\Models\ConfigApprove;
use App\Models\ConfigApproveLine;
use App\Models\LongTermRentalLine;
use App\Models\LongTermRentalLineMonth;
use App\Traits\PurchaseRequisitionTrait;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\DB;
use App\Traits\RentalTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LongTermRentalQuotationController extends Controller
{
    use RentalTrait, LongTermRentalTrait, CustomerTrait;

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::LongTermRentalQuotation);
        $status = $request->status;
        $worksheet_id = $request->worksheet_no;
        $customer_id = $request->customer;
        $worksheet_id = $request->worksheet_no;
        $spec_status_id = $request->spec_status;
        $lt_rental_type = $request->lt_rental_type;

        $rental_price_status_list = RentalTrait::getRentalPriceStatusList();
        $worksheet_list = LongTermRental::where('lt_rentals.spec_status', SpecStatusEnum::CONFIRM)
            ->where('lt_rentals.comparison_price_status', ComparisonPriceStatusEnum::CONFIRM)
            ->orWhere('lt_rentals.status', LongTermRentalStatusEnum::QUOTATION)
            ->select('worksheet_no as name', 'id')->get();

        $list = $this->getLongTermRental($request);
        $customer_list = LongTermRental::select('id', 'customer_name as name')
            ->byQuotationStatus()->get();

        $worksheet_list = LongTermRental::select('id', 'worksheet_no as name')
            ->byQuotationStatus()->get();
        $lt_rental_type_list = LongTermRentalType::withTrashed()->get();
        return view('admin.long-term-rental-quotations.index', [
            'worksheet_id' => $worksheet_id,
            'status' => $status,
            'list' => $list,
            'rental_price_status_list' => $rental_price_status_list,
            'worksheet_list' => $worksheet_list,
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
        $worksheet_id = $request->worksheet_no;
        $s = $request->s;
        $lt_rental_type = $request->lt_rental_type;

        // $list = LongTermRental::select('lt_rentals.id', 'lt_rentals.worksheet_no', 'quotations.status', 'lt_rentals.rental_price_status')
        $list = LongTermRental::sortable(['created_at' => 'desc'])
            ->select(
                'lt_rentals.id',
                'lt_rentals.worksheet_no',
                'lt_rentals.spec_status',
                'lt_rentals.rental_price_status',
                'lt_rentals.customer_name',
                'lt_rentals.offer_date',
                'quotations.status',
                'lt_rental_types.name as rental_type'
            )
            ->leftJoin('quotations', 'quotations.id', '=', 'lt_rentals.quotation_id')
            ->leftJoin('lt_rental_types', 'lt_rental_types.id', '=', 'lt_rentals.lt_rental_type_id')
            ->where('lt_rentals.spec_status', SpecStatusEnum::CONFIRM)
            ->where('lt_rentals.comparison_price_status', ComparisonPriceStatusEnum::CONFIRM)
            ->when($status, function ($query) use ($status) {
                $query->where('lt_rentals.rental_price_status', $status);
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
            ->branch()
            ->paginate(PER_PAGE);
        return $list;
    }

    public function edit(LongTermRental $rental)
    {
        $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalQuotation);
        // $rental->status = STATUS_ACTIVE;
        $lt_rental_list = LongTermRentalLine::leftJoin('car_classes', 'car_classes.id', '=', 'lt_rental_lines.car_class_id')
            ->leftJoin('car_colors', 'car_colors.id', '=', 'lt_rental_lines.car_color_id')
            ->where('lt_rental_lines.lt_rental_id', $rental->id)
            ->select(
                'lt_rental_lines.id as lt_rental_line_id',
                'car_classes.name as model',
                'car_classes.full_name as model_full_name',
                'car_colors.name as color',
                'lt_rental_lines.amount as amount',
                'lt_rental_lines.rental_price as rental_price',
                'lt_rental_lines.showroom_price as showroom_price',
            )
            ->get();

        $listStatus = $this->getListLongTermStatus();


        $lt_rental_list->map(function ($item) {
            $lt_rental_line_month = LongTermRentalLineMonth::leftJoin('lt_rental_month', 'lt_rental_month.id', '=', 'lt_rental_lines_months.lt_rental_month_id')
                ->where('lt_rental_lines_months.lt_rental_line_id', $item->lt_rental_line_id)->get()->toArray();
            $item->months = $lt_rental_line_month;

            return $item;
        });

        $lt_rental_month = LongTermRental::leftJoin('lt_rental_month', 'lt_rental_month.lt_rental_id', '=', 'lt_rentals.id')
            ->where('lt_rentals.id', $rental->id)->select('lt_rental_month.month', 'lt_rental_month.id')->get();

        $cars = $this->getCarSpecAndEquipments($rental->id);
        $option = [];
        $option['item_type'] = LongTermRental::class;
        $compare_list = PurchaseRequisitionTrait::getComparePriceList($rental->id, $option);
        $creditor = ($rental->creditor) ? $rental->creditor->name : null;
        $customer_type_list = CustomerTrait::getCustomerType();
        $with_trash = true;
        $lt_rental_type_list = LongTermRentalTrait::getRentalJobTypeList($with_trash);
        $month_list = LongTermRentalMonth::where('lt_rental_id', $rental->id)
            ->selectRaw('id, CONCAT(month, " เดือน") as name')
            ->get();
        $month = $month_list->pluck('id')->toArray();
        $page_title = __('long_term_rentals.quotation');
        return view('admin.long-term-rental-quotations.form', [
            'd' => $rental,
            'page_title' => $page_title,
            'creditor' => $creditor,
            'lt_rental_list' => $lt_rental_list,
            'purchase_requisition_car_list' => $cars,
            'purchase_order_dealer_list' => $compare_list,
            'cannot_add' => true,
            'cannot_edit' => true,
            'lt_rental_month' => $lt_rental_month,
            'listStatus' => $listStatus,
            'customer_type_list' => $customer_type_list,
            'lt_rental_type_list' => $lt_rental_type_list,
            'month_list' => $month_list,
            'month' => $month,
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

    public function show(LongTermRental $rental)
    {
        $this->authorize(Actions::View . '_' . Resources::LongTermRentalQuotation);
        $lt_rental_list = LongTermRentalLine::leftJoin('car_classes', 'car_classes.id', '=', 'lt_rental_lines.car_class_id')
            ->leftJoin('car_colors', 'car_colors.id', '=', 'lt_rental_lines.car_color_id')
            ->where('lt_rental_lines.lt_rental_id', $rental->id)
            ->select(
                'lt_rental_lines.id as lt_rental_line_id',
                'car_classes.name as model',
                'car_classes.full_name as model_full_name',
                'car_colors.name as color',
                'lt_rental_lines.amount as amount',
                'lt_rental_lines.rental_price as rental_price',
            )
            ->get();

        $listStatus = $this->getListLongTermStatus();

        $lt_rental_list->map(function ($item) {
            $lt_rental_line_month = LongTermRentalLineMonth::leftJoin('lt_rental_month', 'lt_rental_month.id', '=', 'lt_rental_lines_months.lt_rental_month_id')
                ->where('lt_rental_lines_months.lt_rental_line_id', $item->lt_rental_line_id)->get()->toArray();
            $item->months = $lt_rental_line_month;

            return $item;
        });

        $lt_rental_month = LongTermRental::leftJoin('lt_rental_month', 'lt_rental_month.lt_rental_id', '=', 'lt_rentals.id')
            ->where('lt_rentals.id', $rental->id)->select('lt_rental_month.month', 'lt_rental_month.id')->get();

        $cars = $this->getCarSpecAndEquipments($rental->id);
        $option = [];
        $option['item_type'] = LongTermRental::class;
        $compare_list = PurchaseRequisitionTrait::getComparePriceList($rental->id, $option);
        $creditor = ($rental->creditor) ? $rental->creditor->name : null;
        $customer_type_list = CustomerTrait::getCustomerType();
        $with_trash = true;
        $lt_rental_type_list = LongTermRentalTrait::getRentalJobTypeList($with_trash);

        $month_list = LongTermRentalMonth::where('lt_rental_id', $rental->id)
            ->selectRaw('id, CONCAT(month, " เดือน") as name')
            ->get();
        $month = $month_list->pluck('id')->toArray();
        $page_title = __('long_term_rentals.quotation');
        return view('admin.long-term-rental-quotations.form', [
            'd' => $rental,
            'page_title' => $page_title,
            'creditor' => $creditor,
            'lt_rental_list' => $lt_rental_list,
            'purchase_requisition_car_list' => $cars,
            'purchase_order_dealer_list' => $compare_list,
            'cannot_add' => true,
            'cannot_edit' => true,
            'lt_rental_month' => $lt_rental_month,
            'view' => true,
            'listStatus' => $listStatus,
            'customer_type_list' => $customer_type_list,
            'lt_rental_type_list' => $lt_rental_type_list,
            'month_list' => $month_list,
            'month' => $month,
        ]);
    }

    public function store(Request $request)
    {

        $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalQuotation);
        $validator = Validator::make($request->all(), [

            'price.*.*' => [
                'required',
            ],
            'purchase_options.*.*' => [
                'required',
            ],


        ], [], [
            'price.*.*' => __('long_term_rentals.price_quotation'),
            'purchase_options.*.*' => __('long_term_rentals.purchase_options_quotation'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $rental_id = $request->id;
        $long_term_rental = LongTermRental::find($rental_id);

        $lt_rental_approve = LongTermRental::find($long_term_rental->id);

        if (strcmp($lt_rental_approve->approval_type, LongTermRentalApprovalTypeEnum::AFFILIATED) === 0) {
            $step_approve_management = new StepApproveManagement();
            $is_configured = $step_approve_management->isApproveConfigured(ConfigApproveTypeEnum::LT_QUOTATION_AFFILIATED);
            if (!$is_configured) {
                return $this->responseWithCode(false, __('lang.config_approve_warning') . __('config_approves.config_type_' . ConfigApproveTypeEnum::LT_QUOTATION_AFFILIATED), null, 422);
            }
        } else if (strcmp($lt_rental_approve->approval_type, LongTermRentalApprovalTypeEnum::UNAFFILIATED) === 0) {
            $step_approve_management = new StepApproveManagement();
            $is_configured = $step_approve_management->isApproveConfigured(ConfigApproveTypeEnum::LT_QUOTATION_UNAFFILIATED);
            if (!$is_configured) {
                return $this->responseWithCode(false, __('lang.config_approve_warning') . __('config_approves.config_type_' . ConfigApproveTypeEnum::LT_QUOTATION_UNAFFILIATED), null, 422);
            }
        }

        $long_term_rental_line = LongTermRentalLine::where('lt_rental_id', $rental_id)->get();
        if ($request->quotation_status) {
            $quotation_count = Quotation::all()->count() + 1;

            $quotation = Quotation::firstOrNew(['id' => $request->quotation_id]);
            if (!($quotation->exists)) {
                $prefix = 'QT';
                $quotation->qt_no = generateRecordNumber($prefix, $quotation_count, false);
            }
            $status_reject = null;
            if ($quotation->status == QuotationStatusEnum::REJECT) {
                $status_reject = QuotationStatusEnum::REJECT;
                if (is_null($quotation->edit_count)) {
                    $quotation->edit_count = 1;
                } else {
                    $quotation->edit_count = $quotation->edit_count + 1;
                }

                $approve_clear_status = new StepApproveManagement();
                $approve_return = $approve_clear_status->clearStatus(Quotation::class, $request->quotation_id);
            }
            $quotation->qt_type = QuotationStatusEnum::DRAFT;
            $quotation->reference_type = LongTermRental::class;
            $quotation->reference_id = $long_term_rental->id;
            $quotation->remark = $request->quotation_remark;
            $quotation->customer_id = $long_term_rental->customer_id;
            $quotation->customer_name = $long_term_rental->customer_name;
            $quotation->customer_address = $long_term_rental->customer_address;
            $quotation->customer_tel = $long_term_rental->customer_tel;
            $quotation->customer_email = $long_term_rental->customer_email;
            $quotation->customer_zipcode = $long_term_rental->customer_zipcode;
            $quotation->customer_province_id = $long_term_rental->customer_province_id;
            $quotation->status = QuotationStatusEnum::PENDING_REVIEW;
            $quotation->save();

            $long_term_rental->quotation_id = $quotation->id;
            $long_term_rental->quotation_remark = $request->quotation_remark;
            $lt_status = $long_term_rental->status;
            $long_term_rental->status = LongTermRentalStatusEnum::QUOTATION;
            $long_term_rental->rental_price_status = LongTermRentalPriceStatusEnum::CONFIRM;
            $long_term_rental->purchase_option_check = $request->purchase_option_check;
            $long_term_rental->save();

            if (($status_reject != QuotationStatusEnum::REJECT && $long_term_rental->rental_price_status != LongTermRentalPriceStatusEnum::CONFIRM) || $lt_status == LongTermRentalStatusEnum::RENTAL_PRICE) {
                $this->dupConditionQuotation($quotation, $long_term_rental);
                $lt_rental = LongTermRental::find($quotation->reference_id);
                if (strcmp($lt_rental->approval_type, LongTermRentalApprovalTypeEnum::AFFILIATED) === 0) {
                    $step_approve_management = new StepApproveManagement();
                    $step_approve_management->createModelApproval(ConfigApproveTypeEnum::LT_QUOTATION_AFFILIATED, Quotation::class, $quotation->id);
                } else if (strcmp($lt_rental->approval_type, LongTermRentalApprovalTypeEnum::UNAFFILIATED) === 0) {
                    $step_approve_management = new StepApproveManagement();
                    $step_approve_management->createModelApproval(ConfigApproveTypeEnum::LT_QUOTATION_UNAFFILIATED, Quotation::class, $quotation->id);
                }
                NotificationTrait::sendNotificationRentalPriceApprove($quotation->id, $quotation, $quotation->qt_no);
            }
        } else {
            if ($long_term_rental->rental_price_status == LongTermRentalPriceStatusEnum::REJECT) {
                $long_term_rental->rental_price_status = LongTermRentalPriceStatusEnum::DRAFT;
            }

            $long_term_rental->purchase_option_check = $request->purchase_option_check;
            $long_term_rental->quotation_remark = $request->quotation_remark;
            $long_term_rental->save();
        }
        if (!empty($request->price)) {
            foreach ($request->price as $key => $price) {
                foreach ($price as $key2 => $price2) {
                    $price2 = str_replace(',', '', $price2);
                    $rental_line_month = LongTermRentalLineMonth::where('lt_rental_line_id', $key)->where('lt_rental_month_id', $key2)
                        ->update([
                            'total_price' => $price2 ? ($price2 * 107) / 100 : 0.00,
                            'vat_price' => $price2 ? ($price2 * 7) / 100 : 0.00,
                            'subtotal_price' => $price2 ? $price2 : 0.00,
                        ]);
                }
            }
        }
        if (!empty($request->purchase_options)) {
            foreach ($request->purchase_options as $key => $purchase_options) {
                foreach ($purchase_options as $key2 => $purchase_options2) {
                    $purchase_options2 = str_replace(',', '', $purchase_options2);
                    $rental_line_month = LongTermRentalLineMonth::where('lt_rental_line_id', $key)->where('lt_rental_month_id', $key2)
                        ->update([
                            'subtotal_purchase_options' => $purchase_options2 ? ($purchase_options2 * 100) / 107 : 0.00,
                            'vat_purchase_options' => $purchase_options2 ? ($purchase_options2 * 7) / 107 : 0.00,
                            'total_purchase_options' => $purchase_options2 ? $purchase_options2 : 0.00,
                        ]);
                }
            }
        }

        $redirect_route = route('admin.long-term-rental.quotations.index');
        return $this->responseValidateSuccess($redirect_route);
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

    private function dupConditionQuotation($quotation, $long_term_rental)
    {
        $condition_quottion = ConditionQuotation::where('status', STATUS_ACTIVE)->where('condition_type', $long_term_rental->approval_type)->orderBy('seq', 'asc')->get();
        if (!empty($condition_quottion)) {
            foreach ($condition_quottion as $item_condition_quottion) {
                $quotation_form = new QuotationForm();
                $quotation_form->quotation_id = $quotation->id;
                $quotation_form->name = $item_condition_quottion->name;
                $quotation_form->seq = $item_condition_quottion->seq;
                $quotation_form->status = $item_condition_quottion->status;
                $quotation_form->save();

                $condition_quottion_checklist = ConditionQuotationChecklist::where('condition_quotations_id', $item_condition_quottion->id)->where('status', STATUS_ACTIVE)->orderBy('seq', 'asc')->get();
                if (!empty($condition_quottion_checklist)) {
                    foreach ($condition_quottion_checklist as $item_condition_quottion_checklist) {
                        $quotation_form_checklist = new QuotationFormChecklist();
                        $quotation_form_checklist->quotation_form_id = $quotation_form->id;
                        $quotation_form_checklist->name = $item_condition_quottion_checklist->name;
                        $quotation_form_checklist->seq = $item_condition_quottion_checklist->seq;
                        $quotation_form_checklist->status = $item_condition_quottion_checklist->status;
                        $quotation_form_checklist->save();
                    }
                }
            }
        }
        return true;
    }
}
