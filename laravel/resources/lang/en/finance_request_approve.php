<?php

return [
    'page_title' => 'อนุมัติขอจัดทำไฟแนนซ์',
    'page_title_excel' => 'เลือกสัญญาเช่าซื้อรถที่ต้องการ',
    'search_lot_no' => 'เลข Lot',
    'search_rental' => 'ผู้ให้เช่า / สถาบันการเงิน',
    'search_date_create' => 'วันที่จัดทำ',
    'search_status' => 'สถานะ',
    'car_total' => 'จำนวนรถ',
    'btn_download_excel' => 'ดาวน์โหลดExcel',
    'modal_export_excel_title' => 'เลือกสัญญาเช่าซื้อรถที่ต้องการ',

    'status_' . FinanceRequestStatusEnum::PENDING => 'รอดำเนินการ',
    'status_' . FinanceRequestStatusEnum::PENDING_APPROVE => 'รออนุมัติ',
    'status_' . FinanceRequestStatusEnum::APPROVE => 'อนุมัติ',
    'status_' . FinanceRequestStatusEnum::REJECT => 'ไม่อนุมัติ',
];
