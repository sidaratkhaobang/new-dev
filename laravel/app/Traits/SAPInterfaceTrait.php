<?php

namespace App\Traits;

use App\Enums\SAPDocumentTypeEnum;
use App\Enums\SAPStatusEnum;
use App\Enums\SAPTransferSubTypeEnum;
use App\Enums\SAPTransferTypeEnum;

trait SAPInterfaceTrait
{
    public static function getSAPTransferARTypeList()
    {
        return collect([
            (object) [
                'id' => SAPTransferTypeEnum::CASH_SALE_S_RENTAL,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::CASH_SALE_S_RENTAL),
                'value' => SAPTransferTypeEnum::CASH_SALE_S_RENTAL,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::CASH_SALE_COUPON,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::CASH_SALE_COUPON),
                'value' => SAPTransferTypeEnum::CASH_SALE_COUPON,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::CREDIT_S_RENTAL,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::CREDIT_S_RENTAL),
                'value' => SAPTransferTypeEnum::CREDIT_S_RENTAL,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::CREDIT_L_RENTAL,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::CREDIT_L_RENTAL),
                'value' => SAPTransferTypeEnum::CREDIT_L_RENTAL,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::BOAT_REPAIR,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::BOAT_REPAIR),
                'value' => SAPTransferTypeEnum::BOAT_REPAIR,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::SALE_BOAT_PARTS,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::SALE_BOAT_PARTS),
                'value' => SAPTransferTypeEnum::SALE_BOAT_PARTS,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::DRIVER_EXCESS,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::DRIVER_EXCESS),
                'value' => SAPTransferTypeEnum::DRIVER_EXCESS,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::EARLY_RETURN_FINE,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::EARLY_RETURN_FINE),
                'value' => SAPTransferTypeEnum::EARLY_RETURN_FINE,
            ],
        ]);
    }

    public static function getSAPTransferARSubTypeList()
    {
        return collect([
            (object) [
                'id' => SAPTransferSubTypeEnum::AFTER_PAYMENT,
                'name' => __('sap_interfaces.transfer_sub_type_' . SAPTransferSubTypeEnum::AFTER_PAYMENT),
                'value' => SAPTransferSubTypeEnum::AFTER_PAYMENT,
            ],
            (object) [
                'id' => SAPTransferSubTypeEnum::START_SERVICE,
                'name' => __('sap_interfaces.transfer_sub_type_' . SAPTransferSubTypeEnum::START_SERVICE),
                'value' => SAPTransferSubTypeEnum::START_SERVICE,
            ],
            (object) [
                'id' => SAPTransferSubTypeEnum::AFTER_SERVICE_INFORM,
                'name' => __('sap_interfaces.transfer_sub_type_' . SAPTransferSubTypeEnum::AFTER_SERVICE_INFORM),
                'value' => SAPTransferSubTypeEnum::AFTER_SERVICE_INFORM,
            ],
            (object) [
                'id' => SAPTransferSubTypeEnum::AFTER_SERVICE_PAID,
                'name' => __('sap_interfaces.transfer_sub_type_' . SAPTransferSubTypeEnum::AFTER_SERVICE_PAID),
                'value' => SAPTransferSubTypeEnum::AFTER_SERVICE_PAID,
            ],
            (object) [
                'id' => SAPTransferSubTypeEnum::PAYMENT_FEE,
                'name' => __('sap_interfaces.transfer_sub_type_' . SAPTransferSubTypeEnum::PAYMENT_FEE),
                'value' => SAPTransferSubTypeEnum::PAYMENT_FEE,
            ],
            (object) [
                'id' => SAPTransferSubTypeEnum::EXPIRED_COUPON,
                'name' => __('sap_interfaces.transfer_sub_type_' . SAPTransferSubTypeEnum::EXPIRED_COUPON),
                'value' => SAPTransferSubTypeEnum::EXPIRED_COUPON,
            ],
            (object) [
                'id' => SAPTransferSubTypeEnum::INVOICE_ISSUE,
                'name' => __('sap_interfaces.transfer_sub_type_' . SAPTransferSubTypeEnum::INVOICE_ISSUE),
                'value' => SAPTransferSubTypeEnum::INVOICE_ISSUE,
            ],
        ]);
    }

    public static function getARDocumentTypeTypeList()
    {
        return collect([
            (object) [
                'id' => SAPDocumentTypeEnum::D1,
                'name' => SAPDocumentTypeEnum::D1,
                'value' => SAPDocumentTypeEnum::D1,
            ],
            (object) [
                'id' => SAPDocumentTypeEnum::D2,
                'name' => SAPDocumentTypeEnum::D2,
                'value' => SAPDocumentTypeEnum::D2,
            ],
            (object) [
                'id' => SAPDocumentTypeEnum::DN,
                'name' => SAPDocumentTypeEnum::DN,
                'value' => SAPDocumentTypeEnum::DN,
            ],
            (object) [
                'id' => SAPDocumentTypeEnum::DK,
                'name' => SAPDocumentTypeEnum::DK,
                'value' => SAPDocumentTypeEnum::DK,
            ],
        ]);
    }

    public static function getSAPStatusList()
    {
        return collect([
            (object) [
                'id' => SAPStatusEnum::PENDING,
                'name' => __('sap_interfaces.status_' . SAPStatusEnum::PENDING),
                'value' => SAPStatusEnum::PENDING,
            ],
            (object) [
                'id' => SAPStatusEnum::SUCCESS,
                'name' => __('sap_interfaces.status_' . SAPStatusEnum::SUCCESS),
                'value' => SAPStatusEnum::SUCCESS,
            ],
            (object) [
                'id' => SAPStatusEnum::FAIL,
                'name' => __('sap_interfaces.status_' . SAPStatusEnum::FAIL),
                'value' => SAPStatusEnum::FAIL,
            ],
            (object) [
                'id' => SAPStatusEnum::CANCEL,
                'name' => __('sap_interfaces.status_' . SAPStatusEnum::CANCEL),
                'value' => SAPStatusEnum::CANCEL,
            ],
        ]);
    }

    public static function getAPDocumentTypeTypeList()
    {
        return collect([
            (object) [
                'id' => SAPDocumentTypeEnum::KR,
                'name' => SAPDocumentTypeEnum::KR,
                'value' => SAPDocumentTypeEnum::KR,
            ],
            (object) [
                'id' => SAPDocumentTypeEnum::KA,
                'name' => SAPDocumentTypeEnum::KA,
                'value' => SAPDocumentTypeEnum::KA,
            ],
        ]);
    }

    public static function getSAPTransferAPSubTypeList()
    {
        return collect([
            (object) [
                'id' => SAPTransferSubTypeEnum::INVOICE_ISSUE,
                'name' => __('sap_interfaces.transfer_sub_type_' . SAPTransferSubTypeEnum::INVOICE_ISSUE),
                'value' => SAPTransferSubTypeEnum::INVOICE_ISSUE,
            ],
        ]);
    }

    public static function getSAPTransferAPTypeList()
    {
        return collect([
            (object) [
                'id' => SAPTransferTypeEnum::REPAIR_COST,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::REPAIR_COST),
                'value' => SAPTransferTypeEnum::REPAIR_COST,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::TAX_COST,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::TAX_COST),
                'value' => SAPTransferTypeEnum::TAX_COST,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::INSURANCE_COST,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::INSURANCE_COST),
                'value' => SAPTransferTypeEnum::INSURANCE_COST,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::INSURANCE_RETURN_COST,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::INSURANCE_RETURN_COST),
                'value' => SAPTransferTypeEnum::INSURANCE_RETURN_COST,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::PATTY_CASH,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::PATTY_CASH),
                'value' => SAPTransferTypeEnum::PATTY_CASH,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::LOT_EQUIPMENT_CASH,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::LOT_EQUIPMENT_CASH),
                'value' => SAPTransferTypeEnum::LOT_EQUIPMENT_CASH,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::LOT_CAR_CASH,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::LOT_CAR_CASH),
                'value' => SAPTransferTypeEnum::LOT_CAR_CASH,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::LOT_CAR_LEASING,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::LOT_CAR_LEASING),
                'value' => SAPTransferTypeEnum::LOT_CAR_LEASING,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::CLOSING_CONTRACT_CAR_EARLY,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::CLOSING_CONTRACT_CAR_EARLY),
                'value' => SAPTransferTypeEnum::CLOSING_CONTRACT_CAR_EARLY,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::FIRST_DAMAGE_COST,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::FIRST_DAMAGE_COST),
                'value' => SAPTransferTypeEnum::FIRST_DAMAGE_COST,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::CAR_WASH_COST,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::CAR_WASH_COST),
                'value' => SAPTransferTypeEnum::CAR_WASH_COST,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::IMPROVEMENT_COST,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::IMPROVEMENT_COST),
                'value' => SAPTransferTypeEnum::IMPROVEMENT_COST,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::ACCIDENT_REPAIR_COST,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::ACCIDENT_REPAIR_COST),
                'value' => SAPTransferTypeEnum::ACCIDENT_REPAIR_COST,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::OIL_FLEET_CARD_COST,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::OIL_FLEET_CARD_COST),
                'value' => SAPTransferTypeEnum::OIL_FLEET_CARD_COST,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::DRIVER_COST,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::DRIVER_COST),
                'value' => SAPTransferTypeEnum::DRIVER_COST,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::COVERAGE_COST,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::COVERAGE_COST),
                'value' => SAPTransferTypeEnum::COVERAGE_COST,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::OWNERSHIP_TRANSFER_COST,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::OWNERSHIP_TRANSFER_COST),
                'value' => SAPTransferTypeEnum::OWNERSHIP_TRANSFER_COST,
            ],
            (object) [
                'id' => SAPTransferTypeEnum::BUY_BOAT_PARTS,
                'name' => __('sap_interfaces.transfer_type_' . SAPTransferTypeEnum::BUY_BOAT_PARTS),
                'value' => SAPTransferTypeEnum::BUY_BOAT_PARTS,
            ],
        ]);
    }
}
