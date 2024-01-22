<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Enums\Actions;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\FinanceRequestStatusEnum;
use App\Enums\RegisterStatusEnum;
use App\Enums\Resources;
use App\Exports\ExportFinanceRequestDealer;
use App\Exports\ExportFinanceRequest;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CMI;
use App\Models\ImportCar;
use App\Models\ImportCarLine;
use App\Models\InstallEquipment;
use App\Models\InstallEquipmentLine;
use App\Models\PrepareFinance;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\Register;
use App\Models\VMI;
use App\Traits\FinanceTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class FinanceRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $this->authorize(Actions::View . '_' . Resources::FinanceRequest);
        $lot_no = $request->lot_no;
        $rental = $request->rental;
        $date_create = $request->date_create;
        $status = $request->status;
        $lot_no_list = [];
        $rental_list = [];
        $status_list = FinanceTrait::getFinanceRequestStatusList();
        $rental_name = FinanceTrait::getCreditorName($rental);
        $lot_name = FinanceTrait::getLotName($lot_no);
        $list = FinanceTrait::getRequestFinanceData($lot_no, $rental, $date_create, $status);
        $page_title = __('finance_request.page_title');
        return view('admin.finance-request.index', [
            'page_title' => $page_title,
            'lot_no_list' => $lot_no_list,
            'lot_no' => $lot_no,
            'lot_name' => $lot_name,
            'rental_list' => $rental_list,
            'rental' => $rental,
            'rental_name' => $rental_name,
            'date_create' => $date_create,
            'status' => $status,
            'status_list' => $status_list,
            'list' => $list,
        ]);
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
        //        Validate Data
        $this->authorize(Actions::Manage . '_' . Resources::FinanceRequest);
        $validator = Validator::make($request->all(), [
            'date_create' => ['required'],
            'bill_date' => ['required'],
            'payment_date' => ['required'],
            'finance_car_data' => ['required'],
        ], [], [
            'date_create' => __('finance_request.search_date_create'),
            'bill_date' => __('finance_request.bill_date'),
            'payment_date' => __('finance_request.payment_date'),
            'finance_car_data' => __('finance_request.finance_car_data'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        //        Validate Car Data
        $validator = Validator::make($request->finance_car_data, [
            '*.finance_type' => ['required'],
        ], [], [
            '*.finance_type' => __('finance_request.type') . __('finance_request.make_finance'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        $date_create = $request->date_create;
        $bill_date = $request->bill_date;
        $payment_date = $request->payment_date;
        $finance_car_data = $request->finance_car_data;

        $prepare_id = $request->prepare_id;
        $prepare_data = PrepareFinance::findOrFail($prepare_id);
        $prepare_status = $prepare_data->status;
        $prepare_data->creation_date = $date_create;
        $prepare_data->billing_date = $bill_date;
        $prepare_data->payment_date = $payment_date;
        $prepare_data->status = FinanceRequestStatusEnum::PENDING_APPROVE;
        $prepare_data->save();
        if ($prepare_status == FinanceRequestStatusEnum::PENDING) {
            $config_type_enum = ConfigApproveTypeEnum::FINANCE_REQUEST;
            $model_type = PrepareFinance::class;
            $model_id = $prepare_id;
            $step_approve_management = new StepApproveManagement();
            $step_approve_management->createModelApproval($config_type_enum, $model_type, $model_id);
        }
        if (!empty($finance_car_data)) {
            foreach ($finance_car_data as $key => $value) {
                $hire_purchase = ImportCarLine::findOrFail($value['finance_id']);
                $hire_purchase->type_car_financing = $value['finance_type'];
                $hire_purchase->save();
            }
        }
        $redirect_route = route('admin.finance-request.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($lot_id, Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::FinanceRequest);
        $status = $request->status;
        $page_title = __('finance_request.page_title_show');
        $list = FinanceTrait::getFinanceCarLot($lot_id, $status);
        $lot_name = FinanceTrait::getLotName($lot_id);
        $view = true;
        $prepare = PrepareFinance::where('lot_id', $lot_id)?->first();
        $rental = $prepare?->insurance_lot?->leasing_id;
        $rental_name = FinanceTrait::getCreditorName($rental);
        return view('admin.finance-request.form', [
            'page_title' => $page_title,
            'list' => $list,
            'rental' => $rental,
            'rental_name' => $rental_name,
            'lot_name' => $lot_name,
            'lot_id' => $lot_id,
            'view' => $view,
            'prepare' => $prepare,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($lot_id, Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::FinanceRequest);
        $status = $request->status;
        $page_title = __('finance_request.page_title_edit');
        $list = FinanceTrait::getFinanceCarLot($lot_id, null);
        $lot_name = FinanceTrait::getLotName($lot_id);
        $prepare = PrepareFinance::where('lot_id', $lot_id)?->first();
        $rental = $prepare?->insurance_lot?->leasing_id;
        $rental_name = FinanceTrait::getCreditorName($rental);
        return view('admin.finance-request.form', [
            'page_title' => $page_title,
            'list' => $list,
            'rental' => $rental,
            'rental_name' => $rental_name,
            'lot_name' => $lot_name,
            'lot_id' => $lot_id,
            'prepare' => $prepare
        ]);
    }

    public function showCarDetail(ImportCarLine $finance_request_id)
    {
        $this->authorize(Actions::View . '_' . Resources::FinanceRequest);
        $d = PrepareFinance::select(
            'import_car_lines.id as id',
            'prepare_finances.lot_id',
            'prepare_finances.status',
            'import_car_lines.id as car_id',
            'import_car_lines.import_car_id',
            'import_cars.po_id'
        )
            ->leftjoin('import_car_lines', 'import_car_lines.lot_id', 'prepare_finances.lot_id')
            ->leftjoin('import_cars', 'import_cars.id', 'import_car_lines.import_car_id')
            ->where('import_car_lines.id', $finance_request_id->id)->first();
        $page_title = __('finance_request.page_title_car_detail');
        $po_data = ImportCar::find($d?->import_car_id)?->purchaseOrder;
        $pr_data = $po_data?->purchaseRequisiton;
        $customer_data = FinanceTrait::getCustomerData($pr_data);
        $delivery_date = FinanceTrait::getDeliveryDate($pr_data, $d?->car_id);
        $rental_price = FinanceTrait::getRentalPrice($pr_data);
        $car = Car::find($d?->id);
        if ($car) {
            $import_car_line = ImportCarLine::find($d?->id);
            $car->receive_date = null;
            if ($import_car_line) {
                $car->receive_date = $import_car_line?->delivery_date;
                $car->registration_type = $import_car_line?->registration_type;
            }
            $car->class_name = $car->carClass?->full_name;
        }
        $accessory_list = InstallEquipment::where('car_id', $import_car_line?->id)->get()->map(function ($item) {
            $accessory_price = InstallEquipmentLine::where('install_equipment_id', $item?->id)->sum('price');
            $accessory_item = InstallEquipmentLine::where('install_equipment_id', $item?->id)->get();
            $item->accessory_price = $accessory_price;
            $item->accessory_item = $accessory_item;
            return $item;
        });
        $total_accessory_price = $accessory_list->sum('accessory_price');
        $car_accessory_vat_price = $total_accessory_price + $po_data?->total;
        $vmi = VMI::select('*', DB::raw('(sum_insured_car+sum_insured_accessory) as insurance_total'))
            ->where('car_id', $import_car_line?->id)->insuranceAvailable()->first();
        $cmi = CMI::select('*', DB::raw('(premium+tax) as cmi_total'))
            ->where('car_id', $import_car_line?->id)->InsuranceAvailable()->first();
        $car_registered = Register::select(DB::raw('(tax+service_fee) as registered_price'))
            ->where('car_id', $import_car_line?->id)
            ->where('status', RegisterStatusEnum::REGISTERED)
            ->first();
        return view('admin.finance-request.form-car-detail', [
            'page_title' => $page_title,
            'd' => $d,
            'po_data' => $po_data,
            'customer_data' => $customer_data,
            'delivery_date' => $delivery_date,
            'rental_price' => $rental_price,
            'accessory_list' => $accessory_list,
            'car' => $car,
            'accessory_list' => $accessory_list,
            'total_accessory_price' => $total_accessory_price,
            'car_accessory_vat_price' => $car_accessory_vat_price,
            'vmi' => $vmi,
            'cmi' => $cmi,
            'cmi' => $cmi,
            'car_registered' => $car_registered,
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
        $this->authorize(Actions::View . '_' . Resources::FinanceRequest);
        //      Search Param
        $lot_no = $request->lot_no;
        $rental = $request->rental;
        $date_create = $request->date_create;
        $status = $request->status;
        //      Search List Data
        $lot_no_list = [];
        $rental_list = [];
        $status_list = FinanceTrait::getFinanceRequestStatusList();
        //        Search Select 2 Default Name
        $rental_name = FinanceTrait::getCreditorName($rental);
        $lot_name = FinanceTrait::getLotName($lot_no);
        //       Table Param
        $list = collect([]);
        $page_title = __('finance_request.page_title_excel');
        return view('admin.finance-request.excels.index', [
            'page_title' => $page_title,
            'lot_no_list' => $lot_no_list,
            'lot_no' => $lot_no,
            'lot_name' => $lot_name,
            'rental_list' => $rental_list,
            'rental' => $rental,
            'rental_name' => $rental_name,
            'date_create' => $date_create,
            'status' => $status,
            'status_list' => $status_list,
            'list' => $list,
        ]);
    }

    public function exportExcelFinanceRequest(Request $request)
    {
        $finance_lot_ids = $request->finance_lot_ids;
        $data_finance_request = PrepareFinance::select(
            '*',
            'import_car_lines.id as import_car_line_id',
        )
            ->leftjoin('import_car_lines',
                'import_car_lines.lot_id',
                'prepare_finances.lot_id')
            ->when(!empty($finance_lot_ids), function ($query_search) use ($finance_lot_ids) {
                $query_search->whereIn('prepare_finances.lot_id', $finance_lot_ids);
            })
            ->whereNotNull('prepare_finances.lot_id')
            ->get()
            ->map(function ($item) {
                $pr_line = PurchaseOrderLine::find($item?->po_line_id);
                $car = Car::find($item?->import_car_line_id);
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
                $item->rental_price = FinanceTrait::getRentalPrice($item?->po?->purchaseRequisiton);
                $vmi = FinanceTrait::getVmi($car?->id);
                $cmi = FinanceTrait::getCmi($car?->id);
                $item->sum_insured_car = $vmi?->sum_insured_car ?? 0;
                $item->sum_insured_accessory = $vmi?->sum_insured_accessory ?? 0;
                $item->insurance_total = $vmi?->insurance_total ?? 0;
                $item->premium = $cmi?->premium ?? 0;
                $item->premium = $cmi?->tax ?? 0;
                $item->premium = $cmi?->cmi_total ?? 0;
                $item->premium = $cmi?->premium_total ?? 0;
                return $item;
            });
        $header_suplier = FinanceTrait::getExportHeaderSuplier($data_finance_request->pluck('import_car_line_id'));
        $table_summary_data = FinanceTrait::getSummarySupplier($data_finance_request->pluck('import_car_line_id'));
        $table_summary_car_price_data = FinanceTrait::getSummaryCarPrice($data_finance_request);
        return Excel::download(new ExportFinanceRequest($data_finance_request,
            $header_suplier,
            $table_summary_data,
            $table_summary_car_price_data
        ), 'template.xlsx');
    }

    public function exportExcelDealerFinanceRequest(Request $request)
    {
        $finance_lot_ids = $request->finance_lot_ids;

        $data_finance_request = PrepareFinance::select(
            'prepare_finances.lot_id',
            'import_car_lines.po_line_id',
            'import_car_lines.id as car_id',
            'import_car_lines.delivery_date'
        )
            ->leftJoin(
                'import_car_lines',
                'import_car_lines.lot_id',
                'prepare_finances.lot_id'
            )
            ->whereIn('prepare_finances.lot_id', $finance_lot_ids)
            ->get()
            ->map(function ($item) {
                $pr_line = PurchaseOrderLine::find($item?->po_line_id);
                $po = PurchaseOrder::find($pr_line?->purchase_order_id);
                $car = Car::find($item?->car_id);
                $item->po_creditor_name = $po?->creditor?->name;
                $item->po_creditor_id = $po?->creditor_id;
                $item->po_no = $po?->po_no;
                $item->seller_name = $po?->creditor?->name;
                $item->car_modal = $car?->carClass?->full_name;
                $item->engine_size = $car?->engine_size;
                $item->car_color = $car?->carColor?->name;
                $item->chassis_no = $car?->chassis_no;
                $item->engine_no = $car?->engine_no;
                $item->car_price_total = $po?->total;
                return $item;
            });
        $data_finance_request = $this->setDealerData($data_finance_request);
        return Excel::download(new ExportFinanceRequestDealer($data_finance_request), 'template.xlsx');
    }

    public function setDealerData($data)
    {
        if (empty($data)) {
            return [];
        }
        $dealer_data = [];
        foreach ($data as $key => $value) {
            if ($value?->lot_id && $value?->po_creditor_id) {
                $dealer_data[$value?->insurance_lot?->lot_no][$value?->po_creditor_name][] = $value;
            }
        }
        return $dealer_data;
    }
}
