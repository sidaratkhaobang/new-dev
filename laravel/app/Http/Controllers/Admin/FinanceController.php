<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\FinanceContractStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\HirePurchase;
use App\Models\ImportCarLine;
use App\Models\InstallEquipment;
use App\Models\InstallEquipmentLine;
use App\Models\PrepareFinance;
use App\Traits\FinanceTrait;
use App\Traits\HistoryTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Finance);

        $page_title = __('finance.page_title');
        // Search Param
        $lot_no = $request->lot_no;
        $rental = $request->rental;
        $car = $request->car;
        $contract = $request->contract;
        $first_payment = $request->first_payment;
        $status = $request->status;
        $contract_start = $request->contract_start;
        $contract_end = $request->contract_end;
        // Search List Data
        $status_list = FinanceTrait::getFinanceStatusList();
        // Search Select 2 Default Name
        $rental_name = FinanceTrait::getCreditorName($rental);
        $lot_name = FinanceTrait::getLotName($lot_no);
        $car_name = FinanceTrait::getCarName($car);
        $contract_name = null;
        $list = $this->getFinanceData($lot_no, $rental, $car, $contract, $contract_start, $status, $contract_end);
        return view('admin.finance.index', [
            'page_title' => $page_title,
            'lot_no' => $lot_no,
            'lot_name' => $lot_name,
            'rental' => $rental,
            'rental_name' => $rental_name,
            'car' => $car,
            'car_name' => $car_name,
            'contract' => $contract,
            'contract_name' => $contract_name,
            'first_payment' => $first_payment,
            'contract_start' => $contract_start,
            'contract_end' => $contract_end,
            'status' => $status,
            'status_list' => $status_list,
            'list' => $list,
        ]);
    }

    public function getFinanceData($lot_no, $rental, $car, $contract, $contract_start, $status, $contract_end)
    {
        $finance_request = [];
        $data_finance_request = HirePurchase::select(
            '*',
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
            AND down_payment_percent IS NULL
            AND rv_car_percent IS NULL,
            "' . FinanceContractStatusEnum::PENDING . '", "' . FinanceContractStatusEnum::SUCCESS . '") AS status_contract'),
            'lot_id',
            'status',
            'car_id',
            'id'
        )
            ->wherein('status', [
                FinanceContractStatusEnum::SUCCESS,
            ])
            ->when(!empty($lot_no), function ($query_search) use ($lot_no) {
                $query_search->where('lot_id', $lot_no);
            })
            ->when(!empty($rental), function ($query_search) use ($rental) {
            })
            ->when(!empty($contract_start), function ($query_search) use ($contract_start) {
                $query_search->whereDate('contract_start_date', $contract_start);
            })
            ->when(!empty($contract_end), function ($query_search) use ($contract_end) {
                $query_search->where('contract_end', $contract_end);
            })
            ->when(!empty($car), function ($query_search) use ($car) {
                $query_search->where('car_id', $car);
            })
            ->when(!empty($contract), function ($query_search) use ($contract) {
                $query_search->where('contract_no', $contract);
            });
        //        $data_finance_request->havingRaw('status_contract = ?', [FinanceContractStatusEnum::SUCCESS]);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(HirePurchase $finance)
    {
        $this->authorize(Actions::View . '_' . Resources::Finance);
        $page_title = __('finance.page_form_title');
        $d = $finance;
        $rental = null;
        $number_installments = null;
        $rental_name = null;
        $number_installments_name = null;
        $payment_list = $this->getPaymentList();
        $interest_rate_list = $this->getInterestRateList();
        $payment = $d->payment = STATUS_ACTIVE ? __('finance_contract.payment_' . STATUS_ACTIVE) : __('finance_contract.payment_' . STATUS_INACTIVE);
        $interest_rate = $d->interest_rate = STATUS_ACTIVE ? __('finance_contract.interest_rate_' . STATUS_ACTIVE) : __('finance_contract.interest_rate_' . STATUS_INACTIVE);
        $down_payment_percent_total = $this->getPaymentTotal($d?->purchase_order?->total, $d?->down_payment_percent);
        $view = true;
        $finance_type = ImportCarLine::find($d?->car_id);
        $prepare = PrepareFinance::where('lot_id', $d?->lot_id)?->first();
        $approve_line = HistoryTrait::getHistory(PrepareFinance::class, $prepare->id);
        $approve_line_logs = $approve_line['approve_line_logs'];
        $accessory_list = InstallEquipment::where('car_id', $d?->id)->get()->map(function ($item) {
            $accessory_price = InstallEquipmentLine::where('install_equipment_id', $item?->id)->sum('price');
            $accessory_item = InstallEquipmentLine::where('install_equipment_id', $item?->id)->get();
            $item->accessory_price = $accessory_price;
            $item->accessory_item = $accessory_item;
            return $item;
        });
        $total_accessory_price = $accessory_list->sum('accessory_price');
        return view('admin.finance.form', [
            'page_title' => $page_title,
            'd' => $d,
            'rental' => $rental,
            'rental_name' => $rental_name,
            'number_installments' => $number_installments,
            'number_installments_name' => $number_installments_name,
            'payment_list' => $payment_list,
            'interest_rate_list' => $interest_rate_list,
            'payment' => $payment,
            'interest_rate' => $interest_rate,
            'down_payment_percent_total' => $down_payment_percent_total,
            'view' => $view,
            'approve_line_logs' => $approve_line_logs,
            'finance_type' => $finance_type,
            'total_accessory_price' => $total_accessory_price,
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

    public function getPaymentTotal($total, $percent)
    {
        $total_down = 0;
        if (!empty($total) && !empty($percent)) {
            $total_down = ($total * $percent) / 100;
        }
        return $total_down;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
}
