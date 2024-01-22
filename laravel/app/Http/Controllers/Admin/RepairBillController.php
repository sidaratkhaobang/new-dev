<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\RepairBillStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\BillSlip;
use App\Models\BillSlipLine;
use App\Models\Creditor;
use App\Models\Rental;
use App\Traits\RepairTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RepairBillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::RepairBill);
        $page_title = __('repair_bills.page_title');
        $bill_no = $request->bill_no;
        $bill_recipient = $request->bill_recipient;
        $center = $request->center;
        $department = $request->department;
        $billing_date = $request->billing_date;
        $status = $request->status;
        $bill_no_name = BillSlip::where('id', $bill_no)->first()?->worksheet_no;
        $bill_recipient_name = RepairTrait::getBillRecipientName($bill_recipient);
        $center_name = RepairTrait::getCreditorName($center);
        $department_name = RepairTrait::getGeographieName($department);
        $status_list = $this->getRepailBillStatus();
        $list = $this->getBillSlipData($bill_no, $bill_recipient, $center, $department, $billing_date, $status);
        return view('admin.repair-bills.index', [
            'page_title' => $page_title,
            'bill_no' => $bill_no,
            'bill_no_name' => $bill_no_name,
            'bill_recipient' => $bill_recipient,
            'bill_recipient_name' => $bill_recipient_name,
            'center' => $center,
            'center_name' => $center_name,
            'department' => $department,
            'department_name' => $department_name,
            'billing_date' => $billing_date,
            'status' => $status,
            'status_list' => $status_list,
            'list' => $list,
        ]);
    }

    public function getRepailBillStatus()
    {
        return collect([
            (object)[
                'id' => RepairBillStatusEnum::PENDING,
                'name' => __('repair_bills.status_' . RepairBillStatusEnum::PENDING),
                'value' => RepairBillStatusEnum::PENDING,
            ],
            (object)[
                'id' => RepairBillStatusEnum::SUCCESS,
                'name' => __('repair_bills.status_' . RepairBillStatusEnum::SUCCESS),
                'value' => RepairBillStatusEnum::SUCCESS,
            ],
        ]);
    }

    public function getBillSlipData($bill_no, $bill_recipient, $center, $department, $billing_date, $status)
    {
        $bill_slip_data = BillSlip::when(!empty($bill_no), function ($query_search) use ($bill_no) {
            $query_search->where('id', $bill_no);
        })
            ->when(!empty($bill_recipient), function ($query_search) use ($bill_recipient) {
                $query_search->where('bill_recipient', $bill_recipient);
            })
            ->when(!empty($center), function ($query_search) use ($center) {
                $query_search->where('center_id', $center);
            })
            ->when(!empty($billing_date), function ($query_search) use ($billing_date) {
                $query_search->where('billing_date', $billing_date);
            })
            ->when(!empty($status), function ($query_search) use ($status) {
                $query_search->where('status', $status);
            })
            ->when(!empty($department), function ($query_search) use ($department) {
                $query_search->whereHas('creditor.province', function ($query) use ($department) {
                    $query->where('geography_id', $department);
                });
            })
            ->paginate(PER_PAGE);
        $bill_slip_data->getCollection()->transform(function ($item) use ($department) {
            $geographie_data = DB::table('geographies')
                ->where('id', $item?->creditor?->province?->geography_id)
                ->first();
            $item->geographie_name = $geographie_data?->name;
            return $item;
        });
        return $bill_slip_data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::RepairBill);
        $page_title = __('lang.create') . __('repair_bills.page_create_title');
        $d = new BillSlip();
        $d->createdBy = Auth::user();
        $d->created_at = date('Y-m-d H:i:s');
        $center_name = null;
        $bill_recipient_name = null;
        return view('admin.repair-bills.form', [
            'page_title' => $page_title,
            'd' => $d,
            'center_name' => $center_name,
            'bill_recipient_name' => $bill_recipient_name,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::RepairBill);
        $validator = Validator::make($request->all(), [
            'center_id' => ['required'],
            'bill_recipient' => ['required'],
            'billing_date' => ['required'],
            'receive_money_date' => ['required'],
            'repair_bill_data' => ['required']
        ], [], [
            'center_id' => __('repair_bills.search_center'),
            'bill_recipient' => __('repair_bills.search_bill_recipient'),
            'billing_date' => __('repair_bills.search_billing_date'),
            'receive_money_date' => __('repair_bills.receive_money_date'),
            'repair_bill_data' => __('repair_bills.repair_bill_data'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        //        Validate Car Data
        $validator = Validator::make($request->repair_bill_data, [
            '*.worksheet_no' => ['required'],
            '*.total_document' => ['required'],
            '*.repair_bill_price' => ['required'],
        ], [], [
            '*.total_document' => __('repair_bills.search_bill_no'),
            '*.total_document' => __('repair_bills.total_document'),
            '*.repair_bill_price' => __('repair_bills.repair_bill_price'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $bill_slip = new BillSlip();
        $bill_slip->worksheet_no = generate_worksheet_no(BillSlip::class, false);
        $bill_slip->billing_slip_old_id = $request->bill_slip_id;
        $bill_slip->center_id = $request->center_id;
        $bill_slip->bill_recipient = $request->bill_recipient;
        $bill_slip->billing_date = $request->billing_date;
        $bill_slip->receive_money_date = $request->receive_money_date;
        $bill_slip->status = RepairBillStatusEnum::SUCCESS;
        $bill_slip->save();
        if (!empty($bill_slip->id)) {
            if (!empty($request->repair_bill_data)) {
                foreach ($request->repair_bill_data as $key_bill_data => $value_bill_data) {
                    $bill_slip_line = new BillSlipLine();
                    $bill_slip_line->billing_slip_id = $bill_slip->id;
                    $bill_slip_line->billing_slip_no = $value_bill_data['worksheet_no'];
                    $bill_slip_line->amount_document = $value_bill_data['total_document'];
                    $bill_slip_line->amount = $value_bill_data['repair_bill_price'];
                    $bill_slip_line->remark = $value_bill_data['remark'];
                    $bill_slip_line->save();
                }
            }
        }
        $redirect_route = route('admin.repair-bills.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(BillSlip $repair_bill)
    {
        $this->authorize(Actions::View . '_' . Resources::RepairBill);
        $page_title = __('lang.view') . __('repair_bills.bill');
        $d = $repair_bill;
        $center_name = RepairTrait::getCreditorName($d?->center_id);
        $bill_recipient_name = RepairTrait::getBillRecipientName($d?->bill_recipient);
        $bill_slip_line_data = BillSlipLine::where('billing_slip_id', $d->id)
            ->get();
        $view = true;
        return view('admin.repair-bills.form', [
            'page_title' => $page_title,
            'd' => $d,
            'center_name' => $center_name,
            'bill_recipient_name' => $bill_recipient_name,
            'bill_slip_line_data' => $bill_slip_line_data,
            'view' => $view,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(BillSlip $repair_bill)
    {
        $this->authorize(Actions::Manage . '_' . Resources::RepairBill);
        $page_title = __('lang.edit') . __('repair_bills.bill');
        $d = $repair_bill;
        $center_name = RepairTrait::getCreditorName($d?->center_id);
        $bill_recipient_name = RepairTrait::getBillRecipientName($d?->bill_recipient);
        $bill_slip_line_data = BillSlipLine::where('billing_slip_id', $d->id)->get();
        return view('admin.repair-bills.form', [
            'page_title' => $page_title,
            'd' => $d,
            'center_name' => $center_name,
            'bill_recipient_name' => $bill_recipient_name,
            'bill_slip_line_data' => $bill_slip_line_data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function printPdf(BillSlip $repair_bill_id)
    {

        $list = $repair_bill_id?->bill_slip_line;
        $total_bill_price = $list->sum('amount');
        $creditor = Creditor::find($repair_bill_id?->center_id);
        $repair_bill_id_old = $this->getWorksheesNo($repair_bill_id->billing_slip_old_id);
        //        return view('admin.repair-bills.pdf.pdf',
        //            [
        //                'd' => $repair_bill_id,
        //                'list' => $list,
        //                'total_bill_price' => $total_bill_price,
        //                'creditor' => $creditor,
        //                'repair_bill_id_old' => $repair_bill_id_old
        //            ]);
        $pdf = PDF::loadView(
            'admin.repair-bills.pdf.pdf',
            [
                'd' => $repair_bill_id,
                'list' => $list,
                'total_bill_price' => $total_bill_price,
                'creditor' => $creditor,
                'repair_bill_id_old' => $repair_bill_id_old
            ]
        );
        return $pdf->stream();
    }

    public function getWorksheesNo($id)
    {
        if (empty($id)) {
            return [];
        }

        $bill_slip_data = BillSlip::find($id);
        return $bill_slip_data;
    }
}
