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

class SideBarMenuRepository
{
    public $groupMenus;

    function __construct()
    {
        $this->groupMenus = [
            new SideBarHeading(__('menu.head_car')),
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

            new SideBarSubmenu(
                __('menu.head_parking_manage'),
                [
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
                            /* new SideBarLink(__('menu.car_move_warehouse')), */
                            new SideBarLink(__('menu.parking_zone'), route('admin.parking-lots.index'), [Actions::View . '_' . Resources::ParkingZone]),
                        ],
                        [
                            // Action __('menu.car_move_warehouse') here
                            Actions::View . '_' . Resources::ParkingZone,
                        ]
                    ),
                ],
                [
                    Actions::View . '_' . Resources::CarParkTransfer,
                    Actions::View . '_' . Resources::CarParkTransferLog,
                    // Action __('menu.car_move_warehouse') here
                    Actions::View . '_' . Resources::ParkingZone,
                ]
            ),
            new SideBarLink(__('menu.car_warehourse'), route('admin.cars.index'), [Actions::View . '_' . Resources::Car]),
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
                ],
                [
                    Actions::View . '_' . Resources::SellingPrice,
                    Actions::View . '_' . Resources::SellingCar,
                    Actions::View . '_' . Resources::SellingPriceApprove,
                    Actions::View . '_' . Resources::CarAuction,
                ]
            ),


            new SideBarHeading(__('menu.head_car_rental')),
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
            // new SideBarLink( __('menu.transport_task'),
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
                __('menu.head_sale_document'),
                [
                    new SideBarLink(__('menu.quotation'), route('admin.quotations.index'), [Actions::View . '_' . Resources::Quotation]),
                    new SideBarLink(__('menu.quotation_approve'), route('admin.quotation-approves.index'), [Actions::View . '_' . Resources::QuotationApprove]),
                ],
                [
                    Actions::View . '_' . Resources::Quotation,
                    Actions::View . '_' . Resources::QuotationApprove,
                ]
            ),
            new SideBarSubmenu(
                __('menu.head_rental_contract'),
                [
                    new SideBarLink(__('menu.contract_check_and_edit'), route('admin.contract-check-and-edit.index'), [Actions::View . '_' . Resources::ContractCheckAndEdit]),
                ],
                [
                    Actions::View . '_' . Resources::ContractCheckAndEdit,
                ]
            ),


            new SideBarHeading(__('menu.head_QA_management')),
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
                __('menu.repairs'),
                [
                    new SideBarLink(__('menu.repair_alert'), route('admin.repairs.index'), [Actions::View . '_' . Resources::Repair]),
                    new SideBarLink(__('menu.repair_order'), route('admin.repair-orders.index'), [Actions::View . '_' . Resources::RepairOrder]),
                    new SideBarLink(__('menu.approve_quotation'), route('admin.repair-quotation-approves.index'), [Actions::View . '_' . Resources::RepairQuotationApprove]),
                    new SideBarLink(__('menu.maintenance-cost'), route('admin.maintenance-cost.index'), [Actions::View . '_' . Resources::MainTenanceCost]),
                    // อนุมัติค่าใช้จ่ายใบสั่งซ่อม here
                    new SideBarLink(__('menu.repair_bill'), route('admin.repair-bills.index'), [Actions::View . '_' . Resources::RepairBill]),
                    new SideBarLink(__('menu.alert_check_distance'), route('admin.check-distance-notices.index'), [Actions::View . '_' . Resources::CheckDistanceNotice]),
                ],
                [
                    Actions::View . '_' . Resources::Repair,
                    Actions::View . '_' . Resources::RepairOrder,
                    Actions::View . '_' . Resources::RepairQuotationApprove,
                    Actions::View . '_' . Resources::MainTenanceCost,
                    // Acction for อนุมัติค่าใช้จ่ายใบสั่งซ่อม here
                    Actions::View . '_' . Resources::RepairBill,
                    Actions::View . '_' . Resources::CheckDistanceNotice,
                ],
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
                    Actions::View . '_' . Resources::GPSRemoveStopSignalAlert,
                    Actions::View . '_' . Resources::GPSCheckSignalShortTerm,
                    Actions::View . '_' . Resources::GPSCheckSignalLongTerm,
                    Actions::View . '_' . Resources::GPSCheckSignalShortTermBranch,
                    Actions::View . '_' . Resources::GPSCheckSignalKratos,
                    Actions::View . '_' . Resources::GPSRemoveStopSignalAlert,
                    Actions::View . '_' . Resources::GPSRemoveSignalJob,
                    Actions::View . '_' . Resources::GPSStopSignalJob,
                    Actions::View . '_' . Resources::GPSHistoricalDataAlert,
                    Actions::View . '_' . Resources::GPSHistoricalDataJob,
                ]
            ),

            new SideBarHeading(__('menu.head_driving_job')),
            new SideBarLink(__('menu.driving_job'), route('admin.driving-jobs.index'), [Actions::View . '_' . Resources::DrivingJob]),
            // menu บันทึกค่าพนักงานขับรถอื่น ๆ

            new SideBarHeading(__('menu.accident_and_replacement')),
            new SideBarSubmenu(
                __('menu.accident'),
                [
                    new SideBarLink(__('menu.accident_report_sheet'), route('admin.accident-inform-sheets.index'), [Actions::View . '_' . Resources::AccidentInformSheet]),
                    new SideBarLink(__('menu.accident_order'), route('admin.accident-orders.index'), [Actions::View . '_' . Resources::AccidentOrder]),
                    new SideBarLink(__('menu.accident_follow_up_repair'), route('admin.accident-follow-up-repairs.index'), [Actions::View . '_' . Resources::AccidentFollowUpRepair]),
                    new SideBarLink(__('menu.accident_order_approve'), route('admin.accident-order-approves.index'), [Actions::View . '_' . Resources::AccidentOrderApprove]),
                    new SideBarLink(__('menu.accident_order_sheet_approve'), route('admin.accident-order-sheet-approves.index'), [Actions::View . '_' . Resources::AccidentOrderSheetApprove]),
                    new SideBarLink(__('menu.accident_order_sheet_ttl_approve'), route('admin.accident-order-sheet-ttl-approves.index'), [Actions::View . '_' . Resources::AccidentOrderSheetTTLApprove]),
                ],
                [
                    Actions::View . '_' . Resources::AccidentInformSheet,
                    Actions::View . '_' . Resources::AccidentOrder,
                    Actions::View . '_' . Resources::AccidentOrderApprove,
                    Actions::View . '_' . Resources::AccidentFollowUpRepair,
                    Actions::View . '_' . Resources::AccidentOrderSheetApprove,
                    Actions::View . '_' . Resources::AccidentOrderSheetTTLApprove,
                ]
            ),
            new SideBarSubmenu(
                __('menu.replacement_job'),
                [
                    new SideBarLink(__('menu.replacement_car_history'), route('admin.replacement-type-cars.index'), [Actions::View . '_' . Resources::ReplacementTypeCar]),
                    new SideBarLink(__('menu.replacement_car'), route('admin.replacement-cars.index'), [Actions::View . '_' . Resources::ReplacementCar]),
                    new SideBarLink(__('menu.replacement_car_approve'), route('admin.replacement-car-approves.index'), [Actions::View . '_' . Resources::ReplacementCarApprove]),
                ],
                [
                    Actions::View . '_' . Resources::ReplacementTypeCar,
                    Actions::View . '_' . Resources::ReplacementCar,
                    Actions::View . '_' . Resources::ReplacementCarApprove,
                ]
            ),

            new SideBarHeading(__('menu.head_registration')),
            new SideBarLink(__('menu.register_new'), route('admin.registers.index'), [Actions::View . '_' . Resources::Register]),
            new SideBarLink(__('menu.ownership_transfer'), route('admin.ownership-transfers.index'), [Actions::View . '_' . Resources::OwnershipTransfer]),
            new SideBarLink(__('menu.tax_renewal'), route('admin.tax-renewals.index'), [Actions::View . '_' . Resources::TaxRenewal]),
            new SideBarLink(__('menu.sign_yellow_ticket'), route('admin.sign-yellow-tickets.index'), [Actions::View . '_' . Resources::SignYellowTicket]),
            new SideBarLink(__('menu.request_change_registration'), route('admin.request-change-registrations.index'), [Actions::View . '_' . Resources::RequestChangeRegistration]),
            new SideBarLink(__('menu.change_registration'), route('admin.change-registrations.index'), [Actions::View . '_' . Resources::ChangeRegistration]),


            new SideBarHeading(__('menu.head_insurance')),
            new SideBarLink(__('menu.request_premium'), route('admin.request-premium.index'), [Actions::View . '_' . Resources::RequestPremium]),
            new SideBarLink(__('menu.insurance_car'), route('admin.insurance-car.index'), [Actions::View . '_' . Resources::InsuranceCar]),
            new SideBarSubmenu(
                __('menu.CMI'),
                [
                    new SideBarLink(__('menu.CMI_new'), route('admin.cmi-cars.index'), [Actions::View . '_' . Resources::CMI]),
                    new SideBarLink(__('menu.insurance_car_cmi_renew'), route('admin.insurance-cmi-renew.index'), [Actions::View . '_' . Resources::InsuranceCarCmiRenew]),
                    new SideBarLink(__('menu.cancel_CMI'), route('admin.cancel-cmi-cars.index'), [Actions::View . '_' . Resources::CancelCMI]),
                ],
                [
                    Actions::View . '_' . Resources::CMI,
                    Actions::View . '_' . Resources::InsuranceCarCmiRenew,
                    Actions::View . '_' . Resources::CancelCMI,
                ]
            ),
            new SideBarSubmenu(
                __('menu.VMI'),
                [
                    new SideBarLink(__('menu.VMI_new'), route('admin.vmi-cars.index'), [Actions::View . '_' . Resources::VMI]),
                    new SideBarLink(__('menu.insurance_car_vmi_renew'), route('admin.insurance-vmi-renew.index'), [Actions::View . '_' . Resources::InsuranceCarVmiRenew]),
                    new SideBarLink(__('menu.cancel_VMI'), route('admin.cancel-vmi-cars.index'), [Actions::View . '_' . Resources::CancelVMI]),
                ],
                [
                    Actions::View . '_' . Resources::VMI,
                    Actions::View . '_' . Resources::InsuranceCarVmiRenew,
                    Actions::View . '_' . Resources::CancelVMI,
                ]
            ),
            new SideBarLink(__('menu.pay_premium_approve'), route('admin.pay-premiums.index'), [Actions::View . '_' . Resources::PayPremiumApprove]),
            // new SideBarLink(__('ต่ออายุภาษีรถยนต์')),
            // new SideBarLink(__('ประกันรถขนส่ง')),
            new SideBarLink(__('menu.insurance_deduct'), route('admin.insurance-deducts.index'), [Actions::View . '_' . Resources::InsuranceDeduct]),
            new SideBarLink(__('menu.insurance_loss_ratio'), route('admin.insurance-loss-ratios.index'), [Actions::View . '_' . Resources::InsuranceLossRatio]),


            new SideBarHeading(__('menu.head_accounting')),
            new SideBarSubmenu(
                __('menu.finances'),
                [
                    new SideBarLink(__('menu.finance_data_prepare'), route('admin.finance-request.index'), [Actions::View . '_' . Resources::FinanceRequest]),
                    new SideBarLink(__('menu.finance_approve'), route('admin.finance-request-approve.index'), [Actions::View . '_' . Resources::FinanceRequestApprove]),
                    new SideBarLink(__('menu.finance_contract'), route('admin.finance-contract.index'), [Actions::View . '_' . Resources::FinanceContract]),
                    new SideBarLink(__('menu.finance_all'), route('admin.finance.index'), [Actions::View . '_' . Resources::Finance]),
                    // new SideBarLink(__('menu.finance_close'),
                ],
                [
                    Actions::View . '_' . Resources::FinanceRequest,
                    Actions::View . '_' . Resources::FinanceRequestApprove,
                    Actions::View . '_' . Resources::FinanceContract,
                    Actions::View . '_' . Resources::Finance,
                    // Action for __('menu.finance_close'),
                ]
            ),
            // new SideBarLink(__('menu.reciept_inform'),
            // new SideBarLink(__('menu.reciept_create'),
            new SideBarLink(__('menu.request_receipt'), route('admin.request-receipts.index'), [Actions::View . '_' . Resources::RequestReceipt]),
            new SideBarLink(__('menu.receipt'), route('admin.receipts.index'), [Actions::View . '_' . Resources::Receipt]),
            new SideBarLink(__('menu.income_account'), route('admin.income-accounts.index'), [Actions::View . '_' . Resources::SAPInterfaceAR]),
            new SideBarLink(__('menu.gl_accouunt'), route('admin.general-ledger-accounts.index'), [Actions::View . '_' . Resources::GlAccouunt]),
            new SideBarLink(__('menu.other_invoice'), route('admin.invoice-others.index'), [Actions::View . '_' . Resources::OtherInvoice]),
            new SideBarLink(__('menu.long_term_rental_invoice'), route('admin.invoice-lt-rentals.index'), [Actions::View . '_' . Resources::LongTermRentalInvoice]),
            new SideBarLink(__('menu.short_term_rental_invoice'), route('admin.invoice-st-rentals.index'), [Actions::View . '_' . Resources::ShortTermRentalInvoice]),
            // new SideBarLink(__('menu.transportation_invoice'),
            // new SideBarLink(__('menu.credit_note_create'),
            new SideBarLink(__('menu.credit_note'), route('admin.credit-notes.index'), [Actions::View . '_' . Resources::CreditNote]),
            new SideBarLink(__('menu.debt_collection'), route('admin.debt-collections.index'), [Actions::View . '_' . Resources::DebtCollection]),
            new SideBarLink(__('menu.billing_verify'), route('admin.check-billings.index'), [Actions::View . '_' . Resources::CheckBillingDate]),
            // new SideBarLink(__('menu.billing_verify'),
            new SideBarLink(__('menu.asset_create'), route('admin.asset-cars.index'), [Actions::View . '_' . Resources::Asset]),
            new SideBarLink(__('menu.expense_account'), route('admin.expense-accounts.index'), [Actions::View . '_' . Resources::SapInterfaceAP]),
            new SideBarLink(__('menu.misc_expenses_record'), route('admin.record-other-expenses.index'), [Actions::View . '_' . Resources::RecordOtherExpenses]),
            new SideBarLink(__('menu.petty_cash_record'), route('admin.record-petty-cashes.index'), [Actions::View . '_' . Resources::RecordPettyCash]),
            new SideBarLink(__('menu.petty_cash_verify'), route('admin.check-petty-cashes.index'), [Actions::View . '_' . Resources::CheckPettyCash]),


            new SideBarHeading(__('menu.head_risk_management')),
            new SideBarSubmenu(
                __('menu.credit_check'),
                [
                    new SideBarLink(__('menu.credit_check_inform'), route('admin.check-credit-new-customers.index'), [Actions::View . '_' . Resources::ContractCheckCreditNewCustomer]),
                    new SideBarLink(__('menu.credit_check_job'), route('admin.check-credit-approves.index'), [Actions::View . '_' . Resources::ContractCheckCreditApprove]),
                ],
                [
                    Actions::View . '_' . Resources::ContractCheckCreditNewCustomer,
                    Actions::View . '_' . Resources::ContractCheckCreditApprove,
                ]
            ),
            new SideBarSubmenu(
                __('menu.contract_header'),
                [
                    new SideBarLink(__('menu.contract_list'), route('admin.contracts.index'), [Actions::View . '_' . Resources::ContractAllList]),
                    new SideBarLink(__('menu.contract_category'), route('admin.contract-category.index'), [Actions::View . '_' . Resources::ContractMasterDataCategory]),
                ],
                [
                    Actions::View . '_' . Resources::ContractAllList,
                    Actions::View . '_' . Resources::ContractMasterDataCategory,
                ]
            ),
            new SideBarLink(__('menu.litigations'), route('admin.litigations.index'), [Actions::View . '_' . Resources::Litigation]),
            new SideBarLink(__('menu.litigation_approves'), route('admin.litigation-approves.index'), [Actions::View . '_' . Resources::LitigationApprove]),
            new SideBarLink(__('menu.traffic_ticket'), route('admin.traffic-tickets.index'), [Actions::View . '_' . Resources::TrafficTicket]),
            new SideBarLink(__('menu.m_flow'), route('admin.m-flows.index'), [Actions::View . '_' . Resources::MFlow]),
            new SideBarLink(__('menu.indemnity_claim'), route('admin.compensations.index'), [Actions::View . '_' . Resources::Compensation]),
            new SideBarLink(__('menu.indemnity_claim_approve'), route('admin.compensation-approves.index'), [Actions::View . '_' . Resources::CompensationApprove]),
            // new SideBarSubmenu(
            //     __('งานหนังสือมอบอำนาจ'),
            //     [
            //         new SideBarLink(__('ขอหนังสือมอบอำนาจ')
            //         new SideBarLink(__('อนุมัติหนังสือมอบอำนาจ')
            //     ],
            //     [
            //         // Actions for ขอหนังสือมอบอำนาจ,
            //         // Actions for อนุมัติหนังสือมอบอำนาจ
            //     ]
            // ),


            new SideBarHeading(__('menu.call_center')),
            // new SideBarLink(__('menu.general_inquiry')
            new SideBarLink(__('menu.accident_report'), route('admin.accident-informs.index'), [Actions::View . '_' . Resources::AccidentInform]),
            new SideBarLink(__('menu.follow_up_repair'), route('admin.call-center-follow-up-repairs.index'), [Actions::View . '_' . Resources::CallCenterFollowUpRepair]),
            new SideBarLink(__('menu.repair_alert'), route('admin.call-center-repairs.index'), [Actions::View . '_' . Resources::CallCenterRepair]),
            new SideBarLink(__('menu.repair_order'), route('admin.call-center-repair-orders.index'), [Actions::View . '_' . Resources::CallCenterRepairOrder]),

            // เมนู รายการขอจัดทำไฟแนนซ์
            //            new SideBarHeading(__('menu.head_finance')),


            new SideBarHeading(__('menu.head_system_data')),
            new SideBarLink(__('menu.branch_company'), route('admin.branches.index'), [Actions::View . '_' . Resources::Branch]),
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
                __('menu.head_staff'),
                [
                    new SideBarSubmenu(
                        __('menu.set_driver'),
                        [
                            new SideBarLink(__('menu.driver'), route('admin.drivers.index'), [Actions::View . '_' . Resources::Driver]),
                            new SideBarLink(__('menu.position'), route('admin.positions.index'), [Actions::View . '_' . Resources::Position]),
                            new SideBarLink(__('menu.driving_skills'), route('admin.driving-skills.index'), [Actions::View . '_' . Resources::DrivingSkill]),
                            new SideBarLink(__('menu.driver_wage_categories'), route('admin.driver-wage-categories.index'), [Actions::View . '_' . Resources::DriverWageCategory]),
                            new SideBarLink(__('menu.driver_wages'), route('admin.driver-wages.index'), [Actions::View . '_' . Resources::DriverWage]),
                        ],
                        [
                            Actions::View . '_' . Resources::Driver,
                            Actions::View . '_' . Resources::Position,
                            Actions::View . '_' . Resources::DrivingSkill,
                            Actions::View . '_' . Resources::DriverWageCategory,
                            Actions::View . '_' . Resources::DriverWage,
                        ]
                    ),
                ],
                [
                    Actions::View . '_' . Resources::Driver,
                    Actions::View . '_' . Resources::Position,
                    Actions::View . '_' . Resources::DrivingSkill,
                    Actions::View . '_' . Resources::DriverWageCategory,
                    Actions::View . '_' . Resources::DriverWage,
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
            new SideBarLink(__('menu.master_insurers'), route('admin.insurances-companies.index'), [Actions::View . '_' . Resources::InsuranceCompanies]),
            new SideBarLink(__('menu.garage'), route('admin.garages.index'), [Actions::View . '_' . Resources::Garage]),
            new SideBarLink(__('menu.auction_place'), route('admin.auction-places.index'), [Actions::View . '_' . Resources::AuctionPlace]),
            new SideBarLink(__('menu.master_check_distance'), route('admin.check-distances.index'), [Actions::View . '_' . Resources::CheckDistance]),
            new SideBarLink(__('menu.master_repair_list'), route('admin.repair-lists.index'), [Actions::View . '_' . Resources::RepairList]),
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
            new SideBarLink(__('menu.master_condition'), route('admin.condition-repair-services.index'), [Actions::View . '_' . Resources::ConditionRepairService]),
            new SideBarLink(__('menu.pdpa'), route('admin.pdpa-managements.index'), [Actions::View . '_' . Resources::Pdpa]),
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


            new SideBarHeading('System Admin'),
            new SideBarLink(__('menu.users'), route('admin.users.index'), [Actions::View . '_' . Resources::User]),
            new SideBarLink(__('menu.user_departments'), route('admin.departments.index'), [Actions::View . '_' . Resources::Department]),
            new SideBarLink(__('menu.sections'), route('admin.sections.index'), [Actions::View . '_' . Resources::Section]),
            new SideBarLink(__('menu.role'), route('admin.roles.index'), [Actions::View . '_' . Resources::Role]),
            new SideBarLink(__('menu.permission'), route('admin.permissions.index'), [Actions::View . '_' . Resources::Permission]),
            new SideBarLink(__('menu.config_approve'), route('admin.config-approves.index-branch'), [Actions::View . '_' . Resources::ConfigApprove]),

            new SideBarHeading(__('menu.head_dashboard')),
            new SideBarLink(__('menu.head_dashboard'), route('admin.home'), []),
        ];
    }

    function render()
    {
        $html = '';
        $html_heading_prev = '';
        $html_content = '';
        foreach ($this->groupMenus as $item) {
            if (is_a($item, SideBarHeading::class)) {
                // process header // hide when empty
                if (!empty($html_content)) {
                    $html .= $html_heading_prev;
                    $html .= $html_content;
                }
                $html_content = null;
                $html_heading_prev = $item->render();
            } else {
                // collect html content
                $html_content .= $item->render();
            }
            //$html .= $item->render();
        }

        if (!empty($html_content)) {
            $html .= $html_heading_prev;
            $html .= $html_content;
        }

        return $html;
    }
}
