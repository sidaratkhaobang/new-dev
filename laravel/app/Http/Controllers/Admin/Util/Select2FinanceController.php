<?php

namespace App\Http\Controllers\Admin\Util;

use App\Enums\CreditorTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CreditorTypeRelation;
use App\Models\HirePurchase;
use App\Models\InsuranceLot;
use App\Models\PrepareFinance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Select2FinanceController extends Controller
{
    public function getCreditorLeasingList(Request $request)
    {
        $search = $request?->s;
        $creditor_leasing_list = [];
        $creditor_leasing_list = CreditorTypeRelation::leftJoin('creditors', 'creditors.id', 'creditor_id')
            ->leftJoin('creditor_types', 'creditor_types.id', 'creditor_type_id')
            ->select('creditors.name', 'creditors.id as id')
            ->when(!empty($search), function ($query_search) use ($search) {
                $query_search->where('creditors.name', 'Like', '%' . $search . '%');
            })
            ->where('creditors.status', STATUS_ACTIVE)
            ->where('creditor_types.type', CreditorTypeEnum::LEASING)
            ->limit(30)
            ->get()->map(function ($item) {
                $item->id = $item->id;
                $item->text = $item->name;
                return $item;
            });
        return $creditor_leasing_list;

    }

    public function getLotList(Request $request)
    {
        $search = $request?->s;
        $lot_list = [];
        $lot_list = InsuranceLot::select('lot_no as name', 'id')
            ->when($search, function ($query_search) use ($search) {
                $query_search->where('lot_no', 'Like', '%' . $search . '%');
            })
            ->limit(30)
            ->get()
            ->map(function ($item) {
                $item->id = $item->id;
                $item->text = $item->name;
                return $item;
            });
        return $lot_list;
    }

    public function getCarList(Request $request)
    {
        $search = $request?->s;
        $listCar = Car::select('id', 'license_plate', 'engine_no', 'chassis_no')
            ->when(!empty($search), function ($query_search) use ($search) {
                $query_search->where('license_plate', 'like', '%' . $search . '%');
                $query_search->orWhere('engine_no', 'like', '%' . $search . '%');
                $query_search->orWhere('chassis_no', 'like', '%' . $search . '%');
            })
            ->get()
            ->map(function ($item) {
                $text = null;
                if ($item?->license_plate) {
                    $text = __('inspection_cars.license_plate') . ' ' . $item?->license_plate;
                } else if ($item?->engine_no) {
                    $text = __('inspection_cars.engine_no') . ' ' . $item?->engine_no;
                } else if ($item?->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $item?->chassis_no;
                }
                $item->id = $item->id;
                $item->text = $text;
                return $item;
            });
        return response()->json($listCar);
    }

    public function getContract(Request $request)
    {
        $search = $request->s;
        $contract_no = HirePurchase::when(!empty($search), function ($query_search) use ($search) {
            $query_search->where('contract_no', 'Like', '%' . $search . '%');
        })
            ->get()
            ->map(function ($item) {
                $item->id = $item->id;
                $item->text = $item->contract_no;
                return $item;
            });

        return $contract_no;
    }

    public function getFinanceRequestExportData(Request $request)
    {
        $lot_no = $request->lot_no;
        $rental = $request->rental;
        $date_create = $request->date_create;
        $status = $request->status;
        $finance_car_lot_id = $request->finance_car_lot_id;
        $data_finance_request = PrepareFinance::select(
            DB::raw("count(prepare_finances.lot_id) as car_total"),
            'prepare_finances.lot_id',
            'prepare_finances.status',)
            ->leftjoin('import_car_lines', 'import_car_lines.lot_id', 'prepare_finances.lot_id')
            ->groupby('prepare_finances.lot_id', 'prepare_finances.status')
            ->when(!empty($finance_car_lot_id), function ($query_search) use ($finance_car_lot_id) {
                $query_search->whereNotin('prepare_finances.lot_id', $finance_car_lot_id);
            })
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
            ->get()->map(function ($item) {
                $item->lot_no = $item?->insurance_lot?->lot_no;
                $item->leasing = $item?->insurance_lot?->creditor?->name;
                $item->badge_status = __('finance_request.status_' . $item?->status);
                $item->badge_class = __('finance_request.status_' . $item?->status . '_class');
                return $item;
            });

        return response()->json([
            'data' => $data_finance_request,
            'success' => true,
        ]);
    }

    public function getFinanceContractExportData(Request $request)
    {
        $lot_no = $request->lot_no;
        $rental = $request->rental;
        $date_create = $request->date_create;
        $status = $request->status;
        $car_id = $request->car_id;
        $contract_no = $request->contract_no;
        $contract_start_date = $request->contract_start_date;
        $contract_end_date = $request->contract_end_date;
        $finance_car_id = $request->finance_car_id;
        $data_finance_request = HirePurchase::select(
            'hire_purchases.car_id',
            'hire_purchases.lot_id',
            'hire_purchases.status',
            'hire_purchases.finance_date',
            'hire_purchases.contract_no'
        )
            ->when(!empty($finance_car_id), function ($query_search) use ($finance_car_id) {
                $query_search->whereNotin('hire_purchases.car_id', $finance_car_id);
            })
            ->when(!empty($car_id), function ($query_search) use ($car_id) {
                $query_search->where('hire_purchases.car_id', $car_id);
            })
            ->when(!empty($contract_start_date), function ($query_search) use ($contract_start_date) {
                $query_search->where('hire_purchases.contract_start_date', $contract_start_date);
            })
            ->when(!empty($contract_end_date), function ($query_search) use ($contract_end_date) {
                $query_search->where('hire_purchases.contract_end_date', $contract_end_date);
            })
            ->when(!empty($contract_no), function ($query_search) use ($contract_no) {
                $query_search->where('hire_purchases.contract_no', $contract_no);
            })
            ->when(!empty($lot_no), function ($query_search) use ($lot_no) {
                $query_search->where('hire_purchases.lot_id', $lot_no);
            })
            ->when(!empty($rental), function ($query_search) use ($rental) {

            })
            ->when(!empty($date_create), function ($query_search) use ($date_create) {
                $query_search->whereDate('hire_purchases.creation_date', $date_create);
            })
            ->when(!empty($status), function ($query_search) use ($status) {
                $query_search->where('hire_purchases.status', $status);
            })
            ->get()->map(function ($item) {
                $item->license_plate = $item?->car?->license_plate;
                $item->engine_no = $item?->car?->engine_no;
                $item->chassis_no = $item?->car?->chassis_no;
                $item->lot_no = $item?->insurance_lot?->lot_no;
                $item->leasing = $item?->insurance_lot?->creditor?->name;
                $item->badge_status = __('finance_request.status_' . $item?->status);
                $item->badge_class = __('finance_request.status_' . $item?->status . '_class');
                return $item;
            });
        return response()->json([
            'data' => $data_finance_request,
            'success' => true,
        ]);
    }
}
