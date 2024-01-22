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

class ExpenseAccountController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::SapInterfaceAP);
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
            ->where('sap_interfaces.account_type', SAPAccountTypeEnum::AP)
            ->orderBy('sap_interfaces.created_at', 'DESC')
            ->paginate(PER_PAGE);

        $transfer_type_list = SAPInterfaceTrait::getSAPTransferAPTypeList();
        $transfer_sub_type_list = SAPInterfaceTrait::getSAPTransferAPSubTypeList();
        $document_type_list = SAPInterfaceTrait::getAPDocumentTypeTypeList();
        $status_list = SAPInterfaceTrait::getSAPStatusList();
        return view('admin.expense-accounts.index', [
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

    public function show(SAPInterface $expense_account)
    {
        $this->authorize(Actions::View . '_' . Resources::SapInterfaceAP);
        return redirect()->route('admin.expense-accounts.index');
        //pending
        // $sap_interface_lines = SAPInterfaceLine::where('sap_interface_id', $expense_account->id)->orderBy('flag')->get();
        // $page_title = __('lang.view') . __('sap_interfaces.expense_account');
        // return view('admin.income-accounts.form', [
        //     'd' => $expense_account,
        //     'view' => true,
        //     'page_title' => $page_title,
        //     'sap_interface_lines' => $sap_interface_lines,
        // ]);
    }
}
