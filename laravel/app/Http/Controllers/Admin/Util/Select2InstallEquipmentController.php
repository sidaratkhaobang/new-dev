<?php

namespace App\Http\Controllers\Admin\Util;

use App\Enums\CarEnum;
use App\Enums\CreditorTypeEnum;
use App\Enums\ImportCarLineStatusEnum;
use App\Enums\LongTermRentalTypeEnum;
use App\Enums\POStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Accessories;
use App\Models\Bom;
use App\Models\Car;
use App\Models\Creditor;
use App\Models\ImportCarLine;
use App\Models\InstallEquipment;
use App\Models\InstallEquipmentPurchaseOrder;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class Select2InstallEquipmentController extends Controller
{

    public function getAccessoryList(Request $request)
    {
        $accessories = Accessories::select('id', 'name', 'code')
            ->where('status', STATUS_ACTIVE)
            ->whereNotNull('creditor_id')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('accessories.name', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->accessory_id)) {
                    $query->where('accessories.id', $request->accessory_id);
                }
                if (!empty($request->supplier_id)) {
                    $query->where('accessories.creditor_id', $request->supplier_id);
                }
            })
            ->orderBy('name')
            ->limit(50)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => ($item->code) ? $item->code . ' - ' . $item->name : $item->name
                ];
            });
        return response()->json($accessories);
    }

    public function getAccessoryCodeList(Request $request)
    {
        $accessories = Accessories::select('id', 'code')
            ->where('status', STATUS_ACTIVE)
            ->whereNotNull('creditor_id')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    // $query->where('accessories.code', 'like', '%' . $request->s . '%');
                    $query->orWhere('accessories.code', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->accessory_id)) {
                    $query->where('accessories.id', $request->accessory_id);
                }
                if (!empty($request->supplier_id)) {
                    $query->where('accessories.creditor_id', $request->supplier_id);
                }
            })
            ->orderBy('code')
            ->limit(50)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->code
                ];
            });
        return response()->json($accessories);
    }

    public function getAccessorySupplierList(Request $request)
    {
        $suppliers = Creditor::leftjoin('creditors_types_relation', 'creditors_types_relation.creditor_id', '=', 'creditors.id')
            ->leftjoin('creditor_types', 'creditor_types.id', '=', 'creditors_types_relation.creditor_type_id')
            ->leftjoin('accessories', 'accessories.creditor_id', '=', 'creditors.id')
            ->select('creditors.id', 'creditors.name')
            ->where('creditor_types.type', CreditorTypeEnum::ACCESSORIES)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('creditors.name', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->accessory_id)) {
                    $query->where('accessories.id', $request->accessory_id);
                }
            })
            ->orderBy('creditors.name')
            ->limit(50)
            ->distinct('creditors.id')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($suppliers);
    }

    public function getPurchaseOrderList(Request $request)
    {
        $purchase_orders = PurchaseOrder::whereIn('status', [POStatusEnum::CONFIRM, POStatusEnum::COMPLETE])
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('po_no', 'like', '%' . $request->s . '%');
                }
            })
            ->select('id', 'po_no')
            ->orderBy('po_no')
            ->limit(50)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->po_no
                ];
            });
        return response()->json($purchase_orders);
    }

    public function getPurchaseOrderCarExistedList(Request $request)
    {

        $list = ImportCarLine::leftJoin('purchase_order_lines', 'purchase_order_lines.id', '=', 'import_car_lines.po_line_id')
            ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_lines.purchase_order_id')
            ->leftJoin('creditors', 'creditors.id', '=', 'purchase_orders.creditor_id')
            ->addSelect('purchase_orders.po_no as text', 'purchase_orders.id as id')
            ->where('import_car_lines.status_delivery', ImportCarLineStatusEnum::PENDING_DELIVERY)
            ->orWhere('import_car_lines.status', ImportCarLineStatusEnum::CONFIRM_DATA)
            ->distinct('purchase_orders.id')
            ->orderBy('purchase_orders.po_no')
            ->limit(50)
            ->get();
        return response()->json($list);
    }

    public function getSupplierList(Request $request)
    {
        $install_equipments = InstallEquipment::all();
        $suppliers = Creditor::whereIn('id', $install_equipments->pluck('supplier_id'))
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name', 'like', '%' . $request->s . '%');
                }
            })
            ->select('id', 'name')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($suppliers);
    }

    public function getInstallEquipmentBySupplierList(Request $request)
    {
        $install_equipments = InstallEquipment::select('id', 'worksheet_no')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('worksheet_no', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('supplier_id', $request->parent_id);
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->worksheet_no
                ];
            });
        return response()->json($install_equipments);
    }

    public function getInstallEquipmentPOBySupplierList(Request $request)
    {
        $install_equipment_pos = InstallEquipmentPurchaseOrder::leftjoin('install_equipments', 'install_equipments.id', '=', 'install_equipment_purchase_orders.install_equipment_id')
            ->select('install_equipments.id', 'install_equipment_purchase_orders.worksheet_no')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('install_equipment_purchase_orders.worksheet_no', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->parent_id)) {
                    $query->where('install_equipment_purchase_orders.supplier_id', $request->parent_id);
                }
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->worksheet_no
                ];
            });
        return response()->json($install_equipment_pos);
    }

    public function getCarInPurchaseOrderList(Request $request)
    {
        $po_id = $request->po_id;
        $cars = Car::leftjoin('import_car_lines', 'import_car_lines.id', '=', 'cars.id')
            ->leftjoin('purchase_order_lines', 'purchase_order_lines.id', '=', 'import_car_lines.po_line_id')
            // ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->whereNotIn('cars.status', [CarEnum::DRAFT, CarEnum::SOLD_OUT])
            // ->whereNotNull(['cars.code', 'cars.license_plate'])
            ->when($po_id, function ($query) use ($po_id) {
                $query->where('purchase_order_lines.purchase_order_id', $po_id);
            })
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->orWhere('cars.license_plate', 'like', '%' . $request->s . '%');
                    $query->orWhere('cars.engine_no', 'like', '%' . $request->s . '%');
                    $query->orWhere('cars.chassis_no', 'like', '%' . $request->s . '%');
                }
            })
            ->select('cars.*')
            ->limit(50)
            ->get()->map(function ($item) {
                $license_plate = $item->license_plate ?? __('install_equipments.not_register');
                $engine_no = $item->engine_no ?? __('install_equipments.not_register');
                $chassis_no = $item->chassis_no ?? __('install_equipments.not_register');
                return [
                    'id' => $item->id,
                    'text' => $license_plate . ' / ' . $engine_no . ' / ' . $chassis_no
                ];
            });
        return response()->json($cars);
    }

    public function getAccessoryBOMList(Request $request)
    {
        $boms = Bom::select('id', 'worksheet_no')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->orWhere('worksheet_no', 'like', '%' . $request->s . '%');
                    $query->orWhere('name', 'like', '%' . $request->s . '%');
                }
            })
            ->where('type', LongTermRentalTypeEnum::ACCESSORY)
            ->orderBy('worksheet_no')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->worksheet_no
                ];
            });
        return $boms;
    }

    public function getLotNumber()
    {
        $dataLotNo = InstallEquipment::whereNotNull('lot_no')
            ->select('lot_no')
            ->groupBy('lot_no')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->lot_no,
                    'text' => $item->lot_no
                ];
            });
        return $dataLotNo;
    }

    public function getAllInstallEquipmentList(Request $request)
    {
        $install_equipment_list = InstallEquipment::select('id', 'worksheet_no')
            // ->where('status', STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->orWhere('worksheet_no', 'like', '%' . $request->s . '%');
                }
            })
            ->orderBy('worksheet_no')
            ->limit(50)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->worksheet_no
                ];
            });
        return response()->json($install_equipment_list);
    }

    public function getPOAlreadyCreatedInstallEquipmentList(REquest $request)
    {
        $po_list = PurchaseOrder::Join('install_equipments', 'install_equipments.po_id', '=', 'purchase_orders.id')
            ->select('purchase_orders.id', 'purchase_orders.po_no')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->orWhere('purchase_orders.po_no', 'like', '%' . $request->s . '%');
                }
            })
            ->orderBy('purchase_orders.po_no')
            ->limit(50)
            ->distinct('purchase_orders.id')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->po_no
                ];
            });
        return response()->json($po_list);
    }

    public function getSupplierAlreadyCreatedInstallEquipmentList(REquest $request)
    {
        $po_list = PurchaseOrder::join('install_equipments', 'install_equipments.po_id', '=', 'purchase_orders.id')
            ->select('purchase_orders.id', 'purchase_orders.po_no')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->orWhere('purchase_orders.po_no', 'like', '%' . $request->s . '%');
                }
            })
            ->orderBy('purchase_orders.po_no')
            ->limit(50)
            ->distinct('purchase_orders.id')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->po_no
                ];
            });
        return response()->json($po_list);
    }

    public function getChassisInstallEquipmentList(Request $request)
    {
        $chassis_list = Car::join('install_equipments', 'install_equipments.car_id', '=', 'cars.id')
            ->select('cars.id', 'cars.chassis_no')
            ->whereNotNull('cars.chassis_no')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->orWhere('cars.chassis_no', 'like', '%' . $request->s . '%');
                }
            })
            ->orderBy('cars.chassis_no')
            ->limit(50)
            ->distinct('cars.id')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->chassis_no
                ];
            });
        return response()->json($chassis_list);
    }

    public function getLicensePlateInstallEquipmentList(Request $request)
    {
        $license_plate_list = Car::join('install_equipments', 'install_equipments.car_id', '=', 'cars.id')
            ->select('cars.id', 'cars.license_plate')
            ->whereNotNull('cars.license_plate')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->orWhere('cars.license_plate', 'like', '%' . $request->s . '%');
                }
            })
            ->orderBy('cars.license_plate')
            ->limit(50)
            ->distinct('cars.id')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->license_plate
                ];
            });
        return response()->json($license_plate_list);
    }
}