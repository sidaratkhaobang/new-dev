<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReceiptTypeEnum;
use App\Enums\ReceiptStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Receipt;
use App\Models\Rental;
use App\Models\OrderPromotionCode;
use App\Models\RentalBill;
use App\Models\CustomerBillingAddress;
use App\Enums\RentalBillTypeEnum;
use App\Models\OrderPromotionCodeLine;
use App\Models\Province;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Traits\RentalTrait;
use App\Traits\CustomerTrait;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Receipt);
        $customer_id = $request->customer_id;
        $receipt_type = $request->receipt_type;
        $worksheet_no = $request->worksheet_no;
        $status = $request->status;
        $list = Receipt::when($worksheet_no, function ($query) use ($worksheet_no) {
            return $query->where('receipts.id', $worksheet_no);
        })
            ->when($receipt_type, function ($query) use ($receipt_type) {
                return $query->where('receipts.receipt_type', $receipt_type);
            })
            ->when($customer_id, function ($query) use ($customer_id) {
                return $query->where('receipts.customer_id', $customer_id);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('receipts.status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(PER_PAGE);

        $reciept_customer_list = Receipt::pluck('customer_id')->toArray();
        $worksheet_no_list = Receipt::select('worksheet_no as name', 'id')->get();
        $customer_list = Customer::whereIn('id', $reciept_customer_list)->select('fullname_th as name', 'id')->get();
        $receipt_type_list = $this->getReceiptType();
        $status_list = $this->getReceiptStatus();
        return view('admin.receipts.index', [
            'list' => $list,
            'worksheet_no_list' => $worksheet_no_list,
            'customer_list' => $customer_list,
            'receipt_type_list' => $receipt_type_list,
            'customer_id' => $customer_id,
            'receipt_type' => $receipt_type,
            'worksheet_no' => $worksheet_no,
            'status_list' => $status_list,
            'status' => $status,
        ]);
    }

    public function edit(Receipt $receipt)
    {
        $tax_invoice_list = [];
        $customer_billing_address_bill = null;
        $check_customer_address = BOOL_TRUE;
        if (in_array($receipt->receipt_type, [ReceiptTypeEnum::CAR_RENTAL, ReceiptTypeEnum::OTHER])) {
            $rental_bill = RentalBill::find($receipt->rental_bill_id);
            $check_customer_address = $rental_bill->check_customer_address;
            $customer_billing_address_bill = ($rental_bill->customer_billing_address_id) ? $rental_bill->customer_billing_address_id : null;
            if ($receipt->customer_id) {
                $tax_invoice_list = CustomerBillingAddress::where('customer_id', $receipt->customer_id)->get()
                    ->map(function ($item) {
                        $item->tax_customer_type_id = $item->billing_customer_type;
                        $item->tax_customer_name = $item->name;
                        $item->tax_tax_no = $item->tax_no;
                        $item->tax_customer_province_id = $item->province_id;
                        $item->tax_customer_address = $item->address;
                        $item->tax_customer_tel = $item->tel;
                        $item->tax_customer_email = $item->email;
                        $item->tax_customer_zipcode = $item->zipcode;
                        $item->tax_customer_type_text = __('customers.type_' . $item->billing_customer_type);
                        $province = Province::find($item->province_id);
                        $item->tax_customer_province_text = ($province) ? $province->name_th : null;
                        return $item;
                    })->toArray();
            }
        } else if (strcmp($receipt->receipt_type, ReceiptTypeEnum::VOUCHER_OF_CASH) == 0) {
            $order_promotion_code = OrderPromotionCode::find($receipt->reference_id);
            $check_customer_address = $order_promotion_code->check_customer_address;
            $customer_billing_address_bill = ($order_promotion_code->customer_billing_address_id) ? $order_promotion_code->customer_billing_address_id : null;
            if ($receipt->customer_id) {
                $tax_invoice_list = CustomerBillingAddress::where('customer_id', $receipt->customer_id)->get()
                    ->map(function ($item) {
                        $item->tax_customer_type_id = $item->billing_customer_type;
                        $item->tax_customer_name = $item->name;
                        $item->tax_tax_no = $item->tax_no;
                        $item->tax_customer_province_id = $item->province_id;
                        $item->tax_customer_address = $item->address;
                        $item->tax_customer_tel = $item->tel;
                        $item->tax_customer_email = $item->email;
                        $item->tax_customer_zipcode = $item->zipcode;
                        $item->tax_customer_type_text = __('customers.type_' . $item->billing_customer_type);
                        $province = Province::find($item->province_id);
                        $item->tax_customer_province_text = ($province) ? $province->name_th : null;
                        return $item;
                    })->toArray();
            }
        }

        $customer_type_list = CustomerTrait::getCustomerType();
        $province_list = Province::select('id', 'name_th as name')->get();
        $branch_office_list  = RentalTrait::getBranchOfficeList();

        $page_title = __('lang.edit') . __('receipts.page_title') . ' ' . $receipt->worksheet_no;
        return view('admin.receipts.form', [
            'd' => $receipt,
            'page_title' => $page_title,
            'check_customer_address' => $check_customer_address,
            'tax_invoice_list' => $tax_invoice_list,
            'customer_billing_address_bill' => $customer_billing_address_bill,
            'view' => false,
            'customer_type_list' => $customer_type_list,
            'province_list' => $province_list,
            'branch_office_list' => $branch_office_list,
        ]);
    }

    public function show(Receipt $receipt)
    {
        $tax_invoice_list = [];
        $customer_billing_address_bill = null;
        $check_customer_address = BOOL_TRUE;
        if (in_array($receipt->receipt_type, [ReceiptTypeEnum::CAR_RENTAL, ReceiptTypeEnum::OTHER])) {
            $rental_bill = RentalBill::find($receipt->rental_bill_id);
            $check_customer_address = $rental_bill->check_customer_address;
            $customer_billing_address_bill = ($rental_bill->customer_billing_address_id) ? $rental_bill->customer_billing_address_id : null;
            if ($receipt->customer_id) {
                $tax_invoice_list = CustomerBillingAddress::where('customer_id', $receipt->customer_id)->get()
                    ->map(function ($item) {
                        $item->tax_customer_type_id = $item->billing_customer_type;
                        $item->tax_customer_name = $item->name;
                        $item->tax_tax_no = $item->tax_no;
                        $item->tax_customer_province_id = $item->province_id;
                        $item->tax_customer_address = $item->address;
                        $item->tax_customer_tel = $item->tel;
                        $item->tax_customer_email = $item->email;
                        $item->tax_customer_zipcode = $item->zipcode;
                        $item->tax_customer_type_text = __('customers.type_' . $item->billing_customer_type);
                        $province = Province::find($item->province_id);
                        $item->tax_customer_province_text = ($province) ? $province->name_th : null;
                        return $item;
                    })->toArray();
            }
        } else if (strcmp($receipt->receipt_type, ReceiptTypeEnum::VOUCHER_OF_CASH) == 0) {
            $order_promotion_code = OrderPromotionCode::find($receipt->reference_id);
            $check_customer_address = $order_promotion_code->check_customer_address;
            $customer_billing_address_bill = ($order_promotion_code->customer_billing_address_id) ? $order_promotion_code->customer_billing_address_id : null;
            if ($receipt->customer_id) {
                $tax_invoice_list = CustomerBillingAddress::where('customer_id', $receipt->customer_id)->get()
                    ->map(function ($item) {
                        $item->tax_customer_type_id = $item->billing_customer_type;
                        $item->tax_customer_name = $item->name;
                        $item->tax_tax_no = $item->tax_no;
                        $item->tax_customer_province_id = $item->province_id;
                        $item->tax_customer_address = $item->address;
                        $item->tax_customer_tel = $item->tel;
                        $item->tax_customer_email = $item->email;
                        $item->tax_customer_zipcode = $item->zipcode;
                        $item->tax_customer_type_text = __('customers.type_' . $item->billing_customer_type);
                        $province = Province::find($item->province_id);
                        $item->tax_customer_province_text = ($province) ? $province->name_th : null;
                        return $item;
                    })->toArray();
            }
        }

        $customer_type_list = CustomerTrait::getCustomerType();
        $province_list = Province::select('id', 'name_th as name')->get();
        $branch_office_list  = RentalTrait::getBranchOfficeList();

        $page_title = __('lang.view') . __('receipts.page_title') . ' ' . $receipt->worksheet_no;
        return view('admin.receipts.form', [
            'd' => $receipt,
            'page_title' => $page_title,
            'check_customer_address' => $check_customer_address,
            'tax_invoice_list' => $tax_invoice_list,
            'customer_billing_address_bill' => $customer_billing_address_bill,
            'view' => true,
            'customer_type_list' => $customer_type_list,
            'province_list' => $province_list,
            'branch_office_list' => $branch_office_list,
        ]);
    }

    public function store(Request $request)
    {
        $receipt_old = Receipt::find($request->id);

        if ($receipt_old->customer_id) {
            $customer_data = Customer::find($receipt_old->customer_id);
        }
        if ((strcmp($request->check_customer_address, BOOL_FALSE) == 0)) {
            if (!$request->customer_billing_address_id) {
                return response()->json([
                    'success' => false,
                    'message' => __('lang.required_field_inform')
                ]);
            }
            $customer_data = CustomerBillingAddress::find($request->customer_billing_address_id);
            $customer_data->customer_code = ($customer_data->customer) ? $customer_data->customer->customer_code : null;
        }

        $receipt_count = Receipt::all()->count() + 1;
        $prefix = '';
        $receipt_new = new Receipt();
        $receipt_new->worksheet_no = generateRecordNumber($prefix, $receipt_count, false);
        $receipt_new->reference_id = $receipt_old->reference_id;
        $receipt_new->reference_type = $receipt_old->reference_type;
        $receipt_new->receipt_type = $receipt_old->receipt_type;
        $receipt_new->rental_bill_id = $receipt_old->rental_bill_id;

        $receipt_new->customer_id = ($receipt_old->customer_id) ? $receipt_old->customer_id : null;
        $receipt_new->customer_code = ($customer_data->customer_code) ? $customer_data->customer_code : null;
        $receipt_new->customer_name = ($customer_data->name) ? $customer_data->name : null;
        $receipt_new->customer_address = ($customer_data->address) ? $customer_data->address : null;
        $receipt_new->customer_tel = ($customer_data->tel) ? $customer_data->tel : null;
        $receipt_new->customer_email = ($customer_data->email) ? $customer_data->email : null;
        $receipt_new->customer_tax_no = ($customer_data->tax_no) ? $customer_data->tax_no : null;
        $receipt_new->subtotal = $receipt_old->subtotal;
        $receipt_new->vat = $receipt_old->vat;
        $receipt_new->withholding_tax = $receipt_old->withholding_tax;
        $receipt_new->total = $receipt_old->total;
        $receipt_new->parent_id = $receipt_old->id;
        $receipt_new->save();

        $receipt_old->status = ReceiptStatusEnum::INACTIVE;
        $receipt_old->save();

        if (in_array($receipt_new->receipt_type, [ReceiptTypeEnum::CAR_RENTAL, ReceiptTypeEnum::OTHER])) {
            $rental_bill = RentalBill::find($receipt_new->rental_bill_id);
            if ($rental_bill) {
                $rental_bill->check_customer_address = $request->check_customer_address;
                $rental_bill->receipt_id = $receipt_new->id;
                if ((strcmp($request->check_customer_address, BOOL_FALSE) == 0)) {
                    $rental_bill->customer_billing_address_id = $request->customer_billing_address_id;
                } else {
                    $rental_bill->customer_billing_address_id = null;
                }
                $rental_bill->save();
            }
            if ((strcmp($receipt_new->receipt_type, ReceiptTypeEnum::CAR_RENTAL) == 0) && strcmp($rental_bill->bill_type, RentalBillTypeEnum::PRIMARY) == 0) {
                $rental = Rental::find($rental_bill->rental_id);
                $rental->receipt_no = $receipt_new->worksheet_no;
                $rental->save();
            }
        } else if (strcmp($receipt_new->receipt_type, ReceiptTypeEnum::VOUCHER_OF_CASH) == 0) {
            $order_promotion_code = OrderPromotionCode::find($receipt_new->reference_id);
            if ($order_promotion_code) {
                $order_promotion_code->check_customer_address = $request->check_customer_address;
                $order_promotion_code->receipt_id = $receipt_new->id;
                if ((strcmp($request->check_customer_address, BOOL_FALSE) == 0)) {
                    $order_promotion_code->customer_billing_address_id = $request->customer_billing_address_id;
                } else {
                    $order_promotion_code->customer_billing_address_id = null;
                }
                $order_promotion_code->save();
            }
        }

        $redirect_route = route('admin.receipts.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public static function getReceiptType()
    {
        $receipt_type = collect([
            (object) [
                'id' => ReceiptTypeEnum::CAR_RENTAL,
                'name' => __('receipts.receipt_type_' . ReceiptTypeEnum::CAR_RENTAL),
                'value' => ReceiptTypeEnum::CAR_RENTAL,
            ],
            (object) [
                'id' => ReceiptTypeEnum::VOUCHER_OF_CASH,
                'name' => __('receipts.receipt_type_' . ReceiptTypeEnum::VOUCHER_OF_CASH),
                'value' => ReceiptTypeEnum::VOUCHER_OF_CASH,
            ],
            (object) [
                'id' => ReceiptTypeEnum::OTHER,
                'name' => __('receipts.receipt_type_' . ReceiptTypeEnum::OTHER),
                'value' => ReceiptTypeEnum::OTHER,
            ],
        ]);
        return $receipt_type;
    }

    public static function getReceiptStatus()
    {
        $receipt_status = collect([
            (object) [
                'id' => ReceiptStatusEnum::ACTIVE,
                'name' => __('receipts.status_' . ReceiptStatusEnum::ACTIVE),
                'value' => ReceiptStatusEnum::ACTIVE,
            ],
            (object) [
                'id' => ReceiptStatusEnum::INACTIVE,
                'name' => __('receipts.status_' . ReceiptStatusEnum::INACTIVE),
                'value' => ReceiptStatusEnum::INACTIVE,
            ],
        ]);
        return $receipt_status;
    }

    public function printPdf(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Receipt);
        $receipt = Receipt::find($request->receipt);
        if ($receipt) {
            $page_title = $receipt->worksheet_no;
            $reference_id = $receipt->reference_id;
            $reference_type = $receipt->reference_type;
            if (strcmp($reference_type, Rental::class) == 0) {
                $rental = Rental::find($reference_id);
                $branch_tax_no = ($rental->branch) ? $rental->branch->tax_no : null;
                $branch_name = ($rental->branch) ? $rental->branch->name : null;
                $branch_address = ($rental->branch) ? $rental->branch->address : null;

                $pdf = PDF::loadView(
                    'admin.receipts.component-pdf.pdf',
                    [
                        'd' => $receipt,
                        'branch_tax_no' => $branch_tax_no,
                        'branch_name' => $branch_name,
                        'branch_address' => $branch_address,
                        'page_title' => $page_title,
                    ]
                );
                return  $pdf->stream();
            } else if (strcmp($reference_type, OrderPromotionCode::class) == 0) {
                $branch_promotion = OrderPromotionCodeLine::leftJoin('promotion_codes', 'promotion_codes.id', '=', 'order_promotion_code_lines.promotion_code_id')
                    ->leftJoin('promotions', 'promotions.id', '=', 'promotion_codes.promotion_id')
                    ->leftJoin('branches', 'branches.id', '=', 'promotions.branch_id')
                    ->where('order_promotion_code_lines.order_promotion_code_id', $reference_id)
                    ->select('branches.name as branch_name', 'branches.tax_no as branch_tax_no', 'branches.address as branch_address')->first();
                $branch_name = ($branch_promotion->branch_name) ? $branch_promotion->branch_name : null;
                $branch_tax_no = ($branch_promotion->branch_tax_no) ? $branch_promotion->branch_tax_no : null;
                $branch_address = ($branch_promotion->branch_address) ? $branch_promotion->branch_address : null;

                $pdf = PDF::loadView(
                    'admin.receipts.component-pdf.pdf',
                    [
                        'd' => $receipt,
                        'branch_tax_no' => $branch_tax_no,
                        'branch_name' => $branch_name,
                        'branch_address' => $branch_address,
                        'page_title' => $page_title,
                    ]
                );
                return  $pdf->stream();
            }
        } else {
            return view('admin.receipts.index');
        }
    }
}
