<?php
use App\Enums\LongTermRentalApprovalTypeEnum;
use App\Enums\LongTermRentalJobType;
use App\Enums\AuctionResultEnum;
use App\Enums\SpecStatusEnum;
use App\Enums\ComparisonPriceStatusEnum;
use App\Enums\ImportCarStatusEnum;
use App\Enums\InstallEquipmentStatusEnum;
use App\Enums\LongTermRentalPriceStatusEnum;
use App\Enums\LongTermRentalProgressStatusEnum;
use App\Enums\QuotationStatusEnum;
use App\Enums\LongTermRentalStatusEnum;

return [
    'page_title' => 'เช่าระยะยาว',
    'add_new' => 'เพิ่มใบขอเช่า',
    'total_items' => 'รายการทั้งหมด',
    //customer
    'customer_table' => 'ข้อมูลลูกค้า',
    'customer_type' => 'ประเภทลูกค้า',
    'customer_code' => 'รหัสลูกค้า',
    'customer' => 'ลูกค้า',
    'customer_tax' => 'เลขที่ผู้เสียภาษี',
    'email' => 'อีเมล',
    'tel' => 'เบอร์โทรศัพท์มือถือ',
    'province' => 'จังหวัด',
    'zipcode' => 'รหัสไปรษณีย์',
    'address' => 'ที่อยู่',
    'is_required_tax_invoice' => 'ขอใบเสร็จและใบกำกับภาษี',
    //rental
    'rental_detail' => 'ข้อมูลงาน',
    'rental_requisition_sheet' => 'เลขที่ใบขอเช่า',
    'rental_table' => 'ข้อมูลงานประมูล',
    'rental_table_long_term' => 'ข้อมูลงานเช่าระยะยาว',
    'job_type' => 'ประเภทงาน',
    'type' => 'ประเภท',
    'need_pay_auction' => 'ต้องการซื้อซองประมูล',
    'payment_status' => 'สถานะการชำระเงิน',
    'rental_duration' => 'ระยะเวลาเช่า (เดือน)',
    'rental_time' => 'ระยะเวลาเช่า',
    'is_paid_auction' => 'จ่ายเงินสำเร็จ',
    'won_auction' => 'สถานะงานประมูล',
    'auction_submit_date' => 'วันที่ยื่นประมูล',
    'rental_month' => 'ขอจัดทำใบเสนอราคา',
    'rental_months' => 'เดือนสำหรับจัดทำใบเสนอราคา',
    'rental_spec' => 'ข้อมูลการตีสเปครถ',
    'rental_car' => 'ข้อมูลรถ',
    //auction
    'auction_table' => 'ข้อมูลผลงานประมูล',
    'bidder_price' => 'ราคารวมผู้ที่ได้งาน',
    'bidder_name' => 'ชื่อผู้ที่ได้งาน',
    'auction_winning_date' => 'วันที่ได้งาน',
    'actual_delivery_date' => 'กำหนดส่งมอบจริง',
    'contract_start_date' => 'วันที่เริ่มสัญญา',
    'contract_end_date' => 'วันที่สิ้นสุดสัญญา',
    'reject_reason' => 'เหตุผลที่ไม่ยื่น/ไม่ได้งาน',
    'reject_reason_description' => 'เหตุผล',

    'remark' => 'หมายเหตุ',
    'check_spec' => 'ตีสเปครถ',
    'check_car' => 'เช็กรถ',
    'select_spec_' . BOOL_FALSE => 'ขอตีสเปครถ',
    'select_spec_' . BOOL_TRUE => 'เลือกจาก Bom',
    //upload
    'upload_table' => 'อัปโหลดข้อมูล',
    'tor_file' => 'TOR งาน (ขนาดไฟล์ไม่เกิน 10 MB)',
    'rental_file' => 'เอกสารขอเช่า (ขนาดไฟล์ไม่เกิน 10 MB)',
    'upload_file' => 'อัปโหลดเอกสารเพิ่มเติม',
    'payment_form' => 'เอกสารชำระค่าแบบ (ขนาดไฟล์ไม่เกิน 10 MB)',

    'car_info' => 'กรอกข้อมูลรถยนต์',
    'request_spec' => 'ขอสเปครถยนต์',
    'worksheet_no' => 'เลขที่ใบเช่า',
    'spec_status' => 'สถานะตีสเปค',
    'comparison_price_status' => 'สถานะเปรียบเทียบราคา',
    'approve_price_status' => 'สถานะอนุมัติราคา',
    'rental_price_status' => 'สถานะจัดทำราคาเช่า',
    'quotation_no' => 'เลขที่ใบเสนอราคา',
    'quotation_status' => 'สถานะใบเสนอราคา',
    'requisition_pdf' => 'แบบฟอร์มยืนยันขอเช่า',
    'lt_rental_status' => 'สถานะใบขอเช่า',

    'status_text' => 'รอผลดำเนินการ',
    'status_class' => 'warning',

    'status_text' => 'ได้งาน',
    'status_class' => 'success',

    'status_text' => 'ไม่ได้งาน',
    'status_class' => 'danger',

    'job_type_' . LongTermRentalJobType::AUCTION => 'ยื่นซอง',
    'job_type_' . LongTermRentalJobType::QUOTATION => 'เสนอราคาทั่วไป',
    'job_type_' . LongTermRentalJobType::EBIDDING => 'EBidding',
    'job_type_' . LongTermRentalJobType::BUDGET => 'ขอราคาตั้งงบ',

    'type_' . LongTermRentalApprovalTypeEnum::AFFILIATED => 'ในเครือ',
    'type_' . LongTermRentalApprovalTypeEnum::UNAFFILIATED => 'นอกเครือ',

    'need_pay_auction_' . BOOL_FALSE => 'ไม่ต้องการ',
    'need_pay_auction_' . BOOL_TRUE => 'ต้องการ',

    'won_auction_' . AuctionResultEnum::WAITING => 'รอผลดำเนินการ',
    'won_auction_class_' . AuctionResultEnum::WAITING => 'warning',

    'won_auction_' . AuctionResultEnum::WON => 'ได้งาน',
    'won_auction_class_' . AuctionResultEnum::WON => 'success',

    'won_auction_' . AuctionResultEnum::LOSE => 'ไม่ได้งาน',
    'won_auction_class_' . AuctionResultEnum::LOSE => 'danger',

    //specs and equipment
    'specs_and_equipment' => 'สเปครถ',
    'spec_equipment' => 'สเปคอุปกรณ์',
    'car_table' => 'ข้อมูลรถ',
    'car_class' => 'รุ่น',
    'car_color' => 'สี',
    'car_amount' => 'จำนวน',
    'total_amount' => 'จำนวนทั้งหมด',
    'amount_per_car' => 'จำนวน/คัน',
    'car_amount_unit' => 'จำนวน (คัน)',
    'car_amount_per' => 'จำนวนต่อคัน',
    'accessories_table' => 'ข้อมูลอุปกรณ์เสริมพื้นฐาน',
    'accessories' => 'อุปกรณ์เสริม',
    'type_accessories' => 'ประเภท',
    'amount_accessory' => 'จำนวน(ชุด)',
    'car_unit' => 'คัน',
    'tor_section' => 'หัวข้อใน TOR',
    'summary_car_detail' => 'สรุปรถทั้งหมด',
    'specs_approve' => 'อนุมัติสเปครถและอุปกรณ์',
    'spec_car_accessory' => 'สเปครถและอุปกรณ์',
    'cancel' => 'ยกเลิก',
    'approved' => 'อนุมัติ',
    'reject' => 'ไม่อนุมัติ',
    'remark_reason' => 'เหตุผลที่ไม่อนุมัติ : ',
    'add_manually' => 'เพิ่มด้วยตนเอง',
    'accessory_list' => 'รายการอุปกรณ์เสริม',
    'add_dealer' => 'เพิ่มดิลเลอร์',

    'spec_status_' . SpecStatusEnum::DRAFT => 'ร่าง',
    'spec_status_class_' . SpecStatusEnum::DRAFT => 'primary',

    'spec_status_' . SpecStatusEnum::ACCESSORY_CHECK => 'ตรวจสเปคอุปกรณ์',
    'spec_status_class_' . SpecStatusEnum::ACCESSORY_CHECK => 'primary',

    'spec_status_' . SpecStatusEnum::PENDING_REVIEW => 'รออนุมัติ',
    'spec_status_class_' . SpecStatusEnum::PENDING_REVIEW => 'warning',

    'spec_status_' . SpecStatusEnum::CONFIRM => 'อนุมัติ',
    'spec_status_class_' . SpecStatusEnum::CONFIRM => 'success',

    'spec_status_' . SpecStatusEnum::REJECT => 'ไม่อนุมัติ',
    'spec_status_class_' . SpecStatusEnum::REJECT => 'danger',

    'spec_status_' . SpecStatusEnum::PENDING_CHECK => 'รอเช็กรถ',
    'spec_status_class_' . SpecStatusEnum::PENDING_CHECK => 'warning',

    'spec_status_' . SpecStatusEnum::NO_DELIVERY => 'ไม่มีรถพร้อมส่งมอบ',
    'spec_status_class_' . SpecStatusEnum::NO_DELIVERY => 'danger',

    'spec_status_' . SpecStatusEnum::CHANGE_CAR => 'เปลี่ยนรถ',
    'spec_status_class_' . SpecStatusEnum::CHANGE_CAR => 'secondary',


    // compare price
    'compare_price' => 'เปรียบเทียบราคา',
    'view_compare_price' => 'ดูรายการเปรียบเทียบราคา',
    'compare_price_approve' => 'อนุมัติรายการเปรียบเทียบราคา',
    'sheet_no' => 'เลขที่ใบงาน',
    'selected_dealers' => 'Dealer ที่เลือก',
    'tor_detail' => 'รายละเอียดจาก TOR',
    'discount' => 'ส่วนลด',

    'compare_price_status_' . ComparisonPriceStatusEnum::DRAFT => 'ร่าง',
    'compare_price_status_' . ComparisonPriceStatusEnum::CONFIRM => 'ยืนยัน',
    'compare_price_status_' . ComparisonPriceStatusEnum::PENDING_REVIEW => 'รออนุมัติ',
    'compare_price_status_' . ComparisonPriceStatusEnum::REJECT => 'ไม่อนุมัติ',

    'compare_price_class_' . ComparisonPriceStatusEnum::DRAFT => 'primary',
    'compare_price_class_' . ComparisonPriceStatusEnum::CONFIRM => 'success',
    'compare_price_class_' . ComparisonPriceStatusEnum::PENDING_REVIEW => 'warning',
    'compare_price_class_' . ComparisonPriceStatusEnum::REJECT => 'danger',

    'rental_price_status_' . LongTermRentalPriceStatusEnum::DRAFT => 'ร่าง',
    'rental_price_status_' . LongTermRentalPriceStatusEnum::REJECT => 'ไม่อนุมัติ',
    'rental_price_status_' . LongTermRentalPriceStatusEnum::CONFIRM => 'ยืนยัน',

    'rental_price_class_' . LongTermRentalPriceStatusEnum::DRAFT => 'primary',
    'rental_price_class_' . LongTermRentalPriceStatusEnum::REJECT => 'danger',
    'rental_price_class_' . LongTermRentalPriceStatusEnum::CONFIRM => 'success',

    // quotation
    'quotation' => 'จัดทำราคาเช่า',
    'price' => 'ราคาเช่า',
    'dealers' => 'Dealer',
    'dealer_price' => 'ราคารถยนต์และอุปกรณ์(จากดีลเลอร์ที่สั่งซื้อ)',
    'car_class_select' => 'ยี่ห้อ/รุ่น',
    'order_price' => 'ราคาจัดซื้อ',
    'rental_price' => 'ราคาเช่ารถยนต์และอุปกรณ์',
    'wreck_price' => 'ราคาค่าซากรถยนต์และอุปกรณ์',
    'rental_fee' => 'ค่าเช่า/บาท/คัน',
    'wreck_fee' => 'ค่าซาก/บาท/คัน เมื่อครบ',
    'month' => 'เดือน',
    'save_qt' => 'บันทึก + สร้างใบเสนอราคา',
    'need_wreck_price' => 'ต้องการราคาค่าซาก',

    'purchase_option_' . STATUS_ACTIVE => 'ต้องการ',
    'purchase_option_' . STATUS_DEFAULT => 'ไม่ต้องการ',

    'quotation_status_' . QuotationStatusEnum::DRAFT => 'ร่าง',
    'quotation_status_' . QuotationStatusEnum::CONFIRM => 'ยืนยัน',
    'quotation_status_' . QuotationStatusEnum::PENDING_REVIEW => 'รออนุมัติ',
    'quotation_status_' . QuotationStatusEnum::REJECT => 'ไม่อนุมัติ',
    'quotation_status_' . QuotationStatusEnum::CANCEL => 'ยกเลิก',

    'quotation_class_' . QuotationStatusEnum::DRAFT => 'primary',
    'quotation_class_' . QuotationStatusEnum::CONFIRM => 'success',
    'quotation_class_' . QuotationStatusEnum::PENDING_REVIEW => 'warning',
    'quotation_class_' . QuotationStatusEnum::REJECT => 'danger',
    'quotation_class_' . QuotationStatusEnum::CANCEL => 'secondary',

    'status_class_' . LongTermRentalStatusEnum::QUOTATION => 'success',
    'status_' . LongTermRentalStatusEnum::QUOTATION => 'อนุมัติ',

    'lt_rental_status_class_' . LongTermRentalStatusEnum::NEW => 'primary',
    'lt_rental_status_' . LongTermRentalStatusEnum::NEW => 'ร่าง',
    'lt_rental_status_class_' . LongTermRentalStatusEnum::SPECIFICATION => 'primary',
    'lt_rental_status_' . LongTermRentalStatusEnum::SPECIFICATION => 'ตีสเปครถและอุปกรณ์',
    'lt_rental_status_class_' . LongTermRentalStatusEnum::COMPARISON_PRICE => 'primary',
    'lt_rental_status_' . LongTermRentalStatusEnum::COMPARISON_PRICE => 'เปรียบเทียบราคา',
    'lt_rental_status_class_' . LongTermRentalStatusEnum::RENTAL_PRICE => 'primary',
    'lt_rental_status_' . LongTermRentalStatusEnum::RENTAL_PRICE => 'จัดทำราคาเช่า',
    'lt_rental_status_class_' . LongTermRentalStatusEnum::QUOTATION => 'primary',
    'lt_rental_status_' . LongTermRentalStatusEnum::QUOTATION => 'รออนุมัติใบเสนอราคา',
    'lt_rental_status_class_' . LongTermRentalStatusEnum::QUOTATION_CONFIRM => 'primary',
    'lt_rental_status_' . LongTermRentalStatusEnum::QUOTATION_CONFIRM => 'อนุมัติใบเสนอราคา',
    'lt_rental_status_class_' . LongTermRentalStatusEnum::COMPLETE => 'success',
    'lt_rental_status_' . LongTermRentalStatusEnum::COMPLETE => 'ยืนยันการเช่า',
    'lt_rental_status_class_' . LongTermRentalStatusEnum::CANCEL => 'secondary',
    'lt_rental_status_' . LongTermRentalStatusEnum::CANCEL => 'ยกเลิกการเช่า',

    'approve_status_' . LongTermRentalStatusEnum::COMPLETE => 'ยืนยัน',
    'approve_status_' . LongTermRentalStatusEnum::CANCEL => 'ยกเลิก',

    // 'tor_detail' => 'รายละเอียด TOR',
    'have_accessory' => 'มีอุปกรณ์เสริม',
    'tor_description' => 'รายละเอียด TOR',
    'work_detail' => 'ข้อมูลงาน',
    'disapprove_confirm' => 'ยืนยันไม่อนุมัติ สเปครถและอุปกรณ์',
    'approve_confirm' => 'ยืนยันอนุมัติ สเปครถและอุปกรณ์',
    'document_tor' => 'เอกสาร TOR งาน',

    //bom
    'bom' => 'เพิ่มจาก BOM',
    'name_and_no' => 'ชื่อ/เลขที่ Bom ',
    'add_list_car' => 'เพิ่มรายการรถ',

    'tor_line_check_input' => 'สเปครถ',
    'at_least_one_must_true' => 'กรุณาเลือกอย่างน้อย 1 สเปคต่อ tor',

    'approval_info' => 'ข้อมูลยืนยันขอเช่า',
    'approved_rental_file' => 'เอกสารสั่งเช่า (ขนาดไฟล์ไม่เกิน 10 MB)',
    'approve_status' => 'สถานะยืนยันขอเช่า',
    'rental_duration_invalid' => 'ระยะเวลาเช่า ต้องอยู่ในเดือนสำหรับจัดทำใบเสนอราคา',
    'month_type_invalid' => 'เดือนสำหรับจัดทำใบเสนอราคาต้องเป็นตัวเลขเท่านั้น',

    'offer_date' => 'วันที่ต้องเสนอราคา',
    'reject_detail' => 'ข้อมูลการอนุมัติใบเสนอราคา',
    'rental_car_info' => 'ข้อมูลรถที่ต้องการเช่า',
    'print' => 'พิมพ์แบบฟอร์ม',
    'price_quotation' => 'ราคาเช่ารถยนต์และอุปกรณ์',
    'purchase_options_quotation' => 'ราคาค่าซากรถยนต์และอุปกรณ์',
    'exclude_vat' => '(ไม่รวม VAT)',
    'include_vat' => '(รวม VAT)',
    'car_class_and_color' => 'รุ่น-สี',

    'need_accessory' => 'ต้องสั่งซื้ออุปกรณ์เสริม',
    'optional_accessory' => 'มีอุปกรณ์เสริมที่ต้องสั่งซื้อเพิ่มเติม',
    'car_accessory_detail' => 'ข้อมูลอุปกรณ์เสริมที่มากับรถ',
    'car_accessory' => 'อุปกรณ์เสริมที่มากับรถ',
    'lt_detail' => 'รายละเอียดข้อมูล',
    'pr_line_detail' => 'ข้อมูลรถที่เช่า',
    'pr_line' => 'รถที่เช่า',
    'add_one_line' => 'เพิ่มรถรายคัน',
    'add_all_line' => 'เพิ่มรถทั้งหมด',
    'save_pr' => 'บันทึก + เปิดใบขอซื้อรถ',
    'invalid_amount' => 'จำนวนไม่ถูกต้อง',
    'invalid_month' => 'เดือนไม่ถูกต้อง',


    'confirm_check_car' =>'ข้อมูลยืนยันการเช็กรถ',
    'customer_need' => 'ลูกค้าต้องการ',
    'ready_to_delivery' => 'พร้อมส่งมอบ',

    'delivery_month_year' => 'เดือน/ปี ที่พร้อมส่งมอบ',
    'dealer_check' => 'ข้อมูลการเช็กกำหนดส่งมอบรถยนต์ก่อนเสนอราคา',

    'delivery_month' => 'เดือนที่พร้อมส่งมอบ',
    'response_date' => 'วันที่ตอบกลับ',
    'stock_order' => 'ดีลเลอร์มี Stock /Order Fleet',
    'contact' => 'ข้อมูลผู้ติดต่อ',
    'contact_name' => 'ชื่อผู้ติดต่อ',
    'no_car_dealer' => 'ไม่มีรถส่งมอบ',
    'no_car_reason' => 'เหตุผล',
    'is_ready_to_deliver' => 'พร้อมส่งมอบ',
    'lt_rental_type' => 'ประเภทของงาน',
    'actual_date_need' => 'มีวันที่ส่งมอบ',
    'delivery_date_range' => 'ระยะเวลาส่งมอบ',

//    Spec
    'spec_header' => "การตีสเปครถโดย QA",
    'require_spec' => "ตีสเปคโดย QA",
    'cars_require' => "กรุณาเลือกข้อมูลรถ",
    'add_car' => 'เพิ่มข้อมูลรถ',
    'send_mail' => 'ส่งอีเมล',

    'order_status' => 'สถานะซื้อรถ',
    'delivery_car_status' => 'สถานะส่งมอบรถ',
    'install_equipment_status' => 'สถานะติดตั้งอุปกรณ์',
    'finance_status' => 'สถานะจัดไฟแนนซ์',
    'act_status' => 'สถานะทำพรบ.',
    'insurance_status' => 'สถานะทำประกัน',
    'register_car_status' => 'สถานะจดทะเบียนรถใหม่',
    'delivery_customer_status' => 'สถานะส่งมอบลูกค้า',

    'order_status_class_' . LongTermRentalStatusEnum::COMPLETE => 'warning',
    'order_status_' . LongTermRentalStatusEnum::COMPLETE => 'รอซื้อรถ',

    'order_status_class_' . LongTermRentalProgressStatusEnum::PROCESSING => 'warning',
    'order_status_' . LongTermRentalProgressStatusEnum::PROCESSING => 'กำลังซื้อ',

    'order_status_class_' . LongTermRentalProgressStatusEnum::SUCCESS_ORDER => 'success',
    'order_status_' . LongTermRentalProgressStatusEnum::SUCCESS_ORDER => 'ซื้อรถใหม่เสร็จสิ้น',

    'delivery_car_status_class_' . ImportCarStatusEnum::WAITING_DELIVERY => 'warning',
    'delivery_car_status_' . ImportCarStatusEnum::WAITING_DELIVERY => 'รอส่งมอบรถ ',

    'delivery_car_status_class_' . LongTermRentalProgressStatusEnum::DELIVERING => 'warning',
    'delivery_car_status_' . LongTermRentalProgressStatusEnum::DELIVERING => 'กำลังส่งมอบ',

    'delivery_car_status_class_' . ImportCarStatusEnum::DELIVERY_COMPLETE => 'success',
    'delivery_car_status_' . ImportCarStatusEnum::DELIVERY_COMPLETE => 'ส่งมอบรถเสร็จสิ้น ',

    'install_equipment_status_class_' . InstallEquipmentStatusEnum::WAITING => 'warning',
    'install_equipment_status_' . InstallEquipmentStatusEnum::WAITING => 'รอติดตั้ง',

    'install_equipment_status_class_' . InstallEquipmentStatusEnum::INSTALL_IN_PROCESS => 'warning',
    'install_equipment_status_' . InstallEquipmentStatusEnum::INSTALL_IN_PROCESS => 'กำลังติดตั้ง',

    'install_equipment_status_class_' . InstallEquipmentStatusEnum::COMPLETE => 'success',
    'install_equipment_status_' . InstallEquipmentStatusEnum::COMPLETE => 'ติดตั้งเสร็จสิ้น',
];
