<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\RequestReceiptStatusEnum;
use App\Enums\RequestReceiptTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Models\RequestReceipt;
use App\Models\RequestReceiptLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class RequestReceiptController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::RequestReceipt);
        $type = $request->type;
        $status = $request->status;
        $lists = RequestReceipt::sortable(['created_at' => 'desc'])->search($request)->paginate(PER_PAGE);
        $status_lists = $this->getStatusList();
        $type_lists = $this->getTypeList();
        $page_title = __('request_receipts.page_title');
        return view('admin.request-receipts.index', [
            'lists' => $lists,
            'type' => $type,
            'status' => $status,
            'page_title' => $page_title,
            'status_lists' => $status_lists,
            'type_lists' => $type_lists,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::RequestReceipt);
        $d = new RequestReceipt();
        $d->is_select_db_customer = true;
        $type_list = $this->getTypeList();
        $page_title = __('lang.add') . __('request_receipts.page_title');
        return view('admin.request-receipts.form', [
            'd' => $d,
            'page_title' => $page_title,
            'optional_files' => [],
            'province_name' => null,
            'amphure_name' => null,
            'district_name' => null,
            'customer_zipcode' => null,
            'type_list' => $type_list,
        ]);
    }

    public function edit(RequestReceipt $request_receipt)
    {
        $this->authorize(Actions::Manage . '_' . Resources::RequestReceipt);
        $province_name = $request_receipt->province?->name_th;
        $amphure_name =  $request_receipt->district?->name_th;
        $district_name =  $request_receipt->subDistrict?->name_th;
        $customer_zipcode =  $request_receipt->subDistrict?->zip_code;
        $request_receipt->customer_text = $request_receipt?->customer?->name;
        $optional_files = get_medias_detail($request_receipt->getMedia('optional_files'));
        $request_receipt_list = $this->getRequestReceiptLineList($request_receipt->id);
        $type_list = $this->getTypeList();
        $page_title = __('lang.edit') . __('request_receipts.page_title');
        return view('admin.request-receipts.form', [
            'd' => $request_receipt,
            'page_title' => $page_title,
            'optional_files' => $optional_files,
            'province_name' => $province_name,
            'amphure_name' => $amphure_name,
            'district_name' => $district_name,
            'customer_zipcode' => $customer_zipcode,
            'request_receipt_list' => $request_receipt_list,
            'type_list' => $type_list,
        ]);
    }

    public function show(RequestReceipt $request_receipt)
    {
        $this->authorize(Actions::View . '_' . Resources::RequestReceipt);
        $province_name = $request_receipt->province?->name_th;
        $amphure_name =  $request_receipt->district?->name_th;
        $district_name =  $request_receipt->subDistrict?->name_th;
        $customer_zipcode =  $request_receipt->subDistrict?->zip_code;
        $request_receipt->customer_text = $request_receipt?->customer?->name;
        $optional_files = get_medias_detail($request_receipt->getMedia('optional_files'));
        $request_receipt_list = $this->getRequestReceiptLineList($request_receipt->id);
        $type_list = $this->getTypeList();
        $page_title = __('lang.view') . __('request_receipts.page_title');
        return view('admin.request-receipts.form', [
            'd' => $request_receipt,
            'page_title' => $page_title,
            'optional_files' => $optional_files,
            'province_name' => $province_name,
            'amphure_name' => $amphure_name,
            'district_name' => $district_name,
            'customer_zipcode' => $customer_zipcode,
            'request_receipt_list' => $request_receipt_list,
            'type_list' => $type_list,
            'view' => true,
        ]);
    }

    public function getRequestReceiptLineList($request_receipt_id)
    {
        $request_receipt_list = RequestReceiptLine::where('request_receipt_id', $request_receipt_id)
            ->whereNull('deleted_at')->get();
        return $request_receipt_list;
    }

    public function store(Request $request)
    {
        if (!boolval($request->is_draft)) {
            $validator = Validator::make($request->all(), [
                'type' => [
                    'required',
                ],
                'title' => [
                    'required',
                ],
                'customer_id' => [
                    Rule::when(isset($request->is_select_db_customer[0]), ['required'])
                ],
                'customer_name' => [
                    Rule::when(!isset($request->is_select_db_customer[0]), ['required'])
                ],
                'customer_tax_no' => [
                    'required',
                ],
                'customer_address' => [
                    'required',
                ],


            ], [], [
                'type' => __('request_receipts.type'),
                'title' => __('request_receipts.title'),
                'is_select_db_customer[]' => __('request_receipts.is_select_db_customer'),
                'customer_name' => __('request_receipts.customer_name'),
                'customer_tax_no' => __('request_receipts.customer_tax_no'),
                'customer_id' => __('request_receipts.customer'),
                'customer_address' => __('request_receipts.customer_address'),

            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
            if (empty($request->request_receipt_data)) {
                return $this->responseWithCode(true, __('request_receipts.validate_list'), null, 422);
            }
        }

        $request_receipt = RequestReceipt::firstOrNew(['id' => $request->id]);
        if (!($request_receipt->exists)) {
            $request_receipt->worksheet_no = generate_worksheet_no(RequestReceipt::class, true);
            $request_receipt->status = RequestReceiptStatusEnum::DRAFT;
        }
        $request_receipt->type = $request->type;
        $request_receipt->branch_id = Auth::user()->branch?->id;
        $request_receipt->title = $request->title;
        $request_receipt->detail = $request->detail;
        $request_receipt->is_select_db_customer = isset($request->is_select_db_customer[0]) ? filter_var($request->is_select_db_customer[0], FILTER_VALIDATE_BOOLEAN) : false;
        if (isset($request->is_select_db_customer[0])) {
            $request_receipt->customer_id = $request->customer_id;
        } else {
            $request_receipt->customer_name = $request->customer_name;
        }
        $request_receipt->customer_tax_no = $request->customer_tax_no;
        $request_receipt->customer_address = $request->customer_address;
        $request_receipt->customer_province_id = $request->customer_province_id;
        $request_receipt->customer_district_id = $request->customer_district_id;
        $request_receipt->customer_subdistrict_id = $request->customer_subdistrict_id;
        if (isset($request->status)) {
            $request_receipt->status = $request->status;
        }
        $request_receipt->save();

        if ($request->optional_files__pending_delete_ids) {
            $pending_delete_ids = $request->optional_files__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $request_receipt->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('optional_files')) {
            foreach ($request->file('optional_files') as $file) {
                if ($file->isValid()) {
                    $request_receipt->addMedia($file)->toMediaCollection('optional_files');
                }
            }
        }

        if ($request_receipt->id) {
            $this->saveRequestReceiptLine($request, $request_receipt->id);
        }

        $redirect_route = route('admin.request-receipts.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    private function saveRequestReceiptLine($request, $request_receipt_id)
    {
        $request_receipt = RequestReceipt::find($request_receipt_id);
        $id_arr = [];
        if (!empty($request->request_receipt_data)) {
            foreach ($request->request_receipt_data as $index => $data) {
                $request_receipt_line = RequestReceiptLine::firstOrNew(['id' => $data['id']]);
                $request_receipt_line->request_receipt_id = $request_receipt->id;
                $request_receipt_line->list_name = $data['list_name'];
                $amount = $data['amount'] ? str_replace(',', '', $data['amount']) : null;
                $request_receipt_line->amount = $amount ? $amount : null;
                $fee_deducted = $data['fee_deducted'] ? str_replace(',', '', $data['fee_deducted']) : null;
                $request_receipt_line->fee_deducted = $fee_deducted ? $fee_deducted : null;
                $total = $data['total'] ? str_replace(',', '', $data['total']) : null;
                $request_receipt_line->total = $total ? $total : null;
                $request_receipt_line->save();
                array_push($id_arr, $request_receipt_line->id);
            }
            RequestReceiptLine::where('request_receipt_id', $request_receipt_id)->whereNotIn('id', $id_arr)->delete();
        }
        return true;
    }

    public function getStatusList()
    {
        return collect([
            (object) [
                'id' => RequestReceiptStatusEnum::DRAFT,
                'name' => __('request_receipts.text_' . RequestReceiptStatusEnum::DRAFT),
                'value' => RequestReceiptStatusEnum::DRAFT,
            ],
            (object) [
                'id' => RequestReceiptStatusEnum::WAITING_RECEIPT,
                'name' => __('request_receipts.text_' . RequestReceiptStatusEnum::WAITING_RECEIPT),
                'value' => RequestReceiptStatusEnum::WAITING_RECEIPT,
            ],
            (object) [
                'id' => RequestReceiptStatusEnum::SUCCESS_RECEIPT,
                'name' => __('request_receipts.text_' . RequestReceiptStatusEnum::SUCCESS_RECEIPT),
                'value' => RequestReceiptStatusEnum::SUCCESS_RECEIPT,
            ],
        ]);
    }

    public function getTypeList()
    {
        return collect([
            (object) [
                'id' => RequestReceiptTypeEnum::RECEIPT,
                'name' => __('request_receipts.type_' . RequestReceiptTypeEnum::RECEIPT),
                'value' => RequestReceiptTypeEnum::RECEIPT,
            ],
            (object) [
                'id' => RequestReceiptTypeEnum::RECEIPT_TAX,
                'name' => __('request_receipts.type_' . RequestReceiptTypeEnum::RECEIPT_TAX),
                'value' => RequestReceiptTypeEnum::RECEIPT_TAX,
            ],
        ]);
    }
}
