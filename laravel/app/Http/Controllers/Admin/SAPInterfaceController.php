<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SAPInterface;
use App\Models\SAPInterfaceLine;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Enums\SAPAccountTypeEnum;
use App\Enums\SAPInterfaceLineTypeEnum;
use App\Enums\SAPTransferSubTypeEnum;
use App\Enums\SAPTransferTypeEnum;

class SAPInterfaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::SapInterface);
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $account_type_id = $request->account_type;
        $transfer_type_id = $request->transfer_type;
        $transfer_sub_type_id = $request->transfer_sub_type;
        $list = SAPInterface::select('*')
            ->when($account_type_id, function ($query) use ($account_type_id) {
                return $query->where('account_type', $account_type_id);
            })
            ->when($transfer_type_id, function ($query) use ($transfer_type_id) {
                return $query->where('transfer_type', $transfer_type_id);
            })
            ->when($transfer_sub_type_id, function ($query) use ($transfer_sub_type_id) {
                return $query->where('transfer_sub_type', $transfer_sub_type_id);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(PER_PAGE);

        $account_list = $this->getSAPAccountList();
        $transfer_type_list = $this->getSAPTransferTypeList();
        $transfer_sub_type_list = $this->getSAPTransferSubTypeList();
        return view('admin.sap-interfaces.index', [
            'list' => $list,
            's' => $request->s,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'account_list' => $account_list,
            'account_type_id' => $account_type_id,
            'transfer_type_list' => $transfer_type_list,
            'transfer_type_id' => $transfer_type_id,
            'transfer_sub_type_list' => $transfer_sub_type_list,
            'transfer_sub_type_id' => $transfer_sub_type_id
        ]);
    }

    public function exportSAPInterface(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::SapInterface);
        // dd($request->all());
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $lines = SAPInterfaceLine::select('*')
            ->when($from_date, function ($query) use ($from_date) {
                return $query->whereDate('document_date', '>=', $from_date);
            })
            ->when($to_date, function ($query) use ($to_date) {
                return $query->whereDate('document_date', '<=', $to_date);
            })
            ->orderBy('sap_interface_id')
            ->get();
            // dd($lines);
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
                    'Base amount' => !empty($line?->base_amount)?number_format($line->base_amount, 2, '.', ''):null,
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

    public function show(SAPInterface $sap_interface)
    {
        $this->authorize(Actions::View . '_' . Resources::SapInterface);
        $sap_interface_lines = SAPInterfaceLine::where('sap_interface_id', $sap_interface->id)->orderBy('flag')->get();
        $page_title = __('lang.view') . __('sap_interfaces.page_title');
        return view('admin.sap-interfaces.form', [
            'd' => $sap_interface,
            'view' => true,
            'page_title' => $page_title,
            'sap_interface_lines' => $sap_interface_lines,
        ]);
    }

    public function getSAPAccountList()
    {
        return collect([
            (object) [
                'id' => SAPAccountTypeEnum::AR,
                'name' => SAPAccountTypeEnum::AR,
                'value' => SAPAccountTypeEnum::AR,
            ],
            (object) [
                'id' => SAPAccountTypeEnum::AP,
                'name' => SAPAccountTypeEnum::AP,
                'value' => SAPAccountTypeEnum::AP,
            ],
        ]);
    }

    public function getSAPTransferTypeList()
    {
        return collect([
            (object) [
                'id' => SAPTransferTypeEnum::CASH_SALE_S_RENTAL,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::CASH_SALE_S_RENTAL),
                'value' => SAPTransferTypeEnum::CASH_SALE_S_RENTAL,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::CASH_SALE_COUPON,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::CASH_SALE_COUPON),
                'value' => SAPTransferTypeEnum::CASH_SALE_COUPON,
            ],
        ]);
    }
    public function getSAPTransferSubTypeList()
    {
        return collect([
            (object) [
                'id' => SAPTransferSubTypeEnum::AFTER_PAYMENT,
                'name' => __('sap_interfaces.transfer_sub_type_' . SAPTransferSubTypeEnum::AFTER_PAYMENT),
                'value' => SAPTransferSubTypeEnum::AFTER_PAYMENT,
            ],
            (object) [
                'id' => SAPTransferSubTypeEnum::START_SERVICE,
                'name' => __('sap_interfaces.transfer_sub_type_' . SAPTransferSubTypeEnum::START_SERVICE),
                'value' => SAPTransferSubTypeEnum::START_SERVICE,
            ],
            (object) [
                'id' => SAPTransferSubTypeEnum::AFTER_SERVICE_INFORM,
                'name' => __('sap_interfaces.transfer_sub_type_' . SAPTransferSubTypeEnum::AFTER_SERVICE_INFORM),
                'value' => SAPTransferSubTypeEnum::AFTER_SERVICE_INFORM,
            ],
            (object) [
                'id' => SAPTransferSubTypeEnum::AFTER_SERVICE_PAID,
                'name' => __('sap_interfaces.transfer_sub_type_' . SAPTransferSubTypeEnum::AFTER_SERVICE_PAID),
                'value' => SAPTransferSubTypeEnum::AFTER_SERVICE_PAID,
            ],
            (object) [
                'id' => SAPTransferSubTypeEnum::PAYMENT_FEE,
                'name' => __('sap_interfaces.transfer_sub_type_' . SAPTransferSubTypeEnum::PAYMENT_FEE),
                'value' => SAPTransferSubTypeEnum::PAYMENT_FEE,
            ],
            (object) [
                'id' => SAPTransferSubTypeEnum::EXPIRED_COUPON,
                'name' => __('sap_interfaces.transfer_sub_type_' . SAPTransferSubTypeEnum::EXPIRED_COUPON),
                'value' => SAPTransferSubTypeEnum::EXPIRED_COUPON,
            ],
        ]);
    }
}
