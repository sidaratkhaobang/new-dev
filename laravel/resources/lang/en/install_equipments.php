<?php
use App\Enums\InstallEquipmentStatusEnum;

return [
    'page_title' => 'ใบขอติดตั้งอุปกรณ์',
    'info' => 'ข้อมูลใบขอติดตั้งอุปกรณ์',
    'car_info' => 'ข้อมูลรถติดตั้งอุปกรณ์',
    'accessory_list' => 'รายละเอียดข้อมูลอุปกรณ์เสริม',
    'created_at' => 'วันที่จัดทำ',
    'created_by' => 'ผู้จัดทำ',
    'po' => 'ใบสั่งซื้อ',
    'po_no' => 'เลขที่ใบสั่งซื้อรถ',
    'car_code' => 'รหัสรถ',
    'license_plate' => 'ทะเบียนรถ',
    'engine_no' => 'หมายเลขเครื่องยนต์',
    'chasis_no' => 'หมายเลขตัวถัง',
    'document' => 'เอกสารเพิ่มเติม (ขนาดไฟล์ไม่เกิน 10 MB)',
    'remark' => 'หมายเหตุ',
    'accessory_code_name' => 'รหัส - ชื่ออุปกรณ์',
    'class' => 'รุ่น',
    'code' => 'รหัส',
    'amount_per_unit' => 'จำนวน/คัน',
    'price_per_unit' => 'ราคาต่อหน่วย',
    'discount' => 'ส่วนลด',
    'supplier_en' => 'Supplier',
    'add_accessory' => 'เพิ่มอุปกรณ์เสริม',
    'price_invalid' => 'ข้อมูลราคาไม่ถูกต้อง',
    'amount_invalid' => 'ข้อมูลจำนวนไม่ถูกต้อง',
    'required_supplier' => 'กรุณากรอกข้อมูล Supplier',
    'send_to_inspect' => 'ส่งตรวจรถ',
    'send_to_inspect_all' => 'ส่งตรวจรถทั้งหมด',
    'add_new' => 'เพิ่มใบขอติดตั้งอุปกรณ์',
    'total_items' => 'รายการทั้งหมด',
    'install_equipment_no' => 'เลขที่ใบขอติดตั้ง',
    'install_equipment_po_no' => 'เลขที่ใบสั่งซื้ออุปกรณ์',
    'accessory' => 'อุปกรณ์เสริม',

    'status_' . InstallEquipmentStatusEnum::WAITING => 'รอติดตั้ง',
    'class_' . InstallEquipmentStatusEnum::WAITING => 'primary',

    'status_' . InstallEquipmentStatusEnum::PENDING_REVIEW => 'รออนุมัติใบสั่งซื้อ',
    'class_' . InstallEquipmentStatusEnum::PENDING_REVIEW => 'primary',

    'status_' . InstallEquipmentStatusEnum::CONFIRM => 'อนุมัติ',
    'class_' . InstallEquipmentStatusEnum::CONFIRM => 'primary',

    'status_' . InstallEquipmentStatusEnum::INSTALL_IN_PROCESS => 'กำลังติดตั้ง',
    'class_' . InstallEquipmentStatusEnum::INSTALL_IN_PROCESS => 'warning',

    'status_' . InstallEquipmentStatusEnum::OVERDUE => 'เกินกำหนดติดตั้ง',
    'class_' . InstallEquipmentStatusEnum::OVERDUE => 'warning',

    'status_' . InstallEquipmentStatusEnum::DUE => 'ถึงกำหนดติดตั้ง',
    'class_' . InstallEquipmentStatusEnum::DUE => 'info',

    'status_' . InstallEquipmentStatusEnum::INSTALL_COMPLETE => 'ติดตั้งเสร็จ',
    'class_' . InstallEquipmentStatusEnum::INSTALL_COMPLETE => 'success',

    'status_' . InstallEquipmentStatusEnum::INSPECT_IN_PROCESS => 'กำลังตรวจรถ',
    'class_' . InstallEquipmentStatusEnum::INSPECT_IN_PROCESS => 'secondary',

    'status_' . InstallEquipmentStatusEnum::INSPECT_FAIL => 'ตรวจไม่ผ่าน',
    'class_' . InstallEquipmentStatusEnum::INSPECT_FAIL => 'danger',

    'status_' . InstallEquipmentStatusEnum::REJECT => 'ไม่อนุมัติ',
    'class_' . InstallEquipmentStatusEnum::REJECT => 'danger',

    'status_' . InstallEquipmentStatusEnum::CANCEL => 'ยกเลิก',
    'class_' . InstallEquipmentStatusEnum::CANCEL => 'dark',

    'status_' . InstallEquipmentStatusEnum::COMPLETE => 'เสร็จสิ้น',
    'class_' . InstallEquipmentStatusEnum::COMPLETE => 'success',

    'install_equipment_lines' => 'รายละเอียดข้อมูลอุปกรณ์เสริม',
    'car' => 'รถ',
    'start_date' => 'วันที่เริ่ม',
    'end_date' => 'วันที่เสร็จสิ้น',
    'install_day_amount' => 'จำนวนวันที่ใช้ในการติดตั้ง',
    'inspect_out' => 'ใบส่งรถ',
    'inspect_in' => 'ใบตรวจรับรถ',
    'car_park_transfer' => 'ใบนำรถเข้าออก',
    'driving_job' => 'ใบงาน พขร.',
    'send_inspection' => 'ส่งตรวจรถติดตั้งอุปกรณ์',
    'default_type' => 'ติดตั้งอุปกรณ์',
    'inspection_date' => 'วันที่ต้องตรวจ',
    'inspection_type' => 'ประเภทงานตรวจ',
    'worksheet_list' => 'เลขที่ใบขอติดตั้ง',
    'excel' => 'ดาวน์โหลดไฟล์ Excel',
    'worksheet_not_found' => 'ไม่พบใบขอติดตั้งอุปกรณ์',
    'expected_end_date' => 'วันที่คาดว่าจะเสร็จสิ้น',
    'not_register' => 'ยังไม่จดทะเบียน',
    'car_detail' => 'ทะเบียนรถ / หมายเลขเครื่องยนต์ / หมายเลขตัวถัง',
    'lot' => 'Lot',

];
