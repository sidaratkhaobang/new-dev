<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Traits\InvoiceTrait;
use Illuminate\Http\Request;

class InvoiceLTRentalController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::LongTermRentalInvoice);
        $invoice_id = $request->invoice_id;
        $invoice_no = null;
        if ($invoice_id) {
            $invoice = Invoice::find($invoice_id);
            $invoice_no = $invoice?->invoice_no;
        }
        $customer_id = $request->customer_id;
        $car_id = $request->car_id;
        $license_plate = null;
        $from_contract_start_date = $request->from_contract_start_date;
        $to_contract_start_date = $request->to_contract_start_date;
        $from_contract_end_date = $request->from_contract_end_date;
        $to_contract_end_date = $request->to_contract_end_date;

        $status = $request->status;
        $list = Invoice::select('*')
            ->orderBy('created_at', 'desc')
            ->paginate(PER_PAGE);
        $status_list = InvoiceTrait::getStatusList();
        return view('admin.invoice-lt-rentals.index', [
            'list' => $list,
            'status_list' => $status_list,
            'status' => $status,
            'invoice_id' => $invoice_id,
            'invoice_no' => $invoice_no,
            'customer_id' => $customer_id,
            'car_id' => $car_id,
            'license_plate' => $license_plate,
            'from_contract_start_date' => $from_contract_start_date,
            'to_contract_start_date' => $to_contract_start_date,
            'from_contract_end_date' => $from_contract_end_date,
            'to_contract_end_date' => $to_contract_end_date,
        ]);
    }
}
