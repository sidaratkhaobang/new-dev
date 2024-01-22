<?php

use App\Enums\QuotationStatusEnum;
use App\Enums\RentalTypeEnum;
use App\Models\LongTermRental;
use App\Models\Rental;

return [

    'page_title' => 'ใบเสนอราคา',
    'page_title_approve' => 'อนุมัติใบเสนอราคา',
    'quotation_no' => 'เลขที่ใบเสนอราคา',
    'worksheet_no' => 'เลขที่ใบเช่า',
    'branch' => 'สาขา',
    'customer' => 'ลูกค้า',
    'type_job' => 'ประเภทงาน',
    'type_rental' => 'ประเภทงานเช่า',
    'type' => 'ประเภท',
    'created_at' => 'วันที่เช่า',
    'quotation' => 'ใบเสนอราคา',
    'total_items' => 'รายการทั้งหมด',

    'type_rental_' . LongTermRental::class => 'เช่ายาว',
    'type_rental_' . Rental::class => 'เช่าสั้น',

    'quotation_status_' . QuotationStatusEnum::DRAFT => 'ร่าง',
    'quotation_status_' . QuotationStatusEnum::CONFIRM => 'อนุมัติ',
    'quotation_status_' . QuotationStatusEnum::PENDING_REVIEW => 'รอผลดำเนินการ',
    'quotation_status_' . QuotationStatusEnum::REJECT => 'ไม่อนุมัติ',
    'quotation_status_' . QuotationStatusEnum::CANCEL => 'ยกเลิก',

    'quotation_class_' . QuotationStatusEnum::DRAFT => 'primary',
    'quotation_class_' . QuotationStatusEnum::CONFIRM => 'success',
    'quotation_class_' . QuotationStatusEnum::PENDING_REVIEW => 'warning',
    'quotation_class_' . QuotationStatusEnum::REJECT => 'danger',
    'quotation_class_' . QuotationStatusEnum::CANCEL => 'secondary',

    'rental_type_' . RentalTypeEnum::SHORT => 'เช่าสั้น',
    'rental_type_' . RentalTypeEnum::LONG => 'เช่ายาว',

    //edit quotation
    'customer_table' => 'ข้อมูลลูกค้า',
    'customer_type' => 'ประเภทลูกค้า',
    'customer_code' => 'รหัสลูกค้า',
    'customer_name' => 'ลูกค้า',
    'customer_tel' => 'เบอร์โทรศัพท์มือถือ',
    'customer_address' => 'ที่อยู่',
    'rental_table' => 'ข้อมูลราคาค่าเช่า',
    'condition_table' => 'เงื่อนไขการให้บริการ',
    'condition_add' => 'เพิ่มเงื่อนไข',

    'vat_' . STATUS_ACTIVE => 'รวม VAT',
    'vat_' . STATUS_DEFAULT => 'ไม่รวม VAT',
    'show_vat' => 'ต้องการแสดงราคาเช่ารวม VAT',

    'product_detail' => 'ข้อมูลสินค้า',
    'condition_rental' => 'ข้อกำหนดและเงื่อนไข',
    'condition' => 'ดึงเงื่อนไข',
    'bill_payment' => 'ใบนำฝากชำระเงิน',
];
