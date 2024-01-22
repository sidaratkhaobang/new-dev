<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Enums\SAPAccountTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\SAPInterface;
use App\Models\SAPInterfaceLine;
use App\Traits\SAPInterfaceTrait;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class IncomeAccountController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::SAPInterfaceAR);
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $transfer_type = $request->transfer_type;
        $transfer_sub_type = $request->transfer_sub_type;
        $doc_type_id = $request->doc_type_id;
        $status = $request->status;
        $list = SAPInterface::leftjoin('sap_interface_lines', 'sap_interface_lines.sap_interface_id', '=', 'sap_interfaces.id')
            ->when($doc_type_id, function ($query) use ($doc_type_id) {
                return $query->where('sap_interface_lines.document_type', $doc_type_id);
            })
            ->when($from_date, function ($query) use ($from_date) {
                return $query->where('sap_interface_lines.document_date', '>=', $from_date);
            })
            ->when($to_date, function ($query) use ($to_date) {
                return $query->where('sap_interface_lines.document_date', '<=', $to_date);
            })
            ->select('sap_interfaces.*')
            ->search(null, $request)
            ->where('sap_interfaces.account_type', SAPAccountTypeEnum::AR)
            ->orderBy('sap_interfaces.created_at', 'DESC')
            ->distinct('sap_interfaces.id')
            ->paginate(PER_PAGE);

        $transfer_type_list = SAPInterfaceTrait::getSAPTransferARTypeList();
        $transfer_sub_type_list = SAPInterfaceTrait::getSAPTransferARSubTypeList();
        $document_type_list = SAPInterfaceTrait::getARDocumentTypeTypeList();
        $status_list = SAPInterfaceTrait::getSAPStatusList();
        return view('admin.income-accounts.index', [
            'list' => $list,
            's' => $request->s,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'transfer_type_list' => $transfer_type_list,
            'transfer_type' => $transfer_type,
            'transfer_sub_type_list' => $transfer_sub_type_list,
            'transfer_sub_type' => $transfer_sub_type,
            'doc_type_id' => $doc_type_id,
            'status' => $status,
            'document_type_list' => $document_type_list,
            'status_list' => $status_list
        ]);
    }

    public function show(SAPInterface $income_account)
    {
        $this->authorize(Actions::View . '_' . Resources::SAPInterfaceAR);
        $sap_interface_lines = SAPInterfaceLine::where('sap_interface_id', $income_account->id)->orderBy('flag')->get();
        $page_title = __('lang.view') . __('sap_interfaces.income_account');
        return view('admin.income-accounts.form', [
            'd' => $income_account,
            'view' => true,
            'page_title' => $page_title,
            'sap_interface_lines' => $sap_interface_lines,
        ]);
    }

    public function getIncomeList(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $transfer_type = $request->transfer_type;
        $transfer_sub_type = $request->transfer_sub_type;
        $doc_type_id = $request->doc_type_id;
        $status = $request->status;
        $list = SAPInterface::leftjoin('sap_interface_lines', 'sap_interface_lines.sap_interface_id', '=', 'sap_interfaces.id')
            ->when($doc_type_id, function ($query) use ($doc_type_id) {
                return $query->where('sap_interface_lines.document_type', $doc_type_id);
            })
            ->when($from_date, function ($query) use ($from_date) {
                return $query->where('sap_interface_lines.document_date', '>=', $from_date);
            })
            ->when($to_date, function ($query) use ($to_date) {
                return $query->where('sap_interface_lines.document_date', '<=', $to_date);
            })
            ->select('sap_interfaces.*')
            ->search(null, $request)
            ->where('sap_interfaces.account_type', SAPAccountTypeEnum::AR)
            ->orderBy('sap_interfaces.created_at', 'DESC')
            ->distinct('sap_interfaces.id')
            ->get()
            ->map(function ($item) {
                $item->transfer_type = __('sap_interfaces.transfer_type_' . $item->transfer_type);
                $item->transfer_sub_type = __('sap_interfaces.transfer_sub_type_' . $item->transfer_sub_type);
                $item->created_date = $item->created_at ? get_thai_date_format($item->created_at, 'd/m/Y') : null;
                $item->status = $item->status ? __('sap_interfaces.status_' . $item->status) : null;
                return $item;

            });


        return response()->json($list);
    }

    public function exportSAPInterface(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::SapInterface);
        $sap_interface_ids = $request->sap_interface_ids;
        $lines = SAPInterfaceLine::select('*')
            ->whereIn('sap_interface_id', $sap_interface_ids)
            ->orderBy('sap_interface_id')
            ->get();
        if (count($lines) > 0) {
            return (new FastExcel($lines))->download('file.xlsx', function ($line) {
                return [
                    'FLAG' => ((!empty($line->flag)) ? '*' : ''),
                    'Posting Date' => date('dmY', strtotime($line->posting_date)),
                    'Document Date' => date('dmY', strtotime($line->document_date)),
                    'Document Type' => $line->document_type,
                    'Company Code' => $line->company_code,
                    'Branch Number' => $line->branch_number,
                    'Currency' => $line->currency,
                    'Currency Rate' => ' ',
                    'Translation Date' => ' ',
                    'Reference Document' => $line->reference_document,
                    'Header Text' => $line->header_text,
                    'Posting Key' => $line->posting_key,
                    'Special Ind.' => ' ',
                    'Account No' => $line->account_no,
                    'Amount in document' => number_format($line->amount_in_document, 2, '.', ''),
                    'Amount in local currency' => number_format($line->amount_in_document, 2, '.', ''),
                    'Cost Center' => $line->cost_center,
                    'Fund code' => ' ',
                    'Cal Tax' => ' ',
                    'Base amount' => !empty($line?->base_amount) ? number_format($line->base_amount, 2, '.', '') : null,
                    'Tax code' => $line->tax_code,
                    'Assignment' => $line->assignment,
                    'Line item text' => $line->text,
                ];
            });
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }
}