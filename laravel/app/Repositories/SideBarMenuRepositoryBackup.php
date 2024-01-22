<?php

namespace App\Repositories;

use App\Models\Role;
use App\Repositories\SideBarLink;
use App\Repositories\SideBarSubmenu;
use Illuminate\Support\Collection;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Traits\GpsTrait;
use App\Traits\CarAuctionTrait;

class SideBarMenuRepositoryBackup
{
    public $groupMenus;

    function __construct()
    {
        $this->groupMenus = [


            new SideBarHeading(__('menu.head_car')),
            new SideBarSubmenu(
                __('menu.head_car'),
                [
                    new SideBarSubmenu(
                        __('menu.head_purchase'),
                        [
                            new SideBarLink(__('menu.purchase_requisitions'), route('admin.purchase-requisitions.index'), [Actions::View . '_' . Resources::PurchaseRequisition]),
                            new SideBarLink(__('menu.purchase_requisition_approve'), route('admin.purchase-requisition-approve.index'), [Actions::View . '_' . Resources::PurchaseRequisitionApprove]),
                            new SideBarLink(__('menu.open_purchase_order'), route('admin.purchase-order-open.index'), [Actions::View . '_' . Resources::OpenPurchaseOrder]),
                            new SideBarLink(__('menu.all_purchase_orders'), route('admin.purchase-orders.index'), [Actions::View . '_' . Resources::PurchaseOrder]),
                            new SideBarLink(__('menu.confirm_purchase_orders'), route('admin.purchase-order-approve.index'), [Actions::View . '_' . Resources::PurchaseOrderApprove]),
                            new SideBarLink(__('menu.import_cars'), route('admin.import-cars.index'), [Actions::View . '_' . Resources::ImportCar]),
                            new SideBarLink(__('menu.prepare_new_cars'), route('admin.prepare-new-cars.index'), [Actions::View . '_' . Resources::ImportCarList]),
                        ],
                        [
                            Actions::View . '_' . Resources::PurchaseRequisition,
                            Actions::View . '_' . Resources::PurchaseRequisitionApprove,
                            Actions::View . '_' . Resources::OpenPurchaseOrder,
                            Actions::View . '_' . Resources::PurchaseOrder,
                            Actions::View . '_' . Resources::PurchaseOrderApprove,
                            Actions::View . '_' . Resources::ImportCar,
                            Actions::View . '_' . Resources::ImportCarList,
                        ]
                    ),
                    new SideBarSubmenu(
                        __('menu.install_equipment'),
                        [
                            new SideBarLink(__('menu.install_equipment_worksheet'), route('admin.install-equipments.index'), [Actions::View . '_' . Resources::InstallEquipment]),
                            new SideBarLink(__('menu.install_equipment_po'), route('admin.install-equipment-purchase-orders.index'), [Actions::View . '_' . Resources::InstallEquipmentPO]),
                            new SideBarLink(__('menu.install_equipment_approval'), route('admin.install-equipment-po-approves.index'), [Actions::View . '_' . Resources::InstallEquipmentPOApprove]),
                        ],
                        [
                            Actions::View . '_' . Resources::InstallEquipment,
                            Actions::View . '_' . Resources::InstallEquipmentPO,
                            Actions::View . '_' . Resources::InstallEquipmentPOApprove,
                        ]
                    ),
                    new SideBarSubmenu(__('menu.head_parking_manage'), [
                        new SideBarSubmenu(
                            __('menu.head_car_inout'),
                            [
                                new SideBarLink(__('menu.car_inout_license'), route('admin.car-park-transfers.index'), [Actions::View . '_' . Resources::CarParkTransfer]),
                                new SideBarLink(__('menu.car_park_transfer_logs'), route('admin.car-park-transfer-logs.index'), [Actions::View . '_' . Resources::CarParkTransferLog]),
                            ],
                            [
                                Actions::View . '_' . Resources::CarParkTransfer,
                                Actions::View . '_' . Resources::CarParkTransferLog,
                            ]
                        ),
                        new SideBarSubmenu(
                            __('menu.head_parking'),
                            [
                                /* new SideBarLink(__('menu.parking_lot')), */
                                new SideBarLink(__('menu.parking_zone'), route('admin.parking-lots.index'), [Actions::View . '_' . Resources::ParkingZone]),
                            ],
                            [
                                Actions::View . '_' . Resources::ParkingZone,
                            ]
                        ),
                    ]),
                    new SideBarLink(__('menu.car_warehourse'), route('admin.cars.index'), [Actions::View . '_' . Resources::Car]),
                ],
                [
                    Actions::View . '_' . Resources::Car,
                    Actions::View . '_' . Resources::PurchaseRequisition,
                    Actions::View . '_' . Resources::PurchaseRequisitionApprove,
                    Actions::View . '_' . Resources::OpenPurchaseOrder,
                    Actions::View . '_' . Resources::PurchaseOrder,
                    Actions::View . '_' . Resources::PurchaseOrderApprove,
                    Actions::View . '_' . Resources::ImportCar,
                    Actions::View . '_' . Resources::ImportCarList,
                    Actions::View . '_' . Resources::InstallEquipment,
                    Actions::View . '_' . Resources::InstallEquipmentPO,
                    Actions::View . '_' . Resources::InstallEquipmentPOApprove,
                    Actions::View . '_' . Resources::CarParkTransfer,
                    Actions::View . '_' . Resources::CarParkTransferLog,
                    Actions::View . '_' . Resources::ParkingZone,
                ]
            ),
            // new SideBarHeading('Call Center'),
            new SideBarSubmenu(
                __('menu.call_center'),
                [
                    new SideBarLink(__('menu.accident_report'), route('admin.accident-informs.index'), [Actions::View . '_' . Resources::AccidentInform]),
                    new SideBarLink(__('menu.follow_up_repair'), route('admin.call-center-follow-up-repairs.index'), [Actions::View . '_' . Resources::CallCenterFollowUpRepair]),
                    new SideBarSubmenu(
                        __('menu.repairs'),
                        [
                            new SideBarLink(__('menu.alert_check_distance'), route('admin.check-distance-notices.index'), [Actions::View . '_' . Resources::CheckDistanceNotice]),
                            new SideBarLink(__('menu.repair_alert'), route('admin.call-center-repairs.index'), [Actions::View . '_' . Resources::CallCenterRepair]),
                            new SideBarLink(__('menu.repair_order'), route('admin.call-center-repair-orders.index'), [Actions::View . '_' . Resources::CallCenterRepairOrder]),
                        ],
                        [
                            Actions::View . '_' . Resources::CheckDistanceNotice,
                            Actions::View . '_' . Resources::CallCenterRepair,
                            Actions::View . '_' . Resources::CallCenterRepairOrder,
                        ],
                    ),
                ],
                [
                    Actions::View . '_' . Resources::AccidentInform,
                ]
            ),
            new SideBarHeading('QA Management'),
            new SideBarSubmenu(
                __('menu.head_config'),
                [
                    new SideBarLink(__('menu.car_inspections'), route('admin.inspection-jobs.index'), [Actions::View . '_' . Resources::CarInspection]),
                    new SideBarLink(__('menu.config_car_inspections'), route('admin.car-inspections.index'), [Actions::View . '_' . Resources::ConfigCarInspection]),
                    new SideBarLink(__('menu.inspection_flows'), route('admin.car-inspection-types.index'), [Actions::View . '_' . Resources::ConfigInspectionFlow]),
                ],
                [
                    Actions::View . '_' . Resources::CarInspection,
                    Actions::View . '_' . Resources::ConfigCarInspection,
                    Actions::View . '_' . Resources::ConfigInspectionFlow,
                ]
            ),
            new SideBarSubmenu(
                __('menu.gps_management'),
                [
                    new SideBarLink(__('menu.gps_service_charge'), route('admin.gps-service-charges.index'), [Actions::View . '_' . Resources::GPSServiceCharge]),
                    new SideBarLink(__('menu.gps_car'), route('admin.gps-cars.index'), [Actions::View . '_' . Resources::GPSCar]),
                    new SideBarLink(__('menu.gps_alert'), route('admin.gps-alerts.index'), [Actions::View . '_' . Resources::GPSAlert]),
                    new SideBarLink(__('menu.gps_signal_check_alert'), route('admin.gps-check-signal-alerts.index'), [Actions::View . '_' . Resources::GPSCheckSignalAlert]),
                    new SideBarLink(
                        __('menu.gps_signal_check_job'),
                        GpsTrait::sideBarCheckJob(),
                        [
                            Actions::View . '_' . Resources::GPSCheckSignalShortTerm,
                            Actions::View . '_' . Resources::GPSCheckSignalLongTerm,
                            Actions::View . '_' . Resources::GPSCheckSignalShortTermBranch,
                            Actions::View . '_' . Resources::GPSCheckSignalKratos
                        ]
                    ),
                    new SideBarLink(__('menu.gps_remove_stop_alert'), route('admin.gps-remove-stop-signal-alerts.index'), [Actions::View . '_' . Resources::GPSRemoveStopSignalAlert]),
                    new SideBarLink(
                        __('menu.gps_remove_stop_job'),
                        GpsTrait::sideBarRemoveJob(),
                        [
                            Actions::View . '_' . Resources::GPSRemoveStopSignalAlert,
                            Actions::View . '_' . Resources::GPSRemoveSignalJob,
                            Actions::View . '_' . Resources::GPSStopSignalJob,
                        ]
                    ),
                    new SideBarLink(__('menu.gps_historical_data_alert'), route('admin.gps-historical-data-alerts.index'), [Actions::View . '_' . Resources::GPSHistoricalDataAlert]),
                    new SideBarLink(__('menu.gps_historical_data_job'), route('admin.gps-historical-data-jobs.index'), [Actions::View . '_' . Resources::GPSHistoricalDataJob]),
                ],
                [
                    Actions::View . '_' . Resources::GPSCar,
                    Actions::View . '_' . Resources::GPSCheckSignalAlert,
                    Actions::View . '_' . Resources::GPSCheckSignalShortTerm,
                    Actions::View . '_' . Resources::GPSCheckSignalLongTerm,
                    Actions::View . '_' . Resources::GPSCheckSignalShortTermBranch,
                    Actions::View . '_' . Resources::GPSCheckSignalKratos,
                    Actions::View . '_' . Resources::GPSRemoveStopSignalAlert,
                    Actions::View . '_' . Resources::GPSRemoveSignalJob,
                    Actions::View . '_' . Resources::GPSStopSignalJob,
                ]
            ),
            new SideBarSubmenu(
                __('menu.contract.title'),
                [
                    new SideBarLink(__('menu.contract.sub.check_credits_new_customers'), route('admin.check-credit-new-customers.index'), [Actions::View . '_' . Resources::ContractCheckCreditNewCustomer]),
                    new SideBarLink(__('menu.contract.sub.check_credit_approves'), route('admin.check-credit-approves.index'), [Actions::View . '_' . Resources::ContractCheckCreditApprove]),
                    new SideBarLink(__('menu.contract.sub.contract_list'), route('admin.contracts.index'), [Actions::View . '_' . Resources::ContractAllList]),
                    new SideBarLink(__('menu.contract.sub.contract_check_and_edit'), route('admin.contract-check-and-edit.index'), [Actions::View . '_' . Resources::ContractCheckAndEdit]),
                    new SideBarSubmenu(
                        __('menu.contract.sub.contract_master_data.title'),
                        [
                            new SideBarLink(__('menu.contract.sub.contract_master_data.sub.contract_category'), route('admin.contract-category.index'), [Actions::View . '_' . Resources::ContractMasterDataCategory]),
                        ],
                        [
                            Actions::View . '_' . Resources::ContractMasterDataCategory,
                        ]
                    ),
                ],
                [
                    Actions::View . '_' . Resources::ContractCheckCreditNewCustomer,
                    Actions::View . '_' . Resources::ContractCheckCreditApprove,
                    Actions::View . '_' . Resources::ContractAllList,
                    Actions::View . '_' . Resources::ContractCheckAndEdit,
                    Actions::View . '_' . Resources::ContractMasterDataCategory,
                    Actions::View . '_' . Resources::ContractMasterDataCategory,
                ]
            ),
            new SideBarSubmenu(
                __('menu.accident_and_replacement'),
                [
                    new SideBarSubmenu(
                        __('menu.replacement_job'),
                        [
                            new SideBarLink(__('menu.replacement_car_inform'), route('admin.replacement-car-informs.index'), [Actions::View . '_' . Resources::ReplacementCarInform]),
                            new SideBarLink(__('menu.replacement_type_car'), route('admin.replacement-type-cars.index'), [Actions::View . '_' . Resources::ReplacementTypeCar]),
                            new SideBarLink(__('menu.replacement_car'), route('admin.replacement-cars.index'), [Actions::View . '_' . Resources::ReplacementCar]),
                            new SideBarLink(__('menu.replacement_car_approve'), route('admin.replacement-car-approves.index'), [Actions::View . '_' . Resources::ReplacementCarApprove]),
                        ],
                        [
                            Actions::View . '_' . Resources::ReplacementCarInform,
                            Actions::View . '_' . Resources::ReplacementTypeCar,
                            Actions::View . '_' . Resources::ReplacementCar,
                            Actions::View . '_' . Resources::ReplacementCarApprove,
                        ]
                    ),
                ],
                [
                    Actions::View . '_' . Resources::ReplacementCarInform,
                    Actions::View . '_' . Resources::ReplacementTypeCar,
                    Actions::View . '_' . Resources::ReplacementCar,
                    Actions::View . '_' . Resources::ReplacementCarApprove,
                ]
            ),
            new SideBarSubmenu(
                __('menu.accident'),
                [
                    new SideBarSubmenu(
                        __('menu.garage_job'),
                        [
                            new SideBarLink(__('menu.garage'), route('admin.garages.index'), [Actions::View . '_' . Resources::Garage]),
                        ],
                        [
                            Actions::View . '_' . Resources::Garage,
                        ]
                    ),

                    new SideBarLink(__('menu.accident_report_sheet'), route('admin.accident-inform-sheets.index'), [Actions::View . '_' . Resources::AccidentInformSheet]),
                    new SideBarLink(__('menu.accident_order'), route('admin.accident-orders.index'), [Actions::View . '_' . Resources::AccidentOrder]),
                    new SideBarLink(__('menu.accident_order_approve'), route('admin.accident-order-approves.index'), [Actions::View . '_' . Resources::AccidentOrderApprove]),
                    new SideBarLink(__('menu.accident_order_sheet_approve'), route('admin.accident-order-sheet-approves.index'), [Actions::View . '_' . Resources::AccidentOrderSheetApprove]),
                    new SideBarLink(__('menu.accident_order_sheet_ttl_approve'), route('admin.accident-order-sheet-ttl-approves.index'), [Actions::View . '_' . Resources::AccidentOrderSheetTTLApprove]),
                    new SideBarLink(__('menu.accident_follow_up_repair'), route('admin.accident-follow-up-repairs.index'), [Actions::View . '_' . Resources::AccidentFollowUpRepair]),
                ],
                [
                    Actions::View . '_' . Resources::Garage,
                    Actions::View . '_' . Resources::AccidentInformSheet,
                    Actions::View . '_' . Resources::AccidentOrder,
                    Actions::View . '_' . Resources::AccidentOrderApprove,
                    Actions::View . '_' . Resources::AccidentOrderSheetApprove,
                    Actions::View . '_' . Resources::AccidentOrderSheetTTLApprove,
                ]
            ),
            new SideBarSubmenu(
                __('menu.repairs'),
                [
                    new SideBarLink(__('menu.repair_alert'), route('admin.repairs.index'), [Actions::View . '_' . Resources::Repair]),
                    new SideBarLink(__('menu.repair_order'), route('admin.repair-orders.index'), [Actions::View . '_' . Resources::RepairOrder]),
                    new SideBarLink(__('menu.approve_quotation'), route('admin.repair-quotation-approves.index'), [Actions::View . '_' . Resources::RepairQuotationApprove]),
                    new SideBarSubmenu(
                        __('menu.master_repairs'),
                        [
                            new SideBarLink(__('menu.master_check_distance'), route('admin.check-distances.index'), [Actions::View . '_' . Resources::CheckDistance]),
                            new SideBarLink(__('menu.master_repair_list'), route('admin.repair-lists.index'), [Actions::View . '_' . Resources::RepairList]),
                            new SideBarLink(__('menu.master_condition'), route('admin.condition-repair-services.index'), [Actions::View . '_' . Resources::ConditionRepairService]),
                        ],
                    ),
                ],
                [
                    Actions::View . '_' . Resources::Repair,
                    Actions::View . '_' . Resources::RepairOrder,
                    Actions::View . '_' . Resources::RepairQuotationApprove,
                    Actions::View . '_' . Resources::CheckDistance,
                    Actions::View . '_' . Resources::RepairList,
                    Actions::View . '_' . Resources::ConditionRepairService
                ],
            ),
            new SideBarSubmenu(
                __('menu.head_car_rental'),
                [
                    new SideBarLink(__('menu.short_term_rental_job'), route('admin.short-term-rentals.index'), [Actions::View . '_' . Resources::ShortTermRental]),
                    new SideBarLink(__('menu.operations'), route('admin.operations.index'), [Actions::View . '_' . Resources::Operation]),
                    new SideBarSubmenu(
                        __('menu.short_term_rental'),
                        [
                            new SideBarLink(__('menu.car_rental_categories'), route('admin.car-service-types.index'), [Actions::View . '_' . Resources::CarServiceType]),
                            new SideBarLink(__('menu.service_types'), route('admin.service-types.index'), [Actions::View . '_' . Resources::ServiceType]),
                            new SideBarLink(__('menu.location_groups'), route('admin.location-groups.index'), [Actions::View . '_' . Resources::LocationGroup]),
                            new SideBarLink(__('menu.locations'), route('admin.locations.index'), [Actions::View . '_' . Resources::Location]),
                            // new SideBarLink(__('menu.branches'), route('admin.branches.index'), [Actions::View . '_' . Resources::Branch]),
                            new SideBarLink(__('menu.product'), route('admin.products.index'), [Actions::View . '_' . Resources::Product]),
                            new SideBarLink(__('menu.product_additional'), route('admin.product-additionals.index'), [Actions::View . '_' . Resources::ProductAdditional]),
                            new SideBarLink(__('menu.promotion'), route('admin.promotions.index'), [Actions::View . '_' . Resources::Promotion]),

                        ],
                        [
                            Actions::View . '_' . Resources::CarServiceType,
                            Actions::View . '_' . Resources::ServiceType,
                            Actions::View . '_' . Resources::LocationGroup,
                            Actions::View . '_' . Resources::Location,
                            // Actions::View . '_' . Resources::Branch,
                            Actions::View . '_' . Resources::Product,
                            Actions::View . '_' . Resources::ProductAdditional,
                            Actions::View . '_' . Resources::Promotion,
                        ]
                    ),
                    // new SideBarLink( 'งานรถขนส่ง'),
                    new SideBarSubmenu(
                        __('menu.long_term_rental'),
                        [
                            new SideBarLink(__('menu.long_term_rental_jobs'), route('admin.long-term-rentals.index'), [Actions::View . '_' . Resources::LongTermRental]),
                            new SideBarLink(__('menu.long_term_rental_specs'), route('admin.long-term-rental.specs.index'), [Actions::View . '_' . Resources::LongTermRentalSpec]),
                            new SideBarLink(__('menu.long_term_rental_spec_check_cars'), route('admin.long-term-rental.spec-check-cars.index'), [Actions::View . '_' . Resources::LongTermRentalSpecCheckCar]),
                            new SideBarLink(__('menu.long_term_rental_specs_accessories'), route('admin.long-term-rental.specs.accessories.index'), [Actions::View . '_' . Resources::LongTermRentalSpecsAccessory]),
                            new SideBarLink(__('menu.long_term_rental_spec_approve'), route('admin.long-term-rental.specs-approve.index'), [Actions::View . '_' . Resources::LongTermRentalSpecApprove]),
                            new SideBarLink(__('menu.long_term_rental_compare_price'), route('admin.long-term-rental.compare-price.index'), [Actions::View . '_' . Resources::LongTermRentalComparePrice]),
                            new SideBarLink(__('menu.long_term_rental_quotation'), route('admin.long-term-rental.quotations.index'), [Actions::View . '_' . Resources::LongTermRentalQuotation]),
                        ],
                        [
                            Actions::View . '_' . Resources::LongTermRental,
                            Actions::View . '_' . Resources::LongTermRentalSpec,
                            Actions::View . '_' . Resources::LongTermRentalSpecCheckCar,
                            Actions::View . '_' . Resources::LongTermRentalSpecsAccessory,
                            Actions::View . '_' . Resources::LongTermRentalSpecApprove,
                            Actions::View . '_' . Resources::LongTermRentalComparePrice,
                            Actions::View . '_' . Resources::LongTermRentalQuotation,
                        ]
                    ),
                    new SideBarSubmenu(
                        __('menu.master_long_term_rental'),
                        [
                            new SideBarLink(__('menu.long_term_rental_type'), route('admin.long-term-rental-types.index'), [Actions::View . '_' . Resources::LongTermRentalType]),
                            new SideBarLink(__('menu.long_term_rental_bom'), route('admin.long-term-rental-boms.index'), [Actions::View . '_' . Resources::LongTermRentalBom]),
                            new SideBarLink(__('menu.auction_reject_reason'), route('admin.auction-reject-reasons.index'), [Actions::View . '_' . Resources::AuctionRejectReason]),
                        ],
                        [
                            Actions::View . '_' . Resources::LongTermRentalType,
                            Actions::View . '_' . Resources::LongTermRentalBom,
                            Actions::View . '_' . Resources::AuctionRejectReason,
                        ]
                    ),
                    new SideBarSubmenu(
                        __('menu.head_sale_document'),
                        [
                            new SideBarLink(__('menu.quotation'), route('admin.quotations.index'), [Actions::View . '_' . Resources::Quotation]),
                            new SideBarLink(__('menu.quotation_approve'), route('admin.quotation-approves.index'), [Actions::View . '_' . Resources::QuotationApprove]),
                            new SideBarSubmenu(
                                __('menu.condition_quotation'),
                                [
                                    new SideBarLink(__('menu.condition_quotation_short_term_rental'), route('admin.condition-quotation-short-terms.index'), [Actions::View . '_' . Resources::ShortTermConditionQuotation]),
                                    new SideBarLink(__('menu.condition_quotation_long_term_rental'), route('admin.condition-quotation-long-terms.index'), [Actions::View . '_' . Resources::LongTermConditionQuotation]),
                                ],
                                [
                                    Actions::View . '_' . Resources::ShortTermConditionQuotation,
                                    Actions::View . '_' . Resources::LongTermConditionQuotation,
                                ]
                            ),
                            // new SideBarLink(__('menu.condition_quotation'), route('admin.condition-quotations.index'), [Actions::View . '_' . Resources::ConditionQuotation]),
                            new SideBarLink(__('menu.receipt'), route('admin.receipts.index'), [Actions::View . '_' . Resources::Receipt]),
                        ],
                        [
                            Actions::View . '_' . Resources::Quotation,
                            Actions::View . '_' . Resources::QuotationApprove,
                            Actions::View . '_' . Resources::ShortTermConditionQuotation,
                            Actions::View . '_' . Resources::LongTermConditionQuotation,
                            Actions::View . '_' . Resources::Receipt,
                        ]
                    ),
                    new SideBarSubmenu(
                        __('menu.head_income'),
                        [
                            new SideBarLink(__('menu.income_expense'), route('admin.sap-interfaces.index'), [Actions::View . '_' . Resources::SapInterface]),
                            new SideBarLink(__('menu.gl_accouunt'), route('admin.general-ledger-accounts.index'), [Actions::View . '_' . Resources::GlAccouunt]),
                        ],
                        [
                            Actions::View . '_' . Resources::SapInterface,
                            Actions::View . '_' . Resources::GlAccouunt,
                        ]
                    ),
                ],
                [
                    Actions::View . '_' . Resources::ShortTermRental,
                    Actions::View . '_' . Resources::Operation,
                    Actions::View . '_' . Resources::CarServiceType,
                    Actions::View . '_' . Resources::ServiceType,
                    Actions::View . '_' . Resources::LocationGroup,
                    Actions::View . '_' . Resources::Location,
                    Actions::View . '_' . Resources::Branch,
                    Actions::View . '_' . Resources::Product,
                    Actions::View . '_' . Resources::ProductAdditional,
                    Actions::View . '_' . Resources::Promotion,
                    Actions::View . '_' . Resources::LongTermRental,
                    Actions::View . '_' . Resources::LongTermRentalSpec,
                    Actions::View . '_' . Resources::LongTermRentalSpecCheckCar,
                    Actions::View . '_' . Resources::LongTermRentalSpecsAccessory,
                    Actions::View . '_' . Resources::LongTermRentalSpecApprove,
                    Actions::View . '_' . Resources::LongTermRentalComparePrice,
                    Actions::View . '_' . Resources::LongTermRentalQuotation,
                    Actions::View . '_' . Resources::LongTermRentalType,
                    Actions::View . '_' . Resources::LongTermRentalBom,
                    Actions::View . '_' . Resources::AuctionRejectReason,
                    Actions::View . '_' . Resources::ShortTermConditionQuotation,
                    Actions::View . '_' . Resources::LongTermConditionQuotation,
                    Actions::View . '_' . Resources::Quotation,
                    Actions::View . '_' . Resources::QuotationApprove,
                    Actions::View . '_' . Resources::ShortTermConditionQuotation,
                    Actions::View . '_' . Resources::LongTermConditionQuotation,
                    Actions::View . '_' . Resources::Receipt,
                    Actions::View . '_' . Resources::SapInterface,
                    Actions::View . '_' . Resources::GlAccouunt,
                ]
            ),
            new SideBarSubmenu(
                __('menu.insurers'),
                [
                    new SideBarLink(__('menu.request_premium'), route('admin.request-premium.index'), [Actions::View . '_' . Resources::RequestPremium]),
                    new SideBarLink(__('menu.insurance_car'), route('admin.insurance-car.index'), [Actions::View . '_' . Resources::InsuranceCar]),
                    new SideBarLink(__('menu.CMI'), route('admin.cmi-cars.index'), [Actions::View . '_' . Resources::CMI]),
                    new SideBarLink(__('menu.insurance_car_cmi_renew'), route('admin.insurance-cmi-renew.index'), [Actions::View . '_' . Resources::InsuranceCarCmiRenew]),
                    new SideBarLink(__('menu.cancel_CMI'), route('admin.cancel-cmi-cars.index'), [Actions::View . '_' . Resources::CancelCMI]),
                    new SideBarLink(__('menu.VMI'), route('admin.vmi-cars.index'), [Actions::View . '_' . Resources::VMI]),
                    new SideBarLink(__('menu.insurance_car_vmi_renew'), route('admin.insurance-vmi-renew.index'), [Actions::View . '_' . Resources::InsuranceCarVmiRenew]),
                    new SideBarLink(__('menu.cancel_VMI'), route('admin.cancel-vmi-cars.index'), [Actions::View . '_' . Resources::CancelVMI]),
                    new SideBarLink(__('menu.master_insurers'), route('admin.insurances-companies.index'), [Actions::View . '_' . Resources::InsuranceCompanies]),
                    new SideBarLink(__('menu.insurance_deduct'), route('admin.insurance-deducts.index'), [Actions::View . '_' . Resources::InsuranceDeduct]),
                    new SideBarLink(__('menu.insurance_loss_ratio'), route('admin.insurance-loss-ratios.index'), [Actions::View . '_' . Resources::InsuranceLossRatio]),


                ],
                [
                    Actions::View . '_' . Resources::InsuranceCompanies,
                    Actions::View . '_' . Resources::RequestPremium,
                    Actions::View . '_' . Resources::CMI,
                    Actions::View . '_' . Resources::VMI,
                    Actions::View . '_' . Resources::CancelVMI,
                    Actions::View . '_' . Resources::InsuranceCar,
                    Actions::View . '_' . Resources::InsuranceCarCmiRenew,
                    Actions::View . '_' . Resources::InsuranceCarVmiRenew,
                    Actions::View . '_' . Resources::InsuranceDeduct,
                    Actions::View . '_' . Resources::InsuranceLossRatio,
                ],
            ),
            new SideBarSubmenu(
                __('menu.car_transfer'),
                [
                    new SideBarLink(__('menu.transfer'), route('admin.transfer-cars.index'), [Actions::View . '_' . Resources::TransferCar]),
                    new SideBarLink(__('menu.transfer_receive'), route('admin.transfer-car-receives.index'), [Actions::View . '_' . Resources::TransferCarReceive]),
                ],
                [
                    Actions::View . '_' . Resources::TransferCar,
                    Actions::View . '_' . Resources::TransferCarReceive,
                ]
            ),
            new SideBarSubmenu(
                __('menu.borrow_car'),
                [
                    new SideBarLink(__('menu.borrow_request'), route('admin.borrow-cars.index'), [Actions::View . '_' . Resources::BorrowCar]),
                    new SideBarLink(__('menu.borrow_list'), route('admin.borrow-car-lists.index'), [Actions::View . '_' . Resources::BorrowCarList]),
                    new SideBarLink(__('menu.borrow_confirm_approve'), route('admin.borrow-car-confirm-approves.index'), [Actions::View . '_' . Resources::BorrowCarConfirmApprove]),
                    new SideBarLink(__('menu.borrow_approve'), route('admin.borrow-car-approves.index'), [Actions::View . '_' . Resources::BorrowCarApprove]),
                ],
                [
                    Actions::View . '_' . Resources::BorrowCar,
                    Actions::View . '_' . Resources::BorrowCarList,
                    Actions::View . '_' . Resources::BorrowCarConfirmApprove,
                    Actions::View . '_' . Resources::BorrowCarApprove,
                ]
            ),
            new SideBarSubmenu(
                __('menu.car_auction_head'),
                [
                    new SideBarLink(
                        __('menu.car_selling'),
                        CarAuctionTrait::sideBarSelling(),
                        [
                            Actions::View . '_' . Resources::SellingPrice,
                            Actions::View . '_' . Resources::SellingCar,
                        ]
                    ),
                    new SideBarLink(__('menu.car_selling_approve'), route('admin.selling-price-approves.index'), [Actions::View . '_' . Resources::SellingPriceApprove]),
                    new SideBarLink(__('menu.car_auction'), route('admin.car-auctions.index'), [Actions::View . '_' . Resources::CarAuction]),
                    new SideBarSubmenu(
                        __('menu.master_data'),
                        [
                            new SideBarLink(__('menu.auction_place'), route('admin.auction-places.index'), [Actions::View . '_' . Resources::AuctionPlace]),
                        ],
                        [
                            Actions::View . '_' . Resources::AuctionPlace
                        ]

                    ),
                ],
                [
                    Actions::View . '_' . Resources::SellingPrice,
                    Actions::View . '_' . Resources::SellingCar,
                    Actions::View . '_' . Resources::AuctionPlace,
                ]
            ),
            new SideBarSubmenu(
                __('menu.head_system_personnel'),
                [
                    new SideBarLink(__('menu.driving_job'), route('admin.driving-jobs.index'), [Actions::View . '_' . Resources::DrivingJob]),
                    new SideBarLink(__('menu.report_expense_driving_job'), route('admin.driver-report.index'), [Actions::View . '_' . Resources::DriverReport]),
                    new SideBarSubmenu(
                        __('menu.set_driver'),
                        [
                            new SideBarLink(__('menu.driver'), route('admin.drivers.index'), [Actions::View . '_' . Resources::Driver]),
                            new SideBarLink(__('menu.driving_skills'), route('admin.driving-skills.index'), [Actions::View . '_' . Resources::DrivingSkill]),
                            new SideBarLink(__('menu.driver_wage_categories'), route('admin.driver-wage-categories.index'), [Actions::View . '_' . Resources::DriverWageCategory]),
                            new SideBarLink(__('menu.driver_wages'), route('admin.driver-wages.index'), [Actions::View . '_' . Resources::DriverWage]),
                        ],
                        [
                            Actions::View . '_' . Resources::Driver,
                            Actions::View . '_' . Resources::DrivingSkill,
                            Actions::View . '_' . Resources::DriverWageCategory,
                            Actions::View . '_' . Resources::DriverWage,
                        ]
                    ),
                ],
                [
                    Actions::View . '_' . Resources::DrivingJob,
                    Actions::View . '_' . Resources::DriverReport,
                    Actions::View . '_' . Resources::Driver,
                    Actions::View . '_' . Resources::DrivingSkill,
                    Actions::View . '_' . Resources::DriverWageCategory,
                    Actions::View . '_' . Resources::DriverWage,
                ]
            ),
            new SideBarSubmenu(
                __('menu.head_system_data'),
                [
                    new SideBarSubmenu(
                        __('menu.head_system_car'),
                        [
                            new SideBarLink(__('menu.car_brand'), route('admin.car-brands.index'), [Actions::View . '_' . Resources::CarBrand]),
                            new SideBarLink(__('menu.car_category'), route('admin.car-categories.index'), [Actions::View . '_' . Resources::CarCategory]),
                            new SideBarLink(__('menu.car_type'), route('admin.car-types.index'), [Actions::View . '_' . Resources::CarType]),
                            new SideBarLink(__('menu.car_model'), route('admin.car-classes.index'), [Actions::View . '_' . Resources::CarClass]),
                            new SideBarLink(__('menu.car_tire'), route('admin.car-tires.index'), [Actions::View . '_' . Resources::CarTire]),
                            new SideBarLink(__('menu.car_battery'), route('admin.car-batteries.index'), [Actions::View . '_' . Resources::CarBattery]),
                            new SideBarLink(__('menu.car_wiper'), route('admin.car-wipers.index'), [Actions::View . '_' . Resources::CarWiper]),
                            new SideBarLink(__('menu.car_groups'), route('admin.car-groups.index'), [Actions::View . '_' . Resources::CarGroup]),
                            new SideBarLink(__('menu.car_paint'), route('admin.car-colors.index'), [Actions::View . '_' . Resources::CarColor]),
                            new SideBarLink(__('menu.accessories'), route('admin.accessories.index'), [Actions::View . '_' . Resources::Accessory]),
                        ],
                        [
                            Actions::View . '_' . Resources::CarBrand,
                            Actions::View . '_' . Resources::CarCategory,
                            Actions::View . '_' . Resources::CarType,
                            Actions::View . '_' . Resources::CarClass,
                            Actions::View . '_' . Resources::CarTire,
                            Actions::View . '_' . Resources::CarBattery,
                            Actions::View . '_' . Resources::CarWiper,
                            Actions::View . '_' . Resources::CarGroup,
                            Actions::View . '_' . Resources::CarColor,
                            Actions::View . '_' . Resources::Accessory,
                        ]
                    ),
                    new SideBarSubmenu(
                        __('menu.head_system_customer'),
                        [
                            new SideBarLink(__('menu.customer_group'), route('admin.customer-groups.index'), [Actions::View . '_' . Resources::CustomerGroup]),
                            new SideBarLink(__('menu.customer'), route('admin.customers.index'), [Actions::View . '_' . Resources::Customer]),
                        ],
                        [
                            Actions::View . '_' . Resources::CustomerGroup,
                            Actions::View . '_' . Resources::Customer,
                        ]
                    ),
                    new SideBarLink(__('menu.creditor'), route('admin.creditors.index'), [Actions::View . '_' . Resources::Creditor]),
                    new SideBarLink(__('menu.pdpa'), route('admin.pdpa-managements.index'), [Actions::View . '_' . Resources::Pdpa]),
                ],
                [
                    Actions::View . '_' . Resources::Creditor,
                    Actions::View . '_' . Resources::Pdpa,
                    Actions::View . '_' . Resources::CarBrand,
                    Actions::View . '_' . Resources::CarCategory,
                    Actions::View . '_' . Resources::CarType,
                    Actions::View . '_' . Resources::CarClass,
                    Actions::View . '_' . Resources::CarTire,
                    Actions::View . '_' . Resources::CarBattery,
                    Actions::View . '_' . Resources::CarWiper,
                    Actions::View . '_' . Resources::CarGroup,
                    Actions::View . '_' . Resources::CarColor,
                    Actions::View . '_' . Resources::Accessory,
                    Actions::View . '_' . Resources::CustomerGroup,
                    Actions::View . '_' . Resources::Customer,
                ]
            ),
            new SideBarHeading('Account'),
            new SideBarSubmenu(
                __('menu.head_user'),
                [
                    new SideBarLink(__('menu.users'), route('admin.users.index'), [Actions::View . '_' . Resources::User], 'icon-signal'),
                    new SideBarLink(__('menu.user_departments'), route('admin.departments.index'), [Actions::View . '_' . Resources::Department]),
                    new SideBarLink(__('menu.role'), route('admin.roles.index'), [Actions::View . '_' . Resources::Role]),
                    new SideBarLink(__('menu.permission'), route('admin.permissions.index'), [Actions::View . '_' . Resources::Permission]),
                    new SideBarLink(__('menu.config_approve'), route('admin.config-approves.index'), [Actions::View . '_' . Resources::ConfigApprove]),
                ],
                [
                    Actions::View . '_' . Resources::User,
                    Actions::View . '_' . Resources::Department,
                    Actions::View . '_' . Resources::Role,
                    Actions::View . '_' . Resources::Permission,
                    Actions::View . '_' . Resources::ConfigApprove,
                ],
                'icon-arrow-right-circle'
            ),
        ];
    }

    function render()
    {
        $html = '';
        foreach ($this->groupMenus as $item) {
            $html .= $item->render();
        }
        return $html;
    }
}
