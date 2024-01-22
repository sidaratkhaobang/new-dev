<?php

namespace App\Enums;

abstract class SellingPriceStatusEnum
{
    const PRE_SALE_PRICE = 'PRE_SALE_PRICE'; //รอทำราคาขายล่วงหน้า
    const REQUEST_APPROVE = 'REQUEST_APPROVE'; //รอส่งขออนุมัติ
    const PENDING_REVIEW = 'PENDING_REVIEW'; //รออนุมัติ
    const CONFIRM = 'CONFIRM'; // อนุมัติ
    const REJECT = 'REJECT'; // ไม่อนุมัติ

    const PENDING_SALE = 'PENDING_SALE'; //รถรอส่งขาย
    const PENDING_FINANCE = 'PENDING_FINANCE'; //รอปิดไฟแนนซ์
    const PENDING_TRANSFER = 'PENDING_TRANSFER'; //รอโอนกรรมสิทธ์
}
