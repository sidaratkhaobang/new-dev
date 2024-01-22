<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\AssetCarStatusEnum;
use App\Enums\AssetCarTypeEnum;
use App\Enums\Resources;
use App\Exports\ExportAssetPostValueCar;
use App\Exports\ExportAssetPostValueSubCar;
use App\Http\Controllers\Controller;
use App\Models\AssetAccessory;
use App\Models\AssetCar;
use App\Models\Car;
use App\Models\CarCharacteristicTransport;
use App\Models\CarClass;
use App\Models\Creditor;
use App\Models\HirePurchase;
use App\Models\InsuranceLot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\FinanceTrait;

class AssetCarController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Asset);
        $status = $request->status;
        $lot_id = $request->lot_id;
        $car_class_id = $request->car_class_id;
        $car_id = $request->car_id;
        $list = AssetCar::select('asset_cars.*')
            ->leftjoin('cars', 'cars.id', '=', 'asset_cars.car_id')
            ->when($car_class_id, function ($query) use ($car_class_id) {
                return $query->where('cars.car_class_id', $car_class_id);
            })
            ->search($request->s, $request)
            ->orderBy('asset_cars.created_at', 'DESC')
            ->paginate(PER_PAGE);
        $list->map(function ($item) {
            $item->lot_no = $item->insuranceLot ?  $item->insuranceLot->lot_no : null;
            $item->car_class_name = $item->car && $item->car->carClass ?  $item->car->carClass->full_name : null;
            $license_plate = $item->car ? $item->car->license_plate : null;
            $engine_no = $item->car ? $item->car->engine_no : null;
            $chassis_no =  $item->car ? $item->car->chassis_no : null;
            $item->car_detail = null;
            if ($license_plate) {
                $item->car_detail = $license_plate . ' / ' . $engine_no . ' / ' . $chassis_no;
            } else {
                $item->car_detail = $engine_no . ' / ' . $chassis_no;
            }

            return $item;
        });
        $status_list = $this->getStatusList();
        $lot_name = FinanceTrait::getLotName($lot_id);
        $car_class = CarClass::find($car_class_id);
        $car_class_name = ($car_class) ? $car_class->full_name . ' - ' . $car_class->name : null;
        $car = Car::find($car_id);
        $car_name = null;
        if ($car) {
            if ($car->license_plate) {
                $car_name = $car->license_plate;
            } else if ($car->engine_no) {
                $car_name = __('inspection_cars.engine_no') . ' ' . $car->engine_no;
            } else if ($car->chassis_no) {
                $car_name = __('inspection_cars.chassis_no') . ' ' . $car->chassis_no;
            }
        }

        return view('admin.asset-cars.index', [
            'list' => $list,
            'status_list' => $status_list,
            'status' => $status,
            'lot_name' => $lot_name,
            'lot_id' => $lot_id,
            'car_class_name' => $car_class_name,
            'car_class_id' => $car_class_id,
            'car_id' => $car_id,
            'car_name' => $car_name,
        ]);
    }

    public function show(AssetCar $asset_car)
    {
        $this->authorize(Actions::View . '_' . Resources::Asset);
        $car = Car::find($asset_car->car_id);
        if (!empty($car)) {
            $asset_car->car_class_name = $car->carClass ? $car->carClass->full_name : null;
            $asset_car->engine_no = $car->engine_no ? $car->engine_no : null;
            $asset_car->chassis_no = $car->chassis_no ? $car->chassis_no : null;
            $asset_car->car_price = $car->purchase_price ? $car->purchase_price : null;
            $asset_car->license_plate = $car->license_plate ? $car->license_plate : null;
            $asset_car->registration_date = $car->registered_date ? get_thai_date_format($car->registered_date, 'd/m/Y') : null;
            $car_age = Carbon::now()->diff($car->registered_date);
            $asset_car->car_age = $car_age->y . " ปี " . $car_age->m . " เดือน " . $car_age->d . " วัน";
            $asset_car->cc = $car->carClass->engine_size ? $car->carClass->engine_size : null;
            $asset_car->car_group = $car->carCategory && $car->carCategory->carGroup ? $car->carCategory->carGroup->name : null;
            $asset_car->cost_center = null;
            if (!is_null($asset_car->license_plate)) {
                $string_replace = str_replace(' ', '', $asset_car->license_plate);
                $string_len = mb_strlen($string_replace);
                if ($string_len < 8) {
                    $asset_car->cost_center = '05Z' . $string_replace;
                } else {
                    $asset_car->cost_center = '5Z' . $string_replace;
                }
            }
        }
        $hire_purchase = HirePurchase::find($asset_car->hire_purchase_id);
        if (!empty($hire_purchase)) {
            $asset_car->contract_no = $hire_purchase->contract_no ? $hire_purchase->contract_no : null;
            $asset_car->contract_start_date = $hire_purchase->contract_start_date ? get_thai_date_format($hire_purchase->contract_start_date, 'd/m/Y') : null;
            $asset_car->contract_end_date = $hire_purchase->contract_end_date ? get_thai_date_format($hire_purchase->contract_end_date, 'd/m/Y') : null;
            $asset_car->asset_value_date = $hire_purchase->contract_start_date ? date('d.m.y', strtotime($hire_purchase->contract_start_date)) : null;
        }
        $asset_car->po_no = $asset_car->purchaseOrder ? $asset_car->purchaseOrder->po_no : null;
        $asset_car->lot_no = $asset_car->insuranceLot ? $asset_car->insuranceLot->lot_no : null;
        $asset_car->line_item_text = null;
        if (!empty($asset_car->insuranceLot)) {
            $creditor = Creditor::find($asset_car->insuranceLot->leasing_id);
            $asset_car->leasing_name = $creditor ? $creditor->name : null;
            $asset_car->line_item_text = 'ซื้อรถ-' . $asset_car->leasing_name . $asset_car->lot_no;
        }
        $asset_car->valid_from = date('01.01.Y');
        $asset_car->asset_class = null;
        if ($asset_car->register) {
            $car_characteristic_transport_id = $asset_car->register->car_characteristic_transport_id;
            if ($car_characteristic_transport_id) {
                $car_characteristic_transport = CarCharacteristicTransport::find($car_characteristic_transport_id);
                $asset_car->asset_class = ($car_characteristic_transport) ? $car_characteristic_transport->asset_no : null;
            }
        }

        $value = null;
        if (strcmp($asset_car->asset_class, '61050001') == 0) {
            $value = 25;
        } elseif (strcmp($asset_car->asset_class, '61050002') == 0) {
            $value = 30;
        } elseif (strcmp($asset_car->asset_class, '61050003') == 0) {
            $value = 20;
        } elseif (strcmp($asset_car->asset_class, '61050004') == 0) {
            $value = 35;
        }
        if ($value) {
            $asset_car->scrap = ($asset_car->car_price * $value) / 100;
        } else {
            $asset_car->scrap = '1';
        }

        $asset_accessory = AssetAccessory::leftjoin('install_equipment_purchase_orders', 'install_equipment_purchase_orders.id', '=', 'asset_accessories.poa_id')
            ->leftjoin('install_equipment_po_lines', 'install_equipment_po_lines.install_equipment_po_id', '=', 'install_equipment_purchase_orders.id')
            ->leftjoin('accessories', 'accessories.id', '=', 'install_equipment_po_lines.accessory_id')
            ->where('asset_accessories.asset_car_id', $asset_car->id)
            ->select(
                'install_equipment_purchase_orders.worksheet_no',
                DB::raw("group_concat(accessories.name  SEPARATOR '+ ')  as accessory_name"),
                'install_equipment_po_lines.subtotal',
            )
            ->addSelect('install_equipment_po_lines.amount')
            ->groupBy(
                'install_equipment_purchase_orders.worksheet_no',
                'install_equipment_po_lines.subtotal',
                'install_equipment_po_lines.amount',
            )
            ->get();
        $accessory_price = 0;
        foreach ($asset_accessory as $item_accessory) {
            $accessory_price += $item_accessory->subtotal;
        }

        $page_title = __('lang.view') . __('asset_cars.page_title');
        return view('admin.asset-cars.form', [
            'd' => $asset_car,
            'page_title' => $page_title,
            'asset_accessory' => $asset_accessory,
            'accessory_price' => $accessory_price,
            'view' => true,
        ]);
    }

    public function getStatusList()
    {
        return collect([
            (object) [
                'id' => AssetCarStatusEnum::PENDING,
                'name' => __('asset_cars.status_' . AssetCarStatusEnum::PENDING),
                'value' => AssetCarStatusEnum::PENDING,
            ],
            (object) [
                'id' => AssetCarStatusEnum::COMPLETE,
                'name' => __('asset_cars.status_' . AssetCarStatusEnum::COMPLETE),
                'value' => AssetCarStatusEnum::COMPLETE,
            ],
        ]);
    }

    public function getAssetExcelList(Request $request)
    {
        $lot_id = $request->lot_id;
        $car_id = $request->car_id;
        $car_class_id = $request->car_class_id;
        $status = $request->status;
        $excel_type = $request->excel_type;
        if (in_array($excel_type, [AssetCarTypeEnum::COST_CENTER, AssetCarTypeEnum::ASSET_MASTER_CAR, AssetCarTypeEnum::POST_VALUE_CAR])) {
            $list = AssetCar::leftjoin('cars', 'cars.id', '=', 'asset_cars.car_id')
                ->when($car_class_id, function ($query) use ($car_class_id) {
                    return $query->where('cars.car_class_id', $car_class_id);
                })
                ->select('asset_cars.*')
                ->search(null, $request)
                ->orderBy('asset_cars.created_at', 'DESC')
                ->get();
        }
        if (in_array($excel_type, [AssetCarTypeEnum::ASSET_MASTER_SUB_CAR, AssetCarTypeEnum::POST_VALUE_SUB_CAR])) {
            $list = AssetCar::leftjoin('cars', 'cars.id', '=', 'asset_cars.car_id')
                ->join('asset_accessories', 'asset_accessories.asset_car_id', '=', 'asset_cars.id')
                ->when($car_class_id, function ($query) use ($car_class_id) {
                    return $query->where('cars.car_class_id', $car_class_id);
                })
                ->select('asset_cars.*')
                ->search(null, $request)
                ->distinct()
                ->orderBy('asset_cars.created_at', 'DESC')
                ->get();
        }

        $list->map(function ($item) {
            $item->car_class_text = $item->car && $item->car->carClass ?  $item->car->carClass->full_name : null;
            $item->lot_no = $item->insuranceLot ? $item->insuranceLot->lot_no : null;
            $item->engine_no = $item->car ? $item->car->engine_no : null;
            $item->chassis_no = $item->car ? $item->car->chassis_no : null;
            $item->license_plate = $item->car ? $item->car->license_plate : null;
            return $item;
        });

        return response()->json($list);
    }

    public function exportAssetCar(Request $request)
    {

        $excel_type = $request->excel_type;
        $asset_car_ids = $request->asset_car_ids;
        if (!is_array($asset_car_ids)) {
            return $this->responseWithCode(false, __('lang.store_error_title'), null, 422);
        }

        $asset_cars = AssetCar::select('*')
            ->whereIn('id', $asset_car_ids)->get()->map(function ($item) {
                $car = Car::find($item->car_id);
                if (!empty($car)) {
                    $item->car_class_name = $car->carClass ? $car->carClass->full_name : null;
                    $item->engine_no = $car->engine_no ? $car->engine_no : null;
                    $item->car_price = $car->purchase_price ? $car->purchase_price : null;
                    $item->license_plate = $car->license_plate ? $car->license_plate : null;
                    $item->cc = $car->carClass->engine_size ? $car->carClass->engine_size : null;
                    $item->cost_center = null;
                    if (!is_null($item->license_plate)) {
                        $string_replace = str_replace(' ', '', $item->license_plate);
                        $string_len = mb_strlen($string_replace);
                        if ($string_len < 8) {
                            $item->cost_center = '05Z' . $string_replace;
                        } else {
                            $item->cost_center = '5Z' . $string_replace;
                        }
                    }
                }
                $hire_purchase = HirePurchase::find($item->hire_purchase_id);
                if (!empty($hire_purchase)) {
                    $item->asset_value_date = $hire_purchase->contract_start_date ? date('d.m.y', strtotime($hire_purchase->contract_start_date)) : null;
                }
                $item->po_no = $item->purchaseOrder ? $item->purchaseOrder->po_no : null;
                $item->lot_no = $item->insuranceLot ? $item->insuranceLot->lot_no : null;
                $item->line_item_text = null;
                if (!empty($item->insuranceLot)) {
                    $creditor = Creditor::find($item->insuranceLot->leasing_id);
                    $item->leasing_name = $creditor ? $creditor->name : null;
                    $item->line_item_text = 'ซื้อรถ-' . $item->leasing_name . $item->lot_no;
                }
                $item->valid_from = date('01.01.Y');
                $item->asset_class = null;
                if ($item->register) {
                    $car_characteristic_transport_id = $item->register->car_characteristic_transport_id;
                    if ($car_characteristic_transport_id) {
                        $car_characteristic_transport = CarCharacteristicTransport::find($car_characteristic_transport_id);
                        $item->asset_class = ($car_characteristic_transport) ? $car_characteristic_transport->asset_no : null;
                    }
                }

                $value = null;
                if (strcmp($item->asset_class, '61050001') == 0) {
                    $value = 25;
                } elseif (strcmp($item->asset_class, '61050002') == 0) {
                    $value = 30;
                } elseif (strcmp($item->asset_class, '61050003') == 0) {
                    $value = 20;
                } elseif (strcmp($item->asset_class, '61050004') == 0) {
                    $value = 35;
                }
                if ($value) {
                    $item->scrap = ($item->car_price * $value) / 100;
                } else {
                    $item->scrap = '1';
                }

                $item->main_asset = format_license_plate($item->license_plate);

                return $item;
            });

        if (strcmp($excel_type, AssetCarTypeEnum::COST_CENTER) == 0) {
            if (count($asset_cars) > 0) {
                return (new FastExcel($asset_cars))->download('file.xlsx', function ($line) {
                    return [
                        'Old_Controlling Area' => '0009',
                        'New Controlling Area' => '1000',
                        'Old_Company Code' => '0009',
                        'New_Company Code' => '1000',
                        'New_Cost Center' => $line->cost_center,
                        'Valid From' => $line->valid_from,
                        'Valid To' => '31.12.9999',
                        'Name' => $line->car_class_name,
                        'Description' => $line->car_class_name,
                        'Person responsible' => 'Chay Bor.',
                        'User Responsible' => '',
                        'Department' => 'Vehicles',
                        'Old_CCTR Category' => '',
                        'New_CCTR Category' => 'C',
                        'Old_Hierarchy area' => '',
                        'New_Hierarchy area' => 'TLS',
                        'Business Area' => '',
                        'Old_Currency' => '',
                        'New_Currency' => 'THB',
                        'Old_Profit Center' => '',
                        'New_Profit Center' => '',
                        'Record Quantity' => '',
                        'Actual primary costs' => '',
                        'Plan primary costs' => '',
                        'Act. secondary costs' => '',
                        'Plan secondary costs' => '',
                        'Actual revenues' => '',
                        'Plan revenues' => '',
                        'Commitment update' => '',
                        'Title' => '',
                        'Name 1' => '',
                        'Name 2' => '',
                        'Name 3' => '',
                        'Name 4' => '',
                        'Street name' => '',
                        'Location' => '',
                        'District' => '',
                        'Country' => '',
                        'Jurisdiction' => '',
                        'PO Box' => '',
                        'Postal Code' => '',
                        'PO Box PostCode' => '',
                        'Region' => '',
                        'Language Key' => '',
                        'Telephone 1' => '',
                        'Telephone 2' => '',
                        'Telebox number' => '',
                        'Telex number' => '',
                        'Fax Number' => '',
                        'Teletex number' => '',
                        'Printer destination' => '',
                        'Data line' => '',
                    ];
                });
            }
        } elseif (strcmp($excel_type, AssetCarTypeEnum::ASSET_MASTER_CAR) == 0) {
            if (count($asset_cars) > 0) {
                return (new FastExcel($asset_cars))->download('file.xlsx', function ($line) {
                    return [
                        'Company Code' => '1005',
                        'Asset Class' => $line->asset_class,
                        'Main Asset' => $line->main_asset,
                        'Sub Asset' => '0',
                        'Description 1' => $line->car_class_name,
                        'Description 2' => $line->car_class_name,
                        'SERIAL' => $line->engine_no,
                        'Inventory No.' => '',
                        'Inventory Note' => $line->po_no,
                        'Quantity' => '',
                        'Unit' => 'EA',
                        'Cost Center' => $line->cost_center,
                        'PLANT' => '',
                        'LOCATION' => '',
                        'Fund Code' => 'DZZCAR0501',
                        'Fund Center' => $line->cost_center,
                        'Evaluation Group 1' => $line->cc,
                        'Evaluation Group 2' => '',
                        'Evaluation Group 3' => '',
                        'Evaluation Group 4' => '',
                        'Evaluation Group 5' => '',
                        'VENDOR' => '',
                        'Agreement Date' => '',
                        'Leasing Date' => '',
                        'BASE VALUE AS NEW' => '',
                        'Dep. KEY01' => 'Z001',
                        'UL YEAR01' => '5',
                        'UL PERIOD01' => '',
                        'DEP ST DATE01' => '',
                        'SCRAP01' => $line->scrap,
                        'Dep. KEY02' => 'Z001',
                        'UL YEAR02' => '5',
                        'UL PERIOD02' => '',
                        'DEP ST DATE02' => '',
                        'SCRAP02' => $line->scrap,
                        'Dep. KEY03' => 'Z001',
                        'UL YEAR03' => '5',
                        'UL PERIOD03' => '',
                        'DEP ST DATE03' => '',
                        'SCRAP03' => $line->scrap,
                    ];
                });
            }
        } elseif (strcmp($excel_type, AssetCarTypeEnum::ASSET_MASTER_SUB_CAR) == 0) {
            $asset_accessory = AssetAccessory::leftjoin('install_equipment_purchase_orders', 'install_equipment_purchase_orders.id', '=', 'asset_accessories.poa_id')
                ->leftjoin('install_equipment_lines', 'install_equipment_lines.install_equipment_id', '=', 'install_equipment_purchase_orders.install_equipment_id')
                ->leftjoin('accessories', 'accessories.id', '=', 'install_equipment_lines.accessory_id')
                ->whereIn('asset_accessories.asset_car_id', $asset_car_ids)
                ->select(
                    'asset_accessories.*',
                    'accessories.name as accessory_name',
                    'install_equipment_purchase_orders.subtotal',
                )
                ->get()->map(function ($item) {
                    $car = Car::find($item->car_id);
                    if (!empty($car)) {
                        $item->engine_no = $car->engine_no ? $car->engine_no : null;
                        $item->license_plate = $car->license_plate ? $car->license_plate : null;
                        $item->cost_center = null;
                        if (!is_null($item->license_plate)) {
                            $string_replace = str_replace(' ', '', $item->license_plate);
                            $string_len = mb_strlen($string_replace);
                            if ($string_len < 8) {
                                $item->cost_center = '05Z' . $string_replace;
                            } else {
                                $item->cost_center = '5Z' . $string_replace;
                            }
                        }
                    }
                    $item->op_no = $item->installEquipmentPurchaseOrder && $item->installEquipmentPurchaseOrder->install_equipment ? $item->installEquipmentPurchaseOrder->install_equipment->worksheet_no : null;
                    $item->asset_class = null;
                    $asset_car_data = AssetCar::find($item->asset_car_id);
                    if ($asset_car_data) {
                        $car_characteristic_transport_id = $asset_car_data->register->car_characteristic_transport_id;
                        if ($car_characteristic_transport_id) {
                            $car_characteristic_transport = CarCharacteristicTransport::find($car_characteristic_transport_id);
                            $item->asset_class = ($car_characteristic_transport) ? $car_characteristic_transport->asset_no : null;
                        }
                    }

                    $item->main_asset = format_license_plate($item->license_plate);

                    return $item;
                });
            if (count($asset_accessory) > 0) {
                return (new FastExcel($asset_accessory))->download('file.xlsx', function ($line) {
                    return [
                        'Company Code' => '1005',
                        'Asset Class' => $line->asset_class,
                        'Main Asset' => $line->main_asset,
                        'Sub Asset' => '1',
                        'Description 1' => $line->accessory_name,
                        'Description 2' => $line->accessory_name,
                        'SERIAL' => $line->engine_no,
                        'Inventory No.' => '',
                        'Inventory Note' => $line->op_no,
                        'Quantity' => '',
                        'Unit' => 'EA',
                        'Cost Center' => $line->cost_center,
                        'PLANT' => '',
                        'LOCATION' => '',
                        'Fund Code' => 'DZZCAR0501',
                        'Fund Center' => $line->cost_center,
                        'Evaluation Group 1' => '',
                        'Evaluation Group 2' => '',
                        'Evaluation Group 3' => '',
                        'Evaluation Group 4' => '',
                        'Evaluation Group 5' => '',
                        'VENDOR' => '',
                        'Agreement Date' => '',
                        'Leasing Date' => '',
                        'BASE VALUE AS NEW' => '',
                        'Dep. KEY01' => 'Z001',
                        'UL YEAR01' => '5',
                        'UL PERIOD01' => '',
                        'DEP ST DATE01' => '',
                        'SCRAP01' => '1',
                        'Dep. KEY02' => 'Z001',
                        'UL YEAR02' => '5',
                        'UL PERIOD02' => '',
                        'DEP ST DATE02' => '',
                        'SCRAP02' => '1',
                        'Dep. KEY03' => 'Z001',
                        'UL YEAR03' => '5',
                        'UL PERIOD03' => '',
                        'DEP ST DATE03' => '',
                        'SCRAP03' => '1',
                    ];
                });
            }
        } elseif (strcmp($excel_type, AssetCarTypeEnum::POST_VALUE_CAR) == 0) {
            $asset_lot = AssetCar::select('lot_id', DB::raw('count(lot_id) as lot_count'))
                ->groupBy('lot_id')->whereIn('id', $asset_car_ids)->get();
            $asset_lot->map(function ($item) use ($asset_car_ids) {
                $asset_cars = AssetCar::select('*')
                    ->whereIn('id', $asset_car_ids)
                    ->where('lot_id', $item->lot_id)->get()->map(function ($item_car) {
                        $car = Car::find($item_car->car_id);
                        if (!empty($car)) {
                            $item_car->car_price = $car->purchase_price ? $car->purchase_price : null;
                            $item_car->license_plate = $car->license_plate ? $car->license_plate : null;
                        }
                        $hire_purchase = HirePurchase::find($item_car->hire_purchase_id);
                        if (!empty($hire_purchase)) {
                            $item_car->asset_value_date = $hire_purchase->contract_start_date ? date('d.m.y', strtotime($hire_purchase->contract_start_date)) : null;
                        }
                        $item_car->lot_no = $item_car->insuranceLot ? $item_car->insuranceLot->lot_no : null;
                        $item_car->line_item_text = null;
                        if (!empty($item_car->insuranceLot)) {
                            $creditor = Creditor::find($item_car->insuranceLot->leasing_id);
                            $item_car->leasing_name = $creditor ? $creditor->name : null;
                            $item_car->line_item_text = 'ซื้อรถ-' . $item_car->leasing_name . $item_car->lot_no;
                        }
                        $item_car->main_asset = format_license_plate($item_car->license_plate);
                        return $item_car;
                    });

                $item->asset_cars = $asset_cars;
                $item->arr_add =
                    [
                        'flag' => '*',
                        'posting_key' => '50',
                        'gl' => '149999010',
                        'fund_code' => 'DZZCAR0501',
                        'foreign' => '',
                        'local' => 0,
                        'cost_center' => '05A00AC000',
                        'all_locataion' => $item->asset_cars[0]['lot_no'],
                        'transaction' => '',
                        'asset_date' => $item->asset_cars[0]['asset_value_date'],
                        'quantity' => '',
                        'unit' => '',
                        'line_text' => $item->asset_cars[0]['line_item_text'],
                    ];
                return $item;
            });
            return Excel::download(new ExportAssetPostValueCar($asset_lot), 'template.xlsx');
        } elseif (strcmp($excel_type, AssetCarTypeEnum::POST_VALUE_SUB_CAR) == 0) {
            $accessory_lot = AssetAccessory::select('lot_id', 'car_id')
                ->groupBy('lot_id', 'car_id')->whereIn('asset_car_id', $asset_car_ids)->get();
            $accessory_lot->map(function ($item) use ($asset_car_ids) {
                $asset_accessory = AssetAccessory::leftjoin('install_equipment_purchase_orders', 'install_equipment_purchase_orders.id', '=', 'asset_accessories.poa_id')
                    ->leftjoin('install_equipment_po_lines', 'install_equipment_po_lines.install_equipment_po_id', '=', 'install_equipment_purchase_orders.id')
                    ->leftjoin('accessories', 'accessories.id', '=', 'install_equipment_po_lines.accessory_id')
                    ->whereIn('asset_accessories.asset_car_id', $asset_car_ids)
                    ->where('asset_accessories.lot_id', $item->lot_id)
                    ->where('asset_accessories.car_id', $item->car_id)
                    ->select(
                        'asset_accessories.*',
                        'accessories.name as accessory_name',
                        'install_equipment_po_lines.subtotal',
                    )
                    ->get()
                    ->map(function ($item_accessory) {
                        $car = Car::find($item_accessory->car_id);
                        if (!empty($car)) {
                            $item_accessory->license_plate = $car->license_plate ? $car->license_plate : null;
                        }
                        $item_accessory->main_asset = format_license_plate($item_accessory->license_plate);
                        $item_accessory->lot_no = $item_accessory->insuranceLot ? $item_accessory->insuranceLot->lot_no : null;
                        $hire_purchase = HirePurchase::find($item_accessory->hire_purchase_id);
                        if (!empty($hire_purchase)) {
                            $item_accessory->asset_value_date = $hire_purchase->contract_start_date ? date('d.m.y', strtotime($hire_purchase->contract_start_date)) : null;
                        }

                        return $item_accessory;
                    });
                $item->asset_accessory = $asset_accessory;
                $text_date = date('m/y');
                $item->arr_add =
                    [
                        'flag' => '*',
                        'posting_key' => '50',
                        'gl' => '149999010',
                        'fund_code' => 'DZZCAR0501',
                        'foreign' => '',
                        'local' => 0,
                        'cost_center' => '05A00AC000',
                        'all_locataion' => $item->asset_accessory[0]['lot_no'],
                        'transaction' => '',
                        'asset_date' => $item->asset_accessory[0]['asset_value_date'],
                        'quantity' => '',
                        'unit' => '',
                        'line_text' => 'อุปกรณ์ติดรถยนต์' . $text_date . '(' . $item->asset_accessory[0]['lot_no'] .  ')',
                    ];
                return $item;
            });
            return Excel::download(new ExportAssetPostValueSubCar($accessory_lot), 'template.xlsx');
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }
}
