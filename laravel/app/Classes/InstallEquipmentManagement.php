<?php

namespace App\Classes;

use App\Models\InstallEquipment;
use App\Models\InstallEquipmentLine;
use App\Models\InstallEquipmentPOLine;
use App\Models\InstallEquipmentPurchaseOrder;


class InstallEquipmentManagement
{
    public $install_equipment_id;
    public $install_equipment_lines;
    public $subtotal;
    public $discount;
    public $vat;
    public $total;
    public $total_after_discount;
    public $amount;

    public function __construct()
    {
        $this->install_equipment_id = null;
        $this->install_equipment_lines = [];
        $this->subtotal = 0;
        $this->discount = 0;
        $this->vat = 0;
        $this->total = 0;
        $this->total_after_discount = 0;
        $this->amount = 0;
    }

    function findInstallEquipmentLines($install_equipment_id)
    {
        return InstallEquipmentLine::where('install_equipment_id', $install_equipment_id)->get();
    }

    function findInstallEquipmentPOLines($install_equipment_po_id)
    {
        return InstallEquipmentPOLine::where('install_equipment_po_id', $install_equipment_po_id)->get();
    }

    function findInstallEquipmentsPurchaseOrder($install_equipment_id)
    {
        return InstallEquipmentPurchaseOrder::where('install_equipment_id', $install_equipment_id)->first();
    }

    function findPurchaseOrder($install_equipment_po_id)
    {
        return InstallEquipmentPurchaseOrder::find($install_equipment_po_id);
    }

    function findInstallEquipmentByPOId($install_equipment_po_id)
    {
        $install_equipment_po = InstallEquipmentPurchaseOrder::find($install_equipment_po_id);
        if (!$install_equipment_po) {
            return false;
        }
        return InstallEquipment::find($install_equipment_po->install_equipment_id);
    }

    function updateInstallEquipmentPOLines($install_equipment_id)
    {
        $install_equipment_lines = $this->findInstallEquipmentLines($install_equipment_id);
        $equip_po = $this->findInstallEquipmentsPurchaseOrder($install_equipment_id);
        if (!$equip_po) {
            return false;
        }
        foreach ($install_equipment_lines as $key => $install_equipment_line) {
            $eq_po_line = InstallEquipmentPOLine::
            where('install_equipment_po_id', $equip_po->id)
            ->where('install_equipment_line_id', $install_equipment_line->id)->first();
            if (!$eq_po_line) {
                $eq_po_line = new InstallEquipmentPOLine;
                $eq_po_line->install_equipment_po_id = $equip_po->id;
                $eq_po_line->install_equipment_line_id = $install_equipment_line->id;
            }
            $eq_po_line->accessory_id = $install_equipment_line->accessory_id;
            $eq_po_line->amount = intval($install_equipment_line->amount);

            $price = floatval($install_equipment_line->price);
            $vat = floatval($price * 7 / 107);
            $exclude_vat = $price - $vat;

            $eq_po_line->total = $price;
            $eq_po_line->vat = $vat;
            $eq_po_line->subtotal = $exclude_vat;
            $eq_po_line->save();
        }
        return true;
    }

    function updateInstallEquipmentLines($install_equipment_po_id)
    {
        $install_equipment_po_lines = $this->findInstallEquipmentPOLines($install_equipment_po_id);
        $install_equipment_po_id = $this->findPurchaseOrder($install_equipment_po_id);
        foreach ($install_equipment_po_lines as $key => $install_equipment_po_line) {
            $eq_line = InstallEquipmentLine::where('id', $install_equipment_po_line->install_equipment_line_id)->first();
            if (!$eq_line) {
                $eq_line = new InstallEquipmentLine;
                $eq_line->install_equipment_id = $install_equipment_po_id->install_equipment_id;
            }
            $eq_line->accessory_id = $install_equipment_po_line->accessory_id;
            $eq_line->amount = intval($install_equipment_po_line->amount);
            $eq_line->price = floatval($install_equipment_po_line->total);
            $eq_line->save();
        }
        return true;
    }

    function calculateInstallEquipment($install_equipment_id)
    {
        $sum_price = 0;
        $sum_vat = 0;
        $sum_exclude_vat = 0;
        $install_equipment_lines = $this->findInstallEquipmentLines($install_equipment_id);
        foreach ($install_equipment_lines as $key => $install_equipment_line) {
            $price = floatval($install_equipment_line->price);
            $vat = floatval($price * 7 / 107);
            $exclude_vat = $price - $vat;
            $sum_price += $price;
            $sum_vat += $vat;
            $sum_exclude_vat += $exclude_vat;
        }
        $this->subtotal = $sum_exclude_vat;
        $this->vat = $sum_vat;
        $this->total = $sum_price;
    }

    function calculateInstallEquipmentPO($install_equipment_id)
    {
        $sum_price = 0;
        $sum_vat = 0;
        $sum_exclude_vat = 0;
        $sum_discount = 0;
        $sum_amount = 0;

        $eqipment_po = $this->findInstallEquipmentsPurchaseOrder($install_equipment_id);
        if (!$eqipment_po) {
            return false;
        }

        $install_equipment_po_lines = $this->findInstallEquipmentPOLines($eqipment_po->id);
        foreach ($install_equipment_po_lines as $key => $po_line) {
            $amount = intval($po_line->amount);
            $price = floatval($po_line->total) * $amount;
            $discount = floatval($po_line->discount);
            $vat = floatval($price * 7 / 107);
            $exclude_vat = floatval($price - $vat);
            $sum_price += $price;
            $sum_vat += $vat;
            $sum_discount += $discount;
            $sum_amount += $amount;
            $sum_exclude_vat += $exclude_vat;
        }

        $this->subtotal = $sum_exclude_vat;
        $this->vat = $sum_vat;
        $this->total = $sum_price;
        $this->total_after_discount = $sum_price - $sum_discount;
        $this->discount = $sum_discount;
        $this->amount = $sum_amount;
    }

    function getSummary()
    {
        return [
            'subtotal' => floatval(number_format($this->subtotal, 2, '.', '')),
            'discount' => floatval(number_format($this->discount, 2, '.', '')),
            'vat' => floatval(number_format($this->vat, 2, '.', '')),
            'total' => floatval(number_format($this->total, 2, '.', '')),
            'total_after_discount' => floatval(number_format($this->total_after_discount, 2, '.', '')),
            'amount' => intval($this->amount),

            'subtotal_text' => number_format($this->subtotal, 2, '.', ','),
            'discount_text' => number_format($this->discount, 2, '.', ','),
            'vat_text' => number_format($this->vat, 2, '.', ','),
            'total_text' => number_format($this->total, 2, '.', ','),
            'total_after_discount_text' => number_format($this->total_after_discount, 2, '.', ''),
            'amount_text' => number_format($this->amount),
            // 'msg' => $this->msg,
        ];
    }

}