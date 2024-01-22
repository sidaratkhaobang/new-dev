<?php

namespace App\Traits;

use App\Enums\CheckCreditStatusEnum;
use App\Enums\ContractEnum;
use App\Enums\ContractSignerSideEnum;
use App\Enums\CustomerTypeEnum;
use App\Enums\TransferTypeEnum;
use App\Models\CheckCredits;
use App\Models\Contracts;
use App\Models\LongTermRental;
use App\Models\Rental;

trait ContractTrait
{
    public static function getWorkSheetNumber()
    {
        $no_count = Contracts::count() + 1;
        return generateRecordNumber('RMD', $no_count);
    }

//    public static function getStatusList()
//    {
//        return collect([
//            (object) [
//                'id' => ContractEnum::DRAFT,
//                'name' => __('contract.status_text_' . CheckCreditStatusEnum::DRAFT),
//                'value' => CheckCreditStatusEnum::DRAFT,
//            ],
//        ]);
//    }

    public static function getListStatus()
    {
        return collect([
            (object)[
                'id' => ContractEnum::REQUEST_CONTRACT,
                'name' => __('contract.status_text_' . ContractEnum::REQUEST_CONTRACT),
            ],
            (object)[
                'id' => ContractEnum::ACTIVE_CONTRACT,
                'name' => __('contract.status_text_' . ContractEnum::ACTIVE_CONTRACT),
            ],
            (object)[
                'id' => ContractEnum::SEND_OFFER_SIGN,
                'name' => __('contract.status_text_' . ContractEnum::SEND_OFFER_SIGN),
            ],
            (object)[
                'id' => ContractEnum::SEND_CUSTOMER_SIGN,
                'name' => __('contract.status_text_' . ContractEnum::SEND_CUSTOMER_SIGN),
            ],
            (object)[
                'id' => ContractEnum::ACTIVE_BETWEEN_CONTRACT,
                'name' => __('contract.status_text_' . ContractEnum::ACTIVE_BETWEEN_CONTRACT),
            ],
            (object)[
                'id' => ContractEnum::REQUEST_CHANGE_ADDRESS,
                'name' => __('contract.status_text_' . ContractEnum::REQUEST_CHANGE_ADDRESS),
            ],
            (object)[
                'id' => ContractEnum::REQUEST_CHANGE_USER_CAR,
                'name' => __('contract.status_text_' . ContractEnum::REQUEST_CHANGE_USER_CAR),
            ],
            (object)[
                'id' => ContractEnum::REQUEST_TRANSFER_CONTRACT,
                'name' => __('contract.status_text_' . ContractEnum::REQUEST_TRANSFER_CONTRACT),
            ],
            (object)[
                'id' => ContractEnum::REJECT_REQUEST,
                'name' => __('contract.status_text_' . ContractEnum::REJECT_REQUEST),
            ],
            (object)[
                'id' => ContractEnum::CANCEL_CONTRACT,
                'name' => __('contract.status_text_' . ContractEnum::CANCEL_CONTRACT),
            ],
            (object)[
                'id' => ContractEnum::CLOSE_CONTRACT,
                'name' => __('contract.status_text_' . ContractEnum::CLOSE_CONTRACT),
            ],
        ]);
    }

    public static function getListContrcatType()
    {
        return collect([
            (object)[
                'id' => Rental::class,
                'name' => __('lang.job_type_rental'),
            ],
            (object)[
                'id' => LongTermRental::class,
                'name' => __('lang.job_type_lt_rental'),
            ],
        ]);
    }

    public static function getListConditionStartStatusRadio()
    {
        return collect([
            [
                'value' => ContractEnum::START_RENT_PICKUP_DATE,
                'name' => __('contract.status_text_' . ContractEnum::START_RENT_PICKUP_DATE),
            ],
            [
                'value' => ContractEnum::START_RENT_RETURN_DATE,
                'name' => __('contract.status_text_' . ContractEnum::START_RENT_RETURN_DATE),
            ],
        ]);
    }

    public static function getListHaveFine()
    {
        return collect([
            [
                'value' => 0,
                'name' => __('lang.no_have'),
            ],
            [
                'value' => 1,
                'name' => __('contract.have_fine'),
            ],
        ]);
    }

    public static function getListConditionEndStatusRadio()
    {
        return collect([
            [
                'value' => ContractEnum::END_RENT_EXPIRE_DATE,
                'name' => __('contract.status_text_' . ContractEnum::END_RENT_EXPIRE_DATE),
            ],
            [
                'value' => ContractEnum::END_RENT_RETURN_DATE,
                'name' => __('contract.status_text_' . ContractEnum::END_RENT_RETURN_DATE),
            ],
        ]);
    }

    public static function getListStatusRequest()
    {
        return collect([
            (object)[
                'id' => ContractEnum::REQUEST_CHANGE_USER_CAR,
                'name' => __('contract.status_text_' . ContractEnum::REQUEST_CHANGE_USER_CAR),
            ],
            (object)[
                'id' => ContractEnum::REQUEST_CHANGE_ADDRESS,
                'name' => __('contract.status_text_' . ContractEnum::REQUEST_CHANGE_ADDRESS),
            ],
            (object)[
                'id' => ContractEnum::REQUEST_TRANSFER_CONTRACT,
                'name' => __('contract.status_text_' . ContractEnum::REQUEST_TRANSFER_CONTRACT),
            ],
        ]);
    }

    public static function getContractSignerSideList()
    {
        return collect([
            (object)[
                'id' => ContractSignerSideEnum::HOST,
                'name' => __('contract.singer_' . ContractSignerSideEnum::HOST),
            ],
            (object)[
                'id' => ContractSignerSideEnum::RENTER,
                'name' => __('contract.singer_' . ContractSignerSideEnum::RENTER),
            ],
        ]);
    }
}
