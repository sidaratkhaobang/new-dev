<?php

namespace App\Enums;

abstract class GPSJobTypeEnum
{
    const CONTRACT_EXPIRED = 'CONTRACT_EXPIRED'; //รถหมดสัญญา (QA)
    const AUCTION_SALE = 'AUCTION_SALE'; //รถขายประมูล  (AUC)
    const TOTAL_LOSS = 'TOTAL_LOSS'; //รถ Total loss (RMD)
    const CONTRACT_BRANCH = 'CONTRACT_BRANCH'; //รถหมดสัญญาจากสาขา (AUC)
    const AFTER_CONTRACT_EXPIRED = 'AFTER_CONTRACT_EXPIRED'; //ลูกค้าซื้อรถหลังหมดสัญญา (AUU)
}
