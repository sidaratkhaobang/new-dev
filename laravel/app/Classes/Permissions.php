<?php

namespace App\Classes;

use App\Enums\Actions;
use App\Enums\Resources;

class Permissions
{
    static function getAllPermissions()
    {
        return [
            // Car
            Resources::PurchaseRequisition => [
                _p(Actions::View, Resources::PurchaseRequisition),
                _p(Actions::Manage, Resources::PurchaseRequisition),
            ],
            Resources::PurchaseRequisitionApprove => [
                _p(Actions::View, Resources::PurchaseRequisitionApprove),
                _p(Actions::Manage, Resources::PurchaseRequisitionApprove),
            ],
            Resources::OpenPurchaseOrder => [
                _p(Actions::View, Resources::OpenPurchaseOrder),
                _p(Actions::Manage, Resources::OpenPurchaseOrder),
            ],
            Resources::PurchaseOrder => [
                _p(Actions::View, Resources::PurchaseOrder),
                _p(Actions::Manage, Resources::PurchaseOrder),
            ],
            Resources::PurchaseOrderApprove => [
                _p(Actions::View, Resources::PurchaseOrderApprove),
                _p(Actions::Manage, Resources::PurchaseOrderApprove),
            ],
            Resources::ImportCar => [
                _p(Actions::View, Resources::ImportCar),
                _p(Actions::Manage, Resources::ImportCar),
            ],
            Resources::ImportCarList => [
                _p(Actions::View, Resources::ImportCarList),
                _p(Actions::Manage, Resources::ImportCarList),
            ],
            Resources::InstallEquipment => [
                _p(Actions::View, Resources::InstallEquipment),
                _p(Actions::Manage, Resources::InstallEquipment),
            ],
            Resources::InstallEquipmentPO => [
                _p(Actions::View, Resources::InstallEquipmentPO),
                _p(Actions::Manage, Resources::InstallEquipmentPO),
            ],
            Resources::InstallEquipmentPOApprove => [
                _p(Actions::View, Resources::InstallEquipmentPOApprove),
                _p(Actions::Manage, Resources::InstallEquipmentPOApprove),
            ],
            Resources::CarParkTransfer => [
                _p(Actions::View, Resources::CarParkTransfer),
                _p(Actions::Manage, Resources::CarParkTransfer),
            ],
            Resources::CarParkTransferLog => [
                _p(Actions::View, Resources::CarParkTransferLog),
                _p(Actions::Manage, Resources::CarParkTransferLog),
            ],
            Resources::ParkingZone => [
                _p(Actions::View, Resources::ParkingZone),
                _p(Actions::Manage, Resources::ParkingZone),
            ],
            Resources::Car => [
                _p(Actions::View, Resources::Car),
                _p(Actions::Manage, Resources::Car),
            ],
            Resources::Register => [
                _p(Actions::View, Resources::Register),
                _p(Actions::Manage, Resources::Register),
            ],
            Resources::OwnershipTransfer => [
                _p(Actions::View, Resources::OwnershipTransfer),
                _p(Actions::Manage, Resources::OwnershipTransfer),
            ],
            Resources::TaxRenewal => [
                _p(Actions::View, Resources::TaxRenewal),
                _p(Actions::Manage, Resources::TaxRenewal),
            ],
            Resources::SignYellowTicket => [
                _p(Actions::View, Resources::SignYellowTicket),
                _p(Actions::Manage, Resources::SignYellowTicket),
            ],
            Resources::RequestChangeRegistration => [
                _p(Actions::View, Resources::RequestChangeRegistration),
                _p(Actions::Manage, Resources::RequestChangeRegistration),
            ],
            Resources::ChangeRegistration => [
                _p(Actions::View, Resources::ChangeRegistration),
                _p(Actions::Manage, Resources::ChangeRegistration),
            ],
            Resources::AccidentInform => [
                _p(Actions::View, Resources::AccidentInform),
                _p(Actions::Manage, Resources::AccidentInform),
            ],
            Resources::CallCenterFollowUpRepair => [
                _p(Actions::View, Resources::CallCenterFollowUpRepair),
                _p(Actions::Manage, Resources::CallCenterFollowUpRepair),
            ],
            Resources::CarInspection => [
                _p(Actions::View, Resources::CarInspection),
                _p(Actions::Manage, Resources::CarInspection),
            ],
            Resources::ConfigCarInspection => [
                _p(Actions::View, Resources::ConfigCarInspection),
                _p(Actions::Manage, Resources::ConfigCarInspection),
            ],
            Resources::ConfigInspectionFlow => [
                _p(Actions::View, Resources::ConfigInspectionFlow),
                _p(Actions::Manage, Resources::ConfigInspectionFlow),
            ],
            Resources::GPSAlert => [
                _p(Actions::View, Resources::GPSAlert),
                _p(Actions::Manage, Resources::GPSAlert),
            ],
            Resources::GPSServiceCharge => [
                _p(Actions::View, Resources::GPSServiceCharge),
                _p(Actions::Manage, Resources::GPSServiceCharge),
            ],
            Resources::GPSCar => [
                _p(Actions::View, Resources::GPSCar),
                _p(Actions::Manage, Resources::GPSCar),
            ],
            Resources::GPSCheckSignalAlert => [
                _p(Actions::View, Resources::GPSCheckSignalAlert),
                _p(Actions::Manage, Resources::GPSCheckSignalAlert),
            ],
            Resources::GPSCheckSignalShortTerm => [
                _p(Actions::View, Resources::GPSCheckSignalShortTerm),
                _p(Actions::Manage, Resources::GPSCheckSignalShortTerm),
            ],
            Resources::GPSCheckSignalLongTerm => [
                _p(Actions::View, Resources::GPSCheckSignalLongTerm),
                _p(Actions::Manage, Resources::GPSCheckSignalLongTerm),
            ],
            Resources::GPSCheckSignalReplacement => [
                _p(Actions::View, Resources::GPSCheckSignalReplacement),
                _p(Actions::Manage, Resources::GPSCheckSignalReplacement),
            ],
            Resources::GPSCheckSignalShortTermBranch => [
                _p(Actions::View, Resources::GPSCheckSignalShortTermBranch),
                _p(Actions::Manage, Resources::GPSCheckSignalShortTermBranch),
            ],
            Resources::GPSCheckSignalKratos => [
                _p(Actions::View, Resources::GPSCheckSignalKratos),
                _p(Actions::Manage, Resources::GPSCheckSignalKratos),
            ],
            Resources::GPSRemoveStopSignalAlert => [
                _p(Actions::View, Resources::GPSRemoveStopSignalAlert),
                _p(Actions::Manage, Resources::GPSRemoveStopSignalAlert),
            ],
            Resources::GPSRemoveStopSignalJob => [
                _p(Actions::View, Resources::GPSRemoveStopSignalJob),
                _p(Actions::Manage, Resources::GPSRemoveStopSignalJob),
            ],
            Resources::GPSRemoveSignalJob => [
                _p(Actions::View, Resources::GPSRemoveSignalJob),
                _p(Actions::Manage, Resources::GPSRemoveSignalJob),
            ],
            Resources::GPSStopSignalJob => [
                _p(Actions::View, Resources::GPSStopSignalJob),
                _p(Actions::Manage, Resources::GPSStopSignalJob),
            ],
            Resources::GPSHistoricalDataAlert => [
                _p(Actions::View, Resources::GPSHistoricalDataAlert),
                _p(Actions::Manage, Resources::GPSHistoricalDataAlert),
            ],
            Resources::GPSHistoricalDataJob => [
                _p(Actions::View, Resources::GPSHistoricalDataJob),
                _p(Actions::Manage, Resources::GPSHistoricalDataJob),
            ],
            Resources::ContractCheckCreditNewCustomer => [
                _p(Actions::View, Resources::ContractCheckCreditNewCustomer),
                _p(Actions::Manage, Resources::ContractCheckCreditNewCustomer),
            ],
            Resources::ContractCheckCreditApprove => [
                _p(Actions::View, Resources::ContractCheckCreditApprove),
                _p(Actions::Manage, Resources::ContractCheckCreditApprove),
            ],
            Resources::ContractAllList => [
                _p(Actions::View, Resources::ContractAllList),
                _p(Actions::Manage, Resources::ContractAllList),
            ],
            Resources::ContractCheckAndEdit => [
                _p(Actions::View, Resources::ContractCheckAndEdit),
                _p(Actions::Manage, Resources::ContractCheckAndEdit),
            ],
            Resources::ContractMasterDataCategory => [
                _p(Actions::View, Resources::ContractMasterDataCategory),
                _p(Actions::Manage, Resources::ContractMasterDataCategory),

            ],
            Resources::ReplacementCarInform => [
                _p(Actions::View, Resources::ReplacementCarInform),
                _p(Actions::Manage, Resources::ReplacementCarInform),
            ],
            Resources::ReplacementCar => [
                _p(Actions::View, Resources::ReplacementCar),
                _p(Actions::Manage, Resources::ReplacementCar),
            ],
            Resources::ReplacementCarApprove => [
                _p(Actions::View, Resources::ReplacementCarApprove),
                _p(Actions::Manage, Resources::ReplacementCarApprove),
            ],
            Resources::Garage => [
                _p(Actions::View, Resources::Garage),
                _p(Actions::Manage, Resources::Garage),
            ],
            Resources::AccidentInformSheet => [
                _p(Actions::View, Resources::AccidentInformSheet),
                _p(Actions::Manage, Resources::AccidentInformSheet),
            ],
            Resources::AccidentOrder => [
                _p(Actions::View, Resources::AccidentOrder),
                _p(Actions::Manage, Resources::AccidentOrder),
            ],
            Resources::AccidentOrderApprove => [
                _p(Actions::View, Resources::AccidentOrderApprove),
                _p(Actions::Manage, Resources::AccidentOrderApprove),
            ],
            Resources::AccidentOrderSheetApprove => [
                _p(Actions::View, Resources::AccidentOrderSheetApprove),
                _p(Actions::Manage, Resources::AccidentOrderSheetApprove),
            ],
            Resources::AccidentOrderSheetTTLApprove => [
                _p(Actions::View, Resources::AccidentOrderSheetTTLApprove),
                _p(Actions::Manage, Resources::AccidentOrderSheetTTLApprove),
            ],
            Resources::AccidentFollowUpRepair => [
                _p(Actions::View, Resources::AccidentFollowUpRepair),
                _p(Actions::Manage, Resources::AccidentFollowUpRepair),
            ],
            Resources::ReplacementTypeCar => [
                _p(Actions::View, Resources::ReplacementTypeCar),
                _p(Actions::Manage, Resources::ReplacementTypeCar),
            ],
            Resources::CheckDistance => [
                _p(Actions::View, Resources::CheckDistance),
                _p(Actions::Manage, Resources::CheckDistance),
            ],
            Resources::RepairList => [
                _p(Actions::View, Resources::RepairList),
                _p(Actions::Manage, Resources::RepairList),
            ],
            Resources::ConditionRepairService => [
                _p(Actions::View, Resources::ConditionRepairService),
                _p(Actions::Manage, Resources::ConditionRepairService),
            ],
            Resources::CallCenterRepair => [
                _p(Actions::View, Resources::CallCenterRepair),
                _p(Actions::Manage, Resources::CallCenterRepair),
            ],
            Resources::Repair => [
                _p(Actions::View, Resources::Repair),
                _p(Actions::Manage, Resources::Repair),
            ],
            Resources::CallCenterRepairOrder => [
                _p(Actions::View, Resources::CallCenterRepairOrder),
                _p(Actions::Manage, Resources::CallCenterRepairOrder),
            ],
            Resources::RepairOrder => [
                _p(Actions::View, Resources::RepairOrder),
                _p(Actions::Manage, Resources::RepairOrder),
            ],
            Resources::RepairQuotationApprove => [
                _p(Actions::View, Resources::RepairQuotationApprove),
                _p(Actions::Manage, Resources::RepairQuotationApprove),
            ],
            Resources::CheckDistanceNotice => [
                _p(Actions::View, Resources::CheckDistanceNotice),
                _p(Actions::Manage, Resources::CheckDistanceNotice),
            ],
            Resources::ShortTermRental => [
                _p(Actions::View, Resources::ShortTermRental),
                _p(Actions::Manage, Resources::ShortTermRental),
            ],
            Resources::Operation => [
                _p(Actions::View, Resources::Operation),
                _p(Actions::Manage, Resources::Operation),
            ],
            Resources::CarServiceType => [
                _p(Actions::View, Resources::CarServiceType),
                _p(Actions::Manage, Resources::CarServiceType),
            ],
            Resources::ServiceType => [
                _p(Actions::View, Resources::ServiceType),
                _p(Actions::Manage, Resources::ServiceType),
            ],
            Resources::LocationGroup => [
                _p(Actions::View, Resources::LocationGroup),
                _p(Actions::Manage, Resources::LocationGroup),
            ],
            Resources::Location => [
                _p(Actions::View, Resources::Location),
                _p(Actions::Manage, Resources::Location),
            ],
            Resources::Branch => [
                _p(Actions::View, Resources::Branch),
                _p(Actions::Manage, Resources::Branch),
            ],
            Resources::Product => [
                _p(Actions::View, Resources::Product),
                _p(Actions::Manage, Resources::Product),
            ],
            Resources::ProductAdditional => [
                _p(Actions::View, Resources::ProductAdditional),
                _p(Actions::Manage, Resources::ProductAdditional),
            ],
            Resources::Promotion => [
                _p(Actions::View, Resources::Promotion),
                _p(Actions::Manage, Resources::Promotion),
            ],
            Resources::LongTermRental => [
                _p(Actions::View, Resources::LongTermRental),
                _p(Actions::Manage, Resources::LongTermRental),
            ],
            Resources::LongTermRentalSpec => [
                _p(Actions::View, Resources::LongTermRentalSpec),
                _p(Actions::Manage, Resources::LongTermRentalSpec),
            ],
            Resources::LongTermRentalSpecCheckCar => [
                _p(Actions::View, Resources::LongTermRentalSpecCheckCar),
                _p(Actions::Manage, Resources::LongTermRentalSpecCheckCar),
            ],
            Resources::LongTermRentalSpecsAccessory => [
                _p(Actions::View, Resources::LongTermRentalSpecsAccessory),
                _p(Actions::Manage, Resources::LongTermRentalSpecsAccessory),
            ],
            Resources::LongTermRentalSpecApprove => [
                _p(Actions::View, Resources::LongTermRentalSpecApprove),
                _p(Actions::Manage, Resources::LongTermRentalSpecApprove),
            ],
            Resources::LongTermRentalComparePrice => [
                _p(Actions::View, Resources::LongTermRentalComparePrice),
                _p(Actions::Manage, Resources::LongTermRentalComparePrice),
            ],
            Resources::LongTermRentalQuotation => [
                _p(Actions::View, Resources::LongTermRentalQuotation),
                _p(Actions::Manage, Resources::LongTermRentalQuotation),
            ],
            Resources::LongTermRentalType => [
                _p(Actions::View, Resources::LongTermRentalType),
                _p(Actions::Manage, Resources::LongTermRentalType),
            ],
            Resources::LongTermRentalBom => [
                _p(Actions::View, Resources::LongTermRentalBom),
                _p(Actions::Manage, Resources::LongTermRentalBom),
            ],
            Resources::AuctionRejectReason => [
                _p(Actions::View, Resources::AuctionRejectReason),
                _p(Actions::Manage, Resources::AuctionRejectReason),
            ],
            Resources::Quotation => [
                _p(Actions::View, Resources::Quotation),
                _p(Actions::Manage, Resources::Quotation),
            ],
            Resources::QuotationApprove => [
                _p(Actions::View, Resources::QuotationApprove),
                _p(Actions::Manage, Resources::QuotationApprove),
            ],
            Resources::ShortTermConditionQuotation => [
                _p(Actions::View, Resources::ShortTermConditionQuotation),
                _p(Actions::Manage, Resources::ShortTermConditionQuotation),
            ],
            Resources::LongTermConditionQuotation => [
                _p(Actions::View, Resources::LongTermConditionQuotation),
                _p(Actions::Manage, Resources::LongTermConditionQuotation),
            ],
            Resources::RequestReceipt => [
                _p(Actions::View, Resources::RequestReceipt),
                _p(Actions::Manage, Resources::RequestReceipt),
            ],
            Resources::Receipt => [
                _p(Actions::View, Resources::Receipt),
                _p(Actions::Manage, Resources::Receipt),
            ],
            Resources::SapInterface => [
                _p(Actions::View, Resources::SapInterface),
                _p(Actions::Manage, Resources::SapInterface),
            ],
            Resources::SAPInterfaceAR => [
                _p(Actions::View, Resources::SAPInterfaceAR),
                _p(Actions::Manage, Resources::SAPInterfaceAR),
            ],
            Resources::GlAccouunt => [
                _p(Actions::View, Resources::GlAccouunt),
                _p(Actions::Manage, Resources::GlAccouunt),
            ],
            Resources::TransferCar => [
                _p(Actions::View, Resources::TransferCar),
                _p(Actions::Manage, Resources::TransferCar),
            ],
            Resources::TransferCarReceive => [
                _p(Actions::View, Resources::TransferCarReceive),
                _p(Actions::Manage, Resources::TransferCarReceive),
            ],
            Resources::BorrowCar => [
                _p(Actions::View, Resources::BorrowCar),
                _p(Actions::Manage, Resources::BorrowCar),
            ],
            Resources::BorrowCarList => [
                _p(Actions::View, Resources::BorrowCarList),
                _p(Actions::Manage, Resources::BorrowCarList),
            ],
            Resources::BorrowCarConfirmApprove => [
                _p(Actions::View, Resources::BorrowCarConfirmApprove),
                _p(Actions::Manage, Resources::BorrowCarConfirmApprove),
            ],
            Resources::BorrowCarApprove => [
                _p(Actions::View, Resources::BorrowCarApprove),
                _p(Actions::Manage, Resources::BorrowCarApprove),
            ],

            Resources::DrivingJob => [
                _p(Actions::View, Resources::DrivingJob),
                _p(Actions::Manage, Resources::DrivingJob),
            ],
            Resources::DriverReport => [
                _p(Actions::View, Resources::DriverReport),
                _p(Actions::Manage, Resources::DriverReport),
            ],
            Resources::Driver => [
                _p(Actions::View, Resources::Driver),
                _p(Actions::Manage, Resources::Driver),
            ],
            Resources::Position => [
                _p(Actions::View, Resources::Position),
                _p(Actions::Manage, Resources::Position),
            ],
            Resources::DrivingSkill => [
                _p(Actions::View, Resources::DrivingSkill),
                _p(Actions::Manage, Resources::DrivingSkill),
            ],
            Resources::DriverWageCategory => [
                _p(Actions::View, Resources::DriverWageCategory),
                _p(Actions::Manage, Resources::DriverWageCategory),
            ],
            Resources::DriverWage => [
                _p(Actions::View, Resources::DriverWage),
                _p(Actions::Manage, Resources::DriverWage),
            ],
            Resources::CarBrand => [
                _p(Actions::View, Resources::CarBrand),
                _p(Actions::Manage, Resources::CarBrand),
            ],
            Resources::CarCategory => [
                _p(Actions::View, Resources::CarCategory),
                _p(Actions::Manage, Resources::CarCategory),
            ],
            Resources::CarType => [
                _p(Actions::View, Resources::CarType),
                _p(Actions::Manage, Resources::CarType),
            ],
            Resources::CarClass => [
                _p(Actions::View, Resources::CarClass),
                _p(Actions::Manage, Resources::CarClass),
            ],
            Resources::CarTire => [
                _p(Actions::View, Resources::CarTire),
                _p(Actions::Manage, Resources::CarTire),
            ],
            Resources::CarBattery => [
                _p(Actions::View, Resources::CarBattery),
                _p(Actions::Manage, Resources::CarBattery),
            ],
            Resources::CarWiper => [
                _p(Actions::View, Resources::CarWiper),
                _p(Actions::Manage, Resources::CarWiper),
            ],
            Resources::CarGroup => [
                _p(Actions::View, Resources::CarGroup),
                _p(Actions::Manage, Resources::CarGroup),
            ],
            Resources::CarColor => [
                _p(Actions::View, Resources::CarColor),
                _p(Actions::Manage, Resources::CarColor),
            ],
            Resources::Accessory => [
                _p(Actions::View, Resources::Accessory),
                _p(Actions::Manage, Resources::Accessory),
            ],
            Resources::CustomerGroup => [
                _p(Actions::View, Resources::CustomerGroup),
                _p(Actions::Manage, Resources::CustomerGroup),
            ],
            Resources::Customer => [
                _p(Actions::View, Resources::Customer),
                _p(Actions::Manage, Resources::Customer),
            ],
            Resources::Creditor => [
                _p(Actions::View, Resources::Creditor),
                _p(Actions::Manage, Resources::Creditor),
            ],
            Resources::Pdpa => [
                _p(Actions::View, Resources::Pdpa),
                _p(Actions::Manage, Resources::Pdpa),
            ],
            Resources::User => [
                _p(Actions::View, Resources::User),
                _p(Actions::Manage, Resources::User),
            ],
            Resources::Department => [
                _p(Actions::View, Resources::Department),
                _p(Actions::Manage, Resources::Department),
            ],
            Resources::Section => [
                _p(Actions::View, Resources::Section),
                _p(Actions::Manage, Resources::Section),
            ],
            Resources::Role => [
                _p(Actions::View, Resources::Role),
                _p(Actions::Manage, Resources::Role),
            ],
            Resources::Permission => [
                _p(Actions::View, Resources::Permission),
                _p(Actions::Manage, Resources::Permission),
            ],
            Resources::ConfigApprove => [
                _p(Actions::View, Resources::ConfigApprove),
                _p(Actions::Manage, Resources::ConfigApprove),
            ],
            Resources::InsuranceCompanies => [
                _p(Actions::View, Resources::InsuranceCompanies),
                _p(Actions::Manage, Resources::InsuranceCompanies),
            ],
            Resources::RequestPremium => [
                _p(Actions::View, Resources::RequestPremium),
                _p(Actions::Manage, Resources::RequestPremium),
            ],
            Resources::CMI => [
                _p(Actions::View, Resources::CMI),
                _p(Actions::Manage, Resources::CMI),
            ],
            Resources::CancelCMI => [
                _p(Actions::View, Resources::CancelCMI),
                _p(Actions::Manage, Resources::CancelCMI),
            ],
            Resources::VMI => [
                _p(Actions::View, Resources::VMI),
                _p(Actions::Manage, Resources::VMI),
            ],
            Resources::CancelVMI => [
                _p(Actions::View, Resources::CancelVMI),
                _p(Actions::Manage, Resources::CancelVMI),
            ],
            Resources::AuctionPlace => [
                _p(Actions::View, Resources::AuctionPlace),
                _p(Actions::Manage, Resources::AuctionPlace),
            ],
            Resources::SellingCar => [
                _p(Actions::View, Resources::SellingCar),
                _p(Actions::Manage, Resources::SellingCar),
            ],
            Resources::SellingPrice => [
                _p(Actions::View, Resources::SellingPrice),
                _p(Actions::Manage, Resources::SellingPrice),
            ],
            Resources::SellingPriceApprove => [
                _p(Actions::View, Resources::SellingPriceApprove),
                _p(Actions::Manage, Resources::SellingPriceApprove),
            ],
            Resources::CarAuction => [
                _p(Actions::View, Resources::CarAuction),
                _p(Actions::Manage, Resources::CarAuction),
            ],
            Resources::InsuranceCar => [
                _p(Actions::View, Resources::InsuranceCar),
                _p(Actions::Manage, Resources::InsuranceCar),
            ],
            Resources::InsuranceCarCmiRenew => [
                _p(Actions::View, Resources::InsuranceCarCmiRenew),
                _p(Actions::Manage, Resources::InsuranceCarCmiRenew),
            ],
            Resources::InsuranceCarVmiRenew => [
                _p(Actions::View, Resources::InsuranceCarVmiRenew),
                _p(Actions::Manage, Resources::InsuranceCarVmiRenew),
            ],
            Resources::InsuranceDeduct => [
                _p(Actions::View, Resources::InsuranceDeduct),
                _p(Actions::Manage, Resources::InsuranceDeduct),
            ],
            Resources::InsuranceLossRatio => [
                _p(Actions::View, Resources::InsuranceLossRatio),
                _p(Actions::Manage, Resources::InsuranceLossRatio),
            ],
            Resources::FinanceRequest => [
                _p(Actions::View, Resources::FinanceRequest),
                _p(Actions::Manage, Resources::FinanceRequest),
            ],
            Resources::FinanceRequestApprove => [
                _p(Actions::View, Resources::FinanceRequestApprove),
                _p(Actions::Manage, Resources::FinanceRequestApprove),
            ],
            Resources::FinanceContract => [
                _p(Actions::View, Resources::FinanceContract),
                _p(Actions::Manage, Resources::FinanceContract),
            ],
            Resources::Finance => [
                _p(Actions::View, Resources::Finance),
                _p(Actions::Manage, Resources::Finance),
            ],
            Resources::Litigation => [
                _p(Actions::View, Resources::Litigation),
                _p(Actions::Manage, Resources::Litigation),
            ],
            Resources::LitigationApprove => [
                _p(Actions::View, Resources::LitigationApprove),
                _p(Actions::Manage, Resources::LitigationApprove),
            ],
            Resources::TrafficTicket => [
                _p(Actions::View, Resources::TrafficTicket),
                _p(Actions::Manage, Resources::TrafficTicket),
            ],

            Resources::MFlow => [
                _p(Actions::View, Resources::MFlow),
                _p(Actions::Manage, Resources::MFlow),
            ],
            Resources::Compensation => [
                _p(Actions::View, Resources::Compensation),
                _p(Actions::Manage, Resources::Compensation),
            ],
            Resources::CompensationApprove => [
                _p(Actions::View, Resources::CompensationApprove),
                _p(Actions::Manage, Resources::CompensationApprove),
            ],
            Resources::RepairBill => [
                _p(Actions::View, Resources::RepairBill),
                _p(Actions::Manage, Resources::RepairBill),
            ],
            Resources::MainTenanceCost => [
                _p(Actions::View, Resources::MainTenanceCost),
                _p(Actions::Manage, Resources::MainTenanceCost),
            ],
            Resources::LongTermRentalInvoice => [
                _p(Actions::View, Resources::LongTermRentalInvoice),
                _p(Actions::Manage, Resources::LongTermRentalInvoice),
            ],
            Resources::ShortTermRentalInvoice => [
                _p(Actions::View, Resources::ShortTermRentalInvoice),
                _p(Actions::Manage, Resources::ShortTermRentalInvoice),
            ],
            Resources::OtherInvoice => [
                _p(Actions::View, Resources::OtherInvoice),
                _p(Actions::Manage, Resources::OtherInvoice),
            ],
            Resources::CreditNote => [
                _p(Actions::View, Resources::CreditNote),
                _p(Actions::Manage, Resources::CreditNote),
            ],
            Resources::DebtCollection => [
                _p(Actions::View, Resources::DebtCollection),
                _p(Actions::Manage, Resources::DebtCollection),
            ],
            Resources::CheckBillingDate => [
                _p(Actions::View, Resources::CheckBillingDate),
                _p(Actions::Manage, Resources::CheckBillingDate),
            ],
            Resources::RecordOtherExpenses => [
                _p(Actions::View, Resources::RecordOtherExpenses),
                _p(Actions::Manage, Resources::RecordOtherExpenses),
            ],
            Resources::RecordPettyCash => [
                _p(Actions::View, Resources::RecordPettyCash),
                _p(Actions::Manage, Resources::RecordPettyCash),
            ],
            Resources::CheckPettyCash => [
                _p(Actions::View, Resources::CheckPettyCash),
                _p(Actions::Manage, Resources::CheckPettyCash),
            ],
            Resources::PayPremiumApprove => [
                _p(Actions::View, Resources::PayPremiumApprove),
                _p(Actions::Manage, Resources::PayPremiumApprove),
            ],
            Resources::SapInterfaceAP => [
                _p(Actions::View, Resources::SapInterfaceAP),
                _p(Actions::Manage, Resources::SapInterfaceAP),
            ],
            Resources::Asset => [
                _p(Actions::View, Resources::Asset),
                _p(Actions::Manage, Resources::Asset),
            ],
        ];
    }
}
