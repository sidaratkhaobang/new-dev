<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Enums\InspectionTypeEnum;
use App\Models\InspectionFlow;
use App\Enums\TransferTypeEnum;
use App\Enums\TransferReasonEnum;
use App\Models\InspectionForm;
use App\Models\InspectionStep;

class InspectionFlowsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            [
                'name' => 'รับรถใหม่',
                'type' => InspectionTypeEnum::NEW_CAR,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => false,
                'steps' => [
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจอุปกรณ์/คุณภาพ',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => false,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจรถใหม่',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => true,
                    ],
                ]
            ],
            [
                'name' => 'รถใหม่ติดตั้งอุปกรณ์',
                'type' => InspectionTypeEnum::EQUIPMENT,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => false,
                'steps' => [
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจอุปกรณ์ก่อนติดตั้ง',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => false,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มอุปกรณ์ที่ติดตั้ง',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => false,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจอุปกรณ์/คุณภาพ',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => false,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มอุปกรณ์ที่ติดตั้ง',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => false,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจคุณภาพรถยนต์ (PDI)',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => false,
                    ],
                ]
            ],
            [
                'name' => 'รถเช่ายาว',
                'type' => InspectionTypeEnum::LONG_TERM_RENTAL,
                'is_need_customer_sign_in' => true,
                'is_need_customer_sign_out' => true,
                'steps' => [
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจความสะอาด/ความพร้อม',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจความสะอาด/ความพร้อม',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                ]
            ],
            [
                'name' => 'เรือ (พร้อมคนขับ)',
                'type' => InspectionTypeEnum::BOAT,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => false,
                'steps' => [
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจเรือ',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => true,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจเรือ',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => true,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจเรือ',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => true,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจเรือ',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => true,
                    ],
                ]
            ],
            [
                'name' => 'รถเช่าสั้น (Self-drive)',
                'type' => InspectionTypeEnum::SELF_DRIVE,
                'is_need_customer_sign_in' => true,
                'is_need_customer_sign_out' => true,
                'steps' => [
                    //--------------------------------OUT--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจ selfdrive',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => true,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจความสะอาดความพร้อม selfdrive',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => false,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจ selfdrive',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                    //--------------------------------IN--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจ selfdrive',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจความสะอาดความพร้อม selfdrive',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => false,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจอุปกรณ์รถยนตร์ทดแทน',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => false,
                    ],
                ]
            ],
            [
                'name' => 'รถเช่าสั้น - มินิโค้ช (พร้อมคนขับ)',
                'type' => InspectionTypeEnum::MINI_COACH,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => true,
                'steps' => [
                    //--------------------------------OUT--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจพร้อมคนขับ',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจ mini coach',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => false,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจความสะอาด/ความพร้อม',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                    //--------------------------------IN--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจความสะอาด/ความพร้อม',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจ mini coach',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => false,
                    ],
                ]
            ],
            [
                'name' => 'รถเช่าสั้น - บัส (พร้อมคนขับ)',
                'type' => InspectionTypeEnum::BUS,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => true,
                'steps' => [
                    //--------------------------------OUT--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจพร้อมคนขับ',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจ bus',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => false,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจความสะอาด/ความพร้อม',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                    //--------------------------------IN--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจความสะอาด/ความพร้อม',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจ bus',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => false,
                    ],
                ]
            ],
            [
                'name' => 'รถเช่าสั้น - รถตู้ (พร้อมคนขับ)',
                'type' => InspectionTypeEnum::SPRINTER,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => true,
                'steps' => [
                    //--------------------------------OUT--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจพร้อมคนขับ',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจ sprinter',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => false,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจความสะอาด/ความพร้อม',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                    //--------------------------------IN--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจความสะอาด/ความพร้อม',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจ sprinter',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => false,
                    ],
                ]
            ],
            [
                'name' => 'รถเช่าสั้น - ลิมูซีน (พร้อมคนขับ)',
                'type' => InspectionTypeEnum::LIMOUSINE,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => true,
                'steps' => [
                    //--------------------------------OUT--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจพร้อมคนขับ',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจ Limousine',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => false,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจความสะอาด/ความพร้อม',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                    //--------------------------------IN--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจความสะอาด/ความพร้อม',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจ Limousine',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => false,
                    ],
                ]
            ],
            [
                'name' => 'รถเช่าสั้น - รถขนส่ง (พร้อมคนขับ)',
                'type' => InspectionTypeEnum::CARGO_TRUCK,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => false,
                'steps' => [
                    //--------------------------------OUT--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจรถขนส่ง',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                    //--------------------------------IN--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจรถขนส่ง',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                ]
            ],
            [
                'name' => 'รถเช่าสั้น - รถสไลด์ (พร้อมคนขับ)',
                'type' => InspectionTypeEnum::SLIDE_FORKLIFT,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => false,
                'steps' => [
                    //--------------------------------OUT--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจรถสไลด์',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                    //--------------------------------IN--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจรถสไลด์',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                ]
            ],
            [
                'name' => 'รถยืมใช้พนักงาน',
                'type' => InspectionTypeEnum::BORROWED,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => false,
                'steps' => [
                    //--------------------------------OUT--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจความสะอาด/ความพร้อม',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => true,
                    ],
                    //--------------------------------IN--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจความสะอาด/ความพร้อม',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => true,
                    ],
                ]
            ],
            [
                'name' => 'รถทดแทน-เช่ายาว',
                'type' => InspectionTypeEnum::REPLACEMENT,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => true,
                'steps' => [
                    //--------------------------------OUT--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจความสะอาด/ความพร้อม',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => true,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจความสะอาด/ความพร้อม',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                ]
            ],
            [
                'name' => 'รถทดแทน (Self-drive)',
                'type' => InspectionTypeEnum::REPLACEMENT_SD,
                'is_need_customer_sign_in' => true,
                'is_need_customer_sign_out' => false,
                'steps' => [
                    //--------------------------------OUT--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจความสะอาด/ความพร้อม',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจอุปกรณ์รถยนตร์ทดแทน',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => false,
                    ],
                ]
            ],
            [
                'name' => 'รถอุบัติเหตุ - ส่งรถลูกค้า',
                'type' => InspectionTypeEnum::ACCIDENT_DC,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => true,
                'steps' => [
                    //--------------------------------OUT--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจอุบัติเหตุ (ลูกค้า)',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => true,
                    ],
                ]
            ],
            [
                'name' => 'รถอุบัติเหตุ - รับรถจากลูกค้า',
                'type' => InspectionTypeEnum::ACCIDENT_RC,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => false,
                'steps' => [
                    //--------------------------------IN--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจอุบัติเหตุ (ลูกค้า)',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => true,
                    ],
                ]
            ],
            [
                'name' => 'รถอุบัติเหตุ - ส่งอู่',
                'type' => InspectionTypeEnum::ACCIDENT_DG,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => false,
                'steps' => [
                    //--------------------------------OUT--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_GARAGE,
                        'inspection_form' => 'ฟอร์มตรวจอุบัติเหตุ (ลูกค้า)',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => false,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_GARAGE,
                        'inspection_form' => 'ฟอร์มตรวจรถยนต์สำหรับอู่ซ่อม',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => false,
                    ],
                ]
            ],
            [
                'name' => 'รถอุบัติเหตุ - รับอู่',
                'type' => InspectionTypeEnum::ACCIDENT_RG,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => false,
                'steps' => [
                    //--------------------------------IN--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจอุบัติเหตุ (ลูกค้า)',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => false,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจรถยนต์สำหรับอู่ซ่อม',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => false,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจสอบงานซ่อมอุบัติเหตุรถยนต์',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => false,
                    ],
                ]
            ],
            [
                'name' => 'เปลี่ยนประเภทรถ',
                'type' => InspectionTypeEnum::CHANGE_TYPE,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => false,
                'steps' => [
                    //--------------------------------OUT--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจอุปกรณ์/คุณภาพ',
                        'is_need_images' => false,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => false,
                    ],
                ]
            ],
            [
                'name' => 'โอนย้ายสาขา',
                'type' => InspectionTypeEnum::TRANSFER,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => false,
                'steps' => [
                    //--------------------------------OUT--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจความสะอาด/ความพร้อม',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => false,
                    ],
                    //--------------------------------IN--------------------------------
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจความสะอาด/ความพร้อม',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => false,
                    ],
                ]
            ],
            [
                'name' => 'รถซ่อมบำรุง - ส่งรถลูกค้า',
                'type' => InspectionTypeEnum::MAINTENANCE_DC,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => true,
                'steps' => [
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจสภาพรถยนต์ก่อนและหลังนำรถเข้าซ่อมบำรุง',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => false,
                    ],
                ]
            ],
            [
                'name' => 'รถซ่อมบำรุง - รับรถจากลูกค้า',
                'type' => InspectionTypeEnum::MAINTENANCE_RC,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => false,
                'steps' => [
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                        'inspection_form' => 'ฟอร์มตรวจสภาพรถยนต์ก่อนและหลังนำรถเข้าซ่อมบำรุง',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => false,
                    ],
                ]
            ],
            [
                'name' => 'รถซ่อมบำรุง - ส่งอู่',
                'type' => InspectionTypeEnum::MAINTENANCE_DG,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => false,
                'steps' => [
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_GARAGE,
                        'inspection_form' => 'ฟอร์มตรวจสภาพรถยนต์ก่อนและหลังนำรถเข้าซ่อมบำรุง',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => true,
                        'is_need_send_mobile' => false,
                    ],
                ]
            ],
            [
                'name' => 'รถซ่อมบำรุง - รับอู่',
                'type' => InspectionTypeEnum::MAINTENANCE_RG,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => false,
                'steps' => [
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_GARAGE,
                        'inspection_form' => 'ฟอร์มตรวจสภาพรถยนต์ก่อนและหลังนำรถเข้าซ่อมบำรุง',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => false,
                    ],
                ]
            ],
            [
                'name' => 'รถซ่อมบำรุง',
                'type' => InspectionTypeEnum::MAINTENANCE,
                'is_need_customer_sign_in' => false,
                'is_need_customer_sign_out' => false,
                'steps' => [
                    [
                        'transfer_type' => TransferTypeEnum::OUT,
                        'transfer_reason' => TransferReasonEnum::DELIVER_CUSTOMER,
                        'inspection_form' => 'ฟอร์มตรวจสภาพรถยนต์ก่อนและหลังนำรถเข้าซ่อมบำรุง',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => false,
                    ],
                    [
                        'transfer_type' => TransferTypeEnum::IN,
                        'transfer_reason' => TransferReasonEnum::RECEIVE_GARAGE,
                        'inspection_form' => 'ฟอร์มตรวจสภาพรถยนต์ก่อนและหลังนำรถเข้าซ่อมบำรุง',
                        'is_need_images' => true,
                        'is_need_inspector_sign' => false,
                        'is_need_send_mobile' => false,
                    ],
                ]
            ],
        ];

        foreach ($datas as $data) {
            $exists = InspectionFlow::where('inspection_type', $data['type'])->exists();
            if (!$exists) {
                $d = new InspectionFlow();
                $d->name = $data['name'];
                $d->inspection_type = $data['type'];
                $d->is_need_customer_sign_in = $data['is_need_customer_sign_in'];
                $d->is_need_customer_sign_out = $data['is_need_customer_sign_out'];
                $d->status = STATUS_ACTIVE;
                $d->save();

                if (isset($data['steps'])) {
                    $steps = $data['steps'];
                    $step_seq = 1;
                    foreach ($steps as $step) {
                        $form = InspectionForm::where('name', $step['inspection_form'])->first();
                        if ($form) {
                            $d2 = new InspectionStep();
                            $d2->inspection_flow_id = $d->id;
                            $d2->seq = $step_seq;
                            $d2->transfer_type = $step['transfer_type'];
                            $d2->transfer_reason = $step['transfer_reason'];
                            $d2->inspection_form_id = $form->id;
                            $d2->is_need_images = $step['is_need_images'];
                            $d2->is_need_send_mobile = isset($step['is_need_send_mobile']) ? $step['is_need_send_mobile'] : false;
                            $d2->is_need_inspector_sign = $step['is_need_inspector_sign'];
                            $d2->save();

                            $step_seq++;
                        }
                    }
                }
            }
        }
    }
}
