<?php

namespace App\Http\Controllers\Admin;

use App\Classes\OrderManagement;
use App\Classes\StepApproveManagement;
use App\Classes\ProductManagement;
use App\Enums\Actions;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\LongTermRentalApprovalTypeEnum;
use App\Enums\LongTermRentalPriceStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
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
use App\Enums\CalculateTypeEnum;
use App\Enums\QuotationTypeEnum;
use App\Models\Approve;
use App\Models\ApproveLine;
use App\Models\ConditionQuotation;
use App\Models\ConditionQuotationChecklist;
use App\Models\ConfigApprove;
use App\Models\ConfigApproveLine;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;
use App\Models\QuotationForm;
use App\Models\QuotationFormChecklist;
use App\Models\Product;
use App\Models\User;
use App\Traits\HistoryTrait;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Quotation);
        $worksheet_id = $request->worksheet_no;
        $rental_id = $request->rental_id;
        $customer_id = $request->customer_id;
        $qt_id = $request->qt_id;
        $worksheet_list = Quotation::all();
        $worksheet_list->map(function ($item) {
            $item->name = $item->reference ? $item->reference->worksheet_no : null;
            return $item;
        });

        $rental_list = $this->getRentalType();

        $customer_list = Quotation::leftJoin('customers', 'customers.id', '=', 'quotations.customer_id')->select('customers.name', 'quotations.id')->get();
        $qt_worksheet_list = Quotation::select('qt_no as name', 'id')->get();

        $list = Quotation::select('quotations.*')
            ->sortable(['created_at' => 'desc'])
            ->with(['reference'])
            ->whereIn('reference_type', [Rental::class, LongTermRental::class])
            ->when($worksheet_id, function ($query) use ($worksheet_id) {
                return $query->where('quotations.id', $worksheet_id);
            })
            ->when($rental_id, function ($query) use ($rental_id) {
                return $query->where('quotations.reference_type', $rental_id);
            })
            ->when($customer_id, function ($query) use ($customer_id) {
                return $query->where('quotations.id', $customer_id);
            })
            ->when($qt_id, function ($query) use ($qt_id) {
                return $query->where('quotations.id', $qt_id);
            })
            ->withTrashed()
            ->paginate(PER_PAGE);

        $long_term_model = LongTermRental::class;
        $short_term_model = Rental::class;
        return view('admin.quotations.index', [
            's' => $request->s,
            'list' => $list,
            'long_term_model' => $long_term_model,
            'short_term_model' => $short_term_model,
            'worksheet_id' => $worksheet_id,
            'rental_id' => $rental_id,
            'customer_id' => $customer_id,
            'worksheet_list' => $worksheet_list,
            'rental_list' => $rental_list,
            'customer_list' => $customer_list,
            'qt_worksheet_list' => $qt_worksheet_list,
            'qt_id' => $qt_id,
        ]);
    }

    public function edit(Quotation $quotation)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Quotation);
        $customer_code = null;
        $quotation->check_withholding_tax = null;
        $quotation->withholding_tax = null;
        if ($quotation->rental_bill_id) {
            $rental_bill = RentalBill::find($quotation->rental_bill_id);
            $quotation->check_withholding_tax = $rental_bill->check_withholding_tax;
            $quotation->withholding_tax = $rental_bill->withholding_tax;
        }
        if ($quotation->customer_id) {
            $customer = Customer::find($quotation->customer_id);
            $customer_code = $customer->customer_code;
            $quotation->customer_type = $customer->customer_type;
        }

        $lt_rental = LongTermRental::find($quotation->reference_id);

        if ($lt_rental) {
            $lt_rental_id = $lt_rental->id;
            $lt_rental_lines = LongTermRentalLine::where('lt_rental_id', $lt_rental->id)
                ->get();
            $lt_rental_lines->map(function ($item) {
                $item->car_class_text = ($item->carClass) ? $item->carClass->full_name . ' - ' . $item->carClass->name : '';
                $item->car_color_text = ($item->color) ? $item->color->name : '';
                $item->amount = $item->amount;
                $item->lt_rental_line_id = $item->id;
                $item->remark_quotation = $item->remark_quotation;
                return $item;
            });
            // dd($lt_rental_lines);

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

            $listStatus = $this->getListStatus();

            $quotation_form = QuotationForm::where('quotation_id', $quotation->id)->orderBy('seq', 'asc')->get();
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
            // $approve_return = $approve_line_list->logApprove(Quotation::class, $quotation->id);
            // $approve_line_list = $approve_return['approve_line_list'];
            // $approve = $approve_return['approve'];
            $approve_line = HistoryTrait::getHistory(Quotation::class, $quotation->id);
            $approve_line_list = $approve_line['approve_line_list'];
            $approve = $approve_line['approve'];
            $approve_line_logs = $approve_line['approve_line_logs'];


            $page_title = __('quotations.page_title') . $quotation->qt_no . (isset($quotation->edit_count) ? 'Rev.' . strval(sprintf('%02d', $quotation->edit_count)) : null);
            return view('admin.quotations.form', [
                'd' => $quotation,
                'page_title' => $page_title,
                'customer_code' => $customer_code,
                'lt_rental_lines' => $lt_rental_lines,
                'lt_rental_line_month' => $lt_rental_line_month,
                'lt_rental_month' => $lt_rental_month,
                'quotation_form' => $quotation_form,
                'lt_rental' => $lt_rental,
                'listStatus' => $listStatus,
                'approve_line_list' => $approve_line_list,
                'approve' => $approve,
                'lt_rental_id' => $lt_rental_id,
                'approve_line_logs' => $approve_line_logs,
            ]);
        } else {
            $rental_lines = RentalLine::where('rental_bill_id', $quotation->rental_bill_id)
                ->select(
                    'rental_lines.*',
                )->get();
            $rental_lines->map(function ($item) {
                $rental = Rental::find($item->rental_id);
                $product = Product::find($rental->product_id);
                $item->rental_date = getDaysTimesBetweenDate($rental->pickup_date, $rental->return_date);
                if ($product && strcmp($product->calculate_type, CalculateTypeEnum::DAILY) == 0) {
                    $pm = new ProductManagement($rental->service_type_id);
                    $pm->setDates($rental->pickup_date, $rental->return_date);
                    $item->rental_date = $pm->order_days . " วัน ";
                }
                return $item;
            });
            $rental_bill = RentalBill::find($quotation->rental_bill_id);
            $listStatus = $this->getListStatus();
            $quotation_form = QuotationForm::where('quotation_id', $quotation->id)->orderBy('seq', 'asc')->get();
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

            $page_title = __('quotations.page_title') . $quotation->qt_no . (isset($quotation->edit_count) ? 'Rev.' . strval(sprintf('%02d', $quotation->edit_count)) : null);
            return view('admin.quotations.form', [
                'd' => $quotation,
                'page_title' => $page_title,
                'customer_code' => $customer_code,
                'rental_lines' => $rental_lines,
                'rental_bill' => $rental_bill,
                'lt_rental_line_month' => null,
                'lt_rental_month' => null,
                'quotation_form' => $quotation_form,
                'lt_rental' => $lt_rental,
                'listStatus' => $listStatus,
            ]);
        }
    }

    public function getCondition(Request $request)
    {
        $lt_rental = LongTermRental::find($request->id);
        $condition_quotations = ConditionQuotation::where('condition_type', $lt_rental->approval_type)->orderBy('seq', 'asc')->get();
        $list2 = $condition_quotations->pluck('id')->toArray();
        $condition_quotation_checklists = ConditionQuotationChecklist::whereIn('condition_quotations_id', $list2)->orderBy('seq', 'asc')->get();
        $condition_quotation_checklists->map(function ($item) {
            $item->quotation_form_checklist_status  = $item->status == STATUS_INACTIVE ? false : true;
            $item->quotation_form_checklist_seq  = $item->seq;
            $item->quotation_form_checklist_name  = $item->name;
            $item->quotation_form_checklist_id  = $item->id;
            return $item;
        });
        $condition_quotations->map(function ($item) use ($condition_quotation_checklists) {
            $quotation_form_checklist = $condition_quotation_checklists->where('condition_quotations_id', $item->id)->values();
            $item->sub_quotation_form_checklist  = $quotation_form_checklist;
            $item->quotation_form_status  = $item->status == STATUS_INACTIVE ? false : true;
            $item->quotation_form_id  = $item->id;
            return $item;
        });
        return response()->json($condition_quotations);
    }

    public function show(Quotation $quotation)
    {
        $this->authorize(Actions::View . '_' . Resources::Quotation);
        $customer_code = null;
        $quotation->check_withholding_tax = null;
        $quotation->withholding_tax = null;
        if ($quotation->rental_bill_id) {
            $rental_bill = RentalBill::find($quotation->rental_bill_id);
            $quotation->check_withholding_tax = $rental_bill->check_withholding_tax;
            $quotation->withholding_tax = $rental_bill->withholding_tax;
        }
        if ($quotation->customer_id) {
            $customer = Customer::find($quotation->customer_id);
            $customer_code = $customer->customer_code;
            $quotation->customer_type = $customer->customer_type;
        }

        $lt_rental = LongTermRental::find($quotation->reference_id);
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

            $quotation_form = QuotationForm::where('quotation_id', $quotation->id)->orderBy('seq', 'asc')->get();
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
            // $approve_return = $approve_line_list->logApprove(Quotation::class, $quotation->id);
            // $approve_line_list = $approve_return['approve_line_list'];
            // $approve = $approve_return['approve'];
            $approve_line = HistoryTrait::getHistory(Quotation::class, $quotation->id);
            $approve_line_list = $approve_line['approve_line_list'];
            $approve = $approve_line['approve'];
            $approve_line_logs = $approve_line['approve_line_logs'];

            $page_title = __('quotations.page_title') . $quotation->qt_no . (isset($quotation->edit_count) ? 'Rev.' . strval(sprintf('%02d', $quotation->edit_count)) : null);
            return view('admin.quotations.form', [
                'd' => $quotation,
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
                'approve_line_logs' => $approve_line_logs,
            ]);
        } else {
            $rental_lines = RentalLine::where('rental_bill_id', $quotation->rental_bill_id)
                ->select(
                    'rental_lines.*',
                )->get();
            $rental_lines->map(function ($item) {
                $rental = Rental::find($item->rental_id);
                $product = Product::find($rental->product_id);
                if ($product) {
                    if (strcmp($product->calculate_type, CalculateTypeEnum::DAILY) == 0) {
                        $pm = new ProductManagement($rental->service_type_id);
                        $pm->setDates($rental->pickup_date, $rental->return_date);
                        $item->rental_date = $pm->order_days . " วัน ";
                    } else {
                        $item->rental_date = getDaysTimesBetweenDate($rental->pickup_date, $rental->return_date);
                    }
                }
                return $item;
            });
            $rental_bill = RentalBill::find($quotation->rental_bill_id);
            $listStatus = $this->getListStatus();
            $quotation_form = QuotationForm::where('quotation_id', $quotation->id)->orderBy('seq', 'asc')->get();
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
            // $approve_return = $approve_line_list->logApprove(Quotation::class, $quotation->id);
            // $approve_line_list = $approve_return['approve_line_list'];
            // $approve = $approve_return['approve'];
            $approve_line = HistoryTrait::getHistory(Quotation::class, $quotation->id);
            $approve_line_list = $approve_line['approve_line_list'];
            $approve = $approve_line['approve'];
            $approve_line_logs = $approve_line['approve_line_logs'];

            $page_title = __('quotations.page_title') . $quotation->qt_no . (isset($quotation->edit_count) ? 'Rev.' . strval(sprintf('%02d', $quotation->edit_count)) : null);
            return view('admin.quotations.form', [
                'd' => $quotation,
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
                'approve_line_logs' => $approve_line_logs,
            ]);
        }
    }


    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Quotation);
        $validator = Validator::make($request->all(), [
            'data.*.name' => [
                'required', 'max:255',
            ],
            'data.*.seq' => [
                'required', 'integer',
            ],
            'data.*.sub_quotation_form_checklist.*.quotation_form_checklist_name' => [
                'required', 'max:255',
            ],
            'data.*.sub_quotation_form_checklist.*.quotation_form_checklist_seq' => [
                'required', 'integer'
            ],

        ], [], [
            'data.*.name' => __('condition_quotations.condition_name'),
            'data.*.seq' => __('condition_quotations.condition_seq'),
            'data.*.sub_quotation_form_checklist.*.quotation_form_checklist_name' => __('condition_quotations.checklist_name'),
            'data.*.sub_quotation_form_checklist.*.quotation_form_checklist_seq' => __('condition_quotations.checklist_seq'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        if (!empty($request->remark_quotation)) {
            foreach ($request->remark_quotation as $index => $remark) {
                $lt_rental_line = LongTermRentalLine::find($index);
                $lt_rental_line->remark_quotation = $remark;
                $lt_rental_line->save();
            }
        }

        $quotation_id = $request->id;

        $quotation = Quotation::find($quotation_id);
        $quotation->check_vat = $request->check_vat ? $request->check_vat : STATUS_ACTIVE;
        $quotation->save();

        if ($request->del_section != null) {
            QuotationForm::where('quotation_id', $quotation_id)->whereIn('id', $request->del_section)->delete();
        }
        if (!empty($request->data)) {
            foreach ($request->data as $data) {
                if ($data['id'] != null) {
                    $quotation_form = QuotationForm::firstOrNew(['id' => $data['id']]);
                } else {
                    $quotation_form = new QuotationForm();
                }
                $quotation_form->name = $data['name'];
                $quotation_form->seq = $data['seq'];
                $quotation_form->quotation_id = $quotation_id;
                if ($data['quotation_form_status'] === 'true') {
                    $quotation_form->status = STATUS_ACTIVE;
                } else {
                    $quotation_form->status = STATUS_INACTIVE;
                }
                $quotation_form->save();
                if (isset($data['sub_quotation_form_checklist'])) {
                    if ($request->del_checklist != null) {
                        QuotationFormChecklist::whereIn('id', $request->del_checklist)->delete();
                    }
                    foreach ($data['sub_quotation_form_checklist'] as $index => $data) {
                        if ($data['id'] != null) {
                            $quotation_form_checklist = QuotationFormChecklist::firstOrNew(['id' => $data['id']]);
                        } else {
                            $quotation_form_checklist = new QuotationFormChecklist();
                        }
                        $quotation_form_checklist->name = $data['quotation_form_checklist_name'];
                        $quotation_form_checklist->seq = $data['quotation_form_checklist_seq'];

                        if ($data['quotation_form_checklist_status'] === 'true') {
                            $quotation_form_checklist->status = STATUS_ACTIVE;
                        } else {
                            $quotation_form_checklist->status = STATUS_INACTIVE;
                        }
                        $quotation_form_checklist->quotation_form_id = $quotation_form->id;
                        $quotation_form_checklist->save();
                    }
                }
            }
        }

        $redirect_route = route('admin.quotations.index');
        return $this->responseValidateSuccess($redirect_route);
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

    public function pdf(Request $request)
    {
        $quotation = Quotation::find($request->id);
        $pdfContent = null;
        if ((strcmp($request->type, 'payment') == 0) && (strcmp($quotation->reference_type, Rental::class) == 0)) {
            $pdfContent = $this->printShortTermRentalPayment($quotation);
        } else if (strcmp($quotation->reference_type, Rental::class) == 0) {
            $pdfContent = $this->printShortTermRental($quotation);
        }

        if ($pdfContent) {
            return  $pdfContent->stream();
        } else {
            return redirect()->back();
        }
    }

    public function printShortTermRental($quotation)
    {
        $pdfContent = null;
        if ($quotation) {
            $page_title = $quotation->qt_no;
            $reference_id = $quotation->reference_id;
            $reference_type = $quotation->reference_type;
            if (strcmp($reference_type, Rental::class) == 0) {
                $rental = Rental::find($reference_id);
                $origin_name = $rental->origin ? $rental->origin->name : $rental->origin_name;
                $destination_name = $rental->destination ? $rental->destination->name : $rental->destination_name;

                $rental_lines = RentalLine::select('rental_lines.*')->where('rental_id', $rental->id)->get();
                $rental_lines->map(function ($item) use ($rental) {
                    $product = Product::find($rental->product_id);
                    if ($product && strcmp($product->calculate_type, CalculateTypeEnum::DAILY) == 0) {
                        $pm = new ProductManagement($rental->service_type_id);
                        $pm->setDates($rental->pickup_date, $rental->return_date);
                        $item->rental_date = $pm->order_days . " วัน ";
                    } else {
                        $item->rental_date = getDaysTimesBetweenDate($rental->pickup_date, $rental->return_date);
                    }

                    if (strcmp($item->item_type, Product::class) == 0) {
                        $item->package_name = $product->name;
                        $item->product_name = ($item->car && $item->car->carClass) ? $item->car->carClass->full_name : $item->name;
                    } else {
                        $item->product_name = $item->name ? $item->name : null;
                    }
                    return $item;
                });

                $rental_service_type = ($rental->serviceType) ? $rental->serviceType->service_type : null;
                $service_type_name = ($rental->serviceType) ? $rental->serviceType->name : null;

                $quotation_form = QuotationForm::where('quotation_id', $quotation->id)->where('status', STATUS_ACTIVE)->orderBy('seq', 'asc')->get();
                $quotation_form_arr = $quotation_form->pluck('id')->toArray();
                $quotation_form_checklists = QuotationFormChecklist::whereIn('quotation_form_id', $quotation_form_arr)
                    ->where('status', STATUS_ACTIVE)
                    ->orderBy('seq', 'asc')
                    ->get();
                $quotation_form_checklists->map(function ($item) {
                    $item->quotation_form_checklist_name  = $item->name;
                    $item->quotation_form_checklist_id  = $item->id;
                    return $item;
                });
                $quotation_form->map(function ($item) use ($quotation_form_checklists) {
                    $quotation_form_checklist = $quotation_form_checklists->where('quotation_form_id', $item->id)->values();
                    $item->sub_quotation_form_checklist  = $quotation_form_checklist;
                    $item->quotation_form_id  = $item->id;
                    return $item;
                });

                $quotation->user_name = $quotation ? $quotation->createdBy?->name : '';
                $quotation->user_email = $quotation ? $quotation->createdBy?->email : '';
                $quotation->user_tel = $quotation ? $quotation->createdBy?->tel : '';
                $quotation->create_date = custom_date_format($quotation->created_at);
                $quotation->end_date = date('d/m/Y', strtotime($quotation->created_at . " +30 days"));

                $pdfContent = PDF::loadView(
                    'admin.quotations.short-term-rental-pdf.pdf',
                    [
                        'd' => $quotation,
                        'rental' => $rental,
                        'rental_lines' => $rental_lines,
                        'rental_service_type' => $rental_service_type,
                        'service_type_name' => $service_type_name,
                        'page_title' => $page_title,
                        'quotation_form' => $quotation_form,
                        'origin_name' => $origin_name,
                        'destination_name' => $destination_name,
                    ]
                );
            }
        }
        return  $pdfContent;
    }

    public function printShortTermRentalPayment($quotation)
    {
        $pdfContent = null;
        if ($quotation) {
            $rental_bill = RentalBill::find($quotation->rental_bill_id);
            if ($rental_bill) {
                $rental = Rental::find($rental_bill->rental_id);
                $om = new OrderManagement($rental);
                $om->calculate();
                $summary = $om->getSummary();
                $payment = $summary['total'];
                $amount = floatval($payment) * 100;
                $quotation->total = $payment;

                $code = getPaymentCode($quotation->ref_1, $quotation->ref_2, intval($amount));
                $qrcode = base64_encode(QrCode::format('svg')->size(80)->errorCorrection('H')->generate($code));

                $pdfContent = PDF::loadView(
                    'admin.quotations.short-term-rental-pdf.payment-pdf',
                    [
                        'd' => $quotation,
                        'rental' => $rental,
                        'rental_bill' => $rental_bill,
                        'qrcode' => $qrcode,
                        'code' => $code,
                    ]
                );
            }
        }
        return  $pdfContent;
    }

    public function printShortTermRentalPdf(Request $request)
    {
        if ($request->rental_bill_id) {
            $quotation = Quotation::where('rental_bill_id', $request->rental_bill_id)->first();
            $page_title = $quotation->qt_no;
            $reference_id = $quotation->reference_id;
            $reference_type = $quotation->reference_type;
            if (strcmp($reference_type, RentalBill::class) == 0) {
                $rental_bill = RentalBill::find($quotation->rental_bill_id);
                $rental = Rental::find($rental_bill->rental_id);
                $quotation->check_withholding_tax = $rental_bill->check_withholding_tax;
                $quotation->origin_name = $rental->origin ? $rental->origin->name : null;
                $quotation->destination_name = $rental->destination ? $rental->destination->name : null;
                $om = new OrderManagement($rental);
                $om->calculate();
                $summary = $om->getSummary();
                $quotation->subtotal = $summary['subtotal'];
                $quotation->vat = $summary['vat'];
                $quotation->total = $summary['total'];
                $quotation->withholding_tax = $summary['withholding_tax'];

                $rental_lines = RentalLine::select('rental_lines.*')->where('rental_id', $rental->id)->get();
                $rental_lines->map(function ($item) use ($rental) {
                    $product = Product::find($rental->product_id);
                    if ($product && strcmp($product->calculate_type, CalculateTypeEnum::DAILY) == 0) {
                        $pm = new ProductManagement($rental->service_type_id);
                        $pm->setDates($rental->pickup_date, $rental->return_date);
                        $item->rental_date = $pm->order_days . " วัน ";
                    } else {
                        $item->rental_date = getDaysTimesBetweenDate($rental->pickup_date, $rental->return_date);
                    }

                    if (strcmp($item->item_type, Product::class) == 0) {
                        $item->package_name = $product->name;
                        $item->product_name = ($item->car && $item->car->carClass) ? $item->car->carClass->full_name : $item->name;
                    } else {
                        $item->product_name = $item->name ? $item->name : null;
                    }
                    return $item;
                });

                $rental_service_type = ($rental->serviceType) ? $rental->serviceType->service_type : null;
                $service_type_name = ($rental->serviceType) ? $rental->serviceType->name : null;

                $quotation_form = QuotationForm::where('quotation_id', $quotation->id)->where('status', STATUS_ACTIVE)->orderBy('seq', 'asc')->get();
                $quotation_form_arr = $quotation_form->pluck('id')->toArray();
                $quotation_form_checklists = QuotationFormChecklist::whereIn('quotation_form_id', $quotation_form_arr)
                    ->where('status', STATUS_ACTIVE)
                    ->orderBy('seq', 'asc')
                    ->get();
                $quotation_form_checklists->map(function ($item) {
                    $item->quotation_form_checklist_name  = $item->name;
                    $item->quotation_form_checklist_id  = $item->id;
                    return $item;
                });
                $quotation_form->map(function ($item) use ($quotation_form_checklists) {
                    $quotation_form_checklist = $quotation_form_checklists->where('quotation_form_id', $item->id)->values();
                    $item->sub_quotation_form_checklist  = $quotation_form_checklist;
                    $item->quotation_form_id  = $item->id;
                    return $item;
                });

                $quotation->user_name = $quotation ? $quotation->createdBy?->name : '';
                $quotation->user_email = $quotation ? $quotation->createdBy?->email : '';
                $quotation->user_tel = $quotation ? $quotation->createdBy?->tel : '';
                $quotation->create_date = custom_date_format($quotation->created_at);
                $quotation->end_date = date('d/m/Y', strtotime($quotation->created_at . " +30 days"));

                $pdf = PDF::loadView(
                    'admin.quotations.short-term-rental-pdf.pdf',
                    [
                        'd' => $quotation,
                        'rental' => $rental,
                        'rental_lines' => $rental_lines,
                        'rental_bill' => $rental_bill,
                        'rental_service_type' => $rental_service_type,
                        'service_type_name' => $service_type_name,
                        'page_title' => $page_title,
                        'quotation_form' => $quotation_form,
                    ]
                );

                return  $pdf->stream();
            }
        } else {
            return redirect()->back();
        }
    }

    public function printShortTermRentalPaymentPdf(Request $request)
    {
        if ($request->rental_bill_id) {
            $rental_bill_id = $request->rental_bill_id;
            $quotation = Quotation::where('rental_bill_id', $rental_bill_id)->first();
            $rental_bill = RentalBill::find($request->rental_bill_id);
            if ($rental_bill) {
                $rental = Rental::find($rental_bill->rental_id);
                $om = new OrderManagement($rental);
                $om->calculate();
                $summary = $om->getSummary();
                $payment = $summary['total'];
                $amount = floatval($payment) * 100;
                $quotation->total = $payment;

                $code = getPaymentCode($quotation->ref_1, $quotation->ref_2, intval($amount));
                $qrcode = base64_encode(QrCode::format('svg')->size(80)->errorCorrection('H')->generate($code));

                $pdf = PDF::loadView(
                    'admin.quotations.short-term-rental-pdf.payment-pdf',
                    [
                        'd' => $quotation,
                        'rental' => $rental,
                        'rental_bill' => $rental_bill,
                        'qrcode' => $qrcode,
                        'code' => $code,
                    ]
                );
                return  $pdf->stream();
            }
        } else {
            return redirect()->back();
        }
    }

    public function printLongTermRentalPdf(Request $request)
    {
        if ($request->quotation) {
            $quotation_id = $request->quotation;
            $quotation = Quotation::find($quotation_id);
            $page_title = $quotation->qt_no;
            $reference_id = $quotation->reference_id;
            if (strcmp($quotation->reference_type, LongTermRental::class) == 0) {
                $lt_rental = LongTermRental::find($reference_id);
                if ($lt_rental) {
                    $lt_rental_lines = LongTermRentalLine::where('lt_rental_id', $lt_rental->id)
                        ->get();
                    $lt_rental_lines->map(function ($item) {
                        $item->car_class_text = ($item->carClass) ? $item->carClass->full_name : '';
                        $item->car_color_text = ($item->color) ? $item->color->name : '';
                        $item->remark_quotation = $item->remark_quotation;
                        return $item;
                    });

                    $lt_rental_line_accessory = LongTermRentalLineAccessory::whereIn('lt_rental_line_id', $lt_rental_lines->pluck('id'))->get();
                    $lt_rental_accessory = [];
                    $index = 0;
                    foreach ($lt_rental_lines as $car_index => $car_item) {
                        foreach ($lt_rental_line_accessory as $accessory_index => $accessory_item) {
                            if (strcmp($car_item->id, $accessory_item->lt_rental_line_id) == 0) {
                                $lt_rental_accessory[$index]['accessory_text'] = ($accessory_item->accessory) ? $accessory_item->accessory->name : '';
                                $lt_rental_accessory[$index]['car_index'] = $car_index;
                                $index++;
                            }
                        }
                    }

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

                    $quotation_form = QuotationForm::where('quotation_id', $quotation->id)->where('status', STATUS_ACTIVE)->orderBy('seq', 'asc')->get();
                    $list2 = $quotation_form->pluck('id')->toArray();
                    $quotation_form_checklists = QuotationFormChecklist::whereIn('quotation_form_id', $list2)->where('status', STATUS_ACTIVE)->orderBy('seq', 'asc')->get();
                    $quotation_form_checklists->map(function ($item) {
                        $item->quotation_form_checklist_name  = $item->name;
                        $item->quotation_form_checklist_id  = $item->id;
                        return $item;
                    });
                    $quotation_form->map(function ($item) use ($quotation_form_checklists) {
                        $quotation_form_checklist = $quotation_form_checklists->where('quotation_form_id', $item->id)->values();
                        $item->sub_quotation_form_checklist  = $quotation_form_checklist;
                        $item->quotation_form_id  = $item->id;
                        return $item;
                    });

                    $user = User::find($quotation->created_by);
                    $quotation->user_name = $user ? $user->name : null;
                    $quotation->user_tel = $user ? $user->tel : null;
                    $quotation->user_email = $user ? $user->email : null;

                    $pdf = PDF::loadView(
                        'admin.quotations.long-term-rental-pdf.pdf',
                        [
                            'd' => $quotation,
                            'page_title' => $page_title,
                            'lt_rental_lines' => $lt_rental_lines,
                            'lt_rental_accessory' => $lt_rental_accessory,
                            'lt_rental_line_month' => $lt_rental_line_month,
                            'lt_rental_month' => $lt_rental_month,
                            'quotation_form' => $quotation_form,
                            'lt_rental' => $lt_rental,
                        ]
                    );
                    return  $pdf->stream();
                } else {
                    return  redirect()->route('admin.quotations.index');
                }
            }
        }
    }

    public function updateQuotationStatus(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Quotation);
        $quotation_id = $request->quotations;
        $quotation = Quotation::find($quotation_id);
        $quotation->status = $request->quotation_status;
        if ($request->reject_reason) {
            $quotation->reject_reason = $request->reject_reason;
        }
        $quotation->save();

        if (strcmp($request->quotation_status, QuotationStatusEnum::CONFIRM) === 0 && strcmp($quotation->reference_type, LongTermRental::class) === 0) {
            $lt_rental = LongTermRental::find($quotation->reference_id);
            if (strcmp($lt_rental->approval_type, LongTermRentalApprovalTypeEnum::AFFILIATED) === 0) {
                $step_approve_management = new StepApproveManagement();
                $step_approve_management->createModelApproval(ConfigApproveTypeEnum::LT_QUOTATION_AFFILIATED, Quotation::class, $quotation_id);
            } else if (strcmp($lt_rental->approval_type, LongTermRentalApprovalTypeEnum::UNAFFILIATED) === 0) {
                $step_approve_management = new StepApproveManagement();
                $step_approve_management->createModelApproval(ConfigApproveTypeEnum::LT_QUOTATION_UNAFFILIATED, Quotation::class, $quotation_id);
            }
        }

        if (strcmp($request->quotation_status, QuotationStatusEnum::REJECT) === 0 && strcmp($quotation->reference_type, LongTermRental::class) === 0) {
            $lt_rental = LongTermRental::findOrFail($quotation->reference_id);
            $lt_rental->rental_price_status = LongTermRentalPriceStatusEnum::REJECT;
            $lt_rental->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'ok',
            'redirect' => ($request->redirect_route) ? $request->redirect_route : route('admin.quotations.index')
        ]);
    }

    public function getListStatus()
    {
        return collect([
            [
                'id' => 'active',
                'value' => STATUS_ACTIVE,
                'name' => __('quotations.vat_' . STATUS_ACTIVE),
            ],
            [
                'id' => 'inactive',
                'value' => STATUS_DEFAULT,
                'name' => __('quotations.vat_' . STATUS_DEFAULT),
            ],
        ]);
    }
}
