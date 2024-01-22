<?php

namespace App\Enums;

abstract class ContractEnum
{
    const REQUEST_CONTRACT = 'REQUEST_CONTRACT'; //ขอจัดทำสัญญา
    const ACTIVE_CONTRACT = 'ACTIVE_CONTRACT'; //จัดทำสัญญา
    const SEND_OFFER_SIGN = 'SEND_OFFER_SIGN'; //ส่งเสนอลงนาม
    const SEND_CUSTOMER_SIGN = 'SEND_CUSTOMER_SIGN'; //ส่งให้ลูกค้าเซน
    const ACTIVE_BETWEEN_CONTRACT = 'ACTIVE_BETWEEN_CONTRACT'; //อยู่ระหว่างสัญญา

    const REQUEST_CHANGE_ADDRESS = 'REQUEST_CHANGE_ADDRESS'; //ขอเปลี่ยนแปลงที่อยู่
    const REQUEST_CHANGE_USER_CAR = 'REQUEST_CHANGE_USER_CAR'; //ขอเปลี่ยนแปลงชื่อผู้ใช้รถ
    const REQUEST_TRANSFER_CONTRACT = 'REQUEST_TRANSFER_CONTRACT'; //ขอโอนย้ายบริษัทผู้เช่า

    const REJECT_REQUEST = 'REJECT_REQUEST'; //ไม่ยืนยันการเปลี่ยนแปลง

    const CLOSE_CONTRACT = 'CLOSE_CONTRACT'; //ปิดสัญญา
    const CANCEL_CONTRACT = 'CANCEL_CONTRACT'; //ยกเลิกสัญญา

    const START_RENT_PICKUP_DATE = 'START_RENT_PICKUP_DATE'; //ตั้งแต่วันที่เริ่มเช่า
    const START_RENT_RETURN_DATE = 'START_RENT_RETURN_DATE'; //ตั้งแต่วันที่รับรถ

    const END_RENT_EXPIRE_DATE = 'END_RENT_EXPIRE_DATE'; //ตั้งแต่วันที่สิ้นสุดการเช่า
    const END_RENT_RETURN_DATE = 'END_RENT_RETURN_DATE'; //ตั้งแต่วันที่คืนรถ

    const CONFIRM = 'CONFIRM';  //ยืนยัน
    const REJECT = 'REJECT';    //ไม่ยืนยัน

}
