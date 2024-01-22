<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Invoice;
use App\Traits\InvoiceTrait;
use Illuminate\Http\Request;

class InvoiceOtherController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::OtherInvoice);
        $invoice_id = $request->invoice_id;
        $invoice_no = null;
        if ($invoice_id) {
            $invoice = Invoice::find($invoice_id);
            $invoice_no = $invoice?->invoice_no;
        }
        $customer_id = $request->customer_id;
        $customer_text = null;
        $invoice_type =  $request->invoice_type;
        $invoice_type_list = null;
        $branch_id = $request->branch_id;
        $list = Invoice::select('*')
            ->orderBy('created_at', 'desc')
            ->paginate(PER_PAGE);
        $branch_list = InvoiceTrait::getBranchList();
        return view('admin.invoice-others.index', [
            'list' => $list,
            'invoice_id' => $invoice_id,
            'invoice_no' => $invoice_no,
            'customer_id' => $customer_id,
            'customer_text' => $customer_text,
            'branch_list' => $branch_list,
            'branch_id' => $branch_id,
            'invoice_type' => $invoice_type,
            'invoice_type_list' => $invoice_type_list,
        ]);
    }
}
