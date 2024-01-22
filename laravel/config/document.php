<?php

use App\Models\Compensation;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseOrder;
use App\Models\CarParkTransfer;
use App\Models\Rental;
use App\Models\LongTermRental;
use App\Models\Quotation;
use App\Models\InspectionJob;
use App\Models\DrivingJob;
use App\Models\BillSlip;
use App\Models\MFlow;
use App\Models\OwnershipTransfer;
use App\Models\Register;
use App\Models\RequestReceipt;
use App\Models\TaxRenewal;
use App\Models\TrafficTicket;
use App\Models\Receipt;

return [
    'prefix' => [
        // phase 2
        PurchaseRequisition::class => 'PR',
        PurchaseOrder::class => 'PO',
        CarParkTransfer::class => 'CT',
        Rental::class => 'SR',
        LongTermRental::class => 'LT',
        Quotation::class => 'QT',
        InspectionJob::class => 'QA',
        DrivingJob::class => 'DJ',
        BillSlip::class => 'BL',
        OwnershipTransfer::class => 'OWT',
        TaxRenewal::class => 'TAX',
        Register::class => 'REG',
        MFlow::class => 'MF',
        Compensation::class => 'CP',
        RequestReceipt::class => 'RR',
        TrafficTicket::class => 'TT',
    ],
    'branch_prefix' => [
        // phase 2
        CarParkTransfer::class => true,
        Rental::class => true,
        InspectionJob::class => true,
        DrivingJob::class => true,
    ],
    'branch_registered_code' => [
        // phase 2
        Receipt::class => true,
    ],
    'custom_separator_1' => [
        Receipt::class => '-',
    ],
    'custom_date_format' => [
        Receipt::class => 'ym',
    ],
    'custom_separator_2' => [
        'xxx' => '/',
    ]
];
