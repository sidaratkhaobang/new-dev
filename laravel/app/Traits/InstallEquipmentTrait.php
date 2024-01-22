<?php

namespace App\Traits;

use App\Classes\InstallEquipmentManagement;
use App\Classes\StepApproveManagement;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\InstallEquipmentPOStatusEnum;
use App\Enums\InstallEquipmentStatusEnum;
use App\Models\Approve;
use App\Models\ApproveLine;
use App\Models\ConfigApprove;
use App\Models\ConfigApproveLine;
use App\Models\Creditor;
use App\Models\InstallEquipment;
use App\Models\InstallEquipmentLine;
use App\Models\InstallEquipmentPurchaseOrder;
use Illuminate\Http\Request;

trait InstallEquipmentTrait
{

    public static function createInstallEquipments($po_id, $car_id, $install_equipment_lines, $remark = '', $files = null,$lot_id = null,$lot_no = null)
    {
        if (empty($car_id)) {
            return false;
        }
        $existed_install_equipment_arr = [];
        $prefix = 'OP-';

        if (!isset($install_equipment_lines)) {
            return false;
        }

        foreach ($install_equipment_lines as $key => $item) {
            if (!isset($item['supplier_id']) || !isset($item['accessory_id'])) {
                continue;
            }

            if (array_key_exists($item['supplier_id'], $existed_install_equipment_arr)) {
                $install_equipment_id = $existed_install_equipment_arr[$item['supplier_id']];
                $install_equipment = InstallEquipment::find($install_equipment_id);
            } else {
                $install_equipment = new InstallEquipment;
                $install_equipment_count = InstallEquipment::all()->count() + 1;
                $install_equipment->worksheet_no = generateRecordNumber($prefix, $install_equipment_count);
                $install_equipment->car_id = $car_id;
                $install_equipment->po_id = $po_id;
                $install_equipment->status = InstallEquipmentStatusEnum::PENDING_REVIEW;
                $supplier = Creditor::find($item['supplier_id']);
                if (!$supplier) {
                    continue;
                }
                $install_equipment->supplier_id = $supplier->id;
                $install_equipment->install_day_amount = intval($supplier->install_duration);

                $supplier = Creditor::find($item['supplier_id']);
                $install_equipment->remark = $remark;
                if(!empty($lot_id)){
                    $install_equipment->lot_id = $lot_id;
                }
                if(!empty($lot_no)){
                    $install_equipment->lot_no = $lot_no;
                }
                // find group_id
                $group_id = InstallEquipmentTrait::findGroupId($car_id, $po_id);
                $install_equipment->group_id = $group_id;
                $install_equipment->status = InstallEquipmentStatusEnum::PENDING_REVIEW;
                $install_equipment->save();

                $existed_install_equipment_arr[$item['supplier_id']] = $install_equipment->id;
                if ($files) {
                    foreach ($files as $file) {
                        if ($file) {
                            $install_equipment->addMedia($file)
                                ->preservingOriginal()
                                ->toMediaCollection('install_equipment_files');
                        }
                    }
                }
            }

            $install_equipment_line = InstallEquipmentLine::firstOrNew(['id' => $item['id']]);
            $install_equipment_line->install_equipment_id = $install_equipment->id;
            $install_equipment_line->accessory_id = $item['accessory_id'];
            $install_equipment_line->amount = intval($item['amount']);
            $install_equipment_line->price = floatval($item['price']);
            $install_equipment_line->remark = $item['remark'];
            $install_equipment_line->save();
        }
        if ($existed_install_equipment_arr) {
            foreach ($existed_install_equipment_arr as $key => $install_equipment_id) {
                $po_created = InstallEquipmentTrait::createInstallEquipmentPO($install_equipment_id);
            }
        }
        return $existed_install_equipment_arr;
    }

    public static function findGroupId($car_id, $po_id)
    {
        $group_id = null;
        $appendable = true;
        $last_appendable_install_equipment = InstallEquipmentTrait::getLatestEquipment($car_id, $po_id, $appendable);
        if ($last_appendable_install_equipment) {
            $group_id = intval($last_appendable_install_equipment->group_id);
        } else {
            $last_install_equipment = InstallEquipment::latest('group_id')->first();
            if ($last_install_equipment) {
                $group_id = intval($last_install_equipment->group_id) + 1;
            } else {
                $group_id = 1;
            }
        }
        return $group_id;
    }

    public static function createInstallEquipmentPO($install_equipment_id)
    {
        $install_equipment = InstallEquipment::find($install_equipment_id);
        $prefix = 'POA-';
        if ($install_equipment) {
            $equip_po = new InstallEquipmentPurchaseOrder;
            $equip_po_count = InstallEquipmentPurchaseOrder::all()->count() + 1;
            $equip_po->worksheet_no = generateRecordNumber($prefix, $equip_po_count);
            $equip_po->install_equipment_id = $install_equipment_id;
            $equip_po->supplier_id = $install_equipment->supplier_id;
            $equip_po->car_id = $install_equipment->car_id;

            $install_equip_management = new InstallEquipmentManagement();
            $install_equip_management->calculateInstallEquipment($install_equipment_id);
            $summary = $install_equip_management->getSummary();
            $equip_po->subtotal = $summary['subtotal'];
            $equip_po->discount = $summary['discount'];
            $equip_po->vat = $summary['vat'];
            $equip_po->total = $summary['total'];
            $equip_po->amount = $summary['amount'];
            $equip_po->status = InstallEquipmentPOStatusEnum::PENDING_REVIEW;
            $equip_po->save();

            // create step approves
            $config_enum = ConfigApproveTypeEnum::EQUIPMENT_ORDER;
            $model_type = InstallEquipmentPurchaseOrder::class;
            $model_id = $equip_po->id;
            $step_approve_management = new StepApproveManagement();
            $step_approve_management->createModelApproval($config_enum, $model_type, $model_id);

            // update po line
            $install_equip_management = new InstallEquipmentManagement();
            $created_po_lines = $install_equip_management->updateInstallEquipmentPOLines($install_equipment_id);
        }
        return true;
    }

    public static function getLatestEquipment($car_id, $po_id, $appendable = false)
    {
        return InstallEquipment::where('car_id', $car_id)
            ->where('po_id', $po_id)
            ->when($appendable, function ($q) {
                $q->whereIn('status', [
                    InstallEquipmentStatusEnum::PENDING_REVIEW,
                    InstallEquipmentStatusEnum::WAITING,
                    InstallEquipmentStatusEnum::INSTALL_IN_PROCESS,
                    InstallEquipmentStatusEnum::OVERDUE,
                    InstallEquipmentStatusEnum::DUE,
                    InstallEquipmentStatusEnum::INSTALL_COMPLETE,
                ]);
            })
            ->latest('group_id')->first();
    }

    static function updateInstallEquipmentStatus(array $install_equipment_ids, string $status)
    {
        $models = InstallEquipment::whereIn('id', $install_equipment_ids)->get();
        foreach ($models as $model) {
            $model->status = $status;
            $model->save();
        }
        return true;
    }

    static function getTempIntallEquipmentPOStatusList()
    {
        $status = collect([
            (object) [
                'id' => InstallEquipmentPOStatusEnum::PENDING_REVIEW,
                'name' => __('install_equipment_pos.status_' . InstallEquipmentPOStatusEnum::PENDING_REVIEW),
                'value' => InstallEquipmentPOStatusEnum::PENDING_REVIEW,
            ],
            (object) [
                'id' => InstallEquipmentPOStatusEnum::CONFIRM,
                'name' => __('install_equipment_pos.status_' . InstallEquipmentPOStatusEnum::CONFIRM),
                'value' => InstallEquipmentPOStatusEnum::CONFIRM,
            ],
            (object) [
                'id' => InstallEquipmentPOStatusEnum::REJECT,
                'name' => __('install_equipment_pos.status_' . InstallEquipmentPOStatusEnum::REJECT),
                'value' => InstallEquipmentPOStatusEnum::REJECT,
            ],
            (object) [
                'id' => InstallEquipmentPOStatusEnum::CANCEL,
                'name' => __('install_equipment_pos.status_' . InstallEquipmentPOStatusEnum::CANCEL),
                'value' => InstallEquipmentPOStatusEnum::CANCEL,
            ],
            (object) [
                'id' => InstallEquipmentPOStatusEnum::COMPLETE,
                'name' => __('install_equipment_pos.status_' . InstallEquipmentPOStatusEnum::COMPLETE),
                'value' => InstallEquipmentPOStatusEnum::COMPLETE,
            ],
        ]);
        return $status;
    }
}
