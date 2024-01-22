<?php

namespace App\Enums;

abstract class GPSStopStatusEnum
{
    const ALERT_STOP_SIGNAL = 'ALERT_STOP_SIGNAL'; //แจ้งหยุดสัญญาณ
    const ALERT_REMOVE_GPS = 'ALERT_REMOVE_GPS'; //แจ้งถอด GPS
    const WAIT_STOP_SIGNAL = 'WAIT_STOP_SIGNAL'; //รอหยุดสัญญาณ
    const WAIT_REMOVE_GPS = 'WAIT_REMOVE_GPS'; //รอถอด GPS
    const STOP_SIGNAL = 'STOP_SIGNAL'; //หยุดสัญญาณแล้ว
    const REMOVE_GPS = 'REMOVE_GPS'; //ถอด GPS แล้ว
    const NOT_INSTALL = 'NOT_INSTALL'; //ไม่ได้ติดตั้ง GPS
}
