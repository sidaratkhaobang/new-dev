<?php

namespace App\Enums;

abstract class CheckDistanceTypeEnum
{
    const REPAIR = 'REPAIR'; //ซ่อม
    const CHANGE = 'CHANGE'; //เปลี่ยน
    const SERVICE_CHARGE = 'SERVICE_CHARGE'; //ค่าบริการ
    const CHECK = 'CHECK'; //ตรวจเช็ค
    const ADJUST = 'ADJUST'; //ปรับตั้ง
    const CLEAN = 'CLEAN'; //ทำความสะอาด
    const MODIFY = 'MODIFY'; //แก้ไข
    const PUTTER_OUT = 'PUTTER_OUT'; //ดับไฟ
    const RECAP = 'RECAP'; //ปะยาง
    const FREE_SERVICE = 'FREE_SERVICE'; //บริการฟรี
    const FREE_WAGE = 'FREE_WAGE'; //ฟรีค่าแรง
}
