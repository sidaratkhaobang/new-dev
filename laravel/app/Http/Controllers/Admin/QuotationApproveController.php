<?php

namespace App\Http\Controllers\Admin;

use App\Classes\NotificationManagement;
use App\Classes\StepApproveManagement;
use App\Enums\Actions;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\DepartmentEnum;
use App\Enums\LongTermRentalApprovalTypeEnum;
use App\Enums\LongTermRentalPriceStatusEnum;
use App\Enums\NotificationScopeEnum;
use App\Enums\Resources;
use App\Enums\SelfDriveTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\ImportCarLine;
use App\Traits\LongTermRentalTrait;
use App\Traits\NotificationTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\QuotationLine;
use App\Models\LongTermRental;
use App\Models\LongTermRentalLine;
use App\Models\LongTermRentalLineAccessory;
use App\Models\Rental;
use App\Models\RentalLine;
use App\Models\RentalBill;
use App\Models\LongTermRentalLineMonth;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Enums\QuotationStatusEnum;
use App\Enums\RentalTypeEnum;
use App\Models\Approve;
use App\Models\ApproveLine;
use App\Models\ConfigApprove;
use App\Models\ConfigApproveLine;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;
use App\Models\QuotationForm;
use App\Models\QuotationFormChecklist;
use App\Models\Product;
use App\Traits\HistoryTrait;

class QuotationApproveController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::QuotationApprove);
        $worksheet_id = $request->worksheet_no;
        $approval_type_id = $request->approval_type_id;
        $customer_id = $request->customer_id;
        $qt_id = $request->qt_id;
        $worksheet_list = Quotation::all();
        $worksheet_list->map(function ($item) {
            $item->name = $item->reference ? $item->reference->worksheet_no : null;
            return $item;
        });

        $approval_type_list = LongTermRentalTrait::getRentalApprovalList();

        $customer_list = Quotation::leftJoin('customers', 'customers.id', '=', 'quotations.customer_id')->select('customers.name', 'quotations.id')->get();
        $qt_worksheet_list = Quotation::select('qt_no as name', 'id')->get();

        $list = Quotation::select('quotations.*')
            ->sortable(['created_at' => 'desc'])
            ->with(['reference'])
            ->where('reference_type', LongTermRental::class)
            ->whereIn('status', [
                QuotationStatusEnum::PENDING_REVIEW,
                QuotationStatusEnum::CONFIRM,
                QuotationStatusEnum::REJECT,
            ])
            ->whereHasMorph(
                'reference',
                [LongTermRental::class],
                function (Builder $query, string $type) use ($approval_type_id) {
                    if ($approval_type_id) {
                        $query->where('approval_type', $approval_type_id);
                    }
                }
            )
            ->when($worksheet_id, function ($query) use ($worksheet_id) {
                return $query->where('quotations.id', $worksheet_id);
            })
            ->when($customer_id, function ($query) use ($customer_id) {
                return $query->where('quotations.id', $customer_id);
            })
            ->when($qt_id, function ($query) use ($qt_id) {
                return $query->where('quotations.id', $qt_id);
            })
            ->paginate(PER_PAGE);

        $long_term_model = LongTermRental::class;
        $short_term_model = Rental::class;
        return view('admin.quotation-approves.index', [
            's' => $request->s,
            'list' => $list,
            'long_term_model' => $long_term_model,
            'short_term_model' => $short_term_model,
            'worksheet_id' => $worksheet_id,
            'approval_type_id' => $approval_type_id,
            'customer_id' => $customer_id,
            'worksheet_list' => $worksheet_list,
            'approval_type_list' => $approval_type_list,
            'customer_list' => $customer_list,
            'qt_worksheet_list' => $qt_worksheet_list,
            'qt_id' => $qt_id,
        ]);
    }

    public function show(Quotation $quotation_approve)
    {
        $this->authorize(Actions::View . '_' . Resources::QuotationApprove);
        $customer_code = null;
        $quotation_approve->check_withholding_tax = null;
        $quotation_approve->withholding_tax = null;
        if ($quotation_approve->rental_bill_id) {
            $rental_bill = RentalBill::find($quotation_approve->rental_bill_id);
            $quotation_approve->check_withholding_tax = $rental_bill->check_withholding_tax;
            $quotation_approve->withholding_tax = $rental_bill->withholding_tax;
        }
        if ($quotation_approve->customer_id) {
            $customer = Customer::find($quotation_approve->customer_id);
            $customer_code = $customer->customer_code;
            $quotation_approve->customer_type = $customer->customer_type;
        }

        $lt_rental = LongTermRental::find($quotation_approve->reference_id);
        if ($lt_rental) {
            $lt_rental_lines = LongTermRentalLine::where('lt_rental_id', $lt_rental->id)
                ->get();
            $lt_rental_lines->map(function ($item) {
                $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
                $item->car_color_text = ($item->color) ? $item->color->name : '';
                $item->amount = $item->amount;
                return $item;
            });

            $listStatus = $this->getListStatus();

            $lt_rental_month = LongTermRentalLineMonth::whereIn('lt_rental_lines_months.lt_rental_line_id', $lt_rental_lines->pluck('id'))
                ->leftJoin('lt_rental_month', 'lt_rental_month.id', '=', 'lt_rental_lines_months.lt_rental_month_id')->select(
                    'lt_rental_month.month',
                    'lt_rental_lines_months.lt_rental_month_id',
                )->groupBy(
                    'lt_rental_month.month',
                    'lt_rental_lines_months.lt_rental_month_id',
                )
                ->get();

            $lt_rental_line_month = LongTermRentalLineMonth::whereIn('lt_rental_lines_months.lt_rental_line_id', $lt_rental_lines->pluck('id'))
                ->select(
                    'lt_rental_lines_months.lt_rental_line_id',
                    'lt_rental_lines_months.subtotal_price',
                    'lt_rental_lines_months.total_price',
                    'lt_rental_lines_months.total_purchase_options',
                )->get();

            $quotation_form = QuotationForm::where('quotation_id', $quotation_approve->id)->orderBy('seq', 'asc')->get();
            $list2 = $quotation_form->pluck('id')->toArray();
            $quotation_form_checklists = QuotationFormChecklist::whereIn('quotation_form_id', $list2)->orderBy('seq', 'asc')->get();
            $quotation_form_checklists->map(function ($item) {
                $item->quotation_form_checklist_status  = $item->status == STATUS_INACTIVE ? false : true;
                $item->quotation_form_checklist_seq  = $item->seq;
                $item->quotation_form_checklist_name  = $item->name;
                $item->quotation_form_checklist_id  = $item->id;
                return $item;
            });
            $quotation_form->map(function ($item) use ($quotation_form_checklists) {
                $quotation_form_checklist = $quotation_form_checklists->where('quotation_form_id', $item->id)->values();
                $item->sub_quotation_form_checklist  = $quotation_form_checklist;
                $item->quotation_form_status  = $item->status == STATUS_INACTIVE ? false : true;
                $item->quotation_form_id  = $item->id;
                return $item;
            });

            // $approve_line_list = new StepApproveManagement();
            // $approve_return = $approve_line_list->logApprove(Quotation::class, $quotation_approve->id);
            // $approve_line_list = $approve_return['approve_line_list'];
            // $approve = $approve_return['approve'];
            // if (!is_null($approve_line_list)) {
            //     // can approve or super user
            //     $approve_line_owner = new StepApproveManagement();
            //     $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
            // } else {
            //     $approve_line_owner = null;
            // }
            $approve_line = HistoryTrait::getHistory(Quotation::class, $quotation_approve->id);
            $approve_line_list = $approve_line['approve_line_list'];
            $approve = $approve_line['approve'];
            $approve_line_logs = $approve_line['approve_line_logs'];
            if (!is_null($approve_line_list)) {
                // can approve or super user
                $approve_line_owner = new StepApproveManagement();
                // $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
                $approve_line_owner = $approve_line_owner->checkCanApprove(Quotation::class, $quotation_approve->id);

            } else {
                $approve_line_owner = null;
            }

            $page_title = __('quotations.page_title') . $quotation_approve->qt_no . (isset($quotation_approve->edit_count) ? 'Rev.' . strval(sprintf('%02d', $quotation_approve->edit_count)) : null);
            return view('admin.quotation-approves.form', [
                'd' => $quotation_approve,
                'page_title' => $page_title,
                'customer_code' => $customer_code,
                'lt_rental_lines' => $lt_rental_lines,
                // 'lt_rental_accessory' => $lt_rental_accessory,
                'lt_rental_line_month' => $lt_rental_line_month,
                'lt_rental_month' => $lt_rental_month,
                'quotation_form' => $quotation_form,
                'view' => true,
                'lt_rental' => $lt_rental,
                'listStatus' => $listStatus,
                'approve_line_list' => $approve_line_list,
                'approve' => $approve,
                'approve_line_owner' => $approve_line_owner,
                'approve_line_logs' => $approve_line_logs,
            ]);
        } else {
            $rental_lines = RentalLine::where('rental_bill_id', $quotation_approve->rental_bill_id)
                ->select(
                    'rental_lines.*',
                )->get();
            $rental_bill = RentalBill::find($quotation_approve->rental_bill_id);
            $listStatus = $this->getListStatus();
            $quotation_form = QuotationForm::where('quotation_id', $quotation_approve->id)->orderBy('seq', 'asc')->get();
            $list2 = $quotation_form->pluck('id')->toArray();
            $quotation_form_checklists = QuotationFormChecklist::whereIn('quotation_form_id', $list2)->orderBy('seq', 'asc')->get();
            $quotation_form_checklists->map(function ($item) {
                $item->quotation_form_checklist_status  = $item->status == STATUS_INACTIVE ? false : true;
                $item->quotation_form_checklist_seq  = $item->seq;
                $item->quotation_form_checklist_name  = $item->name;
                $item->quotation_form_checklist_id  = $item->id;
                return $item;
            });
            $quotation_form->map(function ($item) use ($quotation_form_checklists) {
                $quotation_form_checklist = $quotation_form_checklists->where('quotation_form_id', $item->id)->values();
                $item->sub_quotation_form_checklist  = $quotation_form_checklist;
                $item->quotation_form_status  = $item->status == STATUS_INACTIVE ? false : true;
                $item->quotation_form_id  = $item->id;
                return $item;
            });

            // $approve_line_list = new StepApproveManagement();
            // $approve_return = $approve_line_list->logApprove(Quotation::class, $quotation_approve->id);
            // $approve_line_list = $approve_return['approve_line_list'];
            // $approve = $approve_return['approve'];
            // if (!is_null($approve_line_list)) {
            //     // can approve or super user
            //     $approve_line_owner = new StepApproveManagement();
            //     $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
            // } else {
            //     $approve_line_owner = null;
            // }
            $approve_line = HistoryTrait::getHistory(Quotation::class, $quotation_approve->id);
            $approve_line_list = $approve_line['approve_line_list'];
            $approve = $approve_line['approve'];
            $approve_line_logs = $approve_line['approve_line_logs'];
            if (!is_null($approve_line_list)) {
                // can approve or super user
                $approve_line_owner = new StepApproveManagement();
                $approve_line_owner = $approve_line_owner->checkCanApprove(Quotation::class, $quotation_approve->id);
            } else {
                $approve_line_owner = null;
            }

            $page_title = __('quotations.page_title') . $quotation_approve->qt_no . (isset($quotation_approve->edit_count) ? 'Rev.' . strval(sprintf('%02d', $quotation_approve->edit_count)) : null);
            return view('admin.quotations.form', [
                'd' => $quotation_approve,
                'page_title' => $page_title,
                'customer_code' => $customer_code,
                'rental_lines' => $rental_lines,
                'rental_bill' => $rental_bill,
                'lt_rental_line_month' => null,
                'lt_rental_month' => null,
                'quotation_form' => $quotation_form,
                'lt_rental' => $lt_rental,
                'listStatus' => $listStatus,
                'view' => true,
                'approve_line_list' => $approve_line_list,
                'approve' => $approve,
                'approve_line_owner' => $approve_line_owner,
                'approve_line_logs' => $approve_line_logs,
            ]);
        }
    }


    public static function getRentalType()
    {
        $rental_type = collect([
            (object) [
                'id' => Rental::class,
                'name' => __('quotations.rental_type_' . RentalTypeEnum::SHORT),
                'value' => Rental::class,
            ],
            (object) [
                'id' => LongTermRental::class,
                'name' => __('quotations.rental_type_' . RentalTypeEnum::LONG),
                'value' => LongTermRental::class,
            ],
        ]);
        return $rental_type;
    }

    public function updateQuotationStatus(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::QuotationApprove);
        $quotation_id = $request->quotations;
        $quotation = Quotation::find($quotation_id);
        // update approve step
        $approve_update = new StepApproveManagement();
        // $approve_update = $approve_update->updateApprove($request, $quotation, $request->quotation_status, Quotation::class);
        $approve_update = $approve_update->updateApprove(Quotation::class, $quotation->id, $request->quotation_status,null,$request->reject_reason);


        $quotation->status = $approve_update;
        if ($request->reject_reason) {
            $quotation->reject_reason = $request->reject_reason;
        }
        $quotation->save();

        if (strcmp($approve_update, QuotationStatusEnum::CONFIRM) === 0 && strcmp($quotation->reference_type, LongTermRental::class) === 0) {
            //            $lt_rental = LongTermRental::find($quotation->reference_id);
            //            if (strcmp($lt_rental->approval_type, LongTermRentalApprovalTypeEnum::AFFILIATED) === 0) {
            //                $step_approve_management = new StepApproveManagement();
            //                $step_approve_management->createModelApproval(ConfigApproveTypeEnum::LT_QUOTATION_AFFILIATED, Quotation::class, $quotation->id);
            //            } else if (strcmp($lt_rental->approval_type, LongTermRentalApprovalTypeEnum::UNAFFILIATED) === 0) {
            //                $step_approve_management = new StepApproveManagement();
            //                $step_approve_management->createModelApproval(ConfigApproveTypeEnum::LT_QUOTATION_UNAFFILIATED, Quotation::class, $quotation->id);
            //            }
            NotificationTrait::sendNotificationRentalPriceApprove($quotation->id, $quotation, $quotation->qt_no);
        }

        if (strcmp($approve_update, QuotationStatusEnum::REJECT) === 0 && strcmp($quotation->reference_type, LongTermRental::class) === 0) {
            $lt_rental = LongTermRental::findOrFail($quotation->reference_id);
            $lt_rental->rental_price_status = LongTermRentalPriceStatusEnum::REJECT;
            $lt_rental->save();
            $this->sendNotificationReject($quotation, $quotation->qt_no);
        }

        return response()->json([
            'success' => true,
            'message' => 'ok',
            'redirect' => ($request->redirect_route) ? $request->redirect_route : route('admin.quotation-approves.index')
        ]);
    }

    public function sendNotificationReject($modelQuotation, $dataWorkSheetNo)
    {
        $dataDepartment = [
            DepartmentEnum::AMO_SALE_ADMIN,
        ];
        $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
        $url = route('admin.quotations.show', ['quotation' => $modelQuotation]);
        $notiTypeChange = new NotificationManagement('ไม่อนุมัติใบเสนอราคาเช่ายาว', 'ใบเสนอราคา ' . $dataWorkSheetNo . ' ไม่ได้รับการอนุมัติ', $url, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, [], 'danger');
        $notiTypeChange->send();
    }
}
