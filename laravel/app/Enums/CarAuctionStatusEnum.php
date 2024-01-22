<?php

namespace App\Enums;

abstract class CarAuctionStatusEnum
{
    const PENDING_SALE = 'PENDING_SALE'; //รอส่งขาย
    const READY_AUCTION = 'READY_AUCTION'; //พร้อมส่งประมูล
    const SEND_AUCTION = 'SEND_AUCTION'; //ส่งรถไปยัง Auction
    const PENDING_AUCTION = 'PENDING_AUCTION'; //กำลังรอประมูล
    const SOLD_OUT = 'SOLD_OUT'; //ขายแล้ว
    const CHANGE_AUCTION = 'CHANGE_AUCTION'; //เปลี่ยนสถานที่ประมูล
}
