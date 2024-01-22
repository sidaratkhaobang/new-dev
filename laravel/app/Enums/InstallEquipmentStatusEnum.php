<?php

namespace App\Enums;

abstract class InstallEquipmentStatusEnum 
{
    const WAITING = 'WAITING'; // รอติดตั้ง 
    const PENDING_REVIEW = 'PENDING_REVIEW'; // รออนุมัติใบสั่งซื้อ
    const CONFIRM = 'CONFIRM'; // อนุมัติใบสั่งซื้อ
    const INSTALL_IN_PROCESS = 'INSTALL_IN_PROCESS'; 
    const OVERDUE = 'OVERDUE';
    const DUE = 'DUE';
    const INSTALL_COMPLETE = 'INSTALL_COMPLETE';
    const INSPECT_IN_PROCESS = 'INSPECT_IN_PROCESS';
    const INSPECT_FAIL = 'INSPECT_FAIL';
    const REJECT = 'REJECT';
    const CANCEL = 'CANCEL';
    const COMPLETE = 'COMPLETE';
}