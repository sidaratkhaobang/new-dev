<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\CreditNoteStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\CreditNote;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;

class CreditNoteController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CreditNote);
        
        $customer_id = $request->customer_id;
        $status = $request->status;
        $invoice_id = $request->invoice_id;
        $invoice_no = null;
        if ($invoice_id) {
            $invoice = Invoice::find($invoice_id);
            $invoice_no = $invoice ? $invoice->invoice_no : null;
        }
        $credit_note_id = $request->credit_note_id;
        $credit_note_no = null;
        if ($credit_note_id) {
            $credit_note = CreditNote::find($credit_note_id);
            $credit_note_no = $credit_note ? $credit_note->credit_note_no : null;
        }

        $customer_id = $request->customer_id;
        $customer_name = null;
        if ($customer_id) {
            $customer = Customer::find($customer_id);
            $customer_name = $customer ? $customer->name : null;
        }

        $list = CreditNote::search($request->s, $request)
            ->paginate(PER_PAGE);
        
        $status_list = $this->getStatusList();
        return view('admin.credit-notes.index', [
            'list' => $list,
            's' => $request->s,
            'status_list' => $status_list,
            'status' => $status,
            'invoice_id' => $invoice_id,
            'invoice_no' => $invoice_no,
            'credit_note_id' => $credit_note_id,
            'credit_note_no' => $credit_note_no,
            'customer_id' => $customer_id,
            'customer_name' => $customer_name,
        ]);
    }

    public static function getStatusList()
    {
        return collect([
            (object) [
                'id' => CreditNoteStatusEnum::IN_PROCESS,
                'name' => __('litigations.status_' . CreditNoteStatusEnum::IN_PROCESS),
                'value' => CreditNoteStatusEnum::IN_PROCESS,
            ],
            (object) [
                'id' => CreditNoteStatusEnum::COMPLETE,
                'name' => __('litigations.status_' . CreditNoteStatusEnum::COMPLETE),
                'value' => CreditNoteStatusEnum::COMPLETE,
            ],
        ]);
    }
}
