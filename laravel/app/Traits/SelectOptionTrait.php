<?php

namespace App\Traits;

use App\Enums\CalculateTypeEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Enums\OrderChannelEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\RentalStatusEnum;
use App\Enums\PaymentGatewayEnum;

trait SelectOptionTrait
{
    function getListStatus()
    {
        return collect([
            [
                'id' => 'active',
                'value' => STATUS_ACTIVE,
                'name' => __('lang.status_' . STATUS_ACTIVE),
            ],
            [
                'id' => 'inactive',
                'value' => STATUS_INACTIVE,
                'name' => __('lang.status_' . STATUS_INACTIVE),
            ],
        ]);
    }

    function getListLongTermStatus()
    {
        return collect([
            [
                'id' => 'active',
                'value' => STATUS_ACTIVE,
                'name' => __('long_term_rentals.purchase_option_' . STATUS_ACTIVE),
            ],
            [
                'id' => 'inactive',
                'value' => STATUS_DEFAULT,
                'name' => __('long_term_rentals.purchase_option_' . STATUS_DEFAULT),
            ],
        ]);
    }

    function getYesNoList($labels = [])
    {
        $yes_label = isset($labels[0]) ? $labels[0] : __('lang.yes');
        $no_label = isset($labels[1]) ? $labels[1] : __('lang.no');
        return collect([
            [
                'id' => 1,
                'value' => 1,
                'name' => $yes_label,
            ],
            [
                'id' => 0,
                'value' => 0,
                'name' => $no_label,
            ],
        ]);
    }

    function getBranchList()
    {
        return Branch::select('name', 'id')->get();
    }

    function getOrderChannelList()
    {
        return collect([
            [
                'id' => OrderChannelEnum::SMARTCAR,
                'value' => OrderChannelEnum::SMARTCAR,
                'name' => 'จองผ่านระบบ Smart Car',
            ],
            [
                'id' => OrderChannelEnum::WEBSITE,
                'value' => OrderChannelEnum::WEBSITE,
                'name' => 'จองผ่านเว็บไซต์ true leasing',
            ],
        ]);
    }

    function getPackageTypeList()
    {
        return collect([
            [
                'id' => CalculateTypeEnum::DAILY,
                'value' => CalculateTypeEnum::DAILY,
                'name' => 'รายวัน',
            ],
            [
                'id' => CalculateTypeEnum::MONTHLY,
                'value' => CalculateTypeEnum::MONTHLY,
                'name' => 'รายเดือน',
            ],
        ]);
    }

    function getPaymentTypeList()
    {
        return collect([
            [
                'id' => 'CASH',
                'value' => 'CASH',
                'name' => 'เงินสด',
            ],
            [
                'id' => 'BILL',
                'value' => 'BILL',
                'name' => 'วางบิล',
            ],
        ]);
    }

    public function getPaymentMethodList()
    {
        $list = collect([
            [
                'id' => PaymentMethodEnum::CREDIT_CARD,
                'value' => PaymentMethodEnum::CREDIT_CARD,
                'text' => __('short_term_rentals.payment_' . PaymentMethodEnum::CREDIT_CARD),
            ],
            [
                'id' => PaymentMethodEnum::BANK_TRANSFER,
                'value' => PaymentMethodEnum::BANK_TRANSFER,
                'text' => __('short_term_rentals.payment_' . PaymentMethodEnum::BANK_TRANSFER),
            ],
            [
                'id' => PaymentMethodEnum::TRUE_WALLET,
                'value' => PaymentMethodEnum::TRUE_WALLET,
                'text' => __('short_term_rentals.payment_' . PaymentMethodEnum::TRUE_WALLET),
            ],
        ]);
        return $list;
    }

    public function getPaymentGateWayList()
    {
        $list = collect([
            (object)[
                'id' => PaymentGatewayEnum::OMISE,
                'value' => PaymentGatewayEnum::OMISE,
                'name' => __('short_term_rentals.payment_gateway_' . PaymentGatewayEnum::OMISE),
            ],
            (object)[
                'id' => PaymentGatewayEnum::MC_PAYMENT,
                'value' => PaymentGatewayEnum::MC_PAYMENT,
                'name' => __('short_term_rentals.payment_gateway_' . PaymentGatewayEnum::MC_PAYMENT),
            ],
        ]);
        return $list;
    }

    public function getPaymentStatusList()
    {
        return collect([
            [
                'id' => RentalStatusEnum::PAID,
                'value' => RentalStatusEnum::PAID,
                'text' => __('short_term_rentals.status_' . RentalStatusEnum::PAID),
            ],
        ]);
    }
}
