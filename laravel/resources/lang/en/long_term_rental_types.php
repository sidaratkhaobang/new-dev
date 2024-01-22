<?php

use App\Enums\AuctionStatusEnum;

return [
    'page_title' => 'ประเภทงาน',
    'name' => 'ชื่องาน',
    'type' => 'ประเภทงาน',
    'add_new' => 'เพิ่มประเภทงาน',
    'total_items' => 'รายการทั้งหมด',

    'type_' . AuctionStatusEnum::AUCTION => 'ประมูล',
    'type_' . AuctionStatusEnum::NO_AUCTION => 'ไม่ประมูล',
];
