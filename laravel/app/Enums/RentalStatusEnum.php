<?php

namespace App\Enums;

abstract class RentalStatusEnum
{
    const DRAFT = 'DRAFT';                  //ร่าง
    const PENDING = 'PENDING';              //รอชำระเงิน
    const PAID = 'PAID';                    //ยืนยัน
    const SUCCESS = 'SUCCESS';              //เสร็จสิ้น
    const PREPARE = 'PREPARE';              //เตรียมรถ
    const CHANGE = 'CHANGE';                //เปลี่ยนแปลงข้อมูลรถ
    const AWAIT_RECEIVE = 'AWAIT_RECEIVE';  //รอรับรถ
    const ACTIVE = 'ACTIVE';                //กำลังเช่า
    const AWAIT_RETURN = 'AWAIT_RETURN';    //เตรียมคืนรถ
    const TEMPORARY = 'TEMPORARY';
    const REMARK = 'REMARK';
    const CANCEL = 'CANCEL';                //ยกเลิก
}
