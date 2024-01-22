<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\FinanceContractStatusEnum;
use App\Enums\OwnershipTransferStatusEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\Resources;
use App\Exports\ExportFinanceContract;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\HirePurchase;
use App\Models\ImportCarLine;
use App\Models\InstallEquipment;
use App\Models\InstallEquipmentLine;
use App\Models\OwnershipTransfer;
use App\Models\PrepareFinance;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Traits\FinanceTrait;
use App\Traits\HistoryTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class FinanceContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page_title = __('finance_contract.page_title');
        // Search Param
        $lot_no = $request->lot_no;
        $rental = $request->rental;
        $car = $request->car;
        $contract = $request->contract;
        $date_create = $request->date_create;
        $status = $request->status;
        // Search List Data
        $status_list = FinanceTrait::getRequestContractStatusList();
        // Search Select 2 Default Name
        $rental_name = FinanceTrait::getCreditorName($rental);
        $lot_name = FinanceTrait::getLotName($lot_no);
        $car_name = FinanceTrait::getCarName($car);
        $contract_name = null;
        $list = $this->getFinanceContractData($lot_no, $rental, $car, $contract, $date_create, $status);
        return view('admin.finance-contract.index', [
            'page_title' => $page_title,
            'lot_no' => $lot_no,
            'lot_name' => $lot_name,
            'rental' => $rental,
            'rental_name' => $rental_name,
            'car' => $car,
            'car_name' => $car_name,
            'contract' => $contract,
            'contract_name' => $contract_name,
            'date_create' => $date_create,
            'status' => $status,
            'status_list' => $status_list,
            'list' => $list,
        ]);
    }

    public function getFinanceContractData($lot_no, $rental, $car, $contract, $date_create, $status)
    {
        $finance_request = [];
        $data_finance_request = HirePurchase::select(
            DB::raw('IF(
            contract_no IS NULL
            AND finance_date IS NULL
            AND contract_start_date IS NULL
            AND contract_end_date IS NULL
            AND first_payment_date IS NULL
            AND amount_installments IS NULL
            AND pay_installments IS NULL
            AND payment IS NULL
            AND interest_rate_percent IS NULL
            AND interest_rate IS NULL
            AND down_payment_percent IS NULL,
            "' . FinanceContractStatusEnum::PENDING . '", "' . FinanceContractStatusEnum::SUCCESS . '") AS status_contract'),
            'lot_id',
            'status',
            'car_id',
            'id',
            'contract_no',
            'finance_date',
        )
            ->when(!empty($lot_no), function ($query_search) use ($lot_no) {
                $query_search->where('lot_id', $lot_no);
            })
            ->when(!empty($rental), function ($query_search) use ($rental) {
            })
            ->when(!empty($date_create), function ($query_search) use ($date_create) {
                $query_search->whereDate('finance_date', $date_create);
            })
            ->when(!empty($car), function ($query_search) use ($car) {
                $query_search->where('car_id', $car);
            })
            ->when(!empty($contract), function ($query_search) use ($contract) {
                $query_search->where('contract_no', $contract);
            });
        if (!empty($status)) {
            $data_finance_request->havingRaw('status_contract = ?', [$status]);
        }
        $data_finance_request = $data_finance_request->paginate(PER_PAGE);
        if (!empty($data_finance_request)) {
            $finance_request = $data_finance_request;
        }
        return $finance_request;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::FinanceContract);
        $validator = Validator::make($request->all(), [
            'down_payment_percent' => ['required'],
            'finance_date' => ['required'],
            'contract_start_date' => ['required'],
            'contract_end_date' => ['required'],
            'first_payment_date' => ['required'],
            'amount_installments' => ['required'],
            'pay_installments' => ['required'],
            'payment' => ['required'],
            'interest_rate_percent' => ['required'],
            'interest_rate' => ['required'],

        ], [], [
            'down_payment_percent' => __('finance_contract.down_payment_percent'),
            'finance_date' => __('finance_contract.finance_date'),
            'contract_start_date' => __('finance_contract.contract_start_date'),
            'contract_end_date' => __('finance_contract.contract_end_date'),
            'first_payment_date' => __('finance_contract.first_payment_date'),
            'amount_installments' => __('finance_contract.amount_installments'),
            'pay_installments' => __('finance_contract.pay_installments'),
            'payment' => __('finance_contract.payment'),
            'interest_rate_percent' => __('finance_contract.interest_rate_percent'),
            'interest_rate' => __('finance_contract.'),

            //            'lot_id' => __('finance_request.search_lot_no'),
            //            'date_create' => __('finance_request.search_date_create'),
            //            'bill_date' => __('finance_request.bill_date'),
            //            'payment_date' => __('finance_request.payment_date'),
            //            'rental' => __('finance_request.search_rental'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $hire_purchase = HirePurchase::findOrFail($request->hire_purchase_id);
        $hire_purchase->contract_no = $request->contract_no;
        $hire_purchase->finance_date = $request->finance_date;
        $hire_purchase->number_installments = $request->number_installments;
        $hire_purchase->contract_start_date = $request->contract_start_date;
        $hire_purchase->contract_end_date = $request->contract_end_date;
        $hire_purchase->first_payment_date = $request->first_payment_date;
        $hire_purchase->amount_installments = (int)$request->amount_installments;
        $hire_purchase->pay_installments = $request->pay_installments;
        $hire_purchase->payment = $request->payment;
        $hire_purchase->interest_rate_percent = $request->interest_rate_percent;
        $hire_purchase->interest_rate = $request->interest_rate;
        $hire_purchase->down_payment_percent = $request->down_payment_percent;
        $hire_purchase->rv_car_percent = $request->rv_car_percent;
        $hire_purchase->rv_accessory_percent = $request->rv_accessory_percent;
        $hire_purchase->remark = $request->remark;
        $check_status = $hire_purchase->status;
        $hire_purchase->status = FinanceContractStatusEnum::SUCCESS;
        $hire_purchase->save();

        $ownership_transfer = $this->createOwnershipTransfer($hire_purchase->id, $hire_purchase->car_id);

        return $this->responseWithCode(true, DATA_SUCCESS, [], 200);
    }

    public function createOwnershipTransfer($hire_purchase_id, $car_id)
    {

        $ownership_transfer_check = OwnershipTransfer::where('hire_purchase_id', $hire_purchase_id)
            ->where('car_id', $car_id)
            ->first();
        if (!$ownership_transfer_check) {
            $ownership_transfer = new OwnershipTransfer();
            $prefix = 'OT-';
            $ownership_transfer->worksheet_no = generate_worksheet_no(OwnershipTransfer::class, false);
            $ownership_transfer->hire_purchase_id = $hire_purchase_id;
            $ownership_transfer->car_id = $car_id;
            $ownership_transfer->status = OwnershipTransferStatusEnum::WAITING_TRANSFER;
            $ownership_transfer->save();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(HirePurchase $finance_contract)
    {
        $page_title = __('finance_contract.page_title_edit');
        $d = $finance_contract;
        $number_installments = null;
        $rental = $finance_contract?->insurance_lot?->leasing_id;
        $rental_name = FinanceTrait::getCreditorName($rental);
        $number_installments_name = null;
        $number_installments_list = $this->getNumberInstallment();
        $payment_list = $this->getPaymentList();
        $interest_rate_list = $this->getInterestRateList();
        $prepare = PrepareFinance::where('lot_id', $d?->lot_id)?->first();
        $approve_line = HistoryTrait::getHistory(PrepareFinance::class, $prepare->id);
        $approve_line_logs = $approve_line['approve_line_logs'];
        $finance_type = ImportCarLine::find($d?->car_id);
        $accessory_list = InstallEquipment::where('car_id', $d?->id)->get()->map(function ($item) {
            $accessory_price = InstallEquipmentLine::where('install_equipment_id', $item?->id)->sum('price');
            $accessory_item = InstallEquipmentLine::where('install_equipment_id', $item?->id)->get();
            $item->accessory_price = $accessory_price;
            $item->accessory_item = $accessory_item;
            return $item;
        });
        $total_accessory_price = $accessory_list->sum('accessory_price');
        $view = true;
        return view('admin.finance-contract.form', [
            'page_title' => $page_title,
            'd' => $d,
            'rental' => $rental,
            'rental_name' => $rental_name,
            'number_installments' => $number_installments,
            'number_installments_name' => $number_installments_name,
            'number_installments_list' => $number_installments_list,
            'payment_list' => $payment_list,
            'interest_rate_list' => $interest_rate_list,
            'approve_line' => $approve_line,
            'approve_line_logs' => $approve_line_logs,
            'view' => $view,
            'finance_type' => $finance_type,
            'total_accessory_price' => $total_accessory_price,
        ]);
    }

    public function getNumberInstallment(): object
    {
        return collect([
            (object)[
                'id' => 12,
                'value' => 12,
                'name' => 12,
            ],
            (object)[
                'id' => 24,
                'value' => 24,
                'name' => 24,
            ],
            (object)[
                'id' => 36,
                'value' => 36,
                'name' => 36,
            ],
            (object)[
                'id' => 48,
                'value' => 48,
                'name' => 48,
            ],
            (object)[
                'id' => 60,
                'value' => 60,
                'name' => 60,
            ],
            (object)[
                'id' => 72,
                'value' => 72,
                'name' => 72,
            ],

        ]);
    }

    public function getPaymentList()
    {
        return collect([
            [
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => __('finance_contract.payment_' . STATUS_ACTIVE),
            ],
            [
                'id' => STATUS_INACTIVE,
                'value' => STATUS_INACTIVE,
                'name' => __('finance_contract.payment_' . STATUS_INACTIVE),
            ],
        ]);
    }

    public function getInterestRateList()
    {
        return collect([
            [
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => __('finance_contract.interest_rate_' . STATUS_ACTIVE),
            ],
            [
                'id' => STATUS_INACTIVE,
                'value' => STATUS_INACTIVE,
                'name' => __('finance_contract.interest_rate_' . STATUS_INACTIVE),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(HirePurchase $finance_contract)
    {
        $page_title = __('finance_contract.page_title_edit');
        $d = $finance_contract;
        $number_installments = null;
        $rental = $finance_contract?->insurance_lot?->leasing_id;
        $rental_name = FinanceTrait::getCreditorName($rental);
        $number_installments_name = null;
        $number_installments_list = $this->getNumberInstallment();
        $payment_list = $this->getPaymentList();
        $interest_rate_list = $this->getInterestRateList();
        $accessory_list = InstallEquipment::where('car_id', $d?->id)->get()->map(function ($item) {
            $accessory_price = InstallEquipmentLine::where('install_equipment_id', $item?->id)->sum('price');
            $accessory_item = InstallEquipmentLine::where('install_equipment_id', $item?->id)->get();
            $item->accessory_price = $accessory_price;
            $item->accessory_item = $accessory_item;
            return $item;
        });
        $total_accessory_price = $accessory_list->sum('accessory_price');
        $finance_type = ImportCarLine::find($d?->car_id);
        return view('admin.finance-contract.form', [
            'page_title' => $page_title,
            'd' => $d,
            'rental' => $rental,
            'rental_name' => $rental_name,
            'number_installments' => $number_installments,
            'number_installments_name' => $number_installments_name,
            'number_installments_list' => $number_installments_list,
            'payment_list' => $payment_list,
            'interest_rate_list' => $interest_rate_list,
            'total_accessory_price' => $total_accessory_price,
            'finance_type' => $finance_type,
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

    public function indexExcel(Request $request)
    {
        $page_title = __('finance_contract.page_title_excel');
        // Search Param
        $lot_no = $request->lot_no;
        $rental = $request->rental;
        $car = $request->car;
        $contract = $request->contract;
        $date_create = $request->date_create;
        $status = $request->status;
        // Search List Data
        $status_list = [];
        // Search Select 2 Default Name
        $rental_name = FinanceTrait::getCreditorName($rental);
        $lot_name = FinanceTrait::getLotName($lot_no);
        $car_name = FinanceTrait::getCarName($car);
        $contract_name = null;
        $list = collect([]);
        return view('admin.finance-contract.excels.index', [
            'page_title' => $page_title,
            'lot_no' => $lot_no,
            'lot_name' => $lot_name,
            'rental' => $rental,
            'rental_name' => $rental_name,
            'car' => $car,
            'car_name' => $car_name,
            'contract' => $contract,
            'contract_name' => $contract_name,
            'date_create' => $date_create,
            'status' => $status,
            'status_list' => $status_list,
            'list' => $list,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $finance_car_ids = $request->finance_car_ids;
        $data_finance_request = HirePurchase::select(
            'hire_purchases.*',
            'import_car_lines.id as import_car_line_id',
            'import_car_lines.po_line_id',
            'import_car_lines.type_car_financing'
        )
            ->leftjoin('import_car_lines',
                'import_car_lines.lot_id',
                'hire_purchases.lot_id')
            ->when(!empty($finance_car_ids), function ($query_search) use ($finance_car_ids) {
                $query_search->whereIn('hire_purchases.car_id', $finance_car_ids);
            })
            ->get()
            ->map(function ($item) {
                $pr_line = PurchaseOrderLine::find($item?->po_line_id);
                $car = Car::find($item?->car_id);
                $po = PurchaseOrder::find($pr_line?->purchase_order_id);
                $item->creditor_id = $po?->creditor_id;
                $item->po_no = $po?->po_no;
                $item->car_model = $car?->carClass?->full_name;
                $item->car_cc = $car?->carClass?->engine_size;
                $item->car_color = $car?->carColor?->name;
                $item->car = $car;
                $item->po = $po;
                $item->po_price = $po?->total;
                $item->install_equipment = FinanceTrait::getInstallEquipmentList($car?->id);
                $item->accessory_total_with_vat = FinanceTrait::getAccessoryPriceTotal($car?->id);
                $item->price_car_with_accessory = $item?->po?->total + $item->accessory_total_with_vat;
                $item->customer_data = FinanceTrait::getCustomerData($item?->po?->purchaseRequisiton);
                $item->delivery_date = FinanceTrait::getDeliveryDate($item?->po?->purchaseRequisiton, $car?->id);
                $item->rental_price = FinanceTrait::getRentalPrice($item?->po?->purchaseRequisiton);
                $item->rental_duration = FinanceTrait::getRentalDuration($item?->po?->purchaseRequisiton);
                $item->rental_remark = ($item?->po?->purchaseRequisiton?->rental_type == RentalTypeEnum::SPARE) ? 'ทดแทนรถหมดสัญญา' : 'สัญญาใหม่';
                $item->rv_price = FinanceTrait::calRvPrice($po?->total, $item?->rv_car_percent);
                return $item;
            });
        $header_suplier = FinanceTrait::getExportHeaderSuplier($data_finance_request->pluck('import_car_line_id'));
        $table_summary_data = FinanceTrait::getSummarySupplier($data_finance_request->pluck('import_car_line_id'));
        $table_summary_car_price_data = FinanceTrait::getSummaryCarPrice($data_finance_request);
        return Excel::download(new ExportFinanceContract($data_finance_request,
            $header_suplier,
            $table_summary_data,
            $table_summary_car_price_data
        ), 'template.xlsx');
    }
}
