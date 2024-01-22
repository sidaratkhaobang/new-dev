<?php

use App\Enums\BorrowCarEnum;
use App\Enums\ChangeRegistrationStatusEnum;
use App\Enums\ChangeRegistrationTypeEnum;
use App\Enums\FaceSheetStatusEnum;
use App\Enums\FaceSheetTypeEnum;
use App\Enums\LockLicensePlateTypeEnum;
use App\Enums\OwnershipTransferStatusEnum;
use App\Enums\ReceiptTypeEnum;
use App\Enums\ReceiptStatusEnum;
use App\Enums\RegisterColorEnum;
use App\Enums\RegisterSignEnum;
use App\Enums\RegisterSignTypeEnum;
use App\Enums\RegisterStatusEnum;
use App\Enums\ResponsibleSignYellowTicketEnum;
use App\Enums\SignYellowTicketStatusEnum;
use App\Enums\TaxRenewalStatusEnum;
use App\Enums\TrainingSignYellowTicketEnum;

return [
    'page_title' => 'ใบสั่งป้ายเหลือง',
    'add_new' => 'เพิ่มใบสั่งป้ายเหลือง',
    'lot_no' => 'เลข lot',
    'car_class' => 'ยี่ห้อ/รุ่นรถ',
    'engine_no' => 'หมายเลขเครื่องยนต์',
    'chassis_no' => 'หมายเลขตัวถัง',
    'status' => 'สถานะ',
    'po_detail' => 'ข้อมูลการสั่งซื้อรถ',
    'po_no' => 'เลขที่ใบสั่งซื้อรถ',
    'creditor_name' => 'ชื่อผู้ขาย',
    'leasing' => 'Leasing',
    'paid_date' => 'วันที่จ่ายเงิน',
    'delivery_date' => 'วันที่นัดส่งมอบลูกค้า',
    'receive_data_date' => 'วันที่ได้รับข้อมูล (วันที่จัด Lot)',
    'car_detail' => 'ข้อมูลรถ',
    'cc' => 'CC',
    'car_color' => 'สีรถ',
    'car_characteristic' => 'ลักษณะรถ',
    // 'car_characteristic_transport' => 'ลักษณะตามกรมขนส่ง',
    'car_category' => 'ประเภทรถ',
    'license_plate_registered' => 'ป้ายที่จดทะเบียน',
    'registered_detail' => 'ข้อมูลจดทะเบียน',
    'document_date' => 'วันที่เอกสารครบพร้อมจด',
    'document_date_amount' => 'ระยะเวลารอเอกสารครบ',
    'receive_registered_dress_date' => 'วันที่ได้รับชุดจดทะเบียน',
    'receive_registered_dress_date_amount' => 'จำนวนวันที่รอชุดจดทะเบียน',
    'receive_cmi' => 'วันที่ได้รับพรบ.',
    'receive_cmi_amount' => 'จำนวนวันที่รอพรบ.',
    'receive_document_sale_date' => 'วันที่ได้รับชุดแจ้งจำหน่าย',
    'receive_document_sale_date_amount' => 'จำนวนวันที่รอชุดแจ้งจำหน่าย',
    'receive_roof_receipt_date' => 'วันที่ได้รับใบเสร็จหลังคา/NGV',
    'receive_roof_receipt_date_amount' => 'จำนวนวันที่รอใบเสร็จหลังคา/NGV',

    'is_lock_license_plate' => 'มีการล็อกเลขหรือไม่',
    'lock_license_plate' . STATUS_ACTIVE  => 'มี',
    'lock_license_plate' . STATUS_DEFAULT => 'ไม่มี',

    'is_receipt_roof' . STATUS_ACTIVE  => 'ใช่',
    'is_receipt_roof' . STATUS_DEFAULT => 'ไม่ใช่',

    'type_lock_license_plate' => 'ประเภทการล็อกเลข',
    'detail_lock_license_plate' => 'เลขทะเบียนเก่า/รายละเอียดการจองเลข',
    'send_registered_date' => 'วันที่ส่งจดทะเบียน',
    'optional_files' => 'เอกสารเพิ่มเติม (ขนาดไม่เกิน 10 MB)',
    'remark' => 'หมายเหตุ',
    'save_register' => 'บันทึกส่งจดทะเบียน',
    'prepare_register' => 'เตรียมข้อมูลก่อนจดทะเบียน',
    'save_after_register' => 'บันทึกข้อมูลหลังจดทะเบียน',
    'car_status' => 'สถานะรถ',
    'car_slot' => 'ช่องจอด',
    'optional_detail' => 'รายละเอียดเพิ่มเติม',
    'is_receipt_roof' => 'ต้องใช้ใบเสร็จหลังคา/NGV',
    'avance' => 'ข้อมูลการเบิกเงิน',
    'memo_no' => 'เลขที่ MEMO เบิกเงิน',
    'memo' => 'เลขที่ memo',
    'receipt_avance' => 'ค่าใบเสร็จ',
    'operation_fee_avance' => 'ค่าดำเนินการ',
    'total_avance' => 'รวมเบิก',
    'color_registered' => 'สีที่จดทะเบียน',
    'car_characteristic_transport' => 'ลักษณะรถตามกรมขนส่ง',
    'registered_date' => 'วันที่จดทะเบียนเสร็จ',
    'receive_information_date' => 'วันที่ได้รับข้อมูลมาบันทึกข้อมูล',
    'registered_date_amount' => 'จำนวนวันจดทะเบียน',
    'license_plate' => 'เลขทะเบียน',
    'car_tax_exp_date' => 'วันหมดอายุภาษีรถยนต์',
    'link' => 'ลิงก์ไฟล์แนบสำเนาทะเบียนรถ',
    'proceed_detail' => 'ข้อมูลค่าดำเนินการ',
    'receipt_date' => 'วันที่ออกใบเสร็จ',
    'receipt_no' => 'เลขที่ใบเสร็จ',
    'tax' => 'ค่าภาษี',
    'service_fee' => 'ค่าบริการ',
    'total' => 'รวม',
    'receive_register_sign' => 'การได้รับป้ายทะเบียน',
    'face_sheet' => 'พิมพ์ใบปะหน้า',
    'select_car_face_sheet' => 'พิมพ์ใบปะหน้า',
    'engine_chassis_no' => 'หมายเลขตัวถัง/หมายเลขเครื่องยนต์',
    'add_car' => 'เพิ่มรถ',
    'car_total' => 'จำนวนรถทั้งหมด ',
    'no_data' => 'ไม่มีข้อมูลรถ',
    'validate_status' => 'สถานะรถที่เลือกไม่ตรงกับข้อมูลที่เลือกแล้ว',
    'facesheet_type' => 'ประเภทใบปะหน้า',
    'topic_face_sheet' => 'หัวข้อในใบประหน้า',
    'save_avance' => 'บันทึกเบิกเงิน Advance',
    'avance_withdraw' => 'เบิกเงิน Advance',
    'edit_multiple' => 'แก้ไขข้อมูลหลายรายการ',
    'validate_car_duplicate' => 'รถถูกเลือกแล้ว',
    'download_file' => 'ดาว์นโหลดไฟล์',
    'validate_import' => 'ข้อมูลไม่ถูกต้อง/ไม่ครบถ้วน',
    'save_draft' => 'บันทึกร่าง',
    'save_registered' => 'บันทึกจดทะเบียนเสร็จสิ้น',
    'contract_no' => 'เลขที่สัญญา',
    'actual_last_payment_date' => 'วันที่จ่ายเงินงวดสุดท้าย',
    'request_type' => 'ประเภทคำขอ',    
    'license_plate_engine_chassis' => 'ทะเบียนรถ/หมายเลขตัวถัง/หมายเลขเครื่องยนต์',
    'send_tax' => 'ส่งต่อภาษี',
    'expire_month' => 'เดือนที่หมดอายุ',
    'expire_date' => 'วันที่หมดอายุภาษี', 
    'save_date' => 'วันที่บันทึกข้อมูล',
    'responsible' => 'หน่วยงานที่รับผิดชอบ',
    'lawsuit_total' => 'จำนวนคดี',
    'total' => 'ยอดรวมทั้งหมด',
    'total_pay_dlt' => 'ยอดเงินรวมชำระกรมขนส่ง',
    'total_no_pay' => 'ยอดเงินคงค้าง',
    'find_detail' => 'ข้อมูลใบสั่ง',
    'lawsuit_detail' => 'ข้อมูลคดี',
    'add' => 'เพิ่ม',
    'add_case' => 'เพิ่มคดี',
    'incident_date' => 'วันที่เกิดเหตุ',
    'accident_place' => 'สถานที่เกิดเหตุ',
    'lawsuit' => 'คดีที่เกิดเหตุ',
    'amount' => 'จำนวนเงิน',
    'driver_type' => 'ประเภทผู้ขับ',
    'driver' => 'ผู้ขับ',
    'tel' => 'เบอร์โทร',
    'training' => 'การเข้าอบรม',
    'is_wrong' => 'ผิดจริงหรือไม่',
    'announ_pay_find_date' => 'วันที่แจ้งข้อมูลเพื่อดำเนินการชำระค่าปรับ',
    'responsibility' => 'การรับผิดชอบ',
    'branch' => 'สาขา',
    'car_status' => 'สถานะรถ',
    'receive_find_date' => 'วันที่ได้รับข้อมูลใบสั่ง',
    'save_info_find' => 'บันทึกข้อมูลใบสั่ง',
    'notification_date' => 'วันที่แจ้งข้อมูลดำเนินการค่าปรับ',
    'save_inspec' => 'บันทึกผลการตรวจสอบ',
    'save_paid' => 'บันทึกชำระเงินกับกรมขนส่งเสร็จสิ้น',
    'save_paid_fine' => 'บันทึกชำระค่าปรับเสร็จสิ้น',
    'pay_receipt_detail' => 'ข้อมูลการชำระเงิน (ใบเสร็จ)',
    'paid_date' => 'วันที่ชำระ',
    'dlt_receipt' => 'ใบเสร็จจากกรมขนส่ง',
    'amount_total' => 'จำนวน',
    'payment' => 'การชำระเงิน',
    'pay_find_detail' => 'ข้อมูลการชำระเงิน (ค่าปรับ)',
    'payment_fine_date' => 'วันที่ชำระ (ใบเสร็จ)',
    'validate_case' => 'ข้อมูลคดีอย่างน้อย 1 รายการ',
    
    'responsible_' . ResponsibleSignYellowTicketEnum::DRIVER => 'พนักงานขับรถ (คุณวีระ)',
    'responsible_' . ResponsibleSignYellowTicketEnum::LADKRABANG => 'สาขาลาดกระบัง',
    'responsible_' . ResponsibleSignYellowTicketEnum::PHUKET => 'สาขาภูเก็ต',
    'responsible_' . ResponsibleSignYellowTicketEnum::CHAINGRAI => 'สาขาเชียงราย',
    'responsible_' . ResponsibleSignYellowTicketEnum::CHAINGMAI => 'สาขาเชียงใหม่',
    'responsible_' . ResponsibleSignYellowTicketEnum::KRABI => 'สาขากระบี่',
    'responsible_' . ResponsibleSignYellowTicketEnum::PATTAYA => 'สาขาพัทยา',
    'responsible_' . ResponsibleSignYellowTicketEnum::PRAPADAENG => 'สาขาพระประแดง',

    'training_' . STATUS_ACTIVE => 'เข้าอบรม',
    'training_' . STATUS_DEFAULT => 'ไม่เข้าอบรม',

    'mistake_' . STATUS_ACTIVE => 'ผิด',
    'mistake_' . STATUS_DEFAULT => 'ถูก',

    'payment_' . STATUS_ACTIVE => 'ชำระแล้ว',
    'payment_' . STATUS_DEFAULT => 'ยังไม่ชำระ',

    'status_' . SignYellowTicketStatusEnum::DRAFT . '_class' => 'primary',
    'status_' . SignYellowTicketStatusEnum::DRAFT . '_text' => 'ร่าง',

    'status_' . SignYellowTicketStatusEnum::WAITING_WRONG . '_class' => 'warning',
    'status_' . SignYellowTicketStatusEnum::WAITING_WRONG . '_text' => 'รอตรวจสอบความผิด',

    'status_' . SignYellowTicketStatusEnum::WAITING_PAY_DLT . '_class' => 'warning',
    'status_' . SignYellowTicketStatusEnum::WAITING_PAY_DLT . '_text' => 'รอชำระเงินกับกรมขนส่ง',

    'status_' . SignYellowTicketStatusEnum::WAITING_PAY_FINE . '_class' => 'primary',
    'status_' . SignYellowTicketStatusEnum::WAITING_PAY_FINE . '_text' => 'รอการชำระเงินค่าปรับ',

    'status_' . SignYellowTicketStatusEnum::SUCCESS . '_class' => 'success',
    'status_' . SignYellowTicketStatusEnum::SUCCESS . '_text' => 'ดำเนินการเสร็จสิ้น',


    'type_face_sheet_' . FaceSheetTypeEnum::REGISTER_NEW_CAR => 'จดทะเบียนรถใหม่',
    'type_face_sheet_' . FaceSheetTypeEnum::RETURN_LEASING => 'คืนเล่มลีสซิ่ง',

    'request_type_' . ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY . '_class' => 'primary',
    'request_type_' . ChangeRegistrationTypeEnum::LICENSE_PLATE_TAX_COPY . '_text' => 'คัดป้ายเหล็ก/ป้ายภาษี',

    'request_type_' . ChangeRegistrationTypeEnum::CHANGE_COLOR . '_class' => 'primary',
    'request_type_' . ChangeRegistrationTypeEnum::CHANGE_COLOR . '_text' => 'เปลี่ยนสี',

    'request_type_' . ChangeRegistrationTypeEnum::CHANGE_CHARACTERISTIC . '_class' => 'warning',
    'request_type_' . ChangeRegistrationTypeEnum::CHANGE_CHARACTERISTIC . '_text' => 'เปลี่ยนลักษณะ',

    'request_type_' . ChangeRegistrationTypeEnum::CHANGE_TYPE . '_class' => 'warning',
    'request_type_' . ChangeRegistrationTypeEnum::CHANGE_TYPE . '_text' => 'เปลี่ยนประเภท',

    'request_type_' . ChangeRegistrationTypeEnum::SWAP_LICENSE_PLATE . '_class' => 'success',
    'request_type_' . ChangeRegistrationTypeEnum::SWAP_LICENSE_PLATE . '_text' => 'สลับเลข',

    'request_type_' . ChangeRegistrationTypeEnum::CANCEL_USE_CAR . '_class' => 'success',
    'request_type_' . ChangeRegistrationTypeEnum::CANCEL_USE_CAR . '_text' => 'ยกเลิก ม.79',

    'registered_sign_type_' . RegisterSignTypeEnum::GREEN_SIGN => 'ป้ายเขียว',
    'registered_sign_type_' . RegisterSignTypeEnum::WHITE_SIGN => 'ป้ายขาว',
    'registered_sign_type_' . RegisterSignTypeEnum::BLUE_SIGN => 'ป้ายฟ้า',
    'registered_sign_type_' . RegisterSignTypeEnum::YELLOW_SIGN => 'ป้ายเหลือง',
    'registered_sign_type_' . RegisterSignTypeEnum::GREEN_SERVICE_SIGN => 'ป้ายเขียวบริการ',

    'registered_color_' . RegisterColorEnum::WHITE => 'ขาว',
    'registered_color_' . RegisterColorEnum::BLACK => 'ดำ',
    'registered_color_' . RegisterColorEnum::GREY => 'เทา',
    'registered_color_' . RegisterColorEnum::RED => 'แดง',
    'registered_color_' . RegisterColorEnum::BLUE => 'น้ำเงิน',
    'registered_color_' . RegisterColorEnum::MIX => 'หลากสี',

    'receive_sign_' . RegisterSignEnum::IRON_SIGN => 'ป้ายเหล็ก',
    'receive_sign_' . RegisterSignEnum::TAX_SIGN => 'ป้ายภาษี',
    'receive_sign_' . RegisterSignEnum::REGISTRATION_BOOK => 'เล่มทะเบียน',

    'lock_license_plate_type_' . LockLicensePlateTypeEnum::USE_OLD_LICENSE_PLATE => 'ลูกค้าใช้เลขเก่า',
    'lock_license_plate_type_' . LockLicensePlateTypeEnum::RESERVE_LICENSE_PLATE => 'ลูกค้าจองเลขไว้',

];
