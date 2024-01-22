<?php

namespace App\Enums;

abstract class ReplacementTypeEnum
{
    const SEND_REPLACE_RECEIVE_MAIN = 'SEND_REPLACE_RECEIVE_MAIN'; // ส่งรถทดแทนรับรถหลัก
    const SEND_MAIN_RECEIVE_REPLACE = 'SEND_MAIN_RECEIVE_REPLACE'; // ส่งรถหลักรับรถทดแทน
    const RECEIVE_MAIN = 'RECEIVE_MAIN'; // รับรถหลัก
    const SEND_MAIN = 'SEND_MAIN'; // ส่งรถหลัก
    const SEND_REPLACE = 'SEND_REPLACE'; // ส่งรถทดแทน
    const RECEIVE_REPLACE = 'RECEIVE_REPLACE'; // รับรถทดแทน
    // const CHANGE_REPLACE = 'CHANGE_REPLACE'; // เปลี่ยนรถทดแทน
}