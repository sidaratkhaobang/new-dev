<?php

namespace App\Http\Controllers\Admin;

use App\Classes\StepApproveManagement;
use App\Enums\Actions;
use App\Enums\ApproveStatusEnum;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\FinanceCarStatusEnum;
use App\Enums\FinanceRequestStatusEnum;
use App\Enums\RegisterStatusEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CMI;
use App\Models\ImportCar;
use App\Models\ImportCarLine;
use App\Models\InstallEquipment;
use App\Models\InstallEquipmentLine;
use App\Models\LongTermRental;
use App\Models\LongTermRentalLineMonth;
use App\Models\LongTermRentalPRLine;
use App\Models\PrepareFinance;
use App\Models\Register;
use App\Models\VMI;
use App\Traits\FinanceTrait;
use App\Traits\HistoryTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FinanceRequestApproveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::FinanceRequestApprove);
        //      Search Param
        $lot_no = $request->lot_no;
        $rental = $request->rental;
        $date_create = $request->date_create;
        $status = $request->status;
        //      Search List Data
        $lot_no_list = [];
        $rental_list = [];
        $status_list = FinanceTrait::getFinanceRequestStatusList();
        //        Remove Pending Status
        unset($status_list[0]);
        //        Search Select 2 Default Name
        $rental_name = FinanceTrait::getCreditorName($rental);
        $lot_name = FinanceTrait::getLotName($lot_no);
        //       Table Param
        $list = $this->getRequestFinanceData($lot_no, $rental, $date_create, $status);
        $page_title = __('finance_request_approve.page_title');
        return view('admin.finance-request-approve.index', [
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


    public function getRequestFinanceData($lot_no, $rental, $date_create, $status)
    {
        $finance_request = [];
        $data_finance_request = PrepareFinance::select(DB::raw("count(prepare_finances.lot_id) as car_total"), 'prepare_finances.lot_id', 'prepare_finances.status')
            ->leftjoin('import_car_lines', 'import_car_lines.lot_id', 'prepare_finances.lot_id')
            ->groupby('prepare_finances.lot_id', 'prepare_finances.status')
            ->wherein('prepare_finances.status', [
                FinanceRequestStatusEnum::APPROVE,
                FinanceRequestStatusEnum::PENDING_APPROVE,
                FinanceRequestStatusEnum::REJECT,
            ])
            ->when(!empty($lot_no), function ($query_search) use ($lot_no) {
                $query_search->where('prepare_finances.lot_id', $lot_no);
            })
            ->when(!empty($rental), function ($query_search) use ($rental) {
            })
            ->when(!empty($date_create), function ($query_search) use ($date_create) {
                $query_search->whereDate('prepare_finances.creation_date', $date_create);
            })
            ->when(!empty($status), function ($query_search) use ($status) {
                $query_search->where('prepare_finances.status', $status);
            })
            ->paginate(PER_PAGE);
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
    public function show($lot_id, Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::FinanceRequestApprove);
        $status = $request->status;
        $page_title = __('finance_request_approve.page_title');
        $list = $this->getFinanceCarLot($lot_id, null);
        $lot_name = FinanceTrait::getLotName($lot_id);
        $prepare = PrepareFinance::where('lot_id', $lot_id)?->first();
        $approve_line = HistoryTrait::getHistory(PrepareFinance::class, $prepare->id);
        $approve_line_list = $approve_line['approve_line_list'];
        $approve = $approve_line['approve'];
        $approve_line_logs = $approve_line['approve_line_logs'];
        $rental = $prepare?->insurance_lot?->leasing_id;
        $rental_name = FinanceTrait::getCreditorName($rental);
        if (!is_null($approve_line_list)) {
            // can approve or super user
            $approve_line_owner = new StepApproveManagement();
            // $approve_line_owner = $approve_line_owner->checkCanApprove($approve);
            $approve_line_owner = $approve_line_owner->checkCanApprove(PrepareFinance::class, $prepare->id, ConfigApproveTypeEnum::FINANCE_REQUEST);

        } else {
            $approve_line_owner = null;
        }
        return view('admin.finance-request-approve.form', [
            'page_title' => $page_title,
            'list' => $list,
            'rental' => $rental,
            'rental_name' => $rental_name,
            'lot_name' => $lot_name,
            'lot_id' => $lot_id,
            'prepare' => $prepare,
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
            'approve_line_owner' => $approve_line_owner,
            'approve_line_logs' => $approve_line_logs,
        ]);
    }

    public function getFinanceCarLot($lot_id, $status)
    {
        $list = [];
        $list = $data_finance_request = PrepareFinance::select(
            'import_car_lines.id as id',
            'prepare_finances.lot_id',
            'prepare_finances.status',
            'import_car_lines.id as car_id',
            'import_car_lines.import_car_id',
            'import_cars.po_id',
            'import_car_lines.type_car_financing'
        )
            ->leftjoin('import_car_lines', 'import_car_lines.lot_id', 'prepare_finances.lot_id')
            ->leftjoin('import_cars', 'import_cars.id', 'import_car_lines.import_car_id')
            ->where('prepare_finances.lot_id', $lot_id)
            ->when(!empty($status), function ($query_search) use ($status) {
                $query_search->where('prepare_finances.status', $status);
            })
            ->paginate(PER_PAGE);;
        //        $list = HirePurchase::where('lot_id', $lot_id)
        //            ->where('status', $status)
        //            ->paginate(PER_PAGE);
        $list->map(function ($item) {
            $item->accessory_price = $this->getTotalAccessoryPrice($item?->car_id);
            $item->car_price_vat = $item?->purchase_order?->total;
            $item->car_total_price = $item->accessory_price + $item->car_price_vat;
            $item->finance_type_list = $this->getListFinanceType($item?->car_id);
            return $item;
        });

        return $list;
    }

    public function getTotalAccessoryPrice($car_id)
    {
        $price = 0;
        if (!empty($car_id)) {
            $accessory_list = InstallEquipment::where('car_id', $car_id)->get()->map(function ($item) {
                $accessory_price = InstallEquipmentLine::where('install_equipment_id', $item?->id)->sum('price');
                $item->accessory_price = $accessory_price;
                return $item;
            });
            $price = $accessory_list->sum('accessory_price');
            if (empty($price)) {
                $price = 0;
            }
        }
        return $price;
    }

    public function getListFinanceType($car_id)
    {
        $car_accessory = InstallEquipment::where('car_id', $car_id)->first();
        if (!empty($car_accessory)) {
            return collect([
                (object)[
                    'id' => FinanceCarStatusEnum::CAR,
                    'value' => FinanceCarStatusEnum::CAR,
                    'name' => __('finance_request.car_status' . FinanceCarStatusEnum::CAR),
                ],
                (object)[
                    'id' => FinanceCarStatusEnum::CAR_AND_ACCESSORY,
                    'value' => FinanceCarStatusEnum::CAR_AND_ACCESSORY,
                    'name' => __('finance_request.car_status' . FinanceCarStatusEnum::CAR_AND_ACCESSORY),
                ],
            ]);
        } else {
            return collect([
                (object)[
                    'id' => FinanceCarStatusEnum::CAR,
                    'value' => FinanceCarStatusEnum::CAR,
                    'name' => __('finance_request.status_' . FinanceCarStatusEnum::CAR),
                ],
            ]);
        }
    }

    public function showCarDetail(ImportCarLine $finance_request_id)
    {
        $this->authorize(Actions::View . '_' . Resources::FinanceRequestApprove);
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
        $customer_data = $this->getCustomerData($pr_data);
        $delivery_date = $this->getDeliveryDate($pr_data, $d);
        $rental_price = $this->getRentalPrice($pr_data);
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
            'car_registered' => $car_registered,
        ]);
    }

    public function getCustomerData($pr_data)
    {
        $customer_data = [];
        if (!empty($pr_data) && $pr_data?->rental_type == RentalTypeEnum::LONG) {
            $customer_data = LongTermRental::find($pr_data?->reference_id);
        }
        return $customer_data;
    }

    public function getDeliveryDate($pr_data, $po_data)
    {
        $delivery_date = null;
        if (!empty($pr_data) && $pr_data?->rental_type == RentalTypeEnum::LONG) {
            $lt_rental_data = LongTermRental::find($pr_data?->reference_id);
            $delivery_date = $lt_rental_data?->actual_delivery_date;
        } else {
            $delivery_date = $po_data?->importCarLine?->delivery_date;
        }
        return $delivery_date;
    }

    public function getRentalPrice($pr_data)
    {
        $rental_price = [];
        if (!empty($pr_data) && $pr_data?->rental_type == RentalTypeEnum::LONG) {
            $rental = LongTermRental::find($pr_data?->reference_id);
            $rental_pr_line = LongTermRentalPRLine::where('lt_rental_id', $rental?->id)?->first();
            $rental_pr_line_month = LongTermRentalLineMonth::where('lt_rental_month_id', $rental_pr_line?->lt_rental_month_id)?->first();
            $rental_price = $rental_pr_line_month?->total_price;
        }
        return $rental_price;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
    }

    public function updateStatus(Request $request)
    {
        if (in_array($request->finance_request_status, [
            FinanceRequestStatusEnum::REJECT,
        ])) {
            $validator = Validator::make($request->all(), [
                'reject_reason' => ['required', 'max:255'],
            ], [], [
                'reject_reason' => __('lang.reason')
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }
        $lot_id = $request->lot_id;
        FinanceTrait::createHirePurchase($lot_id);
        if ($request->finance_request_id) {
            $prepare_finance = PrepareFinance::find($request->finance_request_id);

            // update approve step
            $finance_status = ($request->finance_request_status == FinanceRequestStatusEnum::APPROVE) ? ApproveStatusEnum::CONFIRM : $request->finance_request_status;
            $approve_update = new StepApproveManagement();
            // $approve_update = $approve_update->updateApprove($request, $prepare_finance, $finance_status, PrepareFinance::class);
            $approve_update = $approve_update->updateApprove(PrepareFinance::class, $prepare_finance->id, $finance_status, null, null);
            if ($approve_update == ApproveStatusEnum::REJECT) {
                $approve_update = ApproveStatusEnum::REJECT;
            } else {
                $approve_update = ($approve_update == ApproveStatusEnum::CONFIRM) ? FinanceRequestStatusEnum::APPROVE : FinanceRequestStatusEnum::PENDING_APPROVE;
            }
            $prepare_finance->status = $approve_update;
            $prepare_finance->save();

            if ($prepare_finance->status == FinanceRequestStatusEnum::APPROVE) {
                FinanceTrait::createHirePurchase($lot_id);
                FinanceTrait::createAssetCar($lot_id);
            }
            return response()->json([
                'success' => true,
                'message' => 'ok',
                'redirect' => ($request->redirect_route) ? $request->redirect_route : route('admin.finance-request-approve.index')
            ]);
            // });
        } else {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found'),
                'redirect' => ($request->redirect_route) ? $request->redirect_route : route('admin.finance-request-approve.index')
            ]);
        }
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
