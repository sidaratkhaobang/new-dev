<?php

namespace App\Traits;

use App\Enums\AssetCarStatusEnum;
use App\Enums\FinanceCarStatusEnum;
use App\Enums\FinanceContractStatusEnum;
use App\Enums\FinanceRequestStatusEnum;
use App\Enums\FinanceStatusEnum;
use App\Enums\RegisterStatusEnum;
use App\Enums\RentalTypeEnum;
use App\Models\AssetAccessory;
use App\Models\AssetCar;
use App\Models\Car;
use App\Models\CMI;
use App\Models\Creditor;
use App\Models\HirePurchase;
use App\Models\ImportCarLine;
use App\Models\InstallEquipment;
use App\Models\InstallEquipmentLine;
use App\Models\InstallEquipmentPurchaseOrder;
use App\Models\InsuranceLot;
use App\Models\LongTermRental;
use App\Models\LongTermRentalLineMonth;
use App\Models\LongTermRentalPRLine;
use App\Models\PrepareFinance;
use App\Models\Register;
use App\Models\VMI;
use Illuminate\Support\Facades\DB;

trait FinanceTrait
{
    static function getLotName($lot_id)
    {
        $name_lot = null;
        if (!empty($lot_id)) {
            $name_lot = InsuranceLot::find($lot_id)?->lot_no;
        }
        return $name_lot;
    }

    static function getVmi($car_id)
    {
        if (empty($car_id)) {
            return [];
        }
        $vmi = VMI::select('*', DB::raw('(sum_insured_car+sum_insured_accessory) as insurance_total'))
            ->where('car_id', $car_id)->insuranceAvailable()->first();

        return $vmi;
    }

    static function getCmi($car_id)
    {
        if (empty($car_id)) {
            return [];
        }
        $cmi = CMI::select('*', DB::raw('(premium+tax) as cmi_total'))
            ->where('car_id', $car_id)->InsuranceAvailable()->first();

        return $cmi;
    }

    static function getFinanceRequestStatusList()
    {
        return collect([
            (object)[
                'id' => FinanceRequestStatusEnum::PENDING,
                'name' => __('finance_request.status_' . FinanceRequestStatusEnum::PENDING),
                'value' => FinanceRequestStatusEnum::PENDING,
            ],
            (object)[
                'id' => FinanceRequestStatusEnum::PENDING_APPROVE,
                'name' => __('finance_request.status_' . FinanceRequestStatusEnum::PENDING_APPROVE),
                'value' => FinanceRequestStatusEnum::PENDING_APPROVE,
            ],
            (object)[
                'id' => FinanceRequestStatusEnum::APPROVE,
                'name' => __('finance_request.status_' . FinanceRequestStatusEnum::APPROVE),
                'value' => FinanceRequestStatusEnum::APPROVE,
            ],
            (object)[
                'id' => FinanceRequestStatusEnum::REJECT,
                'name' => __('finance_request.status_' . FinanceRequestStatusEnum::REJECT),
                'value' => FinanceRequestStatusEnum::REJECT,
            ],
        ]);
    }

    static function getRequestContractStatusList()
    {
        return collect([
            (object)[
                'id' => FinanceContractStatusEnum::PENDING,
                'name' => __('finance_contract.status_' . FinanceContractStatusEnum::PENDING),
                'value' => FinanceContractStatusEnum::PENDING,
            ],
            (object)[
                'id' => FinanceContractStatusEnum::SUCCESS,
                'name' => __('finance_contract.status_' . FinanceContractStatusEnum::SUCCESS),
                'value' => FinanceContractStatusEnum::SUCCESS,
            ],
        ]);
    }

    static function getFinanceStatusList()
    {
        return collect([
            (object)[
                'id' => FinanceStatusEnum::READY,
                'name' => __('finance.status_' . FinanceStatusEnum::READY),
                'value' => FinanceStatusEnum::READY,
            ],
            (object)[
                'id' => FinanceStatusEnum::CLOSE,
                'name' => __('finance.status_' . FinanceStatusEnum::CLOSE),
                'value' => FinanceStatusEnum::CLOSE,
            ],
        ]);
    }

    static function getCarName($car_id)
    {
        $car_name = null;
        if (!empty($car_id)) {
            $car_data = Car::find($car_id);
            if ($car_data?->license_plate) {
                $car_name = __('inspection_cars.license_plate') . ' ' . $car_data?->license_plate;
            } else if ($car_data?->engine_no) {
                $car_name = __('inspection_cars.engine_no') . ' ' . $car_data?->engine_no;
            } else if ($car_data?->chassis_no) {
                $car_name = __('inspection_cars.chassis_no') . ' ' . $car_data?->chassis_no;
            }
        }
        return $car_name;
    }

    static function createHirePurchaseCar($lot_id)
    {
        if (!empty($lot_id)) {
            $check_finance_data = PrepareFinance::where('lot_id', $lot_id)->first();
            if (empty($check_finance_data)) {
                $prepare_finance = new PrepareFinance();
                $prepare_finance->lot_id = $lot_id;
                $prepare_finance->status = FinanceRequestStatusEnum::PENDING;
                $prepare_finance->creation_date = null;
                $prepare_finance->billing_date = null;
                $prepare_finance->payment_date = null;
                $prepare_finance->contact = null;
                $prepare_finance->tel = null;
                $prepare_finance->remark = null;
                $prepare_finance->save();
            }
        }
    }

    static function createHirePurchase($lot_id)
    {
        if (!empty($lot_id)) {
            $data_finance_request = PrepareFinance::select(
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
                ->where('prepare_finances.lot_id', $lot_id)
                ->get();
            if (!empty($data_finance_request)) {
                foreach ($data_finance_request as $key => $value) {
                    $car_data_check = HirePurchase::where('car_id', $value->car_id)->first();
                    if (empty($car_data_check)) {
                        $hire_purchase = new HirePurchase();
                        $hire_purchase->car_id = $value->car_id;
                        $hire_purchase->lot_id = $value->lot_id;
                        $hire_purchase->po_id = $value->po_id;
                        $hire_purchase->status = FinanceContractStatusEnum::PENDING;
                        $hire_purchase->save();
                    }
                }
            }
        }
    }

    static function createAssetCar($lot_id)
    {
        if (!empty($lot_id)) {
            $is_status_register = null;
            $hire_purchase_data = HirePurchase::where('lot_id', $lot_id)->get();
            if (!empty($hire_purchase_data)) {
                foreach ($hire_purchase_data as $item) {
                    $register = Register::where('car_id', $item->car_id)->first();
                    if (!empty($register)) {
                        $is_status_register = $register->status === RegisterStatusEnum::REGISTERED ? true : null;
                    }
                    $asset_car = new AssetCar();
                    $asset_car->car_id = $item->car_id;
                    $asset_car->lot_id = $item->lot_id;
                    $asset_car->po_id = $item->po_id;
                    $asset_car->hire_purchase_id = $item->id;
                    $asset_car->registered_id = $register ? $register->id : null;
                    $asset_car->status = $is_status_register ? AssetCarStatusEnum::COMPLETE : AssetCarStatusEnum::PENDING;
                    $asset_car->save();
                    $install_equipment_pos = InstallEquipmentPurchaseOrder::where('car_id', $asset_car->car_id)->get();
                    if (!empty($install_equipment_pos)) {
                        foreach ($install_equipment_pos as $item_line) {
                            $asset_accessory = new AssetAccessory();
                            $asset_accessory->asset_car_id = $asset_car->id;
                            $asset_accessory->car_id = $asset_car->car_id;
                            $asset_accessory->lot_id = $asset_car->lot_id;
                            $asset_accessory->poa_id = $item_line->id;
                            $asset_accessory->hire_purchase_id = $asset_car->hire_purchase_id;
                            $asset_accessory->save();
                        }
                    }
                }
            }
        }
    }

    static function getRequestFinanceData($lot_no, $rental, $date_create, $status)
    {

        $finance_request = [];
        $data_finance_request = PrepareFinance::select(DB::raw("count(prepare_finances.lot_id) as car_total"), 'prepare_finances.lot_id', 'prepare_finances.status')
            ->leftjoin('import_car_lines', 'import_car_lines.lot_id', 'prepare_finances.lot_id')
            ->groupby('prepare_finances.lot_id', 'prepare_finances.status')
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

    static function getFinanceCarLot($lot_id, $status)
    {
        $list = $data_finance_request = PrepareFinance::select(
            'import_car_lines.id as id',
            'prepare_finances.lot_id',
            'prepare_finances.status',
            'import_car_lines.id as car_id',
            'import_car_lines.import_car_id',
            'import_cars.po_id',
            'import_car_lines.type_car_financing'
        )
            ->join('import_car_lines', 'import_car_lines.lot_id', 'prepare_finances.lot_id')
            ->join('import_cars', 'import_cars.id', 'import_car_lines.import_car_id')
            ->where('prepare_finances.lot_id', $lot_id)
            ->when(!empty($status), function ($query_search) use ($status) {
                $query_search->where('prepare_finances.status', $status);
            })
            ->paginate(PER_PAGE);;
        $list->map(function ($item) {
            $item->accessory_price = FinanceTrait::getTotalAccessoryPrice($item?->car_id);
            $item->car_price_vat = $item?->purchase_order?->total;
            $item->car_total_price = $item->accessory_price + $item->car_price_vat;
            $item->finance_type_list = FinanceTrait::getListFinanceType($item?->car_id);
            return $item;
        });

        return $list;
    }

    static function getTotalAccessoryPrice($car_id)
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

    static function getListFinanceType($car_id)
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
                    'name' => __('finance_request.car_status' . FinanceCarStatusEnum::CAR),
                ],
            ]);
        }
    }

    static function getCustomerData($pr_data)
    {
        $customer_data = null;
        if (!empty($pr_data) && $pr_data?->rental_type == RentalTypeEnum::LONG) {
            $customer_data = LongTermRental::find($pr_data?->reference_id);
        }
        return $customer_data;
    }

    static function getDeliveryDate($pr_data, $car_id)
    {
        $delivery_date = null;
        if (!empty($pr_data) && $pr_data?->rental_type == RentalTypeEnum::LONG) {
            $lt_rental_data = LongTermRental::find($pr_data?->reference_id);
            $delivery_date = $lt_rental_data?->actual_delivery_date;
        } else {
            $delivery_date = ImportCarLine::find($car_id)?->delivery_date;
        }
        return $delivery_date;
    }

    static function getRentalPrice($pr_data)
    {
        if (empty($pr_data)) {
            return null;
        }
        if ($pr_data?->rental_type == RentalTypeEnum::LONG) {
            $rental = LongTermRental::find($pr_data?->reference_id);
            $rental_pr_line = LongTermRentalPRLine::where('lt_rental_id', $rental?->id)?->first();
            $rental_pr_line_month = LongTermRentalLineMonth::where('lt_rental_month_id', $rental_pr_line?->lt_rental_month_id)?->first();
            $rental_price = $rental_pr_line_month?->total_price;
        } else {
            return null;
        }
        return $rental_price;
    }

    static function getRentalDuration($pr_data)
    {
        if (empty($pr_data)) {
            return null;
        }
        if ($pr_data?->rental_type == RentalTypeEnum::LONG) {
            $rental = LongTermRental::find($pr_data?->reference_id);
            $rental_pr_line = LongTermRentalPRLine::where('lt_rental_id', $rental?->id)?->first();
            $rental_duration = $rental_pr_line?->ltMonth?->month;
        } else {
            return null;
        }
        return $rental_duration;
    }


    static function getInstallEquipmentList($car_id)
    {
        if (empty($car_id)) {
            return [];
        }

        $install_equipment = InstallEquipment::where('car_id', $car_id)->get();

        if (empty($install_equipment)) {
            return [];
        }
        $install_equipment_line_datas = [];
        foreach ($install_equipment as $key_install_equipment => $value_install_equipment) {
            $install_equipment_line = InstallEquipment::where('id', $value_install_equipment->id)
                ->get()
                ->map(function ($item) {
                    $model_install_equipment_line = InstallEquipmentLine::where('install_equipment_id', $item?->id);
                    $item->accessory_total_price = $model_install_equipment_line->sum('price');
                    $item->accessory_name_all = FinanceTrait::getAccessoryName($model_install_equipment_line);
                    return $item;
                });
            $install_equipment_line_datas[$value_install_equipment->supplier_id] = !empty($install_equipment_line) ? $install_equipment_line : [];
        }

        return $install_equipment_line_datas;

    }

    static function getAccessoryName($install_equipment_line)
    {
        if (empty($install_equipment_line)) {
            return null;
        }
        $accessory_name = $install_equipment_line->get()
            ->pluck('accessory.name')
            ->toArray();
        return implode(',', $accessory_name);
    }

    static function getExportHeaderSuplier($finance_data_car_ids)
    {
        if (empty($finance_data_car_ids)) {
            return [];
        }
        $header_suplier = InstallEquipment::select(
            'creditors.name',
            'supplier_id')
            ->leftJoin(
                'creditors',
                'creditors.id',
                'install_equipments.supplier_id')
            ->whereIn('car_id', $finance_data_car_ids)
            ->groupBy('supplier_id', 'creditors.name')
            ->get()
            ->map(function ($item) {
                return $item;
            });
        return $header_suplier;
    }

    static function getSummarySupplier($finance_data_car_ids): array
    {
        if (empty($finance_data_car_ids)) {
            return [];
        }
        $total_summary_accessory_prices = InstallEquipment::select(
            'supplier_id',
            DB::raw("sum(install_equipment_lines.price) as total_price")
        )
            ->leftJoin(
                'install_equipment_lines',
                'install_equipment_lines.install_equipment_id',
                'install_equipments.id')
            ->whereIn('install_equipments.car_id', $finance_data_car_ids)
            ->groupBy('supplier_id')
            ->get();
        if (empty($total_summary_accessory_prices)) {
            return [];
        }
        $summary_accessory_prices_arrs = [];
        foreach ($total_summary_accessory_prices as $key => $value) {
            $summary_accessory_prices_arrs[$value->supplier_id] = $value->total_price;
        }
        return $summary_accessory_prices_arrs;
    }

    static function getCarSummaryByModel($data_finance_request): array
    {
        if (empty($data_finance_request)) {
            return [];
        }
        $car_model_summary_arrs = [];
        foreach ($data_finance_request as $key => $value) {
            if (array_key_exists($value->import_car_line_id, $car_model_summary_arrs)) {
                $car_model_summary_arrs[$value->import_car_line_id]['total_price'] += $value->po_price;
            } else {
                $car_model_summary_arrs[$value->import_car_line_id] = [
                    'car_model' => $value->car_model,
                    'total_price' => $value->po_price,
                ];
            }
        }
        return $car_model_summary_arrs;
    }

    static function getAccessorySummary($data_finance_request): array
    {
        if (empty($data_finance_request)) {
            return [];
        }
        $accessory_summary_arrs = [];
        foreach ($data_finance_request as $key => $value) {
            foreach ($value->install_equipment as $key_item => $value_item) {
                if (array_key_exists($key_item, $accessory_summary_arrs)) {
                    $accessory_summary_arrs[$key_item]['total_price'] += $value_item->sum('accessory_total_price');
                } else {
                    $accessory_summary_arrs[$key_item] = [
                        'supplier_name' => FinanceTrait::getCreditorName($key_item),
                        'total_price' => $value_item->sum('accessory_total_price'),
                    ];
                }
            }
        }
        return $accessory_summary_arrs;
    }

    static function getCreditorName($creditor_id)
    {
        $name_creditor = null;
        if (!empty($creditor_id)) {
            $name_creditor = Creditor::find($creditor_id)?->name;
        }
        return $name_creditor;
    }

    static function getAccessoryPriceTotal($car_id): int
    {
        if (empty($car_id)) {
            return 0;
        }
        $install_equipment = InstallEquipment::where('car_id', $car_id)
            ->pluck('id')
            ->toArray();
        if (empty($install_equipment)) {
            return 0;
        }
        $install_equipment_line = InstallEquipmentLine::whereIn('install_equipment_id', $install_equipment)
            ->sum('price');
        return $install_equipment_line;

    }

    static function getSummaryCarPrice($data_finance_request): array
    {
        if (empty($data_finance_request)) {
            return [];
        }

        $table_summary_car_price_data = [
            'po_price' => $data_finance_request->sum('po_price'),
            'accessory_total_with_vat' => $data_finance_request->sum('accessory_total_with_vat'),
            'price_car_with_accessory' => $data_finance_request->sum('price_car_with_accessory'),
            'rental_price' => $data_finance_request->sum('rental_price'),
            'sum_insured_car' => $data_finance_request->sum('sum_insured_car'),
            'sum_insured_accessory' => $data_finance_request->sum('sum_insured_accessory'),
            'insurance_total' => $data_finance_request->sum('insurance_total'),
            'premium' => $data_finance_request->sum('premium'),
            'tax' => $data_finance_request->sum('tax'),
            'cmi_total' => $data_finance_request->sum('cmi_total'),
            'premium_total' => $data_finance_request->sum('premium_total'),
            'car_model_summary' => FinanceTrait::getCarSummaryByModel($data_finance_request),
            'accessory_summary' => FinanceTrait::getAccessorySummary($data_finance_request),
        ];
        return $table_summary_car_price_data;
    }

    static function calRvPrice($total, $rv_percent)
    {
        if (empty($total) || empty($rv_percent)) {
            return 0;
        }
        $rv_price = round(($total * $rv_percent) / 100, 2);
        return number_format($rv_price);
    }
}
